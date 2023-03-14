<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', $_ENV['WP_DB_NAME']);

/** MySQL database username */
define( 'DB_USER', $_ENV['WP_DB_USER']);

/** MySQL database password */
define( 'DB_PASSWORD', $_ENV['WP_DB_PASSWORD']);

/** MySQL hostname */
define( 'DB_HOST', $_ENV['WP_DB_HOST'] .':3306');

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

define( 'AS3CF_SETTINGS', serialize( array(
	'provider' => 'aws',
	'access-key-id' => $_ENV['WP_S3_ACCESS_KEY'],
	'secret-access-key' => $_ENV['WP_S3_SECRET_KEY'],
	'bucket' => $_ENV['WP_S3_BUCKET'],
	'delivery-domain' => 'files.adventistas.org'
) ) );

define( 'FORCE_SSL', true );
define( 'FORCE_SSL_ADMIN',true );
$_SERVER['HTTPS']='on';	

/** Ajustes adventistas.org */
define( 'DISALLOW_FILE_EDIT', true );
define( 'WP_AUTO_UPDATE_CORE', false );
define( 'SITE', 'maranata' );
define('PA_LANG', true);

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'QtWdw>S`Pq|W2X+eA^A.*ib&B!Zw<5aWkL-mL>-YA#r#m<v[fo[9Zy81~A[?$D#6' );
define( 'SECURE_AUTH_KEY',  'O,SbCYjI6Ih4oTr91 iAfS> eW|tOC pB2*Ft<]]@HXE3M[V4Y`JT/%?qh-3iWtu' );
define( 'LOGGED_IN_KEY',    '**PNA{VB_o!b+;b%i%VX:RfCmSQiR~quP-sH2~1C>E/B:/(@|7)rF}d9t%bxwi0a' );
define( 'NONCE_KEY',        'w61Z3%Hn(Ci=zbWPJ&])B<iVdA$x=yBRYIwY%vu;Zj<M6uI[-Db]q=ZF?[:k<Y21' );
define( 'AUTH_SALT',        'f0>d@EzG`i[-otw{8{w&1ze5fN)$c:xVVUyNEAw|,~+T)gHaePg.Vi7D0Ez?||?E' );
define( 'SECURE_AUTH_SALT', '!D%16dv*lAWIcLUkp[_}V|7:^DodX_b<F`$.m6.WWiV!G08<WGOy]^L&4VvrwlTx' );
define( 'LOGGED_IN_SALT',   '*:x--KCdJ}Nyge_OF3IZTWb9fytX8Kj.6MEJ}G!Ifc2[:vj494<c27eZdR=D>AcL' );
define( 'NONCE_SALT',       '{j$=:@ 3yNL{H%Y6WWyWzBPS<^dr+R4 F4I xZ&6A9@Z=6mT-X?[sl6Ck!lh.tlA' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
