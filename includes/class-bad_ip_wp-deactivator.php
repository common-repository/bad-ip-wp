<?php

/**
 * Fired during plugin deactivation
 *
 * @link       https://iridiumintel.com
 * @since      1.0.0
 *
 * @package    bad_ip_wp
 * @subpackage bad_ip_wp/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    bad_ip_wp
 * @subpackage bad_ip_wp/includes
 * @author     i--i <bad_ip@iridiumintel.com>
 */
class bad_ip_wp_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
        global $wpdb;
        $table_denied = $wpdb->prefix . 'bad_ip_denied';
        $table_reports = $wpdb->prefix . 'bad_ip_reports';
        $table_settings = $wpdb->prefix . 'bad_ip_settings';
        $table_ip_whitelist = $wpdb->prefix . 'bad_ip_whitelist';
        $table_ip_blacklist = $wpdb->prefix . 'bad_ip_blacklist';
        $table_query_whitelist = $wpdb->prefix . 'bad_ip_query_whitelist';
        $sql_denied = "DROP TABLE IF EXISTS $table_denied";
        $sql_reports = "DROP TABLE IF EXISTS $table_reports";
        $sql_settings = "DROP TABLE IF EXISTS $table_settings";
        $sql_ip_whitelist = "DROP TABLE IF EXISTS $table_ip_whitelist";
        $sql_ip_blacklist = "DROP TABLE IF EXISTS $table_ip_blacklist";
        $sql_query_whitelist = "DROP TABLE IF EXISTS $table_query_whitelist";

        $wpdb->query($sql_denied);
        $wpdb->query($sql_reports);
        $wpdb->query($sql_settings);
        $wpdb->query($sql_ip_whitelist);
        $wpdb->query($sql_ip_blacklist);
        $wpdb->query($sql_query_whitelist);

	}

}
