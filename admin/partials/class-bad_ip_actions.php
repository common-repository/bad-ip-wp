<?php

class theActions {


    /**
     * @return mixed
     *
     * @since    1.0.0
     */
    function getUserIP() {
        // Get real visitor IP behind CloudFlare network
        if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
            $_SERVER['REMOTE_ADDR'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
            $_SERVER['HTTP_CLIENT_IP'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
        }
        isset($_SERVER['HTTP_CLIENT_IP']) ? $client  = $_SERVER['HTTP_CLIENT_IP'] : $client = null;
        isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $forward = $_SERVER['HTTP_X_FORWARDED_FOR'] : $forward = null;
        isset($_SERVER['REMOTE_ADDR']) ? $remote  = $_SERVER['REMOTE_ADDR'] : $remote = null;

        if(isset($client) && filter_var($client, FILTER_VALIDATE_IP)) {
            $ip = $client;
        }
        elseif(isset($forward) && filter_var($forward, FILTER_VALIDATE_IP)) {
            $ip = $forward;
        }
        else {
            $ip = $remote;
        }

        return $ip;
    }

    function checkOnline($domain) {
        $curlInit = curl_init($domain);
        curl_setopt($curlInit,CURLOPT_CONNECTTIMEOUT,10);
        curl_setopt($curlInit,CURLOPT_HEADER,true);
        curl_setopt($curlInit,CURLOPT_NOBODY,true);
        curl_setopt($curlInit,CURLOPT_RETURNTRANSFER,true);
     
        //get answer
        $response = curl_exec($curlInit);
     
        curl_close($curlInit);
        return $response ? true : false;
     }

     
    /**
     * checks if visitor is crawler bot
     */
    function is_bot(){
        if(isset($_SERVER['HTTP_USER_AGENT'])){
            return preg_match('/rambler|abacho|acoi|accona|aspseek|altavista|estyle|scrubby|lycos|geona|ia_archiver|alexa|sogou|skype|facebook|twitter|pinterest|linkedin|naver|bing|google|yahoo|duckduckgo|yandex|baidu|teoma|xing|java\/1.7.0_45|bot|crawl|slurp|spider|mediapartners|\sask\s|\saol\s/i', $_SERVER['HTTP_USER_AGENT']);
        }
        return false;
    }

    function getQueryWhitelist() {  // todo decouple and centralize ... refactor
        global $wpdb;
        // $queryListArr = @array_map('trim', unserialize(file_get_contents(BAD_IP_WP_DIR.'/query_whitelist.bin')));
        $table_query_whitelist = $wpdb->prefix . 'bad_ip_query_whitelist';
        $queryListArr = $wpdb->get_results("SELECT * FROM $table_query_whitelist; ");
        if (!isset($queryListArr)) {
            $queryListArr = [];
            return $queryListArr;
        } else {
            $queryListExit = [];
            foreach ($queryListArr as $query) {
                $queryListExit[] = $query->query;
            }
            return $queryListExit;
        }
        
    }

    function checkQuery($query) {
        $queryListArr = self::getQueryWhitelist();
        return true ? in_array($query, $queryListArr) : false;
    }

    function getIPWhitelist() {
        global $wpdb;
        $table_whitelist = $wpdb->prefix . 'bad_ip_whitelist';
        $ipListArr = $wpdb->get_results("SELECT * FROM $table_whitelist; ");
        if (!isset($ipListArr)) {
            $ipListArr = [];
            return $ipListArr;
        } else {
            $ipListExit = [];
            foreach ($ipListArr as $ip) {
                $ipListExit[] = $ip->ip;
            }
            return $ipListExit;
        }
    }

    function checkIPInWhiteList($ip) {
        $ipArr = self::getIPWhitelist();
        return true ? in_array($ip, $ipArr) : false;
    }

    function getIPBlacklist() {
        global $wpdb;
        $table_blacklist = $wpdb->prefix . 'bad_ip_blacklist';
        $ipListArr = $wpdb->get_results("SELECT * FROM $table_blacklist; ");
        if (!isset($ipListArr)) {
            $ipListArr = [];
            return $ipListArr;
        } else {
            $ipListExit = [];
            foreach ($ipListArr as $ip) {
                $ipListExit[] = $ip->ip;
            }
            return $ipListExit;
        }
    }

    function checkIPInBlacklist($ip) {
        $ipArr = self::getIPBlacklist();
        return true ? in_array($ip, $ipArr) : false;
    }
    

    /**
     * This function runs when WordPress completes its upgrade process
     * It iterates through each plugin updated to see if ours is included
     * @param $upgrader_object Array
     * @param $options Array
     */
    function bad_ip_upgrade_completed( $upgrader_object, $options ) {
        // The path to our plugin's main file
        $our_plugin = plugin_basename( __FILE__ );
        // If an update has taken place and the updated type is plugins and the plugins element exists
        if( $options['action'] == 'update' && $options['type'] == 'plugin' && isset( $options['plugins'] ) ) {
        // Iterate through the plugins being updated and check if ours is there
        foreach( $options['plugins'] as $plugin ) {
        if( $plugin == $our_plugin ) {
        // Set a transient to record that our plugin has just been updated
        // self::updateDB();
        set_transient( 'bad_ip_updated', 1 );
        }
        }
        }
    }

    /**
     * Show a notice to anyone who has just installed the plugin for the first time
     */
    function bad_ip_display_install_notice() {
        // Check the transient to see if we've just activated the plugin
        if( get_transient( 'bad_ip_activated' ) ) {
        echo '<div class="notice notice-success"><br/>' . __( 'Thanks for using bad_ip', 'bad_ip' ) . '<br/></div>';
        // Delete the transient so we don't keep displaying the activation message
        delete_transient( 'bad_ip_activated' );
        }
    }


   /**
     * Show a notice to anyone who has just updated plugin
     */
    function bad_ip_display_update_notice() {
        // Check the transient to see if we've just updated the plugin
        if( get_transient( 'bad_ip_updated' ) ) {
        echo '<div class="notice notice-success">' . __( 'bad_ip has been updated to '.$this->version, 'bad_ip' ) . '</div>';
        delete_transient( 'bad_ip_updated' );
        }
    }
}
    