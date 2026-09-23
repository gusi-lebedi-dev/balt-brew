<?php
/**
 * WordPress configuration for Baltika Brew.
 * Runtime settings are supplied by the untracked .env.php file.
 */

$config_file = __DIR__ . '/.env.php';
if ( ! is_file( $config_file ) ) {
	throw new RuntimeException( 'Missing .env.php configuration file.' );
}

$secrets = require $config_file;
if ( ! is_array( $secrets ) ) {
	throw new RuntimeException( '.env.php must return an array.' );
}

$required_keys = array( 'db-name', 'db-user', 'db-pass', 'db-host' );
foreach ( $required_keys as $required_key ) {
	if ( ! array_key_exists( $required_key, $secrets ) || '' === (string) $secrets[ $required_key ] ) {
		throw new RuntimeException( sprintf( 'Missing required .env.php key: %s', $required_key ) );
	}
}

$home_url = rtrim( (string) ( $secrets['home-url'] ?? '' ), '/' );
if ( '' !== $home_url ) {
	define( 'WP_HOME', $home_url );
	define( 'WP_SITEURL', $home_url );
}

$forwarded_proto = strtolower( (string) ( $_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '' ) );
if ( ! empty( $secrets['ssl'] ) || 'https' === $forwarded_proto ) {
	$_SERVER['HTTPS'] = 'on';
}

define( 'DB_NAME', (string) $secrets['db-name'] );
define( 'DB_USER', (string) $secrets['db-user'] );
define( 'DB_PASSWORD', (string) $secrets['db-pass'] );
define( 'DB_HOST', (string) $secrets['db-host'] );
define( 'DB_CHARSET', 'utf8mb4' );
define( 'DB_COLLATE', '' );

$salt_seed = (string) ( $secrets['salt'] ?? 'replace-this-development-salt' );
foreach ( array( 'AUTH_KEY', 'SECURE_AUTH_KEY', 'LOGGED_IN_KEY', 'NONCE_KEY', 'AUTH_SALT', 'SECURE_AUTH_SALT', 'LOGGED_IN_SALT', 'NONCE_SALT' ) as $salt_name ) {
	define( $salt_name, hash( 'sha512', $salt_seed . ':' . $salt_name ) );
}

$table_prefix = 'wp_';

define( 'WP_DEBUG', (bool) ( $secrets['debug'] ?? false ) );
define( 'WP_DEBUG_DISPLAY', false );
define( 'WP_DEBUG_LOG', WP_DEBUG );
define( 'DISALLOW_FILE_EDIT', true );
define( 'WP_DEFAULT_THEME', 'balt-brew' );
define( 'FS_CHMOD_DIR', 0755 );
define( 'FS_CHMOD_FILE', 0644 );

if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

require_once ABSPATH . 'wp-settings.php';
