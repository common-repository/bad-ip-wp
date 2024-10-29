<?php

/**
 *
 * @link              https://iridiumintel.com
 * @since             1.0.0
 * @package           bad_ip_wp
 *
 * @wordpress-plugin
 * Plugin Name:       bad_ip WP
 * Plugin URI:        https://bad-ip.iridiumintel.com
 * Description:       Protecting from malicious IP addresses visiting and trying to exploit your website with addition to block Tor endpoints
 * Version:           1.1.2
 * Author:            Iridium Intelligence
 * Author URI:        https://iridiumintel.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       bad_ip_wp
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Timber/Twig/View Library
 */
require_once plugin_dir_path( __FILE__ ) . 'lib/timber-library/timber.php';

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'BAD_IP_WP_VERSION', '1.1.2' );
define( 'BAD_IP_WP_NAME', trim(dirname(plugin_basename(__FILE__)), '/'));
define( 'BAD_IP_WP_URL', plugins_url( basename( plugin_dir_path(__FILE__) ), basename( __FILE__ ) ));
define( 'BAD_IP_WP_DIR', untrailingslashit( plugin_dir_path( __FILE__ ) ));
define( 'BAD_IP_WP_API_URL', 'https://bad-ip.iridiumintel.com');
define( 'BAD_IP_WP_JAIL_URL', 'https://bad-ip.iridiumintel.com/jail');

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-bad_ip_wp-activator.php
 */
function activate_bad_ip_wp() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-bad_ip_wp-activator.php';
	bad_ip_wp_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-bad_ip_wp-deactivator.php
 */
function deactivate_bad_ip_wp() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-bad_ip_wp-deactivator.php';
	bad_ip_wp_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_bad_ip_wp' );
register_deactivation_hook( __FILE__, 'deactivate_bad_ip_wp' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-bad_ip_wp.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_bad_ip_wp() {

	$plugin = new bad_ip_wp();
	$plugin->run();

}
run_bad_ip_wp();
