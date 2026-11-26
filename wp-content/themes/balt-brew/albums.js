(() => {
    'use strict';
    const root = document.querySelector('[data-albums]');
    const grid = root?.querySelector('[data-albums-grid]');
    if (!grid) return;

    const status = root.querySelector('[data-albums-status]');
    const pagination = root.querySelector('[data-albums-pagination]');
    let nextLink = root.querySelector('[data-albums-next]');
    let pending = null;
    let autoLoad = true;
    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
    const animate = (items) => {
        if (reducedMotion.matches) return;
        items.forEach((item, index) => {
            item.style.setProperty('--album-delay', `${(index % 4) * 55}ms`);
            item.classList.add('albums__item--enter');
        });
    };
    animate(Array.from(grid.children));

    // The link is also ordinary server pagination when JavaScript is unavailable.
    const loadNext = () => {
        if (pending) return pending;
        if (!nextLink) return Promise.resolve(false);
        const link = nextLink;
        const url = new URL(link.href, window.location.href);
        if (url.origin !== window.location.origin) return Promise.resolve(false);
        grid.setAttribute('aria-busy', 'true');
        link.setAttribute('aria-disabled', 'true');
        link.textContent = 'Загрузка…';
        if (status) status.textContent = 'Загружаем следующую порцию…';

        pending = (async () => {
            const controller = new AbortController();
            const timeout = window.setTimeout(() => controller.abort(), 15000);
            try {
                const response = await fetch(url.href, { signal: controller.signal, credentials: 'same-origin' });
                if (!response.ok) throw new Error('Page unavailable');
                const doc = new DOMParser().parseFromString(await response.text(), 'text/html');
                const incoming = doc.querySelector('[data-albums]');
                if (!incoming || incoming.dataset.albumsKind !== root.dataset.albumsKind || incoming.dataset.albumId !== root.dataset.albumId) {
                    throw new Error('Unexpected page');
                }
                const existingUrls = new Set(Array.from(grid.children, (item) => item.getAttribute('href')));
                const items = Array.from(incoming.querySelector('[data-albums-grid]')?.children || [])
                    .filter((item) => !existingUrls.has(item.getAttribute('href')));
                if (!items.length) throw new Error('Empty page');
                const newLink = incoming.querySelector('[data-albums-next]');
                if (newLink && new URL(newLink.href, url.href).href === url.href) throw new Error('Repeated page');

                animate(items);
                grid.append(...items);
                if (status) status.textContent = '';
                if (newLink) {
                    link.href = new URL(newLink.getAttribute('href'), url.href).href;
                    link.textContent = 'Показать ещё';
                    link.removeAttribute('aria-disabled');
                } else {
                    // Keep keyboard focus near the user's last action when the link disappears.
                    if (document.activeElement === link) items[0].focus({ preventScroll: true });
                    link.remove();
                    nextLink = null;
                    if (pagination) pagination.hidden = true;
                    observer?.disconnect();
                }
                autoLoad = true;
                return true;
            } catch (_) {
                autoLoad = false;
                link.textContent = 'Повторить загрузку';
                link.removeAttribute('aria-disabled');
                if (status) status.textContent = 'Не удалось загрузить. Нажмите «Повторить загрузку».';
                return false;
            } finally {
                window.clearTimeout(timeout);
                grid.removeAttribute('aria-busy');
                pending = null;
            }
        })();
        return pending;
    };

    root.addEventListener('click', (event) => {
        if (event.target.closest('[data-albums-next]') && !event.ctrlKey && !event.metaKey && !event.shiftKey && !event.altKey) {
            event.preventDefault();
            loadNext();
        }
    });

    const observer = 'IntersectionObserver' in window ? new IntersectionObserver((entries) => {
        if (entries.some((entry) => entry.isIntersecting) && autoLoad && !document.querySelector('[data-album-lightbox][open]')) loadNext();
    }, { rootMargin: '0px 0px 200px 0px' }) : null;
    if (nextLink && pagination) observer?.observe(pagination);

    const dialog = document.querySelector('[data-album-lightbox]');
    if (!dialog || typeof dialog.showModal !== 'function') return;
    const photo = dialog.querySelector('[data-photo-image]');
    const caption = dialog.querySelector('[data-photo-caption]');
    const count = dialog.querySelector('[data-photo-count]');
    const previous = dialog.querySelector('[data-photo-prev]');
    const next = dialog.querySelector('[data-photo-next]');
    let current = 0;
    let trigger = null;
    let moving = false;
    let generation = 0;
    const photos = () => Array.from(grid.querySelectorAll('[data-photo]'));

    const updateButtons = () => {
        previous.disabled = moving || current === 0;
        next.disabled = moving || (current === photos().length - 1 && !nextLink);
    };
    const show = (index) => {
        const entry = photos()[index];
        if (!entry) return;
        current = index;
        photo.alt = entry.dataset.alt || '';
        photo.src = entry.href;
        caption.textContent = entry.dataset.caption || '';
        count.textContent = `${entry.dataset.photoNumber} / ${root.dataset.total}`;
        updateButtons();
    };

    grid.addEventListener('click', (event) => {
        const entry = event.target.closest('[data-photo]');
        if (!entry || event.ctrlKey || event.metaKey || event.shiftKey || event.altKey) return;
        event.preventDefault();
        trigger = entry;
        generation++;
        show(photos().indexOf(entry));
        dialog.showModal();
        document.body.classList.add('album-lightbox-open');
    });

    const move = async (direction) => {
        if (moving || !dialog.open) return;
        const target = current + direction;
        if (target < 0) return;
        if (target < photos().length) {
            show(target);
            return;
        }
        if (!nextLink) return;
        moving = true;
        const requestGeneration = generation;
        updateButtons();
        count.textContent = 'Загрузка…';
        const loaded = await loadNext();
        moving = false;
        if (!dialog.open || requestGeneration !== generation) {
            if (dialog.open) updateButtons();
            return;
        }
        if (loaded) show(target);
        else {
            count.textContent = 'Не удалось загрузить фото. Нажмите →, чтобы повторить.';
            updateButtons();
        }
    };

    previous.addEventListener('click', () => move(-1));
    next.addEventListener('click', () => move(1));
    dialog.querySelector('[data-photo-close]').addEventListener('click', () => dialog.close());
    dialog.addEventListener('click', (event) => {
        if (event.target === dialog || event.target.classList.contains('album-lightbox__panel')) dialog.close();
    });
    dialog.addEventListener('keydown', (event) => {
        if (event.key === 'ArrowLeft' || event.key === 'ArrowRight') {
            event.preventDefault();
            move(event.key === 'ArrowLeft' ? -1 : 1);
        }
    });
    dialog.addEventListener('close', () => {
        generation++;
        document.body.classList.remove('album-lightbox-open');
        trigger?.focus({ preventScroll: true });
        if (nextLink && pagination) {
            observer?.unobserve(pagination);
            observer?.observe(pagination);
        }
    });
    photo.addEventListener('error', () => {
        if (dialog.open) count.textContent = 'Не удалось открыть изображение. Можно перейти к следующему фото.';
    });

    let touchStart = null;
    photo.addEventListener('touchstart', (event) => {
        touchStart = event.touches.length === 1 ? { x: event.touches[0].clientX, y: event.touches[0].clientY } : null;
    }, { passive: true });
    photo.addEventListener('touchend', (event) => {
        if (!touchStart || !event.changedTouches.length) return;
        const dx = event.changedTouches[0].clientX - touchStart.x;
        const dy = event.changedTouches[0].clientY - touchStart.y;
        touchStart = null;
        if (Math.abs(dx) > 50 && Math.abs(dx) > Math.abs(dy) * 1.5) move(dx < 0 ? 1 : -1);
    }, { passive: true });
    photo.addEventListener('touchcancel', () => { touchStart = null; }, { passive: true });
})();
