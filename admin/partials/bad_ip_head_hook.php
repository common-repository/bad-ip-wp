<?php

        global $wpdb;
        $user_ip = self::getUserIP();
        $_now = date("Y-m-d H:i:s");
        
            if (self::checkIPInBlacklist($user_ip) && !self::checkIPInWhiteList($user_ip)) {
                $url = BAD_IP_WP_JAIL_URL;
                 Redirect($url);
            }

        $table_settings = $wpdb->prefix . 'bad_ip_settings';
        $bad_ipSettings = $wpdb->get_results("SELECT * FROM $table_settings ");
        isset($bad_ipSettings) && !empty($bad_ipSettings) ? $bad_ipSettings = $bad_ipSettings[0] : $bad_ipSettings = null;


if (self::checkOnline(BAD_IP_WP_API_URL)) {

    $data = array( 'uid' => $bad_ipSettings->token, 'ip' => $user_ip, );
    $bad_ipSettings->tor_block == '1' ? $data['tor'] = true : null;
    $response = Requests::post( BAD_IP_WP_API_URL.'/api/v1/bad_ip/check/?uid='.$bad_ipSettings->token, array(), json_encode($data) );
    $_rsp = @json_decode($response->body);
    $_data = @json_encode($_rsp->data);

    $bodyRSP = $_data;

    $dataArray = json_decode( $_data, true );
    isset($dataArray['tor_data'][0]) ? $isTor = true : $isTor = false;

    $isBot = self::is_bot();

    if ($bad_ipSettings->deny_access == '1') {

        if ($bad_ipSettings->bot_access == '1' && $isBot) { //skip if is bot and bots are allowed
            return;
        }

        if (self::checkIPInWhiteList($user_ip)) { //skip if is whitelisted
            return;
        }

        if (isset($_rsp) && $_rsp->code == 666) { // if recorded in bad_ip public database

            $table = $wpdb->prefix . "bad_ip_denied";
            $bad_ip['ip'] = $user_ip;
            $bad_ip['seen'] = $_now;
            $isTor ? $bad_ip['type'] = 'tor' : $bad_ip['type'] = 'bad_ip';
            $wpdb->replace($table, $bad_ip);

            $url = BAD_IP_WP_JAIL_URL;
            Redirect($url); // todo buffer output first
//                echo "<script>window.open('".$url."','_self');</script>";


        } else { // check for bad_query against public database

            isset($_SERVER['QUERY_STRING']) ? $QS = $_SERVER['QUERY_STRING'] : $QS = null;
            if ($QS != '') {
                isset($_SERVER['REQUEST_URI']) ? $actual_link = $_SERVER['REQUEST_URI'] : $actual_link = null;
                if ( self::checkQuery($actual_link) ) {
                    return;
                } //if query is whitelisted, skip
                $data = array( 'uid' => $bad_ipSettings->token, 'query' => $actual_link, );
                $response = Requests::post( BAD_IP_WP_API_URL.'/api/v1/bad_query/check/?uid='.$bad_ipSettings->token, array(), json_encode($data) );
                $_rsp = @json_decode($response->body);
                $_data = @json_encode($_rsp->data);

                if (isset($_rsp) && $_rsp->code == 666) { // if recorded in bad_ip public database
                    $table = $wpdb->prefix . "bad_ip_denied";
                    $bad_ip['ip'] = $user_ip;
                    $bad_ip['seen'] = $_now;
                    $bad_ip['type'] = 'bad_query';
                    $wpdb->replace($table, $bad_ip);

                    $url = BAD_IP_WP_JAIL_URL;
                    Redirect($url);  // todo buffer output first
//                            echo "<script>window.open('".$url."','_self');</script>";

                }

            }



        }
    }


}




        