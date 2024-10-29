<?php

global $wpdb;

$table_settings = $wpdb->prefix . 'bad_ip_settings';
$bad_ipSettings = $wpdb->get_results("SELECT * FROM $table_settings ");
isset($bad_ipSettings) && !empty($bad_ipSettings) ? $bad_ipSettings = $bad_ipSettings[0] : $bad_ipSettings = null;

if (!is_null($bad_ipSettings) && !empty($bad_ipSettings) && $bad_ipSettings->login_incidents == '1') {

    session_start();
    isset($_SESSION['login_count']) ? $_SESSION['login_count'] ++ : $_SESSION['login_count'] = 1;


    if ($_SESSION['login_count'] >= (int) $bad_ipSettings->login_attempts) {

        $_now = date("Y-m-d H:i:s");
        $user_ip = self::getUserIP();
        $bad_ipSettings->login_incidents == '1' ? $origin = get_site_url() : $origin = 'anonymous';
        $reporter = get_site_url();
        $_action = 'login';


        /**
         * Send report to bad_ip http api
         */
        $data = array( 'uid' => $bad_ipSettings->token, 'ip' => $user_ip, 'origin' => $origin, 'reporter' => $reporter, 'action' => $_action );
        $response = Requests::post( BAD_IP_WP_API_URL.'/api/v1/bad_ip/?uid='.$bad_ipSettings->token, array(), json_encode($data) );
        $_rsp = @json_decode($response->body);
        $_data = @json_encode($_rsp->data);

        /**
         * Write report to database
         */
        $table = $wpdb->prefix . "bad_ip_reports";
        $bad_ip['ip'] = $user_ip;
        $bad_ip['seen'] = $_now;
        $bad_ip['type'] = 'login';
        $bad_ip['action'] = 'login';
        $wpdb->replace($table, $bad_ip);

    }

}