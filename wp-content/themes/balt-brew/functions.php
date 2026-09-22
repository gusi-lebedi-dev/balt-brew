<?php
/**
 * Baltic theme helpers.
 */

if (!defined('ABSPATH')) {
    exit;
}

add_action('after_setup_theme', static function () {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
});

add_action('init', static function () {
    register_post_type('baltic_news', [
        'labels' => [
            'name' => 'Новости',
            'singular_name' => 'Новость',
            'menu_name' => 'Новости',
            'add_new' => 'Добавить новость',
            'add_new_item' => 'Добавить новость',
            'edit_item' => 'Редактировать новость',
            'new_item' => 'Новая новость',
            'view_item' => 'Посмотреть новость',
            'search_items' => 'Найти новости',
            'not_found' => 'Новости не найдены',
            'not_found_in_trash' => 'В корзине новостей нет',
            'featured_image' => 'Изображение новости',
            'set_featured_image' => 'Установить изображение новости',
            'remove_featured_image' => 'Удалить изображение новости',
        ],
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'show_in_rest' => true,
        'menu_icon' => 'dashicons-megaphone',
        'menu_position' => 22,
        'supports' => ['title', 'editor', 'excerpt', 'thumbnail'],
        'has_archive' => 'news',
        'rewrite' => [
            'slug' => 'news',
            'with_front' => false,
        ],
        'publicly_queryable' => true,
        'exclude_from_search' => false,
    ]);
});

add_action('init', static function () {
    add_rewrite_rule('^articles/?$', 'index.php?baltic_page=articles', 'top');

    $rewrite_version = '2';

    if (get_option('baltic_news_rewrite_version') === $rewrite_version) {
        return;
    }

    flush_rewrite_rules(false);
    update_option('baltic_news_rewrite_version', $rewrite_version, false);
}, 99);

add_action('init', static function () {
    if (get_option('baltic_initial_news_created')) {
        return;
    }

    $existing_news = get_posts([
        'post_type' => 'baltic_news',
        'post_status' => 'any',
        'posts_per_page' => 1,
        'fields' => 'ids',
        'no_found_rows' => true,
    ]);

    if ($existing_news) {
        update_option('baltic_initial_news_created', 1, false);
        return;
    }

    $title = (string) baltic_option('news_headline', 'С ДНЁМ ГОРОДА, КАЛИНИНГРАД');
    $excerpt = (string) baltic_option(
        'news_excerpt',
        'Эта цифра — отражение большой истории нашей уютной области, где каждый город по-своему уникален! Для нас большая честь представлять и прославлять Калининград и Калининградскую область.'
    );
    $date_value = (string) baltic_option('news_date', '22.08.2026');
    $date = DateTime::createFromFormat('d.m.Y', $date_value);

    $post_data = [
        'post_type' => 'baltic_news',
        'post_status' => 'publish',
        'post_title' => $title,
        'post_excerpt' => $excerpt,
        'post_content' => $excerpt,
    ];

    if ($date instanceof DateTime) {
        $post_data['post_date'] = $date->format('Y-m-d H:i:s');
        $post_data['post_date_gmt'] = get_gmt_from_date($post_data['post_date']);
    }

    $post_id = wp_insert_post(wp_slash($post_data), true);

    if (!is_wp_error($post_id)) {
        update_option('baltic_initial_news_created', 1, false);
    }
}, 30);

function baltic_asset(string $path): string
{
    $path = ltrim($path, '/');
    $url = get_template_directory_uri() . '/' . $path;
    $file = get_template_directory() . '/' . $path;

    if (file_exists($file)) {
        $url = add_query_arg('ver', (string) filemtime($file), $url);
    }

    return $url;
}

function baltic_option(string $field, $fallback = '')
{
    if (!function_exists('get_field')) {
        return $fallback;
    }

    // The "Главная страница" options screen is the single editing location.
    $value = get_field($field, 'option');

    // Preserve values entered on a front page before the options screen existed.
    if ($value === null || $value === false || $value === '') {
        $front_page_id = (int) get_option('page_on_front');
        if ($front_page_id > 0) {
            $value = get_field($field, $front_page_id);
        }
    }

    if ($value === null || $value === false || $value === '') {
        return $fallback;
    }

    return $value;
}

function baltic_option_text(string $field, string $fallback = ''): string
{
    return esc_html((string) baltic_option($field, $fallback));
}

function baltic_option_html(string $field, string $fallback = ''): string
{
    $value = (string) baltic_option($field, $fallback);

    // ACF may already have inserted <br> tags; format each newline only once.
    $value = preg_replace('/<br\s*\/?>\r?\n?/i', "\n", $value);
    return wp_kses_post(nl2br($value));
}

function baltic_option_url(string $field, string $fallback = ''): string
{
    $value = baltic_option($field, $fallback);

    if (is_array($value)) {
        $value = $value['url'] ?? $fallback;
    }

    return esc_url((string) $value);
}

function baltic_option_asset(string $field, string $fallback_path): string
{
    return baltic_option_url($field, baltic_asset($fallback_path));
}

require_once get_template_directory() . '/inc/acf-home.php';

add_filter('query_vars', static function (array $vars): array {
    $vars[] = 'baltic_page';

    return $vars;
});

add_filter('template_include', static function (string $template): string {
    $content_page = get_query_var('baltic_page');

    if ($content_page === 'news' || $content_page === 'articles') {
        $page_template = get_template_directory() . '/page-' . $content_page . '.php';

        if (file_exists($page_template)) {
            if ($content_page === 'articles') {
                // The archive is virtual; the main query may otherwise mark page 2 as 404.
                global $wp_query;
                $wp_query->is_404 = false;
                status_header(200);
            }
            return $page_template;
        }
    }

    return $template;
});

add_filter('document_title_parts', static function (array $parts): array {
    $content_page = get_query_var('baltic_page');

    if ($content_page === 'news' || is_post_type_archive('baltic_news')) {
        $parts['title'] = 'Все новости';
    } elseif ($content_page === 'articles') {
        $parts['title'] = 'Все записи автора';
    }

    return $parts;
});

function baltic_content_index_url(string $type): string
{
    return home_url($type === 'baltic_news' ? '/news/' : '/articles/');
}
