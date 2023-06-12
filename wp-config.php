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
 * @link https://wordpress.org/documentation/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'cinema_db' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

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
define( 'AUTH_KEY',         'yOs6I8|RKuS-Au?a2;Et+@,PCOOS0IL[6%}3s4Lv{&gnftL 27$!ctrwl/xH!J)f' );
define( 'SECURE_AUTH_KEY',  'uG}C2RfcdhCp(hTug_6AP~_%By;Ux/67=;@h^Q,eey5.C?3~i iFXV{TL5=f6`[9' );
define( 'LOGGED_IN_KEY',    'MNs^?IftI_n>0DzEg>24LN!uO(y))nU4m]W`~o9)+CK:Fho!b`u3V^m~bwgE#E8]' );
define( 'NONCE_KEY',        'jYq$,E/,gfZ4pUqCUF `Wr;|Sda/=h_5sV#s6a!)topDg:} ?4M 0:/N:_RCw} Y' );
define( 'AUTH_SALT',        '^ks+0#pP&NzYR#6*#F W1(vP%_EroDcUV=/k<L_4tuA:<d+&E[n9(zg$HT5B;/%#' );
define( 'SECURE_AUTH_SALT', 'YtRBo!(Z18$pr#u&=o>xwjN+O]db+`j_?#}_PcFZH1ga/7A(n&RyB>Ae#Bixbdb`' );
define( 'LOGGED_IN_SALT',   'L0lRChrv2`QeVaB+]r5]b5U2GB@*#@gI@8Y(zpU0t9qo0DnL2do@=56y,C`2a7jp' );
define( 'NONCE_SALT',       ']^4,5Z4C)PwvHu5bSm{)NSloj2j:RG-/> RF8~Jlr^6LAr=Q`Mr/#hT+cR_()`M<' );

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
 * @link https://wordpress.org/documentation/article/debugging-in-wordpress/
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
