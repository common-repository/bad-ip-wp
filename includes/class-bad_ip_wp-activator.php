<?php

/**
 * Fired during plugin activation
 *
 * @link       https://iridiumintel.com
 * @since      1.0.0
 *
 * @package    bad_ip_wp
 * @subpackage bad_ip_wp/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    bad_ip_wp
 * @subpackage bad_ip_wp/includes
 * @author     i--i <bad_ip@iridiumintel.com>
 */
class bad_ip_wp_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {

        global $wpdb;

        $token = sha1(mt_rand(10000,99999).time().'x0');

        $charset = $wpdb->get_charset_collate();
        $charset_collate = $wpdb->get_charset_collate();

        $table_reports = $wpdb->prefix . 'bad_ip_reports';
        $sql_reports = "CREATE TABLE IF NOT EXISTS $table_reports (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        ip varchar(20) NOT NULL,
        seen datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
        action tinytext NOT NULL,
        type tinytext NOT NULL,
        PRIMARY KEY  (id)
        ) $charset_collate;";

        $table_denied = $wpdb->prefix . 'bad_ip_denied';
        $sql_denied = "CREATE TABLE IF NOT EXISTS $table_denied (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        ip varchar(20) NOT NULL,
        seen datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
        type tinytext NOT NULL,
        PRIMARY KEY id (id),
        UNIQUE KEY ip (ip)
        ) $charset_collate;";

        $table_settings = $wpdb->prefix . 'bad_ip_settings';
        $sql_settings = "CREATE TABLE IF NOT EXISTS $table_settings (
        deny_access integer(9) NOT NULL,
        tor_block integer(9) NOT NULL,
        bad_queries integer(9) NOT NULL,
        login_incidents integer(9) NOT NULL,
        origin integer(9) NOT NULL,
        reporter integer(9) NOT NULL,
        token varchar(191) NULL,
        type integer(9) NULL,
        login_attempts integer(9) NULL,
        bot_access integer(9) NOT NULL,
        UNIQUE KEY type (type)
        ) $charset_collate;";

        $table_ip_whitelist = $wpdb->prefix . 'bad_ip_whitelist';
        $sql_ip_whitelist = "CREATE TABLE IF NOT EXISTS $table_ip_whitelist (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        ip varchar(20) NOT NULL,
        PRIMARY KEY id (id),
        UNIQUE KEY ip (ip)
        ) $charset_collate;";

        $table_ip_blacklist = $wpdb->prefix . 'bad_ip_blacklist';
        $sql_ip_blacklist = "CREATE TABLE IF NOT EXISTS $table_ip_blacklist (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        ip varchar(20) NOT NULL,
        PRIMARY KEY id (id),
        UNIQUE KEY ip (ip)
        ) $charset_collate;";

        $table_query_whitelist = $wpdb->prefix . 'bad_ip_query_whitelist';
        $sql_query_whitelist = "CREATE TABLE IF NOT EXISTS $table_query_whitelist (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        query varchar(255) NOT NULL,
        PRIMARY KEY id (id)
        ) $charset_collate;";
        

        $sql_init_settings = "INSERT INTO $table_settings 
        (deny_access, tor_block, bad_queries, login_incidents, origin, reporter, token, type, login_attempts, bot_access) 
        VALUES 
        (1,1,1,1,1,0,'$token',1,2,1);";

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

        dbDelta( $sql_reports );
        dbDelta( $sql_denied );
        dbDelta( $sql_settings );
        dbDelta( $sql_ip_whitelist );
        dbDelta( $sql_ip_blacklist );
        dbDelta( $sql_query_whitelist );

        dbDelta( $sql_init_settings );


        set_transient( 'bad_ip_activated', 1 );
	}

}
