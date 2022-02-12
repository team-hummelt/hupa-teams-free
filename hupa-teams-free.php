<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://wwdh.de
 * @since             1.0.0
 * @package           Hupa_Teams
 *
 * @wordpress-plugin
 * Plugin Name:       WP  Team Members FREE
 * Plugin URI:        https://www.hummelt-werbeagentur.de/
 * Description:       Team Plugin für WordPress
 * Version:           1.0.0
 * Author:            Jens Wiecker
 * Author URI:        https://wwdh.de
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
$plugin_data = get_file_data(dirname(__FILE__) . '/hupa-teams-free.php', array('Version' => 'Version'), false);
define("HUPA_TEAMS_FREE_VERSION", $plugin_data['Version']);
/**
 * Currently DATABASE VERSION
 * @since             1.0.0
 */


const HUPA_TEAMS_FREE_DB_VERSION = '1.0.0';


/**
 * MIN PHP VERSION for Activate
 * @since             1.0.0
 */
const HUPA_TEAMS_FREE_PHP_VERSION = '7.4';

/**
 * MIN WordPress VERSION for Activate
 * @since             1.0.0
 */
const HUPA_TEAMS_FREE_WP_VERSION = '5.6';

/**
 * PLUGIN SLUG
 * @since             1.0.0
 */
define('HUPA_TEAMS_FREE_SLUG_PATH', plugin_basename(__FILE__));

/**
 * PLUGIN BASENAME
 * @since             1.0.0
 */
define('HUPA_TEAMS_FREE_BASENAME', plugin_basename(__DIR__));

/**
 * PLUGIN DIR
 * @since             1.0.0
 */
define('HUPA_TEAMS_FREE_DIR', dirname(__FILE__). DIRECTORY_SEPARATOR );

/**
 * PLUGIN ADMIN DIR
 * @since             1.0.0
 */
const HUPA_TEAMS_FREE_ADMIN_DIR = HUPA_TEAMS_FREE_DIR . 'admin' . DIRECTORY_SEPARATOR;

/**
 * PLUGIN Gutenberg Build DIR
 * @since             1.0.0
 */
const HUPA_TEAMS_FREE_SIDEBAR_BUILD_DIR = HUPA_TEAMS_FREE_ADMIN_DIR . 'gutenberg-sidebar' . DIRECTORY_SEPARATOR . 'sidebar-react' . DIRECTORY_SEPARATOR . 'build' . DIRECTORY_SEPARATOR;
const HUPA_TEAMS_FREE_BLOCK_BUILD_DIR = HUPA_TEAMS_FREE_ADMIN_DIR . 'gutenberg-block' . DIRECTORY_SEPARATOR  . 'build' . DIRECTORY_SEPARATOR;
const HUPA_TEAMS_FREE_GUTENBERG_LANGUAGE = HUPA_TEAMS_FREE_DIR . 'languages';

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-hupa-teams-activator.php
 */
function activate_hupa_teams_free() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-hupa-teams-free-activator.php';
	Hupa_Teams_Free_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-hupa-teams-deactivator.php
 */
function deactivate_hupa_teams_free() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-hupa-teams-free-deactivator.php';
	Hupa_Teams_Free_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_hupa_teams_free' );
register_deactivation_hook( __FILE__, 'deactivate_hupa_teams_free' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-hupa-teams_free.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */

global $hupa_team_members;
$hupa_team_members = new Hupa_Teams_Free();
$hupa_team_members->run();
