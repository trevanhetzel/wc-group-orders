<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://trevan.co
 * @since             1.0.0
 * @package           Wc_Group_Orders
 *
 * @wordpress-plugin
 * Plugin Name:       Group Orders for WooCommerce
 * Plugin URI:        https://github.com/trevanhetzel/wc-group-orders
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            Trevan Hetzel
 * Author URI:        https://trevan.co
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wc-group-orders
 * Domain Path:       /languages
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
define( 'WC_GROUP_ORDERS_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-wc-group-orders-activator.php
 */
function activate_wc_group_orders() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wc-group-orders-activator.php';
	Wc_Group_Orders_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-wc-group-orders-deactivator.php
 */
function deactivate_wc_group_orders() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wc-group-orders-deactivator.php';
	Wc_Group_Orders_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_wc_group_orders' );
register_deactivation_hook( __FILE__, 'deactivate_wc_group_orders' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-wc-group-orders.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_wc_group_orders() {

	$plugin = new Wc_Group_Orders();
	$plugin->run();

}

/**
 * Check if WooCommerce is active
 **/
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
	run_wc_group_orders();
}
