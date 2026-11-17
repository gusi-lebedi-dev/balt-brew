(function () {
    'use strict';

    const script = document.currentScript;
    const siteRoot = new URL('.', script.src);
    const siteHome = new URL('../../../', siteRoot);
    const storageKey = 'ageConfirmed';
    const fallbackPrefix = 'baltika-age:';

    function readStorage(name) {
        try {
            return window[name].getItem(storageKey) === 'true';
        } catch (_) {
            return false;
        }
    }

    function writeStorage(name) {
        try {
            window[name].setItem(storageKey, 'true');
            return window[name].getItem(storageKey) === 'true';
        } catch (_) {
            return false;
        }
    }

    function hasConfirmation() {
        if (readStorage('localStorage') || readStorage('sessionStorage')) return true;
        try {
            if (document.cookie.split(';').some(part => part.trim() === 'brewAgeConfirmed=true')) return true;
        } catch (_) { /* Cookies may be unavailable for local files. */ }
        // Last resort for browsers that block both storage APIs and cookies.
        // The grant stays in this tab and is restricted to this site's directory.
        return window.name === fallbackPrefix + siteRoot.href;
    }

    function confirm() {
        const localSaved = writeStorage('localStorage');
        const sessionSaved = writeStorage('sessionStorage');
        if (!localSaved && !sessionSaved) {
            try {
                document.cookie = 'brewAgeConfirmed=true; Path=' + siteHome.pathname + '; SameSite=Lax';
            } catch (_) { /* Use the tab fallback below if cookies are blocked. */ }
            if (!hasConfirmation()) window.name = fallbackPrefix + siteRoot.href;
        }
    }

    function destination() {
        const fallback = siteHome;
        const requested = new URL(window.location.href).searchParams.get('returnTo');
        if (!requested) return fallback.href;
        try {
            const target = new URL(requested, siteRoot);
            if (target.protocol === siteRoot.protocol && target.origin === siteRoot.origin &&
                !target.username && !target.password && target.pathname.startsWith(siteHome.pathname)) {
                return target.href;
            }
        } catch (_) { /* Invalid or external return URLs use the homepage. */ }
        return fallback.href;
    }

    // Old bookmarks can still open the standalone confirmation page.
    if (script.hasAttribute('data-age-gate')) {
        window.BrewAgeGate = Object.freeze({ confirm, destination });
        return;
    }

    function showOverlay() {
        if (hasConfirmation() || document.querySelector('.age-overlay')) return;

        const dialog = document.createElement('dialog');
        dialog.className = 'age-overlay';
        dialog.setAttribute('aria-modal', 'true');
        dialog.setAttribute('aria-labelledby', 'age-overlay-title');
        dialog.setAttribute('aria-describedby', 'age-overlay-description');
        dialog.innerHTML = `
            <div class="age-overlay__panel" data-nosnippet>
                <img class="age-overlay__logo" alt="Балтика Brew">
                <h2 id="age-overlay-title" class="age-overlay__title" tabindex="-1" autofocus>Вам есть 18 лет?</h2>
                <p id="age-overlay-description" class="age-overlay__description">Чтобы продолжить, подтвердите свой возраст.</p>
                <div class="age-overlay__actions">
                    <button type="button" class="age-overlay__button age-overlay__button--yes" data-age-yes>Мне есть 18 лет</button>
                    <button type="button" class="age-overlay__button" data-age-no>Мне нет 18 лет</button>
                </div>
            </div>`;
        dialog.querySelector('img').src = new URL('images/header/logo.png', siteRoot).href;

        const title = dialog.querySelector('h2');
        const description = dialog.querySelector('p');
        const actions = dialog.querySelector('.age-overlay__actions');
        const nativeModal = typeof dialog.showModal === 'function';
        const focusTitle = () => title.focus({ preventScroll: true });
        const keepFocus = () => {
            if (dialog.open && !dialog.contains(document.activeElement)) focusTitle();
        };
        const blockEscape = event => {
            if (event.key === 'Escape' && dialog.open) event.preventDefault();
        };

        dialog.addEventListener('cancel', event => event.preventDefault());
        document.addEventListener('keydown', blockEscape, true);
        dialog.querySelector('[data-age-yes]').addEventListener('click', () => {
            confirm();
            document.removeEventListener('keydown', blockEscape, true);
            document.documentElement.classList.remove('age-overlay-open');
            if (nativeModal) dialog.close();
            else document.removeEventListener('focusin', keepFocus);
            dialog.remove();
        });
        dialog.querySelector('[data-age-no]').addEventListener('click', () => {
            title.textContent = 'Сайт доступен только для пользователей 18+';
            description.textContent = 'Вы сможете посетить его после достижения совершеннолетия.';
            actions.hidden = true;
            focusTitle();
        });

        document.body.append(dialog);
        document.documentElement.classList.add('age-overlay-open');
        if (nativeModal) {
            dialog.showModal();
        } else {
            dialog.classList.add('age-overlay--fallback');
            dialog.setAttribute('open', '');
            dialog.setAttribute('role', 'dialog');
            document.addEventListener('focusin', keepFocus);
        }
        focusTitle();

        // Initial fragment navigation can move focus after a deferred script runs.
        if (document.readyState !== 'complete') {
            document.addEventListener('DOMContentLoaded', keepFocus, { once: true });
            window.addEventListener('load', keepFocus, { once: true });
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', showOverlay, { once: true });
    } else {
        showOverlay();
    }
}());
