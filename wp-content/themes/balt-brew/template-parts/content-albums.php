<?php
/** Album index and photo gallery share the author pages' layout. */
if (!defined('ABSPATH')) {
    exit;
}

$is_album = is_singular('baltic_album');
$album_id = $is_album ? get_queried_object_id() : 0;
$locked = $is_album && post_password_required($album_id);
$photo_ids = $is_album && !$locked ? baltic_album_photo_ids($album_id) : [];
$photo_page_value = get_query_var('photo_page', 1);
$photo_page = is_scalar($photo_page_value) ? max(1, absint($photo_page_value)) : 1;
$photo_total = count($photo_ids);
$photo_offset = ($photo_page - 1) * 12;
$photo_batch = array_slice($photo_ids, $photo_offset, 12);
if ($is_album && !$locked && $photo_page > 1 && !$photo_batch) {
    status_header(404);
}
global $wp_query;
$total = $is_album ? $photo_total : (int) $wp_query->found_posts;
$shown = $is_album ? min($photo_offset + count($photo_batch), $total) : min(max(1, (int) get_query_var('paged')) * 12, $total);
$next_url = '';
if ($is_album && $shown < $total) {
    $next_url = add_query_arg('photo_page', $photo_page + 1, get_permalink($album_id));
} elseif (!$is_album && max(1, (int) get_query_var('paged')) < (int) $wp_query->max_num_pages) {
    $next_url = get_next_posts_page_link($wp_query->max_num_pages);
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
<script src="<?php echo esc_url(baltic_asset('script.js')); ?>" defer></script>
<?php wp_head(); ?>
</head>
<body <?php body_class('content-page content-index-page albums-page'); ?>>
<?php wp_body_open(); ?>
<?php get_template_part('template-parts/content-header'); ?>
<div class="page">
    <main class="content-index albums" data-albums data-albums-kind="<?php echo $is_album ? 'photos' : 'albums'; ?>" data-album-id="<?php echo esc_attr((string) $album_id); ?>" data-total="<?php echo esc_attr((string) $total); ?>">
        <a class="content-page__back" href="<?php echo esc_url($is_album ? get_post_type_archive_link('baltic_album') : home_url('/')); ?>">← <?php echo $is_album ? 'Ко всем фотоальбомам' : 'На главную'; ?></a>
        <h1 class="content-index__title"><?php echo esc_html($is_album ? get_the_title($album_id) : baltic_album_index_title()); ?></h1>

        <?php if ($is_album) : ?>
            <?php while (have_posts()) : the_post(); ?>
                <div class="content-document__body albums__description"><?php the_content(); ?></div>
            <?php endwhile; ?>
        <?php else : ?>
            <?php $description = (string) get_option('options_album_index_description', ''); ?>
            <?php if (trim($description) !== '') : ?>
                <div class="content-document__body albums__description"><?php echo wpautop(esc_html($description)); ?></div>
            <?php endif; ?>
        <?php endif; ?>

        <?php if (!$locked) : ?>
            <div class="<?php echo $is_album ? 'album-photos' : 'content-grid albums__grid'; ?>" data-albums-grid>
                <?php if ($is_album) : ?>
                    <?php foreach ($photo_batch as $photo_index => $photo_id) : ?>
                        <?php
                        $image_url = wp_get_attachment_image_url($photo_id, 'full');
                        $caption = wp_strip_all_tags((string) wp_get_attachment_caption($photo_id));
                        $alt = (string) get_post_meta($photo_id, '_wp_attachment_image_alt', true);
                        $number = $photo_offset + $photo_index + 1;
                        ?>
                        <a class="album-photo" href="<?php echo esc_url($image_url); ?>" data-photo data-photo-number="<?php echo esc_attr((string) $number); ?>" data-caption="<?php echo esc_attr($caption); ?>" data-alt="<?php echo esc_attr($alt); ?>" aria-label="<?php echo esc_attr('Открыть фото ' . $number . ($alt !== '' ? ': ' . $alt : '')); ?>">
                            <?php echo wp_get_attachment_image($photo_id, 'medium_large', false, ['loading' => 'lazy', 'decoding' => 'async', 'sizes' => '(max-width: 540px) 100vw, (max-width: 900px) 50vw, 25vw']); ?>
                        </a>
                    <?php endforeach; ?>
                <?php else : ?>
                    <?php while (have_posts()) : the_post(); ?>
                        <?php $excerpt = has_excerpt() ? get_the_excerpt() : wp_trim_words(wp_strip_all_tags(strip_shortcodes(get_the_content())), 32, '…'); ?>
                        <a class="content-card" href="<?php the_permalink(); ?>">
                            <span class="content-card__image">
                                <?php if (has_post_thumbnail()) : ?>
                                    <?php the_post_thumbnail('medium_large', ['loading' => 'lazy', 'decoding' => 'async']); ?>
                                <?php else : ?>
                                    <img src="<?php echo esc_url(baltic_asset('images/news/default-card.png')); ?>" alt="" loading="lazy">
                                <?php endif; ?>
                            </span>
                            <span class="content-card__title"><?php the_title(); ?></span>
                            <span class="content-card__excerpt"><?php echo esc_html($excerpt); ?></span>
                            <span class="content-card__meta">
                                <span class="content-card__more">Подробнее</span>
                                <time class="content-card__date" datetime="<?php echo esc_attr(get_the_date('c')); ?>"><?php echo esc_html(get_the_date('d.m.Y')); ?></time>
                            </span>
                        </a>
                    <?php endwhile; ?>
                <?php endif; ?>
            </div>
            <?php if ($total === 0 || ($is_album && !$photo_batch)) : ?>
                <p class="content-index__empty"><?php echo $is_album ? 'Фотографий пока нет.' : 'Фотоальбомов пока нет.'; ?></p>
            <?php elseif ($next_url !== '') : ?>
                <div class="albums__pagination" data-albums-pagination>
                    <p class="albums__status" data-albums-status role="status" aria-live="polite"></p>
                    <a class="albums__more" data-albums-next href="<?php echo esc_url($next_url); ?>" rel="next">Показать ещё</a>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </main>
    <?php get_template_part('template-parts/site-footer'); ?>
</div>
<?php if ($is_album && !$locked && $photo_batch) : ?>
    <dialog class="album-lightbox" data-album-lightbox aria-label="Просмотр фотографий">
        <div class="album-lightbox__panel">
            <button class="album-lightbox__close" type="button" data-photo-close aria-label="Закрыть просмотр" autofocus>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true" focusable="false"><path d="m6 6 12 12M18 6 6 18" /></svg>
            </button>
            <button class="album-lightbox__arrow album-lightbox__arrow--prev" type="button" data-photo-prev aria-label="Предыдущее фото">←</button>
            <figure class="album-lightbox__figure">
                <img class="album-lightbox__image" data-photo-image alt="">
                <figcaption class="album-lightbox__caption" data-photo-caption></figcaption>
            </figure>
            <button class="album-lightbox__arrow album-lightbox__arrow--next" type="button" data-photo-next aria-label="Следующее фото">→</button>
            <p class="album-lightbox__count" data-photo-count role="status" aria-live="polite"></p>
        </div>
    </dialog>
<?php endif; ?>
<?php wp_footer(); ?>
</body>
</html>
