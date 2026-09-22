<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Балтика BREW</title>
    <link rel="stylesheet" href="css/style-plug.css">
    <!-- Yandex.Metrika counter -->
    <script type="text/javascript">
        (function(m,e,t,r,i,k,a){
            m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)};
            m[i].l=1*new Date();
            for (var j = 0; j < document.scripts.length; j++) {if (document.scripts[j].src === r) { return; }}
            k=e.createElement(t),a=e.getElementsByTagName(t)[0],k.async=1,k.src=r,a.parentNode.insertBefore(k,a)
        })(window, document,'script','https://mc.yandex.ru/metrika/tag.js?id=109448312', 'ym');

        ym(109448312, 'init', {ssr:true, webvisor:true, clickmap:true, ecommerce:"dataLayer", referrer: document.referrer, url: location.href, accurateTrackBounce:true, trackLinks:true});
    </script>
    <!-- /Yandex.Metrika counter -->
</head>
<body>
<section class="plug">
    <div class="container">
        <div class="video-wrapper">
            <video class="video-main" src="video/video_slide2.mp4" muted autoplay preload="auto" playsinline>
                Ваш браузер не поддерживает видео.
            </video>
        </div>
        <div class="text-wrapper" aria-live="polite"></div>
    </div>
</section>
<div class="cookie-notice" data-cookie-notice>
    <div class="cookie-notice__inner">
        <p class="cookie-notice__text">Мы используем файлы cookie</p>
        <button class="cookie-notice__button" type="button" data-cookie-accept>хорошо</button>
    </div>
</div>
<script>
    (function() {
        const texts = [
            "Магические машины уже запущены",
            "скоро здесь появится сайт балтики брю"
        ];

        const wrapper = document.querySelector('.text-wrapper');
        const videoWrapper = document.querySelector('.video-wrapper');
        const video = document.querySelector('.video-main');
        const videoFadeBeforeEnd = 1;
        let currentIndex = 0;
        let currentElement = createTitleElement(currentIndex);
        let isRestartingVideo = false;

        function showVideo() {
            if (videoWrapper) {
                videoWrapper.classList.add('is-video-ready');
            }
        }

        function hideVideo() {
            if (videoWrapper) {
                videoWrapper.classList.remove('is-video-ready');
            }
        }

        function playVideo() {
            if (!video) return;

            video.muted = true;
            video.play()
                .then(showVideo)
                .catch(function() {});
        }

        function createTitleElement(index) {
            const el = document.createElement(index === 0 ? 'h1' : 'h2');
            el.className = 'dynamic-title';
            el.textContent = texts[index];
            return el;
        }

        currentElement.classList.add('fade-in');
        wrapper.appendChild(currentElement);

        function smoothSwitch() {
            currentElement.classList.replace('fade-in', 'fade-out');

            setTimeout(() => {
                currentIndex = (currentIndex + 1) % texts.length;
                const newElement = createTitleElement(currentIndex);
                newElement.classList.add('fade-out');

                wrapper.replaceChild(newElement, currentElement);
                currentElement = newElement;

                setTimeout(() => {
                    currentElement.classList.replace('fade-out', 'fade-in');
                }, 30);
            }, 800);
        }

        const intervalId = setInterval(smoothSwitch, 3600);

        playVideo();
        if (video) {
            video.addEventListener('loadeddata', showVideo, { once: true });
            video.addEventListener('canplay', playVideo, { once: true });
            video.addEventListener('timeupdate', function() {
                if (!video.duration || isRestartingVideo) return;

                if (video.duration - video.currentTime <= videoFadeBeforeEnd) {
                    hideVideo();
                    isRestartingVideo = true;
                }
            });
            video.addEventListener('ended', function() {
                video.currentTime = 0;
                requestAnimationFrame(function() {
                    isRestartingVideo = false;
                    playVideo();
                });
            });
        }
        document.addEventListener('visibilitychange', function() {
            if (!document.hidden) {
                playVideo();
            } else if (videoWrapper) {
                videoWrapper.classList.remove('is-video-ready');
            }
        });
        window.addEventListener('pageshow', playVideo);

        window.addEventListener('beforeunload', function() {
            clearInterval(intervalId);
        });
    })();

    (function() {
        const notice = document.querySelector('[data-cookie-notice]');
        const acceptButton = document.querySelector('[data-cookie-accept]');
        const storageKey = 'cookieNoticeAccepted';

        if (!notice || !acceptButton) return;

        try {
            if (localStorage.getItem(storageKey) === 'true') {
                notice.classList.add('is-hidden');
                notice.hidden = true;
                return;
            }
        } catch (error) {}

        acceptButton.addEventListener('click', function() {
            notice.classList.add('is-hidden');

            try {
                localStorage.setItem(storageKey, 'true');
            } catch (error) {}

            setTimeout(function() {
                notice.hidden = true;
            }, 250);
        });
    })();
</script>
</body>
</html>
