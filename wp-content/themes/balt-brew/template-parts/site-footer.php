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
                    <button type="button" class="footer__feedback" data-feedback-open aria-haspopup="dialog" aria-controls="feedback-modal" aria-expanded="false">
                        <?php echo baltic_option_text('footer_feedback_label', 'Оставить обращение'); ?>
                    </button>
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

<div class="feedback-modal" id="feedback-modal" data-feedback-modal hidden>
    <div class="feedback-modal__backdrop" data-feedback-close></div>
    <section class="feedback-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="feedback-modal-title" tabindex="-1">
        <button type="button" class="feedback-modal__close" data-feedback-close aria-label="Закрыть форму обратной связи"></button>

        <div class="feedback-modal__form-view" data-feedback-form-view>
            <header class="feedback-modal__header">
                <h2 class="feedback-modal__title" id="feedback-modal-title">Оставьте ваше обращение</h2>
                <p class="feedback-modal__intro">Если у вас возникли вопросы, касающиеся качества продукции, производства, пожалуйста, отправьте сообщение.</p>
            </header>

            <form class="feedback-form" data-feedback-form action="<?php echo esc_url(admin_url('admin-ajax.php')); ?>" method="post" novalidate>
                <input type="hidden" name="action" value="baltic_submit_feedback">
                <input type="hidden" name="nonce" value="<?php echo esc_attr(wp_create_nonce('baltic_feedback')); ?>">
                <label class="feedback-form__honeypot" aria-hidden="true">
                    <span>Не заполняйте это поле</span>
                    <input type="text" name="website" tabindex="-1" autocomplete="off">
                </label>

                <div class="feedback-form__grid">
                    <label class="feedback-field">
                        <span class="feedback-visually-hidden">ФИО</span>
                        <input id="feedback-name" type="text" name="name" placeholder="ФИО*" autocomplete="name" aria-describedby="feedback-name-error" required>
                        <span class="feedback-field__error" id="feedback-name-error" data-feedback-error="name"></span>
                    </label>
                    <label class="feedback-field">
                        <span class="feedback-visually-hidden">Телефон</span>
                        <input id="feedback-phone" type="tel" name="phone" placeholder="Телефон*" autocomplete="tel" inputmode="tel" aria-describedby="feedback-phone-error" required>
                        <span class="feedback-field__error" id="feedback-phone-error" data-feedback-error="phone"></span>
                    </label>
                    <label class="feedback-field">
                        <span class="feedback-visually-hidden">Почта</span>
                        <input id="feedback-email" type="email" name="email" placeholder="Почта" autocomplete="email" aria-describedby="feedback-email-error">
                        <span class="feedback-field__error" id="feedback-email-error" data-feedback-error="email"></span>
                    </label>
                    <label class="feedback-field">
                        <span class="feedback-visually-hidden">Наименование продукции</span>
                        <input id="feedback-product" type="text" name="product" placeholder="Наименование продукции*" aria-describedby="feedback-product-error" required>
                        <span class="feedback-field__error" id="feedback-product-error" data-feedback-error="product"></span>
                    </label>
                    <label class="feedback-field">
                        <span class="feedback-visually-hidden">Дата розлива продукции</span>
                        <input id="feedback-production-date" type="text" name="production_date" placeholder="Дата розлива продукции" inputmode="numeric" aria-describedby="feedback-production-date-error">
                        <span class="feedback-field__error" id="feedback-production-date-error" data-feedback-error="production_date"></span>
                    </label>
                    <label class="feedback-field">
                        <span class="feedback-visually-hidden">Завод-изготовитель</span>
                        <select id="feedback-factory" name="factory" aria-describedby="feedback-factory-error" required>
                            <option value="">Завод-изготовитель*</option>
                            <option value="Санкт-Петербург">Санкт-Петербург</option>
                            <option value="Ярославль">Ярославль</option>
                            <option value="Ростов-на-Дону">Ростов-на-Дону</option>
                            <option value="Новосибирск">Новосибирск</option>
                            <option value="Не знаю">Не знаю</option>
                        </select>
                        <span class="feedback-field__error" id="feedback-factory-error" data-feedback-error="factory"></span>
                    </label>
                    <label class="feedback-field feedback-field--message">
                        <span class="feedback-visually-hidden">Сообщение</span>
                        <textarea id="feedback-message" name="message" placeholder="Сообщение*" aria-describedby="feedback-message-error" required></textarea>
                        <span class="feedback-field__error" id="feedback-message-error" data-feedback-error="message"></span>
                    </label>
                </div>

                <p class="feedback-form__status" data-feedback-status aria-live="polite"></p>
                <button type="submit" class="feedback-form__submit">Отправить</button>
            </form>
        </div>

        <div class="feedback-modal__success" data-feedback-success hidden>
            <h2 class="feedback-modal__success-title">Мы получили ваше обращение</h2>
            <p class="feedback-modal__success-text">Изучим информацию и ответим в ближайшее время</p>
            <button type="button" class="feedback-modal__success-button" data-feedback-close>Хорошо</button>
        </div>
    </section>
</div>
