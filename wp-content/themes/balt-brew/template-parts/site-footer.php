<?php
/**
 * Shared site footer.
 */

if (!defined('ABSPATH')) {
    exit;
}
?>
<footer class="footer">
    <div class="footer__content">
        <div class="footer__top">
            <img src="<?php echo baltic_option_asset('footer_logo', 'images/footer/logo.png'); ?>" alt="Балтика Brew" class="footer__logo">

            <div class="footer_right">
                <div class="phone_wraper">
                    <a href="<?php echo baltic_option_url('footer_vk_url', 'https://vk.ru/baltikabrew'); ?>" class="footer__vk" target="_blank" rel="noopener" aria-label="Балтика Brew во ВКонтакте">
                        <img src="<?php echo esc_url(baltic_asset('images/vk.png')); ?>" alt="VK" class="footer__vk-icon">
                    </a>
                    <div class="footer__phone">
                        <a href="tel:<?php echo esc_attr(baltic_option('footer_phone_href', '+78007002880')); ?>" class="footer__phone-number"><?php echo baltic_option_text('footer_phone', '8 (800) 700 28 80'); ?></a>
                        <span class="footer__phone-subtitle"><?php echo baltic_option_text('footer_phone_subtitle', 'Бесплатно по всей России'); ?></span>
                    </div>
                </div>
                <div class="age_restriction">
                    <img src="<?php echo esc_url(baltic_asset('images/restriction.png')); ?>" alt="Возрастное ограничение 18+">
                </div>
            </div>
        </div>

        <div class="footer__divider"></div>

        <div class="footer__bottom">
            <p class="footer__legal"><?php echo baltic_option_text('footer_company', 'ООО «ПИВОВАРЕННАЯ КОМПАНИЯ «БАЛТИКА»'); ?></p>
            <a href="<?php echo baltic_option_asset('personal_pdf', 'documents/personal-data-regulation.pdf'); ?>" class="footer__legal" target="_blank" rel="noopener">Регламент по обработке персональных данных</a>
            <a href="<?php echo baltic_option_asset('cookie_pdf', 'documents/cookie-policy.pdf'); ?>" class="footer__legal" target="_blank" rel="noopener">Использование cookie-файлов</a>
        </div>
    </div>
</footer>
