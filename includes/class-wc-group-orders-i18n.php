<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://trevan.co
 * @since      1.0.0
 *
 * @package    Wc_Group_Orders
 * @subpackage Wc_Group_Orders/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Wc_Group_Orders
 * @subpackage Wc_Group_Orders/includes
 * @author     Trevan Hetzel <trevan@hetzelcreative.com>
 */
class Wc_Group_Orders_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'wc-group-orders',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
