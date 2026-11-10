<?php
/** Standalone age confirmation view. */
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Балтика Brew — подтверждение возраста</title>
    <?php wp_head(); ?>
</head>
<body <?php body_class( 'age-gate-page' ); ?>>
<?php wp_body_open(); ?>
<!-- Screen 1: Age confirmation -->
    <div class="age-gate" id="ageQuestion">
        <img src="<?php echo esc_url( balt_brew_asset('images/header/logo.png') ); ?>" alt="Балтика Brew" class="age-gate__logo">
        <h1 class="age-gate__title">ВАМ ЕСТЬ 18 ЛЕТ?</h1>
        <p class="age-gate__subtitle">Чтобы продолжить, подтвердите свой возраст.</p>
        <div class="age-gate__buttons">
            <button class="age-gate__button age-gate__button--yes" id="btnYes">Мне есть 18 лет</button>
            <button class="age-gate__button age-gate__button--no" id="btnNo">Мне нет 18 лет</button>
        </div>
    </div>

    <!-- Screen 2: Access denied -->
    <div class="age-gate hidden" id="accessDenied">
        <img src="<?php echo esc_url( balt_brew_asset('images/header/logo.png') ); ?>" alt="Балтика Brew" class="age-gate__logo">
        <h1 class="age-gate__title" tabindex="-1">САЙТ ДОСТУПЕН ТОЛЬКО<br>ДЛЯ ПОЛЬЗОВАТЕЛЕЙ 18+</h1>
        <p class="age-gate__message">Вы сможете его посетить после достижения совершеннолетия</p>
    </div>
<?php wp_footer(); ?>
</body>
</html>
