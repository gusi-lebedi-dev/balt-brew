<?php
/**
 * Shared, ACF-backed site header.
 */

if (!defined('ABSPATH') || baltic_nav_header_hidden()) {
    return;
}

$menu_items = baltic_nav_header_menu_items();
$left_count = (int) ceil(count($menu_items) / 2);
$left_items = array_slice($menu_items, 0, $left_count);
$right_items = array_slice($menu_items, $left_count);
$logo_modifier = baltic_nav_header_has_custom_logo() ? ' header__logo--custom' : '';
$logo_href = baltic_home_section_hidden('hero')
    ? home_url('/')
    : home_url('/#' . baltic_nav_home_anchor('hero'));
?>
<header class="header<?php echo (is_front_page() || is_home()) && !baltic_home_section_hidden('hero') ? ' header--scroll-reveal' : ''; ?>">
    <div class="container header__container">
        <div class="header__nav-bar header__nav-bar--desktop">
            <nav class="header__links header__links--left" aria-label="Основное меню, первая часть">
                <?php foreach ($left_items as $item) : ?>
                    <a class="header__link" href="<?php echo esc_url(baltic_nav_header_link_url((string) $item['url'])); ?>"><?php echo esc_html((string) $item['label']); ?></a>
                <?php endforeach; ?>
            </nav>

            <a class="header__logo-link" href="<?php echo esc_url($logo_href); ?>" aria-label="Балтика Brew — на главную">
                <img class="header__logo<?php echo esc_attr($logo_modifier); ?>" src="<?php echo esc_url(baltic_nav_header_logo_url()); ?>" alt="Балтика Brew">
            </a>

            <nav class="header__links header__links--right" aria-label="Основное меню, вторая часть">
                <?php foreach ($right_items as $item) : ?>
                    <a class="header__link" href="<?php echo esc_url(baltic_nav_header_link_url((string) $item['url'])); ?>"><?php echo esc_html((string) $item['label']); ?></a>
                <?php endforeach; ?>
            </nav>
        </div>

        <div class="header__nav-bar header__nav-bar--mobile">
            <a class="header__logo-link header__logo-link--mobile" href="<?php echo esc_url($logo_href); ?>" aria-label="Балтика Brew — на главную">
                <img class="header__logo header__logo--mobile<?php echo esc_attr($logo_modifier); ?>" src="<?php echo esc_url(baltic_nav_header_logo_url(true)); ?>" alt="Балтика Brew">
            </a>

            <?php if ($menu_items) : ?>
                <button class="header__burger" type="button" aria-label="Меню" aria-controls="site-mobile-menu" aria-expanded="false">
                    <img class="header__burger-icon" src="<?php echo esc_url(baltic_asset('images/header/menu-icon.svg')); ?>" alt="" aria-hidden="true">
                </button>
            <?php endif; ?>
        </div>
    </div>

    <?php if ($menu_items) : ?>
        <nav class="header__mobile-menu" id="site-mobile-menu" aria-label="Основное меню">
            <div class="container">
                <?php foreach ($menu_items as $item) : ?>
                    <a class="header__mobile-link" href="<?php echo esc_url(baltic_nav_header_link_url((string) $item['url'])); ?>"><?php echo esc_html((string) $item['label']); ?></a>
                <?php endforeach; ?>
            </div>
        </nav>
    <?php endif; ?>
</header>