<?php

global $wp_query;
        global $wpdb;

        $table_settings = $wpdb->prefix . 'bad_ip_settings';
        $bad_ipSettings = $wpdb->get_results("SELECT * FROM $table_settings ");
        isset($bad_ipSettings) && !empty($bad_ipSettings) ? $bad_ipSettings = $bad_ipSettings[0] : $bad_ipSettings = null;

        // $whiteListArr = @array_map('trim', unserialize(file_get_contents(BAD_IP_WP_DIR.'/whitelist.bin')));
        // if (is_null($whiteListArr)){
        //     $whiteListArr = [];
        // }

        // $queryListArr = getQueryWhitelist();
        // if (is_null($queryListArr)){
        //     $queryListArr = [];
        // }

        $isBot = self::is_bot();

        if (!is_null($bad_ipSettings) && !empty($bad_ipSettings) && $bad_ipSettings->bad_queries == '1') {

            $_now = date("Y-m-d H:i:s");
            $user_ip = self::getUserIP();
            $bad_ipSettings->login_incidents == '1' ? $origin = get_site_url() : $origin = 'anonymous';
            $reporter = get_site_url();
            isset($_SERVER['REQUEST_URI']) ? $_action = $_SERVER['REQUEST_URI'] : $_action = null;

            //        echo 'QUERY_STRING ', $_SERVER['QUERY_STRING'], PHP_EOL;
            isset($_SERVER['QUERY_STRING']) ? $QS = $_SERVER['QUERY_STRING'] : $QS = null;
            //        echo var_dump($wp_query->query_vars);
            //        echo $_SERVER['QUERY_STRING'];
            if ($wp_query->is_404()) {

                if ($bad_ipSettings->bot_access == '1' && $isBot) { //skip if is bot and bots are allowed
                    return;
                }

                if ($QS != '' && !self::checkIPInWhiteList($user_ip)) {
                    //                $actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
                    isset($_SERVER['REQUEST_URI']) ? $actual_link = $_SERVER['REQUEST_URI'] : $actual_link = null;
                    // $_action = $actual_link;
                    if ( self::checkQuery($_action) ) {
                        return;
                    } //if query is whitelisted, skip
                    $data = array('uid' => $bad_ipSettings->token, 'ip' => $user_ip, 'origin' => $origin, 'reporter' => $reporter, 'action' => $actual_link);
                    $response = Requests::post(BAD_IP_WP_API_URL . '/api/v1/bad_ip/?uid='.$bad_ipSettings->token, array(), json_encode($data));
                    $_rsp = @json_decode($response->body);
                    $_data = @json_encode($_rsp->data);

                    /**
                     * Write report to database
                     */
                    $table = $wpdb->prefix . "bad_ip_reports";
                    $bad_ip['ip'] = $user_ip;
                    $bad_ip['seen'] = $_now;
                    $bad_ip['type'] = 'bad_query';
                    $bad_ip['action'] = $actual_link;
                    $wpdb->replace($table, $bad_ip);
                } else {
                    //                $data = array( 'uid' => 'test', 'ip' => $user_ip, 'origin' => $origin, 'reporter' => $reporter, 'action' => $_action );
                    //                $response = Requests::post( BAD_IP_WP_API_URL.'/v1/bad_ip/?uid=test', array(), json_encode($data) );
                }


            }
        }