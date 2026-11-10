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
    <?php get_template_part('template-parts/site-header'); ?>
