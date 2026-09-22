<?php
/**
 * Shared single template for a news item or author article.
 */

if (!defined('ABSPATH')) {
    exit;
}

$is_news = get_post_type() === 'baltic_news';
$back_url = baltic_content_index_url($is_news ? 'baltic_news' : 'post');
$back_label = $is_news ? 'Ко всем новостям' : 'Ко всем записям';
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo('charset'); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="<?php echo esc_url(baltic_asset('styles.css')); ?>">
<link rel="stylesheet" href="<?php echo esc_url(baltic_asset('styles-mobile.css')); ?>">
<link rel="stylesheet" href="<?php echo esc_url(baltic_asset('pages-fixes.css')); ?>">
<script src="<?php echo esc_url(baltic_asset('age-check.js')); ?>"></script>
<script src="<?php echo esc_url(baltic_asset('script.js')); ?>" defer></script>
<?php wp_head(); ?>
</head>
<body <?php body_class('content-page content-document-page'); ?>>
<?php wp_body_open(); ?>
<div class="page">
    <?php get_template_part('template-parts/content-header'); ?>

    <main class="content-document">
        <?php while (have_posts()) : the_post(); ?>
            <article class="content-document__inner">
                <a class="content-page__back" href="<?php echo esc_url($back_url); ?>">← <?php echo esc_html($back_label); ?></a>
                <h1 class="content-document__title"><?php the_title(); ?></h1>

                <?php if (has_post_thumbnail()) : ?>
                    <div class="content-document__image"><?php the_post_thumbnail('full', ['alt' => the_title_attribute(['echo' => false])]); ?></div>
                <?php elseif ($is_news) : ?>
                    <div class="content-document__image"><img src="<?php echo esc_url(baltic_asset('images/news/default-article.png')); ?>" alt=""></div>
                <?php endif; ?>

                <div class="content-document__body"><?php the_content(); ?></div>
            </article>
        <?php endwhile; ?>
    </main>

    <?php get_template_part('template-parts/site-footer'); ?>
</div>
<?php wp_footer(); ?>
</body>
</html>
