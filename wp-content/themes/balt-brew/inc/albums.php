<?php
/** Photo albums and their editor fields. */

if (!defined('ABSPATH')) {
    exit;
}

add_action('init', static function (): void {
    register_post_type('baltic_album', [
        'labels' => [
            'name' => 'Фотоальбомы',
            'singular_name' => 'Фотоальбом',
            'add_new' => 'Добавить альбом',
            'add_new_item' => 'Добавить фотоальбом',
            'edit_item' => 'Редактировать фотоальбом',
            'new_item' => 'Новый фотоальбом',
            'view_item' => 'Открыть фотоальбом',
            'search_items' => 'Найти фотоальбом',
            'not_found' => 'Фотоальбомов пока нет',
            'not_found_in_trash' => 'В корзине фотоальбомов нет',
            'featured_image' => 'Обложка альбома',
            'set_featured_image' => 'Выбрать обложку',
            'remove_featured_image' => 'Удалить обложку',
        ],
        'public' => true,
        'show_in_rest' => true,
        'menu_icon' => 'dashicons-format-gallery',
        'menu_position' => 23,
        'supports' => ['title', 'editor', 'excerpt', 'thumbnail', 'revisions'],
        'has_archive' => 'albums',
        'rewrite' => ['slug' => 'albums', 'with_front' => false],
    ]);
});

// Runs once after manual theme upload, after all post types and routes exist.
add_action('init', static function (): void {
    if (get_option('baltic_albums_rewrite_version') !== '1') {
        flush_rewrite_rules(false);
        update_option('baltic_albums_rewrite_version', '1', false);
    }
}, 100);

add_filter('query_vars', static function (array $vars): array {
    $vars[] = 'photo_page';
    return $vars;
});

add_action('pre_get_posts', static function (WP_Query $query): void {
    if (!is_admin() && $query->is_main_query() && $query->is_post_type_archive('baltic_album')) {
        $query->set('posts_per_page', 12);
        $query->set('orderby', ['date' => 'DESC', 'ID' => 'DESC']);
        $query->set('has_password', false);
    }
});

add_action('wp_enqueue_scripts', static function (): void {
    if (is_post_type_archive('baltic_album') || is_singular('baltic_album')) {
        wp_enqueue_style('baltic-albums', baltic_asset('albums.css'), [], null);
        wp_enqueue_script('baltic-albums', baltic_asset('albums.js'), [], null, ['strategy' => 'defer']);
    }
});

function baltic_album_index_title(): string
{
    $title = trim((string) get_option('options_album_index_title', ''));
    return $title !== '' ? $title : 'Фотоальбомы';
}

add_filter('document_title_parts', static function (array $parts): array {
    if (is_post_type_archive('baltic_album')) {
        $parts['title'] = baltic_album_index_title();
    }
    return $parts;
});

/** Read stored IDs independently of ACF availability; ignore deleted/non-image media. */
function baltic_album_photo_ids(int $post_id): array
{
    $ids = array_values(array_unique(array_filter(array_map('absint', (array) get_post_meta($post_id, 'album_photos', true)))));
    if (!$ids) {
        return [];
    }
    return get_posts([
        'post_type' => 'attachment',
        'post_status' => 'inherit',
        'post_mime_type' => 'image',
        'post__in' => $ids,
        'orderby' => 'post__in',
        'posts_per_page' => -1,
        'fields' => 'ids',
        'no_found_rows' => true,
    ]);
}

function baltic_register_album_fields(): void
{
    static $registered = false;
    if ($registered || !function_exists('acf_add_local_field_group') || !function_exists('acf_add_options_sub_page')) {
        return;
    }
    $registered = true;

    acf_add_options_sub_page([
        'page_title' => 'Страница фотоальбомов',
        'menu_title' => 'Настройки страницы',
        'menu_slug' => 'baltic-albums-settings',
        'parent_slug' => 'edit.php?post_type=baltic_album',
        'capability' => 'edit_posts',
    ]);
    acf_add_local_field_group([
        'key' => 'group_baltic_album_index',
        'title' => 'Заголовок и описание страницы',
        'fields' => [
            [
                'key' => 'field_baltic_album_index_title',
                'name' => 'album_index_title',
                'label' => 'Заголовок',
                'type' => 'text',
                'default_value' => 'Фотоальбомы',
            ],
            [
                'key' => 'field_baltic_album_index_description',
                'name' => 'album_index_description',
                'label' => 'Описание',
                'type' => 'textarea',
                'rows' => 4,
                'instructions' => 'Текст под заголовком на странице /albums/.',
            ],
        ],
        'location' => [[['param' => 'options_page', 'operator' => '==', 'value' => 'baltic-albums-settings']]],
    ]);
    acf_add_local_field_group([
        'key' => 'group_baltic_album_photos',
        'title' => 'Фотографии альбома',
        'fields' => [[
            'key' => 'field_baltic_album_photos',
            'name' => 'album_photos',
            'label' => 'Фотогалерея',
            'type' => 'gallery',
            'return_format' => 'id',
            'preview_size' => 'medium',
            'library' => 'all',
            'insert' => 'append',
            'mime_types' => 'jpg,jpeg,png,webp,avif,gif',
            'instructions' => 'Добавьте фотографии и перетащите их в нужном порядке. Подписи и альтернативный текст редактируются в медиатеке. Заголовок и основной текст записи — название и описание альбома; «Отрывок» — описание карточки; дата публикации — дата карточки.',
        ]],
        'location' => [[['param' => 'post_type', 'operator' => '==', 'value' => 'baltic_album']]],
    ]);
}
add_action('acf/init', 'baltic_register_album_fields');
add_action('init', 'baltic_register_album_fields', 20);
