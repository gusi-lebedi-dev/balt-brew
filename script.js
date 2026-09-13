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
    const mobileScreen = window.matchMedia('(max-width: 767px)');
    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
    const heroGroups = ['desktop', 'mobile'].map(format => ({
        format,
        intro: document.querySelector(`[data-hero-format="${format}"][data-hero-intro]`),
        loop: document.querySelector(`[data-hero-format="${format}"][data-hero-loop]`),
        loopStarted: false
    })).filter(group => group.intro && group.loop);
    let activeGroup = null;
    let activeGeneration = 0;
    let loaderComplete = false;
    let loaderTimeout = 0;
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
            if (group === activeGroup) finishLoading();
        });
        group.intro.addEventListener('ended', () => startLoop(group));
        group.intro.addEventListener('error', () => {
            if (group !== activeGroup) return;
            finishLoading();
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
        if (nextGroup.intro.readyState >= 2 || nextGroup.intro.error) finishLoading();
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

    const tabs = document.querySelectorAll('.about__card');
    const panels = document.querySelectorAll('.about__panel');
    const timeline = document.querySelector('.timeline');

    if (tabs.length && panels.length) {
        tabs.forEach((tab) => {
            tab.addEventListener('click', () => {
                if (tab.dataset.pos === 'center') return;

                const centerTab = Array.from(tabs).find((t) => t.dataset.pos === 'center');
                const clickedPos = tab.dataset.pos;

                tab.dataset.pos = 'center';
                if (centerTab) centerTab.dataset.pos = clickedPos;

                panels.forEach((p) => p.classList.toggle('about__panel--active', p.dataset.panel === tab.dataset.tab));
                if (timeline) timeline.classList.toggle('timeline--visible', tab.dataset.tab === 'history');
            });
        });
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

    const timelineItems = document.querySelectorAll('.timeline__item');
    const timelineTexts = document.querySelectorAll('.about__timeline-text');

    if (timelineItems.length && timelineTexts.length) {
        timelineItems.forEach((item) => {
            item.addEventListener('click', () => {
                const year = item.dataset.year;

                timelineItems.forEach((i) => {
                    const isActive = i === item;
                    i.classList.toggle('timeline__item--active', isActive);
                    const dotImg = i.querySelector('.timeline__dot');
                    if (dotImg) {
                        dotImg.src = isActive
                            ? 'images/about/timeline-dot-active.png'
                            : 'images/about/timeline-dot.png';
                    }
                });

                timelineTexts.forEach((t) => t.classList.toggle('about__timeline-text--active', t.dataset.year === year));
            });
        });
    }
});
