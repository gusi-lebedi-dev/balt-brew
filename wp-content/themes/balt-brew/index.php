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
<?php
$home_sections_hidden = [];
foreach (['hero', 'products', 'about', 'news', 'video', 'author'] as $home_section) {
    $home_sections_hidden[$home_section] = baltic_home_section_hidden($home_section);
}

$about_tabs = function_exists('baltic_home_about_tabs') ? baltic_home_about_tabs() : [];
$home_sections_hidden['about'] = $home_sections_hidden['about'] || !$about_tabs;

$hero_scroll_target = '';
foreach (['products' => '#assortment', 'about' => '#about', 'news' => '#news', 'video' => '#video', 'author' => '#author'] as $home_section => $target) {
    if (!$home_sections_hidden[$home_section]) {
        $hero_scroll_target = $target;
        break;
    }
}
?>
    <!-- ================= HERO ================= -->
    <?php if (!$home_sections_hidden['hero']) : ?>
    <div class="hero">
        <video class="hero__bg hero__video hero__video--desktop hero__video--intro" playsinline muted preload="none" data-hero-format="desktop" data-hero-intro data-src="<?php echo baltic_option_asset('hero_desktop_intro', 'video/OPEN.mp4'); ?>" aria-hidden="true"></video>
        <video class="hero__bg hero__video hero__video--desktop hero__video--loop" playsinline muted preload="none" loop data-hero-format="desktop" data-hero-loop data-src="<?php echo baltic_option_asset('hero_desktop_loop', 'video/LOOP.mp4'); ?>" aria-hidden="true"></video>
        <video class="hero__bg hero__video hero__video--mobile hero__video--intro" playsinline muted preload="none" data-hero-format="mobile" data-hero-intro data-src="<?php echo baltic_option_asset('hero_mobile_intro', 'video/FINAL 9-16_OPEN.mp4'); ?>" aria-hidden="true"></video>
        <video class="hero__bg hero__video hero__video--mobile hero__video--loop" playsinline muted preload="none" loop data-hero-format="mobile" data-hero-loop data-src="<?php echo baltic_option_asset('hero_mobile_loop', 'video/FINAL 9-16_LOOP.mp4'); ?>" aria-hidden="true"></video>
        <?php if ($hero_scroll_target !== '') : ?>
        <button class="hero__scroll" type="button" data-hero-scroll="<?php echo esc_attr($hero_scroll_target); ?>" aria-label="Перейти к следующему разделу" hidden>
            <img class="hero__scroll-icon" src="<?php echo esc_url(baltic_asset('assets/hero-arrow-finish.png')); ?>" alt="" aria-hidden="true">
        </button>
        <?php endif; ?>
    </div>
    <?php endif; ?>
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
            <!-- <div class="header__nav-bar header__nav-bar--desktop"> -->
                <!-- <nav class="header__links">
                    <a href="#assortment" class="header__link">Ассортимент</a>
                    <a href="#about" class="header__link">О нас</a>
                </nav> -->

                <!-- <a class="logo_header" href="#home">
                    <img src="<?php echo baltic_option_asset('header_logo', 'images/header/logo.png'); ?>" alt="Логотип" class="header__logo">
                </a> -->

                <!-- <nav class="header__links">
                    <div class="header__lang">
                        <a href="#" class="header__lang-link header__lang-link--active">RU</a>
                        <a href="#" class="header__lang-link">EN</a>
                    </div>
                </nav> -->
            <!-- </div> -->

            <!-- Mobile Navigation -->
            <!-- <div class="header__nav-bar header__nav-bar--mobile"> -->
                <!-- <div class="header__lang header__lang--mobile">
                    <a href="#" class="header__lang-link header__lang-link--active">RU</a>
                    <a href="#" class="header__lang-link">EN</a>
                </div> -->

                <!-- <a href="#home" style="margin: 0 auto;">
                    <img src="<?php echo baltic_option_asset('header_logo_mobile', 'images/header/logo-mobile.png'); ?>" alt="Логотип" class="header__logo header__logo--mobile">
                </a> -->

                <!-- <button class="header__burger" aria-label="Открыть меню">
                    <img src="<?php echo esc_url(baltic_asset('images/header/menu-icon.svg')); ?>" alt="" class="header__burger-icon">
                </button> -->
            <!-- </div> -->
        </div>

        <!-- Mobile Menu -->
        <nav class="header__mobile-menu">
            <div class="container">
                <?php if (!$home_sections_hidden['products']) : ?>
                <a href="#assortment" class="header__mobile-link">Ассортимент</a>
                <?php endif; ?>
                <?php if (!$home_sections_hidden['about']) : ?>
                <a href="#about" class="header__mobile-link">О нас</a>
                <?php endif; ?>
            </div>
        </nav>
    </header>
    <!-- ================= PRODUCT ================= -->
    <?php if (!$home_sections_hidden['products']) : ?>
    <?php get_template_part('template-parts/home-products'); ?>
    <?php endif; ?>

    <!-- ================= ABOUT / HISTORY ================= -->
    <?php
    $about_active_tab = array_search(true, array_column($about_tabs, 'initially_active'), true);
    $about_active_tab = $about_active_tab === false ? 0 : $about_active_tab;
    $about_background = baltic_option_asset('about_background', 'images/about/about-desktop-bg.png');
    $about_background_mobile = baltic_option_asset('about_background_mobile', 'images/about/about-mobile-bg.png');
    $about_background_style = sprintf(
        '--about-background-desktop: url("%s"); --about-background-mobile: url("%s");',
        $about_background,
        $about_background_mobile
    );
    ?>
    <?php if (!$home_sections_hidden['about'] && $about_tabs) : ?>
    <section class="section about" id="about" style="<?php echo esc_attr($about_background_style); ?>">
        <div class="about_wrapper">
            <h2 class="about__title gold-title"><?php echo baltic_option_text('about_title', 'о нас'); ?></h2>

            <?php if ($about_tabs) : ?>
                <div class="about__tabs-shell">
                    <button type="button" class="about__tabs-arrow about__tabs-arrow--prev" aria-label="Предыдущий раздел">
                        <img src="<?php echo esc_url(baltic_asset('images/left-arrow.png')); ?>" alt="">
                    </button>

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

                    <button type="button" class="about__tabs-arrow about__tabs-arrow--next" aria-label="Следующий раздел">
                        <img src="<?php echo esc_url(baltic_asset('images/right-arrow.png')); ?>" alt="">
                    </button>
                </div>

                <div class="about__content">
                    <?php foreach ($about_tabs as $tab_index => $tab) : ?>
                        <?php
                        $tab_kind = (string) ($tab['kind'] ?? 'custom');
                        $events = $tab_kind === 'history' ? ($tab['timeline_items'] ?: []) : [];
                        $active_event_index = $events ? max(0, min(count($events) - 1, ((int) ($tab['active_timeline_item'] ?? 1)) - 1)) : 0;
                        $is_history_tab = $tab_kind === 'history';
                        $panel_classes = 'about__panel about__panel--' . $tab_kind;
                        if ($tab_index === $about_active_tab) {
                            $panel_classes .= ' about__panel--active';
                        }
                        ?>
                        <div class="<?php echo esc_attr($panel_classes); ?>" data-about-panel="<?php echo esc_attr((string) $tab_index); ?>" id="about-panel-<?php echo esc_attr((string) $tab_index); ?>" role="tabpanel" aria-labelledby="about-tab-<?php echo esc_attr((string) $tab_index); ?>">
                            <div class="about__history-content">
                                <?php if ($is_history_tab && $events) : ?>
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

                            <?php if ($is_history_tab && $events) : ?>
                                <div class="timeline timeline--visible" style="--timeline-total: <?php echo esc_attr((string) count($events)); ?>;">
                                    <div class="timeline__strip">
                                        <div class="timeline__track" aria-hidden="true"></div>
                                        <?php foreach ($events as $event_index => $event) : ?>
                                            <button
                                                type="button"
                                                class="timeline__item<?php echo $event_index === $active_event_index ? ' timeline__item--active' : ''; ?>"
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
    <?php if (!$home_sections_hidden['news']) : ?>
    <?php
    $latest_news = new WP_Query([
        'post_type' => 'baltic_news',
        'post_status' => 'publish',
        'posts_per_page' => 3,
        'orderby' => 'date',
        'order' => 'DESC',
        'no_found_rows' => true,
    ]);
    $news_count = (int) $latest_news->post_count;
    $news_desktop_fallback = $news_count >= 3
        ? 'images/news/news-grid-desktop.png'
        : 'images/news/news-grid-desktop-2.png';
    $news_mobile_fallback = $news_count >= 3
        ? 'images/news/news-grid-mobile.png'
        : 'images/news/news-grid-mobile-2.png';
    $news_background = baltic_option_asset('news_background', $news_desktop_fallback);
    $news_background_mobile = baltic_option_asset('news_background_mobile', $news_mobile_fallback);
    ?>
    <section class="section news news--count-<?php echo esc_attr((string) $news_count); ?>" id="news">
        <picture class="news__artwork" aria-hidden="true">
            <source media="(max-width: 768px)" srcset="<?php echo $news_background_mobile; ?>">
            <img src="<?php echo $news_background; ?>" alt="">
        </picture>

        <div class="news__container">
            <h2 class="news__title gold-title"><?php echo baltic_option_text('news_title', 'новости'); ?></h2>
            <?php if ($latest_news->have_posts()) : ?>
                <div class="news__grid">
                    <?php while ($latest_news->have_posts()) : $latest_news->the_post(); ?>
                        <?php
                        $news_excerpt = has_excerpt()
                            ? get_the_excerpt()
                            : wp_strip_all_tags(strip_shortcodes(get_the_content()));
                        $news_permalink = get_permalink();
                        $news_image = get_the_post_thumbnail_url(get_the_ID(), 'large')
                            ?: baltic_asset('images/news/default-card.png');
                        ?>
                        <article class="news-card">
                            <a class="news-card__image-link" href="<?php echo esc_url($news_permalink); ?>" aria-label="<?php echo esc_attr(sprintf('Открыть новость: %s', get_the_title())); ?>">
                                <img class="news-card__image" src="<?php echo esc_url($news_image); ?>" alt="" loading="lazy">
                            </a>
                            <h3 class="news-card__title">
                                <a href="<?php echo esc_url($news_permalink); ?>"><?php the_title(); ?></a>
                            </h3>
                            <p class="news-card__excerpt"><?php echo esc_html($news_excerpt); ?></p>
                            <time class="news-card__date" datetime="<?php echo esc_attr(get_the_date('c')); ?>"><?php echo esc_html(get_the_date('d.m.Y')); ?></time>
                        </article>
                    <?php endwhile; ?>
                </div>
                <?php wp_reset_postdata(); ?>
            <?php else : ?>
                <p class="news__empty">Новостей пока нет.</p>
            <?php endif; ?>
            <a class="news__all-link" href="<?php echo esc_url(baltic_content_index_url('baltic_news')); ?>"><?php echo baltic_option_text('news_all_label', 'Все новости'); ?> →</a>
        </div>
    </section>
    <?php endif; ?>

    <!-- ================= VIDEO ================= -->
    <?php if (!$home_sections_hidden['video']) : ?>
    <?php
    $video_background = baltic_option_asset('video_background', 'images/video/bg.png');
    $video_background_mobile = baltic_option_asset('video_background_mobile', 'images/video/video-mobile-bg.png');
    $video_iframe_src = (string) baltic_option(
        'video_iframe_src',
        'https://vk.com/video_ext.php?oid=-206889227&id=456240392&hash=fbe8cad821c65ff9&hd=3'
    );
    // The vkvideo.ru embed endpoint loops through redirects for mobile user agents.
    // Keep existing ACF values working by serving VK embeds through vk.com instead.
    $video_iframe_src = preg_replace(
        '#^https?://(?:www\.)?vkvideo\.ru/video_ext\.php#i',
        'https://vk.com/video_ext.php',
        trim($video_iframe_src)
    );
    ?>
    <section class="section video" id="video">
        <picture class="video__artwork" aria-hidden="true">
            <source media="(max-width: 768px)" srcset="<?php echo $video_background_mobile; ?>">
            <img src="<?php echo $video_background; ?>" alt="" class="video__bg">
        </picture>

        <div class="container">
            <h3 class="video__title"><?php echo baltic_option_text('video_title', 'Название видео'); ?></h3>

            <div class="video__frame">
                <iframe title="Видео Балтика Brew" loading="lazy" src="<?php echo esc_url($video_iframe_src); ?>" width="1280" height="720" allow="autoplay; encrypted-media; fullscreen; picture-in-picture; screen-wake-lock;" frameborder="0" allowfullscreen></iframe>
            </div>

            <p class="video__description"><?php echo baltic_option_html('video_description', 'Возможно короткое описание'); ?></p>
        </div>
    </section>
    <?php endif; ?>

    <!-- ================= AUTHOR ================= -->
    <?php if (!$home_sections_hidden['author']) : ?>
    <?php
    $latest_author_post = new WP_Query([
        'post_type' => 'post',
        'post_status' => 'publish',
        'posts_per_page' => 3,
        'orderby' => 'date',
        'order' => 'DESC',
        'no_found_rows' => true,
    ]);
    $author_count = max(1, (int) $latest_author_post->post_count);
    $author_mobile_fallback = $author_count >= 3
        ? 'images/author/author-grid-mobile-3.png'
        : 'images/author/author-grid-mobile.png';
    $author_background = baltic_option_asset('author_background', 'images/author/author-grid-desktop.png');
    $author_background_mobile = baltic_option_asset('author_background_mobile', $author_mobile_fallback);
    ?>
    <section class="section author author--count-<?php echo esc_attr((string) $author_count); ?>" id="author">
        <picture class="author__artwork" aria-hidden="true">
            <source media="(max-width: 768px)" srcset="<?php echo $author_background_mobile; ?>">
            <img src="<?php echo $author_background; ?>" alt="">
        </picture>

        <div class="author__container">
            <h2 class="author__title gold-title"><?php echo baltic_option_text('author_title', 'Слово автора'); ?></h2>

            <div class="author__grid">
            <?php if ($latest_author_post->have_posts()) : ?>
                <?php while ($latest_author_post->have_posts()) : $latest_author_post->the_post(); ?>
                    <?php
                    $author_excerpt = has_excerpt()
                        ? get_the_excerpt()
                        : wp_strip_all_tags(strip_shortcodes(get_the_content()));
                    $author_permalink = get_permalink();
                    $author_image = get_the_post_thumbnail_url(get_the_ID(), 'large')
                        ?: baltic_asset('images/news/default-card.png');
                    ?>
                    <article class="author-card">
                        <a class="author-card__image-link" href="<?php echo esc_url($author_permalink); ?>" aria-label="<?php echo esc_attr(sprintf('Открыть запись: %s', get_the_title())); ?>">
                            <img class="author-card__image" src="<?php echo esc_url($author_image); ?>" alt="" loading="lazy">
                        </a>
                        <h3 class="author-card__title">
                            <a href="<?php echo esc_url($author_permalink); ?>"><?php the_title(); ?></a>
                        </h3>
                        <p class="author-card__text"><?php echo esc_html($author_excerpt); ?></p>
                        <time class="author-card__date" datetime="<?php echo esc_attr(get_the_date('c')); ?>"><?php echo esc_html(get_the_date('d.m.Y')); ?></time>
                    </article>
                <?php endwhile; ?>
                <?php wp_reset_postdata(); ?>
            <?php else : ?>
                <article class="author-card">
                    <span class="author-card__image-link" aria-hidden="true">
                        <img class="author-card__image" src="<?php echo esc_url(baltic_asset('images/news/default-card.png')); ?>" alt="">
                    </span>
                    <h3 class="author-card__title"><?php echo baltic_option_text('author_heading', 'С ДНЁМ ГОРОДА, КАЛИНИНГРАД'); ?></h3>
                    <p class="author-card__text"><?php echo baltic_option_html('author_text', 'Текст авторского обращения.'); ?></p>
                </article>
            <?php endif; ?>
            </div>
            <a class="author__all-link" href="<?php echo esc_url(baltic_content_index_url('post')); ?>"><?php echo baltic_option_text('author_all_label', 'Все записи'); ?> →</a>
        </div>
    </section>
    <?php endif; ?>

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
