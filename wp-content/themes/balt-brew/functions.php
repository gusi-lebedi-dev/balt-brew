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

// Compatibility for the legacy header/footer templates kept in the theme.
function balt_brew_asset(string $path): string
{
    return baltic_asset($path);
}

function balt_brew_is_front_view(): bool
{
    return is_front_page() || is_home();
}

function baltic_option(string $field, $fallback = '')
{
    if (!function_exists('get_field')) {
        return $fallback;
    }

    $footer_fields = [
        'footer_logo', 'footer_vk_url', 'footer_phone', 'footer_phone_href',
        'footer_phone_subtitle', 'footer_company', 'footer_feedback_label',
        'personal_pdf', 'cookie_pdf', 'cookie_text', 'cookie_button',
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

function baltic_home_section_hidden(string $section): bool
{
    $sections = ['hero', 'products', 'about', 'news', 'video', 'author'];

    if (!in_array($section, $sections, true)) {
        return false;
    }

    return (bool) baltic_option('hide_' . $section, false);
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
    if (function_exists('mb_substr')) {
        return mb_substr($value, 0, $length);
    }

    if (function_exists('grapheme_substr')) {
        $limited_value = grapheme_substr($value, 0, $length);
        if ($limited_value !== false) {
            return $limited_value;
        }
    }

    $characters = preg_split('//u', $value, -1, PREG_SPLIT_NO_EMPTY);
    return is_array($characters)
        ? implode('', array_slice($characters, 0, $length))
        : substr($value, 0, $length);
}

function baltic_feedback_post_value(string $key): string
{
    if (!isset($_POST[$key]) || !is_string($_POST[$key])) {
        return '';
    }

    return wp_unslash($_POST[$key]);
}

function baltic_feedback_email_body(array $values): string
{
    $site_name = sanitize_text_field(wp_specialchars_decode(get_bloginfo('name'), ENT_QUOTES));
    $site_name = $site_name !== '' ? $site_name : 'Балтика Brew';
    $site_url = home_url('/');
    $submitted_at = wp_date('d.m.Y, H:i');
    $empty_value = '<span style="color:#8c8378;">Не указано</span>';

    $fields = [
        'name' => 'ФИО',
        'phone' => 'Телефон',
        'email' => 'Почта',
        'product' => 'Наименование продукции',
        'production_date' => 'Дата розлива',
        'factory' => 'Завод-изготовитель',
    ];
    $rows = '';

    foreach ($fields as $key => $label) {
        $value = (string) ($values[$key] ?? '');
        $display_value = $value === '' ? $empty_value : nl2br(esc_html($value), false);

        if ($key === 'email' && is_email($value)) {
            $display_value = sprintf(
                '<a href="%s" style="color:#0d1d66;text-decoration:underline;">%s</a>',
                esc_url('mailto:' . $value),
                esc_html($value)
            );
        } elseif ($key === 'phone' && $value !== '') {
            $phone_href = preg_replace('/[^\d+]/', '', $value);
            $display_value = sprintf(
                '<a href="%s" style="color:#0d1d66;text-decoration:underline;">%s</a>',
                esc_url('tel:' . $phone_href),
                esc_html($value)
            );
        }

        $rows .= sprintf(
            '<tr><td style="width:42%%;padding:14px 16px;border-bottom:1px solid #eadfce;color:#756758;font:600 13px/1.4 Arial,sans-serif;vertical-align:top;">%s</td><td style="padding:14px 16px;border-bottom:1px solid #eadfce;color:#281f19;font:400 15px/1.5 Arial,sans-serif;vertical-align:top;word-break:break-word;">%s</td></tr>',
            esc_html($label),
            $display_value
        );
    }

    $safe_site_name = esc_html($site_name);
    $safe_site_url = esc_url($site_url);
    $safe_submitted_at = esc_html($submitted_at);
    $message = nl2br(esc_html((string) ($values['message'] ?? '')), false);
    $reply_button = '';

    if (is_email((string) ($values['email'] ?? ''))) {
        $reply_button = sprintf(
            '<table role="presentation" cellspacing="0" cellpadding="0" border="0" style="margin:24px 0 0;"><tr><td style="border-radius:4px;background:#0d1d66;"><a href="%s" style="display:inline-block;padding:12px 20px;color:#ffffff;font:600 14px/1 Arial,sans-serif;text-decoration:none;">Ответить заявителю</a></td></tr></table>',
            esc_url('mailto:' . $values['email'])
        );
    }

    return <<<HTML
<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Новое обращение с сайта {$safe_site_name}</title>
</head>
<body style="margin:0;padding:0;background:#f3eadc;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="width:100%;background:#f3eadc;">
        <tr>
            <td align="center" style="padding:32px 12px;">
                <table role="presentation" width="680" cellspacing="0" cellpadding="0" border="0" style="width:100%;max-width:680px;background:#ffffff;border:1px solid #dfcfb8;border-radius:12px;overflow:hidden;">
                    <tr>
                        <td style="padding:28px 32px;background:#0d1d66;background-image:linear-gradient(90deg,#07153a 0%,#0d1d66 45%,#091a47 100%);">
                            <div style="margin:0 0 8px;color:#e0b55a;font:600 12px/1.3 Arial,sans-serif;letter-spacing:1.6px;text-transform:uppercase;">Балтика Brew</div>
                            <h1 style="margin:0;color:#ffffff;font:700 25px/1.25 Arial,sans-serif;">Новое обращение с сайта</h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:28px 32px 12px;">
                            <p style="margin:0;color:#502c2c;font:400 16px/1.55 Arial,sans-serif;">Посетитель заполнил форму обратной связи. Контактные данные и информация о продукции собраны ниже.</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:12px 16px 0;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="width:100%;border:1px solid #eadfce;border-radius:8px;border-collapse:separate;border-spacing:0;overflow:hidden;">
                                {$rows}
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:28px 32px 32px;">
                            <h2 style="margin:0 0 10px;color:#502c2c;font:700 17px/1.3 Arial,sans-serif;">Сообщение</h2>
                            <div style="padding:18px 20px;background:#fbf6ee;border-left:4px solid #d1a34b;color:#281f19;font:400 15px/1.6 Arial,sans-serif;word-break:break-word;">{$message}</div>
                            {$reply_button}
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:20px 32px;background:#f8f2e8;border-top:1px solid #eadfce;color:#756758;font:400 12px/1.5 Arial,sans-serif;">
                            Отправлено {$safe_submitted_at} через форму на <a href="{$safe_site_url}" style="color:#0d1d66;text-decoration:underline;">{$safe_site_name}</a>.<br>
                            Это автоматическое уведомление — отвечать на него можно только при указанной заявителем почте.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
HTML;
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

    $recipient = 'bsite.robot@dialogforce.tech';
    $site_name = sanitize_text_field(
        wp_specialchars_decode(get_bloginfo('name'), ENT_QUOTES)
    );
    $subject = sprintf(
        '[%s] Новое обращение — %s',
        $site_name !== '' ? $site_name : 'Балтика Brew',
        baltic_feedback_limit($values['product'], 100)
    );
    $body = baltic_feedback_email_body($values);
    $headers = [
        'Content-Type: text/html; charset=UTF-8',
    ];

    if ($values['email'] !== '' && is_email($values['email'])) {
        $headers[] = 'Reply-To: ' . $values['email'];
    }

    $mail_failed_callback = static function ($wp_error): void {
        if (!is_wp_error($wp_error)) {
            return;
        }

        error_log('Baltic feedback wp_mail_failed: ' . $wp_error->get_error_message());
    };

    add_action('wp_mail_failed', $mail_failed_callback);
    $mail_sent = wp_mail($recipient, $subject, $body, $headers);
    remove_action('wp_mail_failed', $mail_failed_callback);

    if (!$mail_sent) {
        wp_send_json_error([
            'message' => 'Не удалось отправить обращение. Попробуйте ещё раз позже.',
        ], 500);
    }

    wp_send_json_success([
        'message' => 'Обращение принято.',
    ]);
}

add_action('wp_ajax_baltic_submit_feedback', 'baltic_handle_feedback');
add_action('wp_ajax_nopriv_baltic_submit_feedback', 'baltic_handle_feedback');
