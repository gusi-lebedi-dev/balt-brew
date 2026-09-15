(() => {
  'use strict';

  function init(root) {
    if (root.dataset.productInitialized) return;
    const slides = Array.from(root.querySelectorAll('.product__slide'));
    const bottles = Array.from(root.querySelectorAll('.product__bottle'));
    const prevBtn = root.querySelector('[data-prev]');
    const nextBtn = root.querySelector('[data-next]');
    const track = root.querySelector('.product__track');
    const status = root.querySelector('[data-status]');
    if (!slides.length || !track || !prevBtn || !nextBtn) return;
    root.dataset.productInitialized = 'true';

    const count = slides.length;
    const wrap = value => (value % count + count) % count;
    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
    let index = wrap(Number.parseInt(root.dataset.slide, 10) || 0);
    let animating = false;
    let pendingSteps = 0;
    let finishTransition = null;
    let drainFrame = 0;
    let offset = 2060;
    const bottleTravel = 2500;
    const copyTravel = 1600;

    // Each real scene is separated by one bridge. Clones keep both wraps adjacent.
    const backgrounds = track.querySelectorAll('.product__background');
    const bridge = track.querySelector('.product__bridge');
    if (count > 1 && backgrounds.length === count && bridge) {
      const clone = element => {
        const copy = element.cloneNode(true);
        copy.setAttribute('aria-hidden', 'true');
        copy.setAttribute('data-carousel-clone', '');
        copy.removeAttribute('id');
        return copy;
      };
      track.prepend(clone(backgrounds[count - 1]), clone(bridge));
      track.append(clone(bridge), clone(backgrounds[0]));
    }
    const hasClones = Boolean(track.querySelector('[data-carousel-clone]'));
    const trackPosition = position => {
      track.style.transform = `translateX(${-((hasClones ? 1 : 0) + position) * offset}px)`;
    };
    const bottleState = (bottle, x, opacity) => {
      if (!bottle) return;
      bottle.style.setProperty('--bottle-x', `${x}px`);
      bottle.style.setProperty('--bottle-opacity', String(opacity));
    };
    const copyState = (slide, x, opacity) => {
      slide.style.setProperty('--copy-x', `${x}px`);
      slide.style.setProperty('--copy-opacity', String(opacity));
    };
    const updateAria = () => {
      prevBtn.setAttribute('aria-disabled', String(count < 2));
      nextBtn.setAttribute('aria-disabled', String(count < 2));
      slides.forEach((slide, i) => {
        const active = i === index;
        slide.setAttribute('aria-hidden', String(!active));
        slide.toggleAttribute('inert', !active);
      });
      bottles.forEach((bottle, i) => bottle.setAttribute('aria-hidden', String(i !== index)));
    };
    const resetPosition = () => {
      root.setAttribute('data-carousel-reset', '');
      trackPosition(index);
      bottles.forEach((bottle, i) => bottleState(bottle, i === index ? 0 : bottleTravel, i === index ? 1 : 0));
      slides.forEach((slide, i) => copyState(slide, i === index ? 0 : -copyTravel, i === index ? 1 : 0));
      void track.offsetWidth;
      root.removeAttribute('data-carousel-reset');
    };
    const resize = () => {
      const width = root.getBoundingClientRect().width;
      root.style.setProperty('--product-scale', String(width / 1440));
      root.style.setProperty('--product-mobile-scale', String(Math.min(.625, Math.max(.44, width / 780))));
      const nextOffset = Number.parseFloat(getComputedStyle(root).getPropertyValue('--product-offset')) || 2060;
      if (nextOffset !== offset) {
        if (finishTransition) finishTransition();
        offset = nextOffset;
        resetPosition();
      }
    };
    if ('ResizeObserver' in window) new ResizeObserver(resize).observe(root);
    else window.addEventListener('resize', resize, { passive: true });
    resize();
    root.dataset.slide = String(index);
    resetPosition();
    updateAria();

    const seconds = value => value.trim().endsWith('ms') ? Number.parseFloat(value) : Number.parseFloat(value) * 1000;
    const transitionDuration = element => {
      const style = getComputedStyle(element);
      const durations = style.transitionDuration.split(',').map(seconds);
      const delays = style.transitionDelay.split(',').map(seconds);
      return Math.max(0, ...durations.map((duration, i) => duration + delays[i % delays.length]));
    };
    const drainQueue = () => {
      if (animating || !pendingSteps) return;
      const direction = Math.sign(pendingSteps);
      pendingSteps -= direction;
      show(direction);
    };

    function show(direction) {
      if (count < 2) return;
      const oldIndex = index;
      const target = wrap(index + direction);
      animating = true;

      // Position the incoming layers before starting a single, shared transition.
      root.setAttribute('data-carousel-reset', '');
      bottleState(bottles[target], direction * bottleTravel, 0);
      copyState(slides[target], -direction * copyTravel, 0);
      void track.offsetWidth;
      root.removeAttribute('data-carousel-reset');
      void track.offsetWidth;

      index = target;
      root.dataset.slide = String(index);
      updateAria();
      if (status) {
        const title = slides[index].querySelector('.product__title');
        status.textContent = `${title ? title.textContent.trim() + '. ' : ''}${index + 1} из ${count}`;
      }

      let timeout = 0;
      let finished = false;
      const finish = () => {
        if (finished) return;
        finished = true;
        clearTimeout(timeout);
        track.removeEventListener('transitionend', onTransitionEnd);
        track.removeEventListener('transitioncancel', onTransitionEnd);
        finishTransition = null;
        resetPosition();
        animating = false;
        if (pendingSteps) drainFrame = requestAnimationFrame(drainQueue);
      };
      const onTransitionEnd = event => {
        if (event.target === track && event.propertyName === 'transform') finish();
      };
      finishTransition = finish;
      track.addEventListener('transitionend', onTransitionEnd);
      track.addEventListener('transitioncancel', onTransitionEnd);

      // The wrap goes to a clone; finish() quietly returns the track to the real scene.
      trackPosition(hasClones ? oldIndex + direction : target);
      bottleState(bottles[oldIndex], -direction * bottleTravel, 0);
      bottleState(bottles[target], 0, 1);
      copyState(slides[oldIndex], -direction * copyTravel, 0);
      copyState(slides[target], 0, 1);
      const duration = reducedMotion.matches ? 0 : transitionDuration(track);
      if (duration === 0) finish();
      else timeout = setTimeout(finish, duration + 120);
    }

    function navigate(direction) {
      // Opposite input cancels queued steps; retain at most one revolution.
      pendingSteps = Math.max(-count, Math.min(count, pendingSteps + direction));
      cancelAnimationFrame(drainFrame);
      drainQueue();
    }
    prevBtn.addEventListener('click', () => navigate(-1));
    nextBtn.addEventListener('click', () => navigate(1));
    if (!root.hasAttribute('tabindex')) root.setAttribute('tabindex', '0');
    root.addEventListener('keydown', event => {
      if (event.altKey || event.ctrlKey || event.metaKey || event.target.closest('input, textarea, select, [contenteditable="true"]')) return;
      if (event.key === 'ArrowLeft' || event.key === 'ArrowRight') {
        event.preventDefault();
        navigate(event.key === 'ArrowLeft' ? -1 : 1);
      } else if (event.key === 'Home' || event.key === 'End') {
        event.preventDefault();
        pendingSteps = (event.key === 'Home' ? 0 : count - 1) - index;
        cancelAnimationFrame(drainFrame);
        drainQueue();
      }
    });
    reducedMotion.addEventListener('change', () => {
      if (reducedMotion.matches && finishTransition) finishTransition();
    });

    let touchStart = null;
    root.addEventListener('touchstart', event => {
      if (event.touches.length !== 1 || event.target.closest('button, a, input, textarea, select')) {
        touchStart = null;
        return;
      }
      const touch = event.touches[0];
      touchStart = { id: touch.identifier, x: touch.clientX, y: touch.clientY };
    }, { passive: true });
    root.addEventListener('touchcancel', () => { touchStart = null; }, { passive: true });
    root.addEventListener('touchend', event => {
      if (!touchStart) return;
      const touch = Array.from(event.changedTouches).find(item => item.identifier === touchStart.id);
      if (!touch) return;
      const dx = touchStart.x - touch.clientX;
      const dy = touchStart.y - touch.clientY;
      touchStart = null;
      if (Math.abs(dx) >= 50 && Math.abs(dx) > Math.abs(dy) * 1.3) navigate(dx > 0 ? 1 : -1);
    }, { passive: true });
  }

  const initialize = () => document.querySelectorAll('[data-product-carousel]').forEach(init);
  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', initialize, { once: true });
  else initialize();
})();
