<!doctype html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Балтика Brew — крафтовая пивоварня. Анти-Лагер Кёльш, Техно IPA, Аэронавт Бланш и другие сорта.">
    <meta property="og:title" content="Балтика Brew — крафтовая пивоварня">
    <meta property="og:description" content="Крафтовое пиво высокого качества. Регулярные и лимитированные линейки продукции.">
    <meta property="og:type" content="website">
    <meta property="og:image" content="<?php echo esc_url( balt_brew_asset( 'images/header/logo.png' ) ); ?>">
    <link rel="icon" type="image/png" href="<?php echo esc_url( balt_brew_asset( 'images/header/logo.png' ) ); ?>">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<?php if ( balt_brew_is_front_view() ) : ?>
    <!-- ================= HERO ================= -->
    <div class="hero">
        <video class="hero__bg hero__video hero__video--desktop hero__video--intro" playsinline muted preload="none" data-hero-format="desktop" data-hero-intro data-src="<?php echo esc_url( balt_brew_asset('video/OPEN.mp4') ); ?>" aria-hidden="true"></video>
        <video class="hero__bg hero__video hero__video--desktop hero__video--loop" playsinline muted preload="none" loop data-hero-format="desktop" data-hero-loop data-src="<?php echo esc_url( balt_brew_asset('video/LOOP.mp4') ); ?>" aria-hidden="true"></video>
        <video class="hero__bg hero__video hero__video--mobile hero__video--intro" playsinline muted preload="none" data-hero-format="mobile" data-hero-intro data-src="<?php echo esc_url( balt_brew_asset('video/FINAL 9-16_OPEN.mp4') ); ?>" aria-hidden="true"></video>
        <video class="hero__bg hero__video hero__video--mobile hero__video--loop" playsinline muted preload="none" loop data-hero-format="mobile" data-hero-loop data-src="<?php echo esc_url( balt_brew_asset('video/FINAL 9-16_LOOP.mp4') ); ?>" aria-hidden="true"></video>
    </div>
<?php endif; ?>

<div class="page">

    <?php if ( balt_brew_is_front_view() ) : ?>
    <!-- ================= LOADER ================= -->
    <div class="loader" id="loader">
        <div class="loader__spinner"></div>
        <div class="loader__text">Загрузка...</div>
        <div class="loader__progress">
            <div class="loader__progress-bar" id="loaderProgress"></div>
        </div>
    </div>

    <?php endif; ?>

    <!-- ================= HEADER ================= -->
    <header class="header" id="home">
        <div class="container">
            <!-- Desktop Navigation -->
            <div class="header__nav-bar header__nav-bar--desktop">
                <!-- <nav class="header__links">
                    <a href="<?php echo esc_url( home_url( '/#assortment' ) ); ?>" class="header__link">Ассортимент</a>
                    <a href="<?php echo esc_url( home_url( '/#about' ) ); ?>" class="header__link">О нас</a>
                </nav> -->

                <a class="logo_header" href="<?php echo esc_url( home_url( '/#home' ) ); ?>">
                    <img src="<?php echo esc_url( balt_brew_asset('images/header/logo.png') ); ?>" alt="Логотип" class="header__logo">
                </a>

                <!-- <nav class="header__links">
                    <div class="header__lang">
                        <a href="#" class="header__lang-link header__lang-link--active">RU</a>
                        <a href="#" class="header__lang-link">EN</a>
                    </div>
                </nav> -->
            </div>

            <!-- Mobile Navigation -->
            <div class="header__nav-bar header__nav-bar--mobile">
                <!-- <div class="header__lang header__lang--mobile">
                    <a href="#" class="header__lang-link header__lang-link--active">RU</a>
                    <a href="#" class="header__lang-link">EN</a>
                </div> -->

                <a href="<?php echo esc_url( home_url( '/#home' ) ); ?>" style="margin: 0 auto;">
                    <img src="<?php echo esc_url( balt_brew_asset('images/header/logo-mobile.png') ); ?>" alt="Логотип" class="header__logo header__logo--mobile">
                </a>

                <!-- <button class="header__burger" aria-label="Открыть меню">
                    <img src="<?php echo esc_url( balt_brew_asset('images/header/menu-icon.svg') ); ?>" alt="" class="header__burger-icon">
                </button> -->
            </div>
        </div>

        <!-- Mobile Menu -->
        <nav class="header__mobile-menu">
            <div class="container">
                <a href="<?php echo esc_url( home_url( '/#assortment' ) ); ?>" class="header__mobile-link">Ассортимент</a>
                <a href="<?php echo esc_url( home_url( '/#about' ) ); ?>" class="header__mobile-link">О нас</a>
            </div>
        </nav>
    </header>
