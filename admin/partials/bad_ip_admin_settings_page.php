<?php

function bad_ip_settings_page() {
    global $wpdb;

    $table_settings = $wpdb->prefix . 'bad_ip_settings';
    $table_ip_whitelist = $wpdb->prefix . 'bad_ip_whitelist';
    $table_ip_blacklist = $wpdb->prefix . 'bad_ip_blacklist';
    $bad_ipSettings = $wpdb->get_results("SELECT * FROM $table_settings ");
    $_uid = get_current_user_id();


    if ( ! empty( $_POST ) ) {

        !is_null(@$_POST['deny_access']) ? $denyStat = sanitize_text_field($_POST['deny_access']) : $denyStat = null;
        !is_null(@$_POST['tor_block']) ? $torStat = sanitize_text_field($_POST['tor_block']) : $torStat = null;
        !is_null(@$_POST['bad_queries']) ? $bad_queryStat = sanitize_text_field($_POST['bad_queries']) : $bad_queryStat = null;
        !is_null(@$_POST['login_incidents']) ? $loginStat = sanitize_text_field($_POST['login_incidents']) : $loginStat = null;
        !is_null(@$_POST['login_attempts']) ? $loginAttemptsStat = sanitize_text_field($_POST['login_attempts']) : $loginAttemptsStat = null;
        !is_null(@$_POST['origin']) ? $originStat = sanitize_text_field($_POST['origin']) : $originStat = null;
        !is_null(@$_POST['token']) ? $token = sanitize_text_field($_POST['token']) : $token = null;
        !is_null(@$_POST['whiteListText']) ? $whiteListText = sanitize_text_field($_POST['whiteListText']) : $whiteListText = null;
        !is_null(@$_POST['blackListText']) ? $blackListText = sanitize_text_field($_POST['blackListText']) : $blackListText = null;
        !is_null(@$_POST['bot_access']) ? $bot_accessStat = sanitize_text_field($_POST['bot_access']) : $bot_accessStat = null;
        !is_null(@$_POST['action']) ? $_action = sanitize_text_field($_POST['action']) : $_action = null;

        if (isset($_action)) {
            $_action === 'upgrade' ? $context['notifications'][] = updateDB() : null;
        }

        if (!is_null($whiteListText)) { 
            $array = preg_split('/[\s]+/', $whiteListText );
            $theList = getIPWhitelist();
            $diff = array_merge(array_diff($array,$theList),array_diff($theList,$array));
            if (!empty($array) && !empty($diff)) { //todo
                !empty($theList) ? $wpdb->query("TRUNCATE TABLE $table_ip_whitelist") : null;
                foreach($array as $k => &$val) { 
                    if(filter_var($val, FILTER_VALIDATE_IP)) { 
                        $exitArr['ip'] = $val;
                        $wpdb->insert( $table_ip_whitelist, $exitArr);
                    }
                } 
                // file_put_contents(BAD_IP_WP_DIR.'/whitelist.bin', serialize($array));
            }
           
            
        }
        
        if (!is_null($blackListText)) { 
            $array = preg_split('/[\s]+/', $blackListText );
            $theList = getIPBlacklist();
            $diff = array_merge(array_diff($array,$theList),array_diff($theList,$array));
            if (!empty($array) && !empty($diff)) { //todo
                !empty($theList) ? $wpdb->query("TRUNCATE TABLE $table_ip_blacklist") : null;
                foreach($array as $k => &$val) { 
                    if(filter_var($val, FILTER_VALIDATE_IP)) { 
                        $exitArr['ip'] = $val;
                        $wpdb->insert( $table_ip_blacklist, $exitArr);
                    }
                } 
                // file_put_contents(BAD_IP_WP_DIR.'/blacklist.bin', serialize($array));
            }
        }


        if (!is_null($denyStat)) {
            $denyStat == '0' ? $bad_ipSettingsNew['deny_access'] = 1 : $bad_ipSettingsNew['deny_access'] = 0;
        }
        if (!is_null($torStat)) {
            $torStat == '0' ? $bad_ipSettingsNew['tor_block'] = 1 : $bad_ipSettingsNew['tor_block'] = 0;
        }
        if (!is_null($bad_queryStat)) {
            $bad_queryStat == '0' ? $bad_ipSettingsNew['bad_queries'] = 1 : $bad_ipSettingsNew['bad_queries'] = 0;
        }
        if (!is_null($loginStat)) {
            $loginStat == '0' ? $bad_ipSettingsNew['login_incidents'] = 1 : $bad_ipSettingsNew['login_incidents'] = 0;
        }
        if (!is_null($originStat)) {
            $originStat == '0' ? $bad_ipSettingsNew['origin'] = 1 : $bad_ipSettingsNew['origin'] = 0;
        }
        if (!is_null($loginAttemptsStat)) {
            $loginAttemptsStat != $bad_ipSettings[0]->login_attempts ? $bad_ipSettingsNew['login_attempts'] = $loginAttemptsStat : null;
        }
        if (!is_null($bot_accessStat)) {
            $bot_accessStat == '0' ? $bad_ipSettingsNew['bot_access'] = 1 : $bad_ipSettingsNew['bot_access'] = 0;
        }

        $token ? $bad_ipSettingsNewx['token'] = $token : null;

        if (!empty($bad_ipSettingsNew)) {
            $where = [ 'type' => 1 ];
            $wpdb->update($table_settings, $bad_ipSettingsNew, $where);

            $bad_ipSettings = $wpdb->get_results("SELECT * FROM $table_settings ");
        }


    }

    $context = Timber::get_context();

    $context['settings'] = $bad_ipSettings[0];
    // $whiteListArr = @array_map('trim', unserialize(file_get_contents(BAD_IP_WP_DIR.'/whitelist.bin')));
    $whiteListArr = getIPWhitelist();
    // $blackListArr = @array_map('trim', unserialize(file_get_contents(BAD_IP_WP_DIR.'/blacklist.bin')));
    $blackListArr = getIPBlacklist();
    $context['whiteList'] = @implode("\n", $whiteListArr);
    $context['blackList'] = @implode("\n", $blackListArr);

    if (needUpgrade()) {  //notifcation about needed upgrade
        $context['needsupgrade'] = true;
    }

    $context['bad_ip_name'] = BAD_IP_WP_NAME;
    $context['bad_ip_url'] = BAD_IP_WP_URL;
    $context['bad_ip_dir'] = BAD_IP_WP_DIR;

    //            var_dump($bad_ipSettings[0]);


    Timber::render( array( 'page-settings.twig'), $context);
}