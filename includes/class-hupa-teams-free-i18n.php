<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://wwdh.de
 * @since      1.0.0
 *
 * @package    Hupa_Teams
 * @subpackage Hupa_Teams/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Hupa_Teams
 * @subpackage Hupa_Teams/includes
 * @author     Jens Wiecker <email@jenswiecker.de>
 */
class Hupa_Teams_Free_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'hupa-teams',
			false,
			dirname(plugin_basename(__FILE__), 2) . '/languages/'
		);

	}



}
