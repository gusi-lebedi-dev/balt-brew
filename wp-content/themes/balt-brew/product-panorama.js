(() => {
    'use strict';

    function initPanorama(root) {
        const track = root.querySelector('[data-panorama-track]');
        const frames = Array.from(track.children);
        const cards = Array.from(root.querySelectorAll('.product-panorama__card'));
        const count = frames.length;
        if (count < 2 || cards.length !== count) return;

        const status = root.querySelector('[data-status]');
        const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
        const mobileLayout = window.matchMedia('(max-width: 768px)');
        const wrap = index => (index + count) % count;
        const clone = frame => frame.cloneNode(true);

        // The end copies allow the camera to cross a single hall when wrapping.
        track.prepend(clone(frames[count - 1]));
        track.append(clone(frames[0]));

        const stops = Array.from(track.children).reduce((offsets, frame) => {
            offsets.push(offsets[offsets.length - 1] + Number(frame.dataset.advance));
            return offsets;
        }, [0]);
        const stop = index => stops[index + 1];
        const trackWidth = stops[stops.length - 1];
        track.style.setProperty('--track-width', `calc(${trackWidth} * var(--unit))`);

        let active = 0;
        let pending = 0;
        let finishTravel = null;

        function setCamera(index) {
            track.style.setProperty('--camera-position', `${-100 * stop(index) / trackWidth}%`);
        }

        function reveal(index) {
            cards[active].setAttribute('aria-hidden', 'true');
            cards[active].setAttribute('inert', '');
            active = index;
            cards[active].setAttribute('aria-hidden', 'false');
            cards[active].removeAttribute('inert');
            root.dataset.slide = String(index);
            status.textContent = cards[index].getAttribute('aria-label');
        }

        function prepareNeighbors(index) {
            for (const offset of [-1, 0, 1]) {
                frames[wrap(index + offset)].querySelectorAll('img[loading="lazy"]').forEach(img => {
                    img.loading = 'eager';
                });
            }
        }

        function withoutTransition(apply) {
            root.setAttribute('data-panorama-reset', '');
            apply();
            void track.offsetWidth;
            root.removeAttribute('data-panorama-reset');
        }

        withoutTransition(() => setCamera(active));
        prepareNeighbors(active);

        function runQueue() {
            if (finishTravel || !pending) return;
            const direction = Math.sign(pending);
            pending -= direction;
            move(direction);
        }

        function move(direction) {
            const from = active;
            const cameraIndex = from + direction;
            const target = wrap(cameraIndex);
            const distance = stop(cameraIndex) - stop(from);
            const outgoing = cards[from];
            const incoming = cards[target];
            const moveCardWithScene = !mobileLayout.matches;

            prepareNeighbors(target);
            if (moveCardWithScene) {
                withoutTransition(() => incoming.style.setProperty('--card-x', String(distance)));
                // Resolve the start position before enabling the card and moving the track.
                void track.offsetWidth;

                outgoing.setAttribute('data-leaving', '');
                reveal(target);
                outgoing.style.setProperty('--card-x', String(-distance));
                incoming.style.removeProperty('--card-x');
            }
            setCamera(cameraIndex);

            let timer;
            const onTransitionEnd = event => {
                if (event.target === track && event.propertyName === 'transform') finish();
            };
            const finish = () => {
                clearTimeout(timer);
                track.removeEventListener('transitionend', onTransitionEnd);
                track.removeEventListener('transitioncancel', onTransitionEnd);
                withoutTransition(() => {
                    setCamera(target);
                    outgoing.removeAttribute('data-leaving');
                    outgoing.style.removeProperty('--card-x');
                    if (!moveCardWithScene) reveal(target);
                });
                finishTravel = null;
                if (pending) requestAnimationFrame(runQueue);
            };

            finishTravel = finish;
            const duration = getComputedStyle(track).transitionDuration;
            const milliseconds = parseFloat(duration) * (duration.endsWith('ms') ? 1 : 1000);
            if (!milliseconds) {
                finish();
            } else {
                track.addEventListener('transitionend', onTransitionEnd);
                track.addEventListener('transitioncancel', onTransitionEnd);
                timer = setTimeout(finish, milliseconds + 120);
            }
        }

        function navigate(direction) {
            pending = Math.max(-count, Math.min(count, pending + direction));
            runQueue();
        }

        root.querySelector('[data-prev]').addEventListener('click', () => navigate(-1));
        root.querySelector('[data-next]').addEventListener('click', () => navigate(1));
        root.tabIndex = 0;
        root.addEventListener('keydown', event => {
            if (event.altKey || event.ctrlKey || event.metaKey || event.target.closest('a, input, textarea, select, [contenteditable="true"]')) return;
            if (event.key === 'ArrowLeft' || event.key === 'ArrowRight') {
                event.preventDefault();
                navigate(event.key === 'ArrowLeft' ? -1 : 1);
            } else if (event.key === 'Home' || event.key === 'End') {
                event.preventDefault();
                pending = 0;
                if (finishTravel) finishTravel();
                const index = event.key === 'Home' ? 0 : count - 1;
                withoutTransition(() => {
                    setCamera(index);
                    reveal(index);
                });
                prepareNeighbors(index);
            }
        });
        reducedMotion.addEventListener('change', () => {
            if (reducedMotion.matches && finishTravel) finishTravel();
        });

        let start = null;
        root.addEventListener('touchstart', event => {
            const touch = event.touches[0];
            start = event.touches.length === 1 && !event.target.closest('button, a')
                ? { id: touch.identifier, x: touch.clientX, y: touch.clientY }
                : null;
        }, { passive: true });
        root.addEventListener('touchcancel', () => { start = null; }, { passive: true });
        root.addEventListener('touchend', event => {
            if (!start) return;
            const touch = Array.from(event.changedTouches).find(item => item.identifier === start.id);
            if (!touch) return;
            const dx = start.x - touch.clientX;
            const dy = start.y - touch.clientY;
            start = null;
            if (Math.abs(dx) >= 48 && Math.abs(dx) > Math.abs(dy) * 1.4) {
                navigate(dx > 0 ? 1 : -1);
            }
        }, { passive: true });
    }

    document.querySelectorAll('[data-product-panorama]').forEach(initPanorama);
})();
