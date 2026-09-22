<?php
/**
 * Shared card index for news and author articles.
 *
 * Expects $baltic_content_type and $baltic_page_title.
 */

if (!defined('ABSPATH')) {
    exit;
}

$baltic_content_type = isset($baltic_content_type) ? (string) $baltic_content_type : 'post';
$baltic_page_title = isset($baltic_page_title) ? (string) $baltic_page_title : 'Публикации';
$current_page = max(1, (int) get_query_var('paged'), isset($_GET['paged']) ? absint($_GET['paged']) : 1);
$content_items = new WP_Query([
    'post_type' => $baltic_content_type,
    'post_status' => 'publish',
    'posts_per_page' => 9,
    'paged' => $current_page,
    'orderby' => 'date',
    'order' => 'DESC',
    'ignore_sticky_posts' => true,
]);
if ($current_page > 1 && $current_page > (int) $content_items->max_num_pages) {
    status_header(404);
}
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
<body <?php body_class('content-page content-index-page'); ?>>
<?php wp_body_open(); ?>
<div class="page">
    <?php get_template_part('template-parts/content-header'); ?>

    <main class="content-index">
        <a class="content-page__back" href="<?php echo esc_url(home_url('/')); ?>">← На главную</a>
        <h1 class="content-index__title"><?php echo esc_html($baltic_page_title); ?></h1>

        <?php if ($content_items->have_posts()) : ?>
            <div class="content-grid">
                <?php while ($content_items->have_posts()) : $content_items->the_post(); ?>
                    <?php
                    $excerpt = has_excerpt()
                        ? get_the_excerpt()
                        : wp_trim_words(wp_strip_all_tags(strip_shortcodes(get_the_content())), 32, '…');
                    ?>
                    <a class="content-card" href="<?php the_permalink(); ?>">
                        <span class="content-card__image">
                            <?php if (has_post_thumbnail()) : ?>
                                <?php the_post_thumbnail('large', ['alt' => the_title_attribute(['echo' => false]), 'loading' => 'lazy']); ?>
                            <?php else : ?>
                                <img src="<?php echo esc_url(baltic_asset('images/news/default-card.png')); ?>" alt="" loading="lazy">
                            <?php endif; ?>
                        </span>
                        <span class="content-card__title"><?php the_title(); ?></span>
                        <span class="content-card__excerpt"><?php echo esc_html($excerpt); ?></span>
                        <time class="content-card__date" datetime="<?php echo esc_attr(get_the_date('c')); ?>"><?php echo esc_html(get_the_date('d.m.Y')); ?></time>
                    </a>
                <?php endwhile; ?>
            </div>
            <?php wp_reset_postdata(); ?>
            <?php if ($content_items->max_num_pages > 1) : ?>
                <nav class="content-pagination" aria-label="Страницы публикаций">
                    <?php for ($page_number = 1; $page_number <= $content_items->max_num_pages; $page_number++) : ?>
                        <a href="<?php echo esc_url($page_number === 1 ? baltic_content_index_url($baltic_content_type) : add_query_arg('paged', $page_number, baltic_content_index_url($baltic_content_type))); ?>"<?php echo $page_number === $current_page ? ' aria-current="page"' : ''; ?>><?php echo esc_html((string) $page_number); ?></a>
                    <?php endfor; ?>
                </nav>
            <?php endif; ?>
        <?php else : ?>
            <p class="content-index__empty">Публикаций пока нет.</p>
        <?php endif; ?>
    </main>

    <?php get_template_part('template-parts/site-footer'); ?>
</div>
<?php wp_footer(); ?>
</body>
</html>
