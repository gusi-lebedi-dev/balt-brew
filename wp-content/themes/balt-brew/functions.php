<?php
/**
 * Theme bootstrap and static route configuration.
 *
 * @package Balt_Brew
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function balt_brew_asset( string $path ): string {
	return get_theme_file_uri( ltrim( $path, '/' ) );
}

function balt_brew_view(): string {
	return (string) get_query_var( 'balt_brew_view', '' );
}

function balt_brew_is_front_view(): bool {
	return ! in_array( balt_brew_view(), array( 'news', 'age-gate' ), true );
}

function balt_brew_news_url(): string {
	return home_url( '/news/' );
}

function balt_brew_setup(): void {
	add_theme_support( 'title-tag' );
	add_theme_support( 'html5', array( 'style', 'script', 'gallery', 'caption' ) );
}
add_action( 'after_setup_theme', 'balt_brew_setup' );

function balt_brew_asset_version( string $path ): string {
	$file = get_theme_file_path( $path );
	return is_file( $file ) ? (string) filemtime( $file ) : '1.0.0';
}

function balt_brew_enqueue_assets(): void {
	$is_age_gate = 'age-gate' === balt_brew_view();

	wp_enqueue_script(
		'balt-brew-age-check',
		balt_brew_asset( 'age-check.js' ),
		array(),
		balt_brew_asset_version( 'age-check.js' ),
		false
	);

	wp_add_inline_script(
		'balt-brew-age-check',
		'window.BaltBrewConfig = ' . wp_json_encode(
			array(
				'homeUrl'     => home_url( '/' ),
				'newsUrl'     => balt_brew_news_url(),
				'ageGateUrl'  => home_url( '/age-gate/' ),
				'assetsBase'  => trailingslashit( get_template_directory_uri() ),
				'isAgeGate'   => $is_age_gate,
			)
		) . ';',
		'before'
	);

	if ( $is_age_gate ) {
		wp_enqueue_style( 'balt-brew-age-gate', balt_brew_asset( 'age-gate.css' ), array(), balt_brew_asset_version( 'age-gate.css' ) );
		wp_enqueue_script( 'balt-brew-age-gate-page', balt_brew_asset( 'age-gate-page.js' ), array( 'balt-brew-age-check' ), balt_brew_asset_version( 'age-gate-page.js' ), true );
		return;
	}

	$styles = array(
		'balt-brew-main'              => 'styles.css',
		'balt-brew-mobile'            => 'styles-mobile.css',
		'balt-brew-product-animation' => 'product-animation.css',
		'balt-brew-loader'            => 'loader.css',
		'balt-brew-pages-fixes'       => 'pages-fixes.css',
	);
	$dependencies = array();
	foreach ( $styles as $handle => $path ) {
		wp_enqueue_style( $handle, balt_brew_asset( $path ), $dependencies, balt_brew_asset_version( $path ) );
		$dependencies = array( $handle );
	}

	wp_enqueue_script( 'balt-brew-main', balt_brew_asset( 'script.js' ), array(), balt_brew_asset_version( 'script.js' ), array( 'in_footer' => true, 'strategy' => 'defer' ) );
	if ( balt_brew_is_front_view() ) {
		wp_enqueue_script( 'balt-brew-product-animation', balt_brew_asset( 'product-animation.js' ), array( 'balt-brew-main' ), balt_brew_asset_version( 'product-animation.js' ), array( 'in_footer' => true, 'strategy' => 'defer' ) );
	}
}
add_action( 'wp_enqueue_scripts', 'balt_brew_enqueue_assets' );

function balt_brew_preload_fonts(): void {
	foreach ( array( 'fonts/Inter-Regular.woff2', 'fonts/tt-backwardssans-regular.woff2', 'fonts/TTTricks-Regular.woff2' ) as $font ) {
		printf( '<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>%s', esc_url( balt_brew_asset( $font ) ), "\n" );
	}
}
add_action( 'wp_head', 'balt_brew_preload_fonts', 2 );

function balt_brew_register_routes(): void {
	add_rewrite_rule( '^news/?$', 'index.php?balt_brew_view=news', 'top' );
	add_rewrite_rule( '^age-gate/?$', 'index.php?balt_brew_view=age-gate', 'top' );

	if ( '1' !== get_option( 'balt_brew_routes_version' ) ) {
		flush_rewrite_rules( false );
		update_option( 'balt_brew_routes_version', '1', false );
	}
}
add_action( 'init', 'balt_brew_register_routes' );

/**
 * Keep the two static routes available before pretty permalinks are enabled.
 * A fresh WordPress installation uses the plain permalink structure.
 */
function balt_brew_detect_static_route( WP $wp ): void {
	$route = trim( (string) $wp->request, '/' );
	if ( in_array( $route, array( 'news', 'age-gate' ), true ) ) {
		$wp->query_vars['balt_brew_view'] = $route;
	}
}
add_action( 'parse_request', 'balt_brew_detect_static_route' );

function balt_brew_query_vars( array $vars ): array {
	$vars[] = 'balt_brew_view';
	return $vars;
}
add_filter( 'query_vars', 'balt_brew_query_vars' );

function balt_brew_template( string $template ): string {
	$view = balt_brew_view();
	if ( 'news' === $view ) {
		return get_theme_file_path( 'page-news.php' );
	}
	if ( 'age-gate' === $view ) {
		return get_theme_file_path( 'age-gate.php' );
	}
	return $template;
}
add_filter( 'template_include', 'balt_brew_template' );

function balt_brew_route_status(): void {
	if ( in_array( balt_brew_view(), array( 'news', 'age-gate' ), true ) ) {
		global $wp_query;
		$wp_query->is_404 = false;
		status_header( 200 );
	}
}
add_action( 'template_redirect', 'balt_brew_route_status' );

function balt_brew_body_classes( array $classes ): array {
	if ( 'news' === balt_brew_view() ) {
		$classes[] = 'news-page';
	}
	return $classes;
}
add_filter( 'body_class', 'balt_brew_body_classes' );

function balt_brew_flush_routes(): void {
	balt_brew_register_routes();
	flush_rewrite_rules();
}
add_action( 'after_switch_theme', 'balt_brew_flush_routes' );
