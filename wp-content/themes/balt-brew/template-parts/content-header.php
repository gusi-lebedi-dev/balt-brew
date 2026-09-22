<?php
/**
 * Header for news and article pages.
 */

if (!defined('ABSPATH')) {
    exit;
}
?>
<header class="content-header">
    <a class="content-header__logo-link" href="<?php echo esc_url(home_url('/')); ?>" aria-label="Балтика Brew — на главную">
        <img class="content-header__logo" src="<?php echo baltic_option_asset('header_logo', 'images/header/logo.png'); ?>" alt="Балтика Brew">
    </a>
</header>
