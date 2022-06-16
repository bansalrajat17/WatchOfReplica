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
define('DB_NAME', 'watchofr_wp402');

/** MySQL database username */
define('DB_USER', 'watchofr_wp402');

/** MySQL database password */
define('DB_PASSWORD', '8]80S-Arp7');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         '3hnxt7rkaneu5mnjknc7mybe6sh2e7gsajckqkp2zjpmrzsnm1bgzeg7ybqqmetm');
define('SECURE_AUTH_KEY',  'afrf0xveblmp6h03jhdswz7rgkosqviy66db0rdseltgjtgqyr0nhpzwhq0nuird');
define('LOGGED_IN_KEY',    'zku3fx6odytudrcdjatmzhbxueziatogurauqh3grkyylm3566acqm61f4wsp2tm');
define('NONCE_KEY',        'qfc7zgfls0aezmvpbfn9arytx5apxedrbggzeqxayyj1xvc5uaarhfpt3bolpto0');
define('AUTH_SALT',        'cqakp14d56lcuhycoobzyfjfhhtutjgec65iusjdgfgfgolvwbjchkc3qxeijwtu');
define('SECURE_AUTH_SALT', 'cqddrpbbvh4l6cxzxnz3zvpk8phpwjljr802j1tvckqfglybbqokiqxpstyqu3iw');
define('LOGGED_IN_SALT',   'c5fq8ywtscrhawjknlvf4fgsedb1hgwd7xed53y0yqp8bevxoit15xmxrigipok0');
define('NONCE_SALT',       'gjkqs3ah3fxokhojow0sgvgtvqenvsh0zwtztg5eionwe41vrwtcyzh1gakpaqhu');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wpo5_';

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
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
