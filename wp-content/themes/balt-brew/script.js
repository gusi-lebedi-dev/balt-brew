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
    if (typeof mobileScreen.addEventListener === 'function') {
        mobileScreen.addEventListener('change', selectVideo);
        reducedMotion.addEventListener('change', selectVideo);
    } else {
        mobileScreen.addListener(selectVideo);
        reducedMotion.addListener(selectVideo);
    }
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
    const aboutPrevButton = document.querySelector('.about__tabs-arrow--prev');
    const aboutNextButton = document.querySelector('.about__tabs-arrow--next');

    if (aboutTabs.length && aboutPanels.length) {
        const tabs = Array.from(aboutTabs);
        const revealAboutTab = (tab) => {
            const list = tab.parentElement;
            const bounds = list.getBoundingClientRect();
            const card = tab.getBoundingClientRect();
            if (card.left < bounds.left || card.right > bounds.right) {
                list.scrollLeft += card.left - bounds.left - (list.clientWidth - card.width) / 2;
            }
        };

        const cycleAboutTab = (step) => {
            const currentIndex = tabs.findIndex((tab) => tab.getAttribute('aria-selected') === 'true');
            const nextIndex = ((currentIndex === -1 ? 0 : currentIndex) + step + tabs.length) % tabs.length;
            tabs[nextIndex].click();
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
                const index = tabs.indexOf(tab);
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

        aboutPrevButton?.addEventListener('click', () => cycleAboutTab(-1));
        aboutNextButton?.addEventListener('click', () => cycleAboutTab(1));

        requestAnimationFrame(() => revealAboutTab(tabs.find(tab => tab.getAttribute('aria-selected') === 'true') || aboutTabs[0]));
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
        const timeline = panel.querySelector('.timeline');
        const content = panel.querySelector('.about__history-content');
        const items = Array.from(panel.querySelectorAll('[data-about-event-target]'));
        const texts = Array.from(panel.querySelectorAll('[data-about-event]'));

        if (!timeline || !content || !items.length || !texts.length) return;

        const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
        let activeIndex = Math.max(0, items.findIndex((item) => item.classList.contains('timeline__item--active')));
        let finishTextTransition = null;
        let activeTextIncomingLayer = null;
        let finishTimelineTransition = null;
        let layoutFrame = 0;

        timeline.classList.add('timeline--animated', 'timeline--reset');
        timeline.scrollLeft = 0;
        content.setAttribute('aria-live', 'polite');
        content.setAttribute('aria-atomic', 'true');

        const eventIdAt = (index) => items[index]?.dataset.aboutEventTarget;

        const showText = (eventId) => {
            texts.forEach((text) => {
                const isActive = text.dataset.aboutEvent === eventId;
                text.classList.toggle('about__timeline-text--active', isActive);
                text.setAttribute('aria-hidden', String(!isActive));
            });
        };

        const updateItems = () => {
            items.forEach((item, index) => {
                const isActive = index === activeIndex;
                item.classList.toggle('timeline__item--active', isActive);
                item.setAttribute('aria-pressed', String(isActive));
                item.tabIndex = isActive ? 0 : -1;
                if (isActive) item.setAttribute('aria-current', 'date');
                else item.removeAttribute('aria-current');
            });
        };

        const shiftForItem = (index) => {
            const item = items[index];
            if (!item || timeline.clientWidth === 0) return null;
            return timeline.clientWidth / 2 - (item.offsetLeft + item.offsetWidth / 2);
        };

        const centerActiveItem = (instant = false) => {
            const shift = shiftForItem(activeIndex);
            if (shift === null) return;

            if (instant) timeline.classList.add('timeline--reset');
            timeline.scrollLeft = 0;
            timeline.style.setProperty('--timeline-shift', `${Math.round(shift * 100) / 100}px`);

            if (instant) {
                void timeline.offsetWidth;
                requestAnimationFrame(() => timeline.classList.remove('timeline--reset'));
            }
        };

        const scheduleCenter = (instant = true) => {
            cancelAnimationFrame(layoutFrame);
            layoutFrame = requestAnimationFrame(() => centerActiveItem(instant));
        };

        const cloneTextLayer = (source, modifier) => {
            const clone = source.cloneNode(true);
            clone.classList.add('about__timeline-fx-layer', modifier);
            clone.classList.remove('about__timeline-text--active');
            clone.removeAttribute('data-about-event');
            clone.setAttribute('aria-hidden', 'true');
            clone.setAttribute('inert', '');
            clone.querySelectorAll('[id]').forEach((element) => element.removeAttribute('id'));
            return clone;
        };

        const animateText = (fromEventId, toEventId) => {
            let outgoingVisual = null;
            if (activeTextIncomingLayer?.isConnected) {
                const style = getComputedStyle(activeTextIncomingLayer);
                outgoingVisual = {
                    opacity: style.opacity,
                    transform: style.transform
                };
            }
            if (finishTextTransition) finishTextTransition();

            const outgoing = texts.find((text) => text.dataset.aboutEvent === fromEventId);
            const incoming = texts.find((text) => text.dataset.aboutEvent === toEventId);
            if (!outgoing || !incoming || outgoing === incoming || reducedMotion.matches) {
                showText(toEventId);
                return;
            }

            content.scrollTop = 0;
            const effect = document.createElement('div');
            effect.className = 'about__timeline-fx';
            effect.setAttribute('aria-hidden', 'true');
            const outgoingLayer = cloneTextLayer(outgoing, 'about__timeline-fx-layer--out');
            const incomingLayer = cloneTextLayer(incoming, 'about__timeline-fx-layer--in');
            if (outgoingVisual) {
                outgoingLayer.style.setProperty('--timeline-copy-start-opacity', outgoingVisual.opacity);
                outgoingLayer.style.setProperty('--timeline-copy-start-transform', outgoingVisual.transform);
            }
            effect.append(outgoingLayer, incomingLayer);
            content.style.minHeight = `${content.getBoundingClientRect().height}px`;
            showText(toEventId);
            content.append(effect);
            content.classList.add('about__history-content--switching');
            activeTextIncomingLayer = incomingLayer;

            let finished = false;
            let timeout = 0;
            const finish = () => {
                if (finished) return;
                finished = true;
                window.clearTimeout(timeout);
                showText(toEventId);
                content.classList.remove('about__history-content--switching');
                content.style.removeProperty('min-height');
                effect.remove();
                if (activeTextIncomingLayer === incomingLayer) activeTextIncomingLayer = null;
                if (finishTextTransition === finish) finishTextTransition = null;
            };

            finishTextTransition = finish;
            incomingLayer.addEventListener('animationend', (event) => {
                if (event.target === incomingLayer) finish();
            });
            timeout = window.setTimeout(finish, 650);
        };

        const animateTimeline = (previousIndex, nextIndex, nextShift) => {
            if (finishTimelineTransition) finishTimelineTransition();
            if (reducedMotion.matches || nextShift === null) return;

            const previousDot = items[previousIndex]?.querySelector('.timeline__dot');
            if (!previousDot) return;
            const timelineBounds = timeline.getBoundingClientRect();
            const dotBounds = previousDot.getBoundingClientRect();
            const ghostStartX = dotBounds.left - timelineBounds.left + dotBounds.width / 2;
            const previousItem = items[previousIndex];
            const ghostEndX = previousItem.offsetLeft + previousItem.offsetWidth / 2 + nextShift;
            const ghost = document.createElement('span');
            ghost.className = 'timeline__dot timeline__active-ghost';
            ghost.setAttribute('aria-hidden', 'true');
            ghost.style.left = `${ghostStartX}px`;
            ghost.style.top = `${dotBounds.top - timelineBounds.top + dotBounds.height / 2}px`;
            ghost.style.setProperty('--timeline-ghost-shift', `${ghostEndX - ghostStartX}px`);
            timeline.append(ghost);

            const incomingItem = items[nextIndex];
            incomingItem.classList.add('timeline__item--activating');
            let finished = false;
            let timeout = 0;
            const finish = () => {
                if (finished) return;
                finished = true;
                window.clearTimeout(timeout);
                incomingItem.classList.remove('timeline__item--activating');
                ghost.remove();
                if (finishTimelineTransition === finish) finishTimelineTransition = null;
            };

            finishTimelineTransition = finish;
            ghost.addEventListener('transitionend', (event) => {
                if (event.target === ghost && event.propertyName === 'transform') finish();
            });
            void ghost.offsetWidth;
            ghost.classList.add('timeline__active-ghost--leaving');
            timeout = window.setTimeout(finish, 620);
        };

        const selectItem = (nextIndex, moveFocus = false) => {
            if (nextIndex < 0 || nextIndex >= items.length) return;
            if (nextIndex === activeIndex) {
                if (moveFocus) items[nextIndex].focus({ preventScroll: true });
                return;
            }

            if (finishTimelineTransition) finishTimelineTransition();
            const previousIndex = activeIndex;
            const previousEventId = eventIdAt(previousIndex);
            const nextShift = shiftForItem(nextIndex);
            animateTimeline(previousIndex, nextIndex, nextShift);
            activeIndex = nextIndex;
            const nextEventId = eventIdAt(activeIndex);

            updateItems();
            centerActiveItem(reducedMotion.matches);
            animateText(previousEventId, nextEventId);

            if (moveFocus) items[activeIndex].focus({ preventScroll: true });
        };

        items.forEach((item, index) => {
            item.addEventListener('click', () => selectItem(index));
            item.addEventListener('keydown', (event) => {
                let nextIndex;
                if (event.key === 'ArrowRight') nextIndex = Math.min(items.length - 1, index + 1);
                if (event.key === 'ArrowLeft') nextIndex = Math.max(0, index - 1);
                if (event.key === 'Home') nextIndex = 0;
                if (event.key === 'End') nextIndex = items.length - 1;
                if (nextIndex === undefined) return;
                event.preventDefault();
                selectItem(nextIndex, true);
            });
        });

        let touchStart = null;
        let suppressSwipeClick = false;
        let suppressSwipeClickTimer = 0;
        const allowTouchClick = () => {
            suppressSwipeClick = false;
            window.clearTimeout(suppressSwipeClickTimer);
        };
        timeline.addEventListener('touchstart', (event) => {
            allowTouchClick();
            if (event.touches.length !== 1) {
                touchStart = null;
                return;
            }
            const touch = event.touches[0];
            touchStart = { id: touch.identifier, x: touch.clientX, y: touch.clientY };
        }, { passive: true });
        timeline.addEventListener('touchcancel', () => { touchStart = null; }, { passive: true });
        timeline.addEventListener('touchend', (event) => {
            if (!touchStart) return;
            const touch = Array.from(event.changedTouches).find((item) => item.identifier === touchStart.id);
            if (!touch) return;
            const dx = touchStart.x - touch.clientX;
            const dy = touchStart.y - touch.clientY;
            touchStart = null;
            if (Math.abs(dx) < 40 || Math.abs(dx) <= Math.abs(dy) * 1.3) return;
            suppressSwipeClick = true;
            suppressSwipeClickTimer = window.setTimeout(allowTouchClick, 450);
            selectItem(Math.max(0, Math.min(items.length - 1, activeIndex + Math.sign(dx))));
        }, { passive: true });
        timeline.addEventListener('click', (event) => {
            if (!suppressSwipeClick) return;
            allowTouchClick();
            event.preventDefault();
            event.stopPropagation();
        }, true);

        updateItems();
        showText(eventIdAt(activeIndex));
        scheduleCenter(true);

        if ('ResizeObserver' in window) {
            new ResizeObserver(() => scheduleCenter(true)).observe(timeline);
        } else {
            window.addEventListener('resize', () => scheduleCenter(true), { passive: true });
        }

        new MutationObserver(() => {
            if (panel.classList.contains('about__panel--active')) scheduleCenter(true);
        }).observe(panel, { attributes: true, attributeFilter: ['class'] });

        document.fonts?.ready.then(() => scheduleCenter(true));
        const handleMotionPreference = () => {
            if (reducedMotion.matches && finishTextTransition) finishTextTransition();
            if (reducedMotion.matches && finishTimelineTransition) finishTimelineTransition();
            scheduleCenter(true);
        };
        if ('addEventListener' in reducedMotion) reducedMotion.addEventListener('change', handleMotionPreference);
        else reducedMotion.addListener(handleMotionPreference);
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

document.addEventListener('DOMContentLoaded', () => {
    const previews = document.querySelectorAll('.news__text, .author__text');
    if (!previews.length) return;

    const updatePreviewLines = () => {
        previews.forEach((preview) => {
            const styles = getComputedStyle(preview);
            const lineHeight = parseFloat(styles.lineHeight) || parseFloat(styles.fontSize) * 1.2;
            const availableHeight = Math.min(600, preview.getBoundingClientRect().height);
            const lines = Math.max(1, Math.floor(availableHeight / lineHeight + 0.0001));
            const value = String(lines);
            if (preview.style.getPropertyValue('--preview-lines') !== value) {
                preview.style.setProperty('--preview-lines', value);
            }
        });
    };

    let pendingFrame = 0;
    const scheduleUpdate = () => {
        if (pendingFrame) return;
        pendingFrame = requestAnimationFrame(() => {
            pendingFrame = 0;
            updatePreviewLines();
        });
    };

    if ('ResizeObserver' in window) {
        const observer = new ResizeObserver(scheduleUpdate);
        previews.forEach((preview) => observer.observe(preview.parentElement));
    }
    window.addEventListener('resize', scheduleUpdate);
    if (document.fonts) document.fonts.ready.then(scheduleUpdate);
    scheduleUpdate();
});
