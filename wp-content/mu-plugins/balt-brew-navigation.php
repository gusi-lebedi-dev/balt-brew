<?php
/**
 * Plugin Name: Balt Brew Navigation
 * Description: Home-section anchors and the editable site header, independent of the theme functions.php file.
 */

if (!defined('ABSPATH')) {
    exit;
}

/** Existing fragments remain the defaults when no anchor has been saved. */
function baltic_nav_home_anchor_defaults(): array
{
    return [
        'hero' => 'home',
        'products' => 'assortment',
        'about' => 'about',
        'news' => 'news',
        'video' => 'video',
        'author' => 'author',
    ];
}

function baltic_nav_home_anchor_normalize($value, string $fallback): string
{
    $anchor = ltrim(trim((string) $value), '#');

    return preg_match('/^[A-Za-z][A-Za-z0-9_-]*$/D', $anchor) ? $anchor : $fallback;
}

function baltic_nav_home_anchors(): array
{
    $anchors = [];
    $used = [];

    foreach (baltic_nav_home_anchor_defaults() as $section => $default) {
        $value = function_exists('baltic_option')
            ? baltic_option('anchor_' . $section, $default)
            : $default;
        $anchor = baltic_nav_home_anchor_normalize($value, $default);
        if (isset($used[$anchor])) {
            $anchor = $default;
            $suffix = 2;
            while (isset($used[$anchor])) {
                $anchor = $default . '-' . $suffix++;
            }
        }

        $anchors[$section] = $anchor;
        $used[$anchor] = true;
    }

    return $anchors;
}

function baltic_nav_home_anchor(string $section): string
{
    return baltic_nav_home_anchors()[$section] ?? '';
}

function baltic_nav_header_hidden(): bool
{
    $value = function_exists('get_field')
        ? get_field('hide_header', 'option')
        : get_option('options_hide_header', false);

    return (bool) $value;
}

function baltic_nav_header_custom_logo_url(): string
{
    $value = function_exists('get_field')
        ? get_field('header_logo', 'option')
        : get_option('options_header_logo', '');

    if (is_array($value)) {
        $value = $value['url'] ?? '';
    }
    if (is_numeric($value)) {
        $value = wp_get_attachment_url((int) $value);
    }

    return is_string($value) ? esc_url_raw(trim($value)) : '';
}

function baltic_nav_header_has_custom_logo(): bool
{
    return baltic_nav_header_custom_logo_url() !== '';
}

function baltic_nav_header_logo_url(bool $mobile = false): string
{
    $custom_logo = baltic_nav_header_custom_logo_url();
    if ($custom_logo !== '') {
        return $custom_logo;
    }

    $path = $mobile ? 'images/header/logo-mobile.png' : 'images/header/logo.png';

    return function_exists('baltic_asset')
        ? baltic_asset($path)
        : get_template_directory_uri() . '/' . $path;
}

/** Accept section fragments, site paths and absolute URLs in the menu editor. */
function baltic_nav_header_link_url(string $url): string
{
    $url = trim($url);
    if ($url === '') {
        return '';
    }

    if ($url[0] === '#') {
        $url = home_url('/' . $url);
    } elseif ($url[0] === '/' && strpos($url, '//') !== 0) {
        $url = home_url($url);
    } elseif (strpos($url, '//') !== 0 && !preg_match('/^[a-z][a-z0-9+.-]*:/i', $url)) {
        return '';
    }

    return esc_url_raw($url);
}

/** Initial links shown in both the ACF editor and the header before first save. */
function baltic_nav_header_default_menu_items(): array
{
    return [
        ['label' => 'Ассортимент', 'url' => '#' . baltic_nav_home_anchor('products')],
        ['label' => 'О нас', 'url' => '#' . baltic_nav_home_anchor('about')],
        ['label' => 'Новости', 'url' => '#' . baltic_nav_home_anchor('news')],
        ['label' => 'Видео', 'url' => '#' . baltic_nav_home_anchor('video')],
        ['label' => 'Слово автора', 'url' => '#' . baltic_nav_home_anchor('author')],
    ];
}

/** Return at most six valid menu entries; a saved empty menu stays empty. */
function baltic_nav_header_menu_items(): array
{
    if (get_option('options_header_menu_items', null) === null) {
        $rows = baltic_nav_header_default_menu_items();
    } else {
        $rows = function_exists('get_field')
            ? (get_field('header_menu_items', 'option') ?: [])
            : [];
    }

    $hidden_sections_by_url = [];
    foreach (['products', 'about', 'news', 'video', 'author'] as $section) {
        $hidden_sections_by_url[baltic_nav_header_link_url('#' . baltic_nav_home_anchor($section))] = $section;
    }

    $items = [];
    foreach ((array) $rows as $row) {
        if (count($items) >= 6) {
            break;
        }
        if (!is_array($row) || !is_string($row['label'] ?? null) || !is_string($row['url'] ?? null)) {
            continue;
        }

        $label = trim(sanitize_text_field($row['label']));
        $url = baltic_nav_header_link_url($row['url']);
        if ($label === '' || $url === '') {
            continue;
        }
        if (isset($hidden_sections_by_url[$url])) {
            $section = $hidden_sections_by_url[$url];
            $section_hidden = function_exists('baltic_home_section_hidden')
                && baltic_home_section_hidden($section);
            $empty_about = $section === 'about'
                && function_exists('baltic_home_about_tabs')
                && !baltic_home_about_tabs();
            if ($section_hidden || $empty_about) {
                continue;
            }
        }

        $items[] = ['label' => $label, 'url' => $url];
    }

    return $items;
}
