<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'steelNiron' );

/** MySQL database username */
define( 'DB_USER', 'root' );

/** MySQL database password */
define( 'DB_PASSWORD', '' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'Kkvpd&a{PHsgX_Zk%GfQ uW<sDkq>p=c`I<mdo{bB-S45^ %iU8>S+q)oB|q=W/u' );
define( 'SECURE_AUTH_KEY',  'fgjpK{uiP;lK5;l<+]xd*Q`m|avXl%}(;5|;O%Vyx&Ym2zOE@fJQs_yE<hRxz)g*' );
define( 'LOGGED_IN_KEY',    '^?iOttJp ehfXO6:e=7I;BqIn)!0L38-uKx*GR/ana;N)L)T[+ajS0F7L-:*m5s|' );
define( 'NONCE_KEY',        'JH;RuwM^iO2;DdGe`?-i%cMc- ZfoKui9{A`]G(>=_F,,Hqat$r)]/2yWlP+Kpke' );
define( 'AUTH_SALT',        'pZ]=g]ZO-u2/&8Ii!wm3PhN?BiLs{&oi9LHn,|u^cg>=!) J@Hp#BB>&jg~#TGSx' );
define( 'SECURE_AUTH_SALT', 'im[;_)D|C]8$8pfGSrk_nFQrC+R}X4!QKR4w2VZ-BW|}=We.U55S/R/lW6UaHwry' );
define( 'LOGGED_IN_SALT',   ';k[FXSu}:hMf~rvj=_x@7SPcP;lYYDE:#FW<A7ZnNp3w94Z>O3r<!F}bd rY7j-?' );
define( 'NONCE_SALT',       ',9d!vx&W.TJ^jPw(c_T]brw]6Y&!P9_%^Ayv8y>klaO#8!$; =-Pu.3INmV-5xL0' );

/**#@-*/

/**
 * WordPress Database Table prefix.
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
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define( 'WP_DEBUG', false );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}
@ini_set('upload_max_size' , '256M' );
/** Sets up WordPress vars and included files. */
require_once( ABSPATH . 'wp-settings.php' );
