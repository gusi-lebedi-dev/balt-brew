(function () {
    'use strict';

    const script = document.currentScript;
    const siteRoot = new URL('.', script.src);
    const storageKey = 'ageConfirmed';
    const fallbackPrefix = 'baltika-age:';
    const allowedPaths = new Set([
        siteRoot.pathname,
        new URL('index.html', siteRoot).pathname,
        new URL('news.html', siteRoot).pathname
    ]);

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
                document.cookie = 'brewAgeConfirmed=true; Path=' + siteRoot.pathname + '; SameSite=Lax';
            } catch (_) { /* Use the tab fallback below if cookies are blocked. */ }
            if (!hasConfirmation()) window.name = fallbackPrefix + siteRoot.href;
        }
    }

    function destination() {
        const fallback = new URL('index.html', siteRoot);
        const requested = new URL(window.location.href).searchParams.get('returnTo');
        if (!requested) return fallback.href;
        try {
            const target = new URL(requested, siteRoot);
            if (target.protocol === siteRoot.protocol && target.origin === siteRoot.origin &&
                !target.username && !target.password && allowedPaths.has(target.pathname)) {
                return target.href;
            }
        } catch (_) { /* Invalid or external return URLs use the homepage. */ }
        return fallback.href;
    }

    window.BrewAgeGate = Object.freeze({ confirm, destination });

    if (!script.hasAttribute('data-age-gate') && !hasConfirmation()) {
        const gate = new URL('age-gate.html', siteRoot);
        gate.searchParams.set('returnTo', window.location.href);
        window.location.replace(gate.href);
    }
}());
