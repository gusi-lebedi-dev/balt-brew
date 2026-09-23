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

    $footer_fields = [
        'footer_logo', 'footer_vk_url', 'footer_phone', 'footer_phone_href',
        'footer_phone_subtitle', 'footer_company', 'footer_feedback_label',
        'feedback_recipient_email', 'personal_pdf', 'cookie_pdf', 'cookie_text',
        'cookie_button',
    ];

    if (in_array($field, $footer_fields, true)) {
        $value = get_field($field, 'option');
    } else {
        $front_page_id = (int) get_option('page_on_front');
        if ($front_page_id > 0 && metadata_exists('post', $front_page_id, $field)) {
            $value = get_field($field, $front_page_id);
        } else {
            // Read legacy options until the one-time field migration runs.
            $value = get_field($field, 'option');
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

function baltic_feedback_limit(string $value, int $length): string
{
    return function_exists('mb_substr')
        ? mb_substr($value, 0, $length)
        : substr($value, 0, $length);
}

function baltic_feedback_post_value(string $key): string
{
    if (!isset($_POST[$key]) || !is_string($_POST[$key])) {
        return '';
    }

    return wp_unslash($_POST[$key]);
}

function baltic_handle_feedback(): void
{
    if (!isset($_POST['nonce']) || !is_string($_POST['nonce']) || !check_ajax_referer('baltic_feedback', 'nonce', false)) {
        wp_send_json_error(['message' => 'Сессия формы истекла. Обновите страницу и попробуйте снова.'], 403);
    }

    if (baltic_feedback_post_value('website') !== '') {
        wp_send_json_success(['message' => 'Обращение принято.']);
    }

    $raw_email = baltic_feedback_post_value('email');

    $values = [
        'name' => baltic_feedback_limit(sanitize_text_field(baltic_feedback_post_value('name')), 160),
        'phone' => baltic_feedback_limit(sanitize_text_field(baltic_feedback_post_value('phone')), 80),
        'email' => baltic_feedback_limit(sanitize_email($raw_email), 160),
        'product' => baltic_feedback_limit(sanitize_text_field(baltic_feedback_post_value('product')), 200),
        'production_date' => baltic_feedback_limit(sanitize_text_field(baltic_feedback_post_value('production_date')), 80),
        'factory' => baltic_feedback_limit(sanitize_text_field(baltic_feedback_post_value('factory')), 120),
        'message' => baltic_feedback_limit(sanitize_textarea_field(baltic_feedback_post_value('message')), 5000),
    ];

    $errors = [];
    foreach (['name', 'phone', 'product', 'factory', 'message'] as $required_field) {
        if ($values[$required_field] === '') {
            $errors[$required_field] = 'Заполните поле';
        }
    }

    if ($values['phone'] !== '' && strlen(preg_replace('/\D+/', '', $values['phone'])) < 7) {
        $errors['phone'] = 'Проверьте номер телефона';
    }
    if ($raw_email !== '' && !is_email($values['email'])) {
        $errors['email'] = 'Проверьте адрес почты';
    }

    if ($errors) {
        wp_send_json_error([
            'message' => 'Проверьте заполнение формы.',
            'fields' => $errors,
        ], 422);
    }

    $remote_address = isset($_SERVER['REMOTE_ADDR']) && is_string($_SERVER['REMOTE_ADDR'])
        ? $_SERVER['REMOTE_ADDR']
        : 'unknown';
    $rate_key = 'baltic_feedback_' . substr(hash_hmac('sha256', $remote_address, wp_salt('nonce')), 0, 32);
    $attempts = (int) get_transient($rate_key);
    if ($attempts >= 8) {
        wp_send_json_error([
            'message' => 'Слишком много обращений. Попробуйте снова через 15 минут.',
        ], 429);
    }
    set_transient($rate_key, $attempts + 1, 15 * MINUTE_IN_SECONDS);

    $recipient = (string) baltic_option('feedback_recipient_email', (string) get_option('admin_email'));
    if (!is_email($recipient)) {
        $recipient = (string) get_option('admin_email');
    }

    $subject = sprintf('Новое обращение с сайта %s', wp_specialchars_decode(get_bloginfo('name'), ENT_QUOTES));
    $body = implode("\n", [
        'ФИО: ' . $values['name'],
        'Телефон: ' . $values['phone'],
        'Почта: ' . ($values['email'] ?: 'не указана'),
        'Продукция: ' . $values['product'],
        'Дата розлива: ' . ($values['production_date'] ?: 'не указана'),
        'Завод-изготовитель: ' . $values['factory'],
        '',
        'Сообщение:',
        $values['message'],
    ]);
    $headers = ['Content-Type: text/plain; charset=UTF-8'];
    if ($values['email'] !== '') {
        $headers[] = sprintf('Reply-To: %s <%s>', $values['name'], $values['email']);
    }

    if (!wp_mail($recipient, $subject, $body, $headers)) {
        wp_send_json_error(['message' => 'Не удалось отправить обращение. Попробуйте ещё раз позже.'], 500);
    }

    wp_send_json_success(['message' => 'Обращение принято.']);
}

add_action('wp_ajax_baltic_submit_feedback', 'baltic_handle_feedback');
add_action('wp_ajax_nopriv_baltic_submit_feedback', 'baltic_handle_feedback');
