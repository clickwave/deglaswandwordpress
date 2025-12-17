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
 * * Localized language
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'local' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', 'root' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

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
define( 'AUTH_KEY',          'KRlWl bi M1J=<i,:TVr_0onWW_?K4mVi&a!GG9cI{VF04LNcDoz%S!(JSp_.8f3' );
define( 'SECURE_AUTH_KEY',   'o@UY=BvS1Gen)y:> dp5nu,geV{}VXV%EHy}t%j*T_nT}GNmj<3PZD1{m~Of-0KW' );
define( 'LOGGED_IN_KEY',     'm8twT3-83z9_]%qNlX@V-1X?]6dC#25ER+|0:;B>b4X$mw^z,2D~^C!*ssu_R? u' );
define( 'NONCE_KEY',         'C8Q}NVoV]>r*yJ[uhOO x2vq3r2WYOqr<Fc,}8mOQ=3Wck4IhA,QP%DRNzfPJY3Q' );
define( 'AUTH_SALT',         'vjr{IW0[1.`+?Rr%{BzN)rAPq/lJ`?gq$w-Q}-<e4*9R%vL[M%?STk-!!hb_r7R!' );
define( 'SECURE_AUTH_SALT',  '&)ZwnZ[MKo##e@CoW#5}4m1G&q.LYswm0o{,aGn#^aDPt|nB90LWye:w:w$kVRt|' );
define( 'LOGGED_IN_SALT',    'i58heAo|M3e>:S]nIl6CkeoU<kLaQ8,UL[uTwC*HEx`7FmHrmV%ZG|kt.{kd^:wN' );
define( 'NONCE_SALT',        '=~El}fDfrbx,Murhs$::K`];%E.,k1X.*4p@9MTw.1%&#3>;@c]vM3nI=H`Djy,B' );
define( 'WP_CACHE_KEY_SALT', '|RjKTS+ UUVvK[hn[7XDmTC[Q)^1h+DLV][rCM?1V3wYAGV6t9n<?o{mNxLC`D:/' );


/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';


/* Add any custom values between this line and the "stop editing" line. */



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
if ( ! defined( 'WP_DEBUG' ) ) {
	define( 'WP_DEBUG', true );
}
define( 'WP_DEBUG_LOG', true );
define( 'WP_DEBUG_DISPLAY', false );
@ini_set( 'display_errors', 0 );

define( 'WP_ENVIRONMENT_TYPE', 'local' );
/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
