<?php

function bad_ip_dashboard_page() {
            

    global $wpdb;
    $context = Timber::get_context();
    $_uid = get_current_user_id();

    if ( ! empty( $_POST ) ) {
        !is_null(@$_POST['action']) ? $_action = sanitize_text_field($_POST['action']) : $_action = null;
        !is_null(@$_POST['query']) ? $_query = sanitize_text_field($_POST['query']) : $_query = null;
        !is_null(@$_POST['token']) ? $token = sanitize_text_field($_POST['token']) : $token = null;
        if (isset($_action)) {
            isset($_query) ? $context['notifications'][] = handleQueryWhitelist($_action, $_query) : null;
            $_action === 'upgrade' ? $context['notifications'][] = updateDB() : null;
        }

    }

    $_limit = 20;

    $table_denied = $wpdb->prefix . 'bad_ip_denied';
    $table_reports = $wpdb->prefix . 'bad_ip_reports';

    $bad_ips_denied = $wpdb->get_results("SELECT * FROM $table_denied ORDER BY seen DESC ");
    $bad_ips_denied_tor = array();
    foreach ($bad_ips_denied as $tor) {
        if ($tor->type == 'tor')
        $bad_ips_denied_tor[] = $tor;
    }
    //                $wpdb->get_results("SELECT * FROM $table_denied WHERE 'type'='tor' ORDER BY 'seen' DESC ");


    $bad_ips_report = $wpdb->get_results("SELECT * FROM $table_reports ORDER BY seen DESC ");
    $bad_ips_report_bad_query = array();
    //                $wpdb->get_results("SELECT * FROM $table_reports WHERE 'type'='bad_query' ORDER BY 'seen' DESC ");
    foreach ($bad_ips_report as $report) {
        if ($report->type == 'bad_query')
            $bad_ips_report_bad_query[] = $report;
    }
    $bad_ips_report_login = array();
    //                $wpdb->get_results("SELECT * FROM $table_reports WHERE 'type'='login' ORDER BY 'seen' DESC ");
    foreach ($bad_ips_report as $report_login) {
        if ($report_login->type == 'login')
            $bad_ips_report_login[] = $report_login;
    }


    $bad_ips_denied_pm = $wpdb->get_results("SELECT seen FROM $table_denied WHERE MONTH(seen) = MONTH(CURDATE()) AND YEAR(seen) = YEAR(CURDATE());");



    

    $context['bad_ip_name'] = BAD_IP_WP_NAME;
    $context['bad_ip_url'] = BAD_IP_WP_URL;
    $context['bad_ip_dir'] = BAD_IP_WP_DIR;


    if (needUpgrade()) { //notifcation about needed upgrade
        $context['needsupgrade'] = true;
    }
    $context['bad_ips_denied'] = $bad_ips_denied;
    $context['bad_ips_denied_tor'] = $bad_ips_denied_tor;
    $context['bad_ips_report'] = $bad_ips_report;
    $context['bad_ips_report_bad_query'] = $bad_ips_report_bad_query;
    $context['bad_ips_report_login'] = $bad_ips_report_login;
    $context['bad_ips_report_pm'] = $bad_ips_denied_pm;


    Timber::render( array( 'page-dashboard.twig'), $context);
}