<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo('charset'); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="Балтика Brew - крафтовая пивоварня. Познакомьтесь с нашими уникальными сортами пива.">
<meta property="og:title" content="Балтика Brew - Крафтовая пивоварня">
<meta property="og:description" content="Крафтовое пиво высокого качества. Регулярные и лимитированные линейки продукции.">
<meta property="og:type" content="website">
<meta property="og:image" content="<?php echo esc_url(baltic_asset('images/header/logo.png')); ?>">
<link rel="icon" type="image/png" href="<?php echo esc_url(baltic_asset('images/header/logo.png')); ?>">
<link rel="preload" href="<?php echo esc_url(baltic_asset('fonts/Inter-Regular.woff2')); ?>" as="font" type="font/woff2" crossorigin>
<link rel="preload" href="<?php echo esc_url(baltic_asset('fonts/tt-backwardssans-regular.woff2')); ?>" as="font" type="font/woff2" crossorigin>
<link rel="preload" href="<?php echo esc_url(baltic_asset('fonts/TTTricks-Regular.woff2')); ?>" as="font" type="font/woff2" crossorigin>
<link rel="stylesheet" href="<?php echo esc_url(baltic_asset('styles.css')); ?>">
<link rel="stylesheet" href="<?php echo esc_url(baltic_asset('styles-mobile.css')); ?>">
<link rel="stylesheet" href="<?php echo esc_url(baltic_asset('product-animation.css')); ?>">
<link rel="stylesheet" href="<?php echo esc_url(baltic_asset('loader.css')); ?>">
<link rel="stylesheet" href="<?php echo esc_url(baltic_asset('pages-fixes.css')); ?>">
<script src="<?php echo esc_url(baltic_asset('age-check.js')); ?>"></script>
<script src="<?php echo esc_url(baltic_asset('script.js')); ?>" defer></script>
<script src="<?php echo esc_url(baltic_asset('product-animation.js')); ?>" defer></script>
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
        <!-- ================= HERO ================= -->
    <div class="hero">
        <video class="hero__bg hero__video hero__video--desktop hero__video--intro" playsinline muted preload="none" data-hero-format="desktop" data-hero-intro data-src="<?php echo baltic_option_asset('hero_desktop_intro', 'video/OPEN.mp4'); ?>" aria-hidden="true"></video>
        <video class="hero__bg hero__video hero__video--desktop hero__video--loop" playsinline muted preload="none" loop data-hero-format="desktop" data-hero-loop data-src="<?php echo baltic_option_asset('hero_desktop_loop', 'video/LOOP.mp4'); ?>" aria-hidden="true"></video>
        <video class="hero__bg hero__video hero__video--mobile hero__video--intro" playsinline muted preload="none" data-hero-format="mobile" data-hero-intro data-src="<?php echo baltic_option_asset('hero_mobile_intro', 'video/FINAL 9-16_OPEN.mp4'); ?>" aria-hidden="true"></video>
        <video class="hero__bg hero__video hero__video--mobile hero__video--loop" playsinline muted preload="none" loop data-hero-format="mobile" data-hero-loop data-src="<?php echo baltic_option_asset('hero_mobile_loop', 'video/FINAL 9-16_LOOP.mp4'); ?>" aria-hidden="true"></video>
        <button class="hero__scroll" type="button" data-hero-scroll="#assortment" aria-label="Перейти к следующему разделу" hidden>
            <img class="hero__scroll-icon" src="<?php echo esc_url(baltic_asset('assets/hero-arrow-finish.png')); ?>" alt="" aria-hidden="true">
        </button>
    </div>
<div class="page">

    <!-- ================= LOADER ================= -->
    <div class="loader" id="loader">
        <div class="loader__spinner"></div>
        <div class="loader__text">Загрузка...</div>
        <div class="loader__progress">
            <div class="loader__progress-bar" id="loaderProgress"></div>
        </div>
    </div>

    <!-- ================= HEADER ================= -->
    <header class="header" id="home">
        <div class="container">
            <!-- Desktop Navigation -->
            <div class="header__nav-bar header__nav-bar--desktop">
                <!-- <nav class="header__links">
                    <a href="#assortment" class="header__link">Ассортимент</a>
                    <a href="#about" class="header__link">О нас</a>
                </nav> -->

                <a class="logo_header" href="#home">
                    <img src="<?php echo baltic_option_asset('header_logo', 'images/header/logo.png'); ?>" alt="Логотип" class="header__logo">
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

                <a href="#home" style="margin: 0 auto;">
                    <img src="<?php echo baltic_option_asset('header_logo_mobile', 'images/header/logo-mobile.png'); ?>" alt="Логотип" class="header__logo header__logo--mobile">
                </a>

                <!-- <button class="header__burger" aria-label="Открыть меню">
                    <img src="<?php echo esc_url(baltic_asset('images/header/menu-icon.svg')); ?>" alt="" class="header__burger-icon">
                </button> -->
            </div>
        </div>

        <!-- Mobile Menu -->
        <nav class="header__mobile-menu">
            <div class="container">
                <a href="#assortment" class="header__mobile-link">Ассортимент</a>
                <a href="#about" class="header__mobile-link">О нас</a>
            </div>
        </nav>
    </header>
    <!-- ================= PRODUCT ================= -->
    <?php get_template_part('template-parts/home-products'); ?>

    <!-- ================= ABOUT / HISTORY ================= -->
    <?php
    $about_tabs = function_exists('baltic_home_about_tabs') ? baltic_home_about_tabs() : [];
    $about_active_tab = array_search(true, array_column($about_tabs, 'initially_active'), true);
    $about_active_tab = $about_active_tab === false ? 0 : $about_active_tab;
    ?>
    <?php if ($about_tabs) : ?>
    <section class="section about" id="about">
        <div class="about_wrapper">
            <h2 class="about__title gold-title"><?php echo baltic_option_text('about_title', 'о нас'); ?></h2>

            <?php if ($about_tabs) : ?>
                <div class="about__tabs" role="tablist" aria-label="О нас" style="--about-tab-count: <?php echo esc_attr((string) count($about_tabs)); ?>;">
                    <?php foreach ($about_tabs as $tab_index => $tab) : ?>
                        <?php
                        $is_active = $tab_index === $about_active_tab;
                        ?>
                        <button
                            type="button"
                            class="about__card<?php echo $is_active ? ' about__card--active' : ''; ?>"
                            data-about-tab="<?php echo esc_attr((string) $tab_index); ?>"
                            role="tab"
                            id="about-tab-<?php echo esc_attr((string) $tab_index); ?>"
                            aria-controls="about-panel-<?php echo esc_attr((string) $tab_index); ?>"
                            tabindex="<?php echo $is_active ? '0' : '-1'; ?>"
                            aria-selected="<?php echo $is_active ? 'true' : 'false'; ?>"
                        >
                            <span class="about__card-label"><?php echo esc_html($tab['label']); ?></span>
                        </button>
                    <?php endforeach; ?>
                </div>

                <div class="about__content">
                    <?php foreach ($about_tabs as $tab_index => $tab) : ?>
                        <?php
                        $events = $tab['timeline_items'] ?: [];
                        $active_event_index = $events ? max(0, min(count($events) - 1, ((int) ($tab['active_timeline_item'] ?? 1)) - 1)) : 0;
                        ?>
                        <div class="about__panel<?php echo $tab_index === $about_active_tab ? ' about__panel--active' : ''; ?>" data-about-panel="<?php echo esc_attr((string) $tab_index); ?>" id="about-panel-<?php echo esc_attr((string) $tab_index); ?>" role="tabpanel" aria-labelledby="about-tab-<?php echo esc_attr((string) $tab_index); ?>">
                            <div class="about__history-content">
                                <?php if ($events) : ?>
                                    <?php foreach ($events as $event_index => $event) : ?>
                                        <div class="about__timeline-text<?php echo $event_index === $active_event_index ? ' about__timeline-text--active' : ''; ?>" data-about-event="<?php echo esc_attr((string) $event_index); ?>">
                                            <h3 class="about__content-title"><?php echo esc_html($event['heading'] ?: $tab['heading']); ?></h3>
                                            <p class="about__content-text"><?php echo wp_kses_post(nl2br((string) ($event['text'] ?: $tab['text']))); ?></p>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <div class="about__timeline-text about__timeline-text--active" data-about-event="0">
                                        <h3 class="about__content-title"><?php echo esc_html($tab['heading']); ?></h3>
                                        <p class="about__content-text"><?php echo wp_kses_post(nl2br((string) $tab['text'])); ?></p>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <?php if ($events) : ?>
                                <div class="timeline timeline--visible" style="--timeline-total: <?php echo esc_attr((string) count($events)); ?>;">
                                    <div class="timeline__track" aria-hidden="true"></div>
                                    <?php foreach ($events as $event_index => $event) : ?>
                                        <?php $timeline_position = count($events) > 1 ? 22 + (56 / (count($events) - 1)) * $event_index : 50; ?>
                                        <button
                                            type="button"
                                            class="timeline__item<?php echo $event_index === $active_event_index ? ' timeline__item--active' : ''; ?>"
                                            style="--timeline-position: <?php echo esc_attr((string) $timeline_position); ?>%; --timeline-index: <?php echo esc_attr((string) $event_index); ?>;"
                                            data-about-event-target="<?php echo esc_attr((string) $event_index); ?>"
                                        >
                                            <span class="timeline__date">
                                                <span class="timeline__year"><?php echo esc_html($event['year']); ?></span>
                                                <span class="timeline__month"><?php echo esc_html($event['month']); ?></span>
                                            </span>
                                            <span class="timeline__dot" aria-hidden="true"></span>
                                        </button>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <?php endif; ?>
    <!-- ================= NEWS ================= -->
    <?php
    $latest_news = new WP_Query([
        'post_type' => 'baltic_news',
        'post_status' => 'publish',
        'posts_per_page' => 1,
        'orderby' => 'date',
        'order' => 'DESC',
        'no_found_rows' => true,
    ]);
    $news_permalink = '';
    ?>
    <section class="section news" id="news">
        <div class="container">
            <h2 class="news__title gold-title"><?php echo baltic_option_text('news_title', 'новости'); ?></h2>
            <?php if ($latest_news->have_posts()) : ?>
                <?php while ($latest_news->have_posts()) : $latest_news->the_post(); ?>
                    <?php
                    $news_excerpt = has_excerpt()
                        ? get_the_excerpt()
                        : wp_strip_all_tags(strip_shortcodes(get_the_content()));
                    $news_permalink = get_permalink();
                    ?>
                    <p class="news__date"><?php echo esc_html(get_the_date('d.m.Y')); ?></p>
                    <h3 class="news__headline"><?php the_title(); ?></h3>
                    <p class="news__text"><?php echo esc_html($news_excerpt); ?></p>
                <?php endwhile; ?>
                <?php wp_reset_postdata(); ?>
            <?php else : ?>
                <p class="news__text">Новостей пока нет.</p>
            <?php endif; ?>
            <div class="feature-actions">
                <?php if ($news_permalink) : ?>
                    <a href="<?php echo esc_url($news_permalink); ?>" class="feature-actions__more"><?php echo baltic_option_text('news_button', 'подробнее'); ?></a>
                <?php endif; ?>
                <a class="section__all-link" href="<?php echo esc_url(baltic_content_index_url('baltic_news')); ?>">Все новости →</a>
            </div>
        </div>
    </section>

    <!-- ================= VIDEO ================= -->
    <section class="section video">
        <img src="<?php echo baltic_option_asset('video_background', 'images/video/bg.png'); ?>" alt="" class="video__bg">

        <div class="container">
            <h3 class="video__title"><?php echo baltic_option_text('video_title', 'Название видео'); ?></h3>

            <div class="video__frame">
                <iframe title="Видео Балтика Brew" loading="lazy" src="<?php echo baltic_option_url('video_iframe_src', 'https://vkvideo.ru/video_ext.php?oid=-206889227&id=456240392&hash=fbe8cad821c65ff9&hd=3'); ?>" width="1280" height="720" allow="autoplay; encrypted-media; fullscreen; picture-in-picture; screen-wake-lock;" frameborder="0" allowfullscreen></iframe>
            </div>

            <p class="video__description"><?php echo baltic_option_html('video_description', 'Возможно короткое описание'); ?></p>
        </div>
    </section>

    <!-- ================= AUTHOR ================= -->
    <?php
    $latest_author_post = new WP_Query([
        'post_type' => 'post',
        'post_status' => 'publish',
        'posts_per_page' => 1,
        'orderby' => 'date',
        'order' => 'DESC',
        'no_found_rows' => true,
    ]);
    $author_permalink = '';
    ?>
    <section class="section author" id="author">
        <img src="<?php echo baltic_option_asset('author_background', 'images/author/bg-author.png'); ?>" alt="" class="author__bg">

        <h2 class="author__title gold-title"><?php echo baltic_option_text('author_title', 'Слово автора'); ?></h2>

        <div class="container">
            <?php if ($latest_author_post->have_posts()) : ?>
                <?php while ($latest_author_post->have_posts()) : $latest_author_post->the_post(); ?>
                    <?php $author_permalink = get_permalink(); ?>
                    <h3 class="author__heading"><?php the_title(); ?></h3>
                    <p class="author__text"><?php echo esc_html(has_excerpt() ? get_the_excerpt() : wp_strip_all_tags(strip_shortcodes(get_the_content()))); ?></p>
                <?php endwhile; wp_reset_postdata(); ?>
            <?php else : ?>
                <h3 class="author__heading"><?php echo baltic_option_text('author_heading', 'С ДНЁМ ГОРОДА, КАЛИНИНГРАД'); ?></h3>
                <p class="author__text"><?php echo baltic_option_html('author_text', 'Текст авторского обращения.'); ?></p>
            <?php endif; ?>
            <div class="feature-actions">
                <?php if ($author_permalink) : ?>
                    <a class="feature-actions__more" href="<?php echo esc_url($author_permalink); ?>">Подробнее</a>
                <?php endif; ?>
                <a class="section__all-link" href="<?php echo esc_url(baltic_content_index_url('post')); ?>">Все статьи →</a>
            </div>
        </div>
    </section>

    <!-- ================= FOOTER ================= -->
    <?php get_template_part('template-parts/site-footer'); ?>

</div>

<!-- Cookie Banner -->
<div class="cookie-banner" id="cookieBanner">
    <div class="cookie-banner__content">
        <p class="cookie-banner__text">
            <?php echo baltic_option_html('cookie_text', 'Мы используем файлы cookie, чтобы сайт работал лучше.'); ?>
            <a href="<?php echo baltic_option_asset('cookie_pdf', 'documents/cookie-policy.pdf'); ?>" target="_blank" rel="noopener" class="cookie-banner__link">Подробнее</a>
        </p>
        <button class="cookie-banner__button" id="acceptCookie"><?php echo baltic_option_text('cookie_button', 'ПРИНЯТЬ'); ?></button>
    </div>
</div>



<?php wp_footer(); ?>
</body>
</html>
