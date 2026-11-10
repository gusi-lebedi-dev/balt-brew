<footer class="footer">
        <div class="footer__content">
            <div class="footer__top">
                <img src="<?php echo esc_url( balt_brew_asset('images/footer/logo.png') ); ?>" alt="Балтика Brew" class="footer__logo">

                <div class="footer_right">
                            <div class="phone_wraper">
                                <a href="https://vk.ru/baltikabrew" class="footer__vk" target="_blank" rel="noopener" aria-label="Балтика Brew во ВКонтакте">
                                    <img src="<?php echo esc_url( balt_brew_asset('images/vk.png') ); ?>" alt="VK" class="footer__vk-icon">
                                </a>
                                <div class="footer__phone">
                                    <a href="tel:+78007002880" class="footer__phone-number">8 (800) 700 28 80</a>
                                    <span class="footer__phone-subtitle">Бесплатно по всей России</span>
                                </div>
                            </div>
                            <div class="age_restriction">
                                <img src="<?php echo esc_url( balt_brew_asset('images/restriction.png') ); ?>" alt=" Возрастное ограничение">
                            </div>
                    </div>
            </div>

            <div class="footer__divider"></div>

            <div class="footer__bottom">
                <p class="footer__legal">ООО «ПИВОВАРЕННАЯ КОМПАНИЯ «БАЛТИКА»</p>
                <a href="<?php echo esc_url( balt_brew_asset('documents/personal-data-regulation.pdf') ); ?>" class="footer__legal" target="_blank" rel="noopener">Регламент по обработке персональных данных</a>
                <a href="<?php echo esc_url( balt_brew_asset('documents/cookie-policy.pdf') ); ?>" class="footer__legal" target="_blank" rel="noopener">Использование cookie-файлов</a>
            </div>
        </div>
    </footer>

</div>

<!-- Cookie Banner -->
<div class="cookie-banner" id="cookieBanner">
    <div class="cookie-banner__content">
        <p class="cookie-banner__text">
            Мы используем файлы cookie, чтобы сайт работал лучше.
            <a href="<?php echo esc_url( balt_brew_asset('documents/cookie-policy.pdf') ); ?>" target="_blank" rel="noopener" class="cookie-banner__link">
                Подробнее
            </a>
        </p>
        <button class="cookie-banner__button" id="acceptCookie">ПРИНЯТЬ</button>
    </div>
</div>

<?php wp_footer(); ?>
</body>
</html>
