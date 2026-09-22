

$( document ).ready(function() {



    const container = $('.produkt');

    // Добавляем фоновые слои один раз
    if ($('.slider-bg').length === 0) {
        container.append('<div class="slider-bg"></div><div class="slider-bg-next"></div>');
    }

    function updateBackground(imageUrl) {
        const $bg = $('.slider-bg');
        const $next = $('.slider-bg-next');

        $next.css({
            'background-image': `url(${imageUrl})`,
            'opacity': 1
        });

        // Подождать окончание анимации и заменить текущий фон
        setTimeout(() => {
            $bg.css('background-image', `url(${imageUrl})`);
            $next.css('opacity', 0);
        }, 800); // должно совпадать с transition
    }

    // Инициализируем слайдер
    $('.slides').on('init', function (event, slick) {
        // Первый слайд
        const $firstSlide = $(slick.$slides.get(0));
        const imageUrl = $firstSlide.data('bg');
        updateBackground(imageUrl);
    });

    $('.slides').on('afterChange', function (event, slick, currentSlide) {
        const $currentSlide = $(slick.$slides.get(currentSlide));
        const imageUrl = $currentSlide.data('bg');
        updateBackground(imageUrl);
    });
    // $('.slides').on('init reInit afterChange', function(event, slick, currentSlide){
    //     const index = currentSlide ?? 0;
    //     const $currentSlide = $(slick.$slides.get(index));
    //
    //     const classList = ['bg-1', 'bg-2', 'bg-3', 'bg-4'];
    //     const container = $('.produkt');
    //
    //     container.removeClass(classList.join(' '));
    //
    //     if ($currentSlide.hasClass('bg-1')) container.addClass('bg-1');
    //     if ($currentSlide.hasClass('bg-2')) container.addClass('bg-2');
    //     if ($currentSlide.hasClass('bg-3')) container.addClass('bg-3');
    //     if ($currentSlide.hasClass('bg-4')) container.addClass('bg-4');
    // });






let sections = $('section'),
    nav = $('.nav'),
    nav_height = nav.outerHeight();
$(window).on('scroll', function () {
    $('.nav a').removeClass('active');
    let cur_pos = $(this).scrollTop();
    sections.each(function() {
        let top = $(this).offset().top - nav_height - 180,
            bottom = top + $(this).outerHeight();
        if (cur_pos >= top && cur_pos <= bottom) {
            nav.find('a').removeClass('active');
            sections.removeClass('active');
            $(this).addClass('active');
            nav.find('a[href="#'+$(this).attr('id')+'"]').addClass('active');
        }
    });
});
nav.find('a').on('click', function () {
    let $el = $(this),
        id = $el.attr('href');

    $('.menu').removeClass('menu_active');
    $('body').removeClass('menu_active');

    $('html, body').animate({
        scrollTop: $(id).offset().top - nav_height
    }, 600);
    return false;
});
});
const menuInit = () => {
    const menu = document.querySelector('.menu')
    const open = document.querySelector('.header__burger')
    const close = document.querySelector('.js-menu__close')
    const body = document.querySelector('body')
    if (!menu || !open || !close) return
    open.addEventListener('click', () => {
        menu.classList.add('menu_active')
        body.classList.add('menu_active')
    })
    close.addEventListener('click', () => {
        menu.classList.remove('menu_active')
        body.classList.remove('menu_active')
    })
}
    function setCookie(name, value, days) {
        let expires = "";
        if (days) {
            let date = new Date();
            date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
            expires = "; expires=" + date.toUTCString();
        }
        document.cookie = name + "=" + (value || "") + expires + "; path=/";
    }

    function getCookie(name) {
        let matches = document.cookie.match(new RegExp("(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"));
        return matches ? decodeURIComponent(matches[1]) : undefined;
    }


    function checkCookies() {
        let cookieNote = document.getElementById('cookie_note');
        let cookieBtnAccept = cookieNote.querySelector('.cookie_accept');


        if (!getCookie('cookies_policy')) {
            cookieNote.classList.add('show');
        }


        cookieBtnAccept.addEventListener('click', function () {
            setCookie('cookies_policy', 'true', 365);
            cookieNote.classList.remove('show');
        });
    }
    function checkAge() {
        let ageBtnAccept = document.querySelector('.button_ok');

        let page_ag = document.querySelector('.page_ag');
        let page_app = document.querySelector('.page_app');

        if (!getCookie('cookies_age')) {

            page_ag.classList.add('show');
            page_app.classList.remove('show');
        }

        ageBtnAccept.addEventListener('click', function () {
            setCookie('cookies_age', 'true', 365);

            page_ag.classList.remove('show');
            page_app.classList.add('show');
            $('.slides').slick({
                slidesToShow: 1,
                slidesToScroll: 1,
                // autoplay: true,
                // autoplaySpeed: 2000,
                // dots: false,
                infinite: false,
                swipe: true,
                swipeToSlide: true,
                touchMove: true,
                responsive: [
                    {
                        breakpoint: 800,
                        settings: {
                            arrows: false,
                            dots: true,
                        }
                    }
                ]
            });

        });
    }
    checkCookies();
    checkAge();


const openModal = (sel) => {
    document.querySelector(sel).style.display = 'flex';
    document.querySelector('.modal__bg').style.display = 'block';
    document.body.classList.add('fixed-scroll');
}


const closeModal = (sel) => {
    document.querySelectorAll(sel).forEach(item => {
        item.style.display = 'none';
    })
    document.querySelector('.modal__bg').style.display = 'none';
    document.body.classList.remove('fixed-scroll');
}

$('.btn_news').on('click', function (e) {
    e.preventDefault();
    const idnews = $(this).data('idnews');
if(idnews > 0) {
    closeModal('.modal');
    openModal('.modal.news_' + idnews);
}
});
const closeButton = () => {
    const closeModalsBtn = document.querySelectorAll('.modal__close');
    if (!closeModalsBtn) return;
    closeModalsBtn.forEach(item => {
        item.addEventListener('click', () => {
            closeModal('.modal');
        })
    })
}

// Плавный скролл для .banner__down к .produkt
    $('.banner__down').on('click', function () {
        const target = $('.produkt');
        const headerHeight = $('.header').outerHeight();
        if (target.length) {
            $('html, body').animate({
                scrollTop: target.offset().top - headerHeight
            }, 600); // 600ms — скорость анимации
        } else {
            console.log('Ошибка: Элемент .produkt не найден!');
        }
    });

const init = () => {
    menuInit();
    closeButton();
};
document.addEventListener('DOMContentLoaded', init);




