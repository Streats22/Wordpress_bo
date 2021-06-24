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
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'wordpress' );

/** MySQL database username */
define( 'DB_USER', 'root' );

/** MySQL database password */
define( 'DB_PASSWORD', 'root' );

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
define( 'AUTH_KEY',         '3fbK,@9cq`dc4Ed!u65q8=gxldX`hg6.[b;fJKT7:T @nb6HUN7W:@>/,tgQnjcH' );
define( 'SECURE_AUTH_KEY',  '142/nRi#jopiu1->SW2-g@Qa3uw* xbSa!wFTs1t[D6Cm*MEf&Rm-ycnb6|;T^+H' );
define( 'LOGGED_IN_KEY',    'jC$2}zfVsdCnna%GZnQvE5M3KcX@1y$pVCRv*-N`|7L=n(B^N^3)1M3XP%C~YP`S' );
define( 'NONCE_KEY',        '~54(phif:u#C *lEm0H{)L4y7^=_K$:K4E`+hN-3uw`(P}oN.g,!>,M8t&E:AX2V' );
define( 'AUTH_SALT',        '[igKSp-af~vj@^uo*Qo<Zq[MXwTL(M&g-J=!KWU#4RZV:uQ[*jwf<@IY./Tt{8/=' );
define( 'SECURE_AUTH_SALT', '!1g:<M.uF+oVCW^}:DnliXVe50t?~7a|x3mD[[s$Y#[BDO4<S-B];4sA7dF+?5E~' );
define( 'LOGGED_IN_SALT',   '9V8UvwF/nstCG%.Ss<&}NM;tg:KEOj!u.uuWPsKJo#21EW[,|$mUVAc#q.kCC<.S' );
define( 'NONCE_SALT',       'uvm=4rTO,X@A4-ya+278n;r-#r<Qpn$D[*%t11V&Z!^A{Bx>2EBWthFNJp)l_ysW' );

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
 * visit the documentation.
 *
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
