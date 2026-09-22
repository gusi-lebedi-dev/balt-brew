document.addEventListener('DOMContentLoaded', () => {
    // Storage can be unavailable in private/restricted contexts; controls still work.
    const cookieBanner = document.getElementById('cookieBanner');
    const acceptCookie = document.getElementById('acceptCookie');
    if (cookieBanner && acceptCookie) {
        let accepted = false;
        try { accepted = localStorage.getItem('cookieAccepted') === 'true'; } catch (_) { /* Use this visit only. */ }
        cookieBanner.classList.toggle('cookie-banner--visible', !accepted);
        acceptCookie.addEventListener('click', () => {
            try { localStorage.setItem('cookieAccepted', 'true'); } catch (_) { /* Dismiss without persistence. */ }
            cookieBanner.classList.remove('cookie-banner--visible');
        });
    }

    const loader = document.getElementById('loader');
    const loaderProgress = document.getElementById('loaderProgress');
    const heroVideos = Array.from(document.querySelectorAll('.hero__video'));
    const heroScrollButton = document.querySelector('[data-hero-scroll]');
    const mobileScreen = window.matchMedia('(max-width: 767px)');
    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
    const heroGroups = ['desktop', 'mobile'].map(format => ({
        format,
        intro: document.querySelector(`[data-hero-format="${format}"][data-hero-intro]`),
        loop: document.querySelector(`[data-hero-format="${format}"][data-hero-loop]`),
        loopStarted: false
    })).filter(group => group.intro && group.loop);

    if (heroScrollButton) {
        heroScrollButton.addEventListener('click', () => {
            const target = document.querySelector(heroScrollButton.dataset.heroScroll);
            if (!target) return;

            target.scrollIntoView({
                behavior: reducedMotion.matches ? 'auto' : 'smooth',
                block: 'start'
            });
        });
    }
    let activeGroup = null;
    let activeGeneration = 0;
    let loaderComplete = false;
    let loaderTimeout = 0;
    let heroScrollTimeout = 0;
    const scheduleHeroScroll = group => {
        if (!heroScrollButton || !heroScrollButton.hidden || group !== activeGroup || heroScrollTimeout) return;
        const generation = activeGeneration;
        heroScrollTimeout = window.setTimeout(() => {
            heroScrollTimeout = 0;
            if (group !== activeGroup || generation !== activeGeneration) return;
            heroScrollButton.hidden = false;
            requestAnimationFrame(() => requestAnimationFrame(() => {
                if (group === activeGroup && generation === activeGeneration) {
                    heroScrollButton.classList.add('hero__scroll--visible');
                }
            }));
        }, 8000);
    };
    const finishLoading = () => {
        if (loaderComplete) return;
        loaderComplete = true;
        clearTimeout(loaderTimeout);
        if (loaderProgress) loaderProgress.style.width = '100%';
        if (loader) {
            loader.classList.add('loader--hidden');
            loader.setAttribute('aria-hidden', 'true');
        }
    };
    const setVideoVisible = (video, visible) => {
        if (!video) return;
        video.classList.toggle('hero__video--visible', visible);
    };
    const loadVideo = video => {
        if (!video || video.getAttribute('src') || !video.dataset.src) return;
        video.src = video.dataset.src;
        video.load();
    };
    const playVideo = video => {
        if (!video || document.hidden || reducedMotion.matches) return;
        try {
            const result = video.play();
            if (result && typeof result.catch === 'function') result.catch(() => {
                // Autoplay restrictions still leave the first available frame visible.
                if (activeGroup && video === activeGroup.intro) finishLoading();
            });
        } catch (_) {
            if (activeGroup && video === activeGroup.intro) finishLoading();
        }
    };

    const revealLoop = (group, generation) => {
        if (group !== activeGroup || generation !== activeGeneration) return;
        setVideoVisible(group.loop, true);
        // Keep the intro's last frame underneath until the short crossfade completes.
        window.setTimeout(() => {
            if (group === activeGroup && generation === activeGeneration) setVideoVisible(group.intro, false);
        }, 200);
    };
    const startLoop = group => {
        if (group !== activeGroup || group.loopStarted || document.hidden || reducedMotion.matches) return;
        group.loopStarted = true;
        const generation = activeGeneration;
        loadVideo(group.loop);
        group.loop.currentTime = 0;
        playVideo(group.loop);

        if ('requestVideoFrameCallback' in group.loop) {
            group.loop.requestVideoFrameCallback(() => revealLoop(group, generation));
        } else {
            const revealAfterPaint = () => requestAnimationFrame(() => requestAnimationFrame(() => revealLoop(group, generation)));
            if (group.loop.readyState >= 2) revealAfterPaint();
            else group.loop.addEventListener('loadeddata', revealAfterPaint, { once: true });
        }
    };

    heroGroups.forEach(group => {
        group.intro.addEventListener('loadeddata', () => {
            if (group === activeGroup) {
                finishLoading();
                scheduleHeroScroll(group);
            }
        });
        group.intro.addEventListener('ended', () => startLoop(group));
        group.intro.addEventListener('error', () => {
            if (group !== activeGroup) return;
            finishLoading();
            scheduleHeroScroll(group);
            startLoop(group);
        });
    });

    const selectVideo = () => {
        const format = mobileScreen.matches ? 'mobile' : 'desktop';
        const nextGroup = heroGroups.find(group => group.format === format) || heroGroups[0];
        if (!nextGroup) return finishLoading();

        if (nextGroup === activeGroup) {
            if (reducedMotion.matches) {
                nextGroup.intro.pause();
                nextGroup.loop.pause();
            } else if (nextGroup.loopStarted) {
                playVideo(nextGroup.loop);
            } else if (nextGroup.intro.ended) {
                startLoop(nextGroup);
            } else {
                playVideo(nextGroup.intro);
            }
            return;
        }

        activeGeneration += 1;
        window.clearTimeout(heroScrollTimeout);
        heroScrollTimeout = 0;
        if (heroScrollButton && !heroScrollButton.classList.contains('hero__scroll--visible')) {
            heroScrollButton.hidden = true;
        }
        activeGroup = nextGroup;
        heroVideos.forEach(video => {
            video.pause();
            setVideoVisible(video, false);
            video.preload = video.dataset.heroFormat === format ? 'auto' : 'none';
        });
        nextGroup.loopStarted = false;
        loadVideo(nextGroup.intro);
        loadVideo(nextGroup.loop);
        if (nextGroup.intro.readyState >= 1) nextGroup.intro.currentTime = 0;
        if (nextGroup.loop.readyState >= 1) nextGroup.loop.currentTime = 0;
        setVideoVisible(nextGroup.intro, true);
        if (nextGroup.intro.readyState >= 2 || nextGroup.intro.error) {
            finishLoading();
            scheduleHeroScroll(nextGroup);
        }
        if (reducedMotion.matches) nextGroup.intro.pause();
        else playVideo(nextGroup.intro);
    };
    loaderTimeout = setTimeout(finishLoading, 5000);
    selectVideo();
    mobileScreen.addEventListener('change', selectVideo);
    reducedMotion.addEventListener('change', selectVideo);
    document.addEventListener('visibilitychange', () => {
        if (!activeGroup) return;
        if (document.hidden) {
            activeGroup.intro.pause();
            activeGroup.loop.pause();
        } else if (activeGroup.loopStarted) {
            playVideo(activeGroup.loop);
        } else if (activeGroup.intro.ended) {
            startLoop(activeGroup);
        } else {
            playVideo(activeGroup.intro);
        }
    });

    const header = document.querySelector('.header');
    if (!header) return;

    const SCROLL_THRESHOLD = 160;
    let lastScrollY = window.scrollY;
    let ticking = false;

    const updateHeader = () => {
        const currentScrollY = window.scrollY;

        // Добавляем фон при скролле
        header.classList.toggle('header--scrolled', currentScrollY > SCROLL_THRESHOLD);

        // Скрываем хедер при скролле вниз, показываем при скролле вверх
        if (currentScrollY > lastScrollY && currentScrollY > SCROLL_THRESHOLD) {
            // Скролл вниз
            header.classList.add('header--hidden');
        } else {
            // Скролл вверх
            header.classList.remove('header--hidden');
        }

        lastScrollY = currentScrollY;
        ticking = false;
    };

    const requestTick = () => {
        if (!ticking) {
            window.requestAnimationFrame(updateHeader);
            ticking = true;
        }
    };

    updateHeader();
    window.addEventListener('scroll', requestTick, { passive: true });

    // Mobile burger menu
    const burger = document.querySelector('.header__burger');
    const mobileMenu = document.querySelector('.header__mobile-menu');

    if (burger && mobileMenu) {
        burger.addEventListener('click', () => {
            mobileMenu.classList.toggle('header__mobile-menu--open');
            burger.setAttribute('aria-expanded', mobileMenu.classList.contains('header__mobile-menu--open'));
        });

        // Close menu when clicking on links
        const mobileLinks = document.querySelectorAll('.header__mobile-link');
        mobileLinks.forEach(link => {
            link.addEventListener('click', () => {
                mobileMenu.classList.remove('header__mobile-menu--open');
                burger.setAttribute('aria-expanded', 'false');
            });
        });
    }

    const aboutTabs = document.querySelectorAll('[data-about-tab]');
    const aboutPanels = document.querySelectorAll('[data-about-panel]');

    if (aboutTabs.length && aboutPanels.length) {
        const revealAboutTab = (tab) => {
            const list = tab.parentElement;
            const bounds = list.getBoundingClientRect();
            const card = tab.getBoundingClientRect();
            if (card.left < bounds.left || card.right > bounds.right) {
                list.scrollLeft += card.left - bounds.left - (list.clientWidth - card.width) / 2;
            }
        };
        aboutTabs.forEach((tab) => {
            tab.addEventListener('click', () => {
                const tabId = tab.dataset.aboutTab;

                aboutTabs.forEach((button) => {
                    const isActive = button === tab;
                    button.classList.toggle('about__card--active', isActive);
                    button.setAttribute('aria-selected', String(isActive));
                    button.tabIndex = isActive ? 0 : -1;
                });

                aboutPanels.forEach((panel) => {
                    panel.classList.toggle('about__panel--active', panel.dataset.aboutPanel === tabId);
                });
                requestAnimationFrame(() => revealAboutTab(tab));
            });
            tab.addEventListener('keydown', (event) => {
                const index = Array.from(aboutTabs).indexOf(tab);
                let target;
                if (event.key === 'ArrowRight') target = (index + 1) % aboutTabs.length;
                if (event.key === 'ArrowLeft') target = (index - 1 + aboutTabs.length) % aboutTabs.length;
                if (event.key === 'Home') target = 0;
                if (event.key === 'End') target = aboutTabs.length - 1;
                if (target === undefined) return;
                event.preventDefault();
                aboutTabs[target].click();
                aboutTabs[target].focus({ preventScroll: true });
            });
        });
        requestAnimationFrame(() => revealAboutTab(Array.from(aboutTabs).find(tab => tab.getAttribute('aria-selected') === 'true') || aboutTabs[0]));
    }

    const brewerSlides = document.querySelectorAll('.about__brewers-slide');
    const brewerPrevBtn = document.querySelector('.about__brewers-arrow--prev');
    const brewerNextBtn = document.querySelector('.about__brewers-arrow--next');

    if (brewerSlides.length && brewerPrevBtn && brewerNextBtn) {
        const showBrewerSlide = (index) => {
            const total = brewerSlides.length;
            const activeIndex = (index + total) % total;
            brewerSlides.forEach((slide, i) => {
                slide.classList.toggle('about__brewers-slide--active', i === activeIndex);
            });
        };

        const activeBrewerIndex = () => {
            const idx = Array.from(brewerSlides).findIndex((s) => s.classList.contains('about__brewers-slide--active'));
            return idx === -1 ? 0 : idx;
        };

        brewerPrevBtn.addEventListener('click', () => showBrewerSlide(activeBrewerIndex() - 1));
        brewerNextBtn.addEventListener('click', () => showBrewerSlide(activeBrewerIndex() + 1));
    }

    const aboutTimelines = document.querySelectorAll('.about__panel');

    aboutTimelines.forEach((panel) => {
        const items = panel.querySelectorAll('[data-about-event-target]');
        const texts = panel.querySelectorAll('[data-about-event]');

        if (!items.length || !texts.length) return;

        items.forEach((item) => {
            item.addEventListener('click', () => {
                const eventId = item.dataset.aboutEventTarget;

                items.forEach((button) => {
                    button.classList.toggle('timeline__item--active', button === item);
                });

                texts.forEach((text) => {
                    text.classList.toggle('about__timeline-text--active', text.dataset.aboutEvent === eventId);
                });
            });
        });
    });
});

document.addEventListener('DOMContentLoaded', () => {
    const prepositions = [
        'в', 'во',
        'на',
        'за',
        'из', 'изо', 'и',
        'к', 'ко',
        'с', 'со',
        'у',
        'о', 'об', 'обо',
        'от', 'ото',
        'до',
        'по',
        'под', 'подо',
        'над', 'надо',
        'без',
        'при',
        'про',
        'для',
        'мы',
        'через'
    ];

    const regexp = new RegExp(
        `(^|\\s)(${prepositions.join('|')})\\s+`,
        'gi'
    );

    function processTextNode(node) {
        node.nodeValue = node.nodeValue.replace(
            regexp,
            '$1$2\u00A0'
        );
    }

    function walk(node) {
        if (node.nodeType === Node.TEXT_NODE) {
            processTextNode(node);
            return;
        }

        if (node.nodeType !== Node.ELEMENT_NODE) return;

        const excludedTags = [
            'SCRIPT',
            'STYLE',
            'TEXTAREA',
            'INPUT',
            'SELECT',
            'OPTION',
            'CODE',
            'PRE'
        ];

        if (excludedTags.includes(node.tagName)) return;

        node.childNodes.forEach(walk);
    }

    walk(document.body);
});
