<?php

/**
             * database upgrade
             */
            function updateDB() { // update database based on current version being updated
                        
                require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
                global $wpdb;
                $dbname = $wpdb->dbname;
                $charset = $wpdb->get_charset_collate();
                $charset_collate = $wpdb->get_charset_collate();
                $status = 'No updates were applied';

                // >= 1.0.1
                $marks_table_name = $wpdb->prefix . "bad_ip_settings";
                $is_bot_access_col = $wpdb->get_results(  "SELECT `COLUMN_NAME` FROM `INFORMATION_SCHEMA`.`COLUMNS`    WHERE `table_name` = '{$marks_table_name}' AND `TABLE_SCHEMA` = '{$dbname}' AND `COLUMN_NAME` = 'bot_access';"  );
                if( empty($is_bot_access_col) ):
                    $add_bot_access_column = "ALTER TABLE `{$marks_table_name}` ADD `bot_access` INTEGER(9) NOT NULL DEFAULT 1 AFTER `login_attempts`; ";
                    $wpdb->query( $add_bot_access_column );
                    $status = 'Update successful';
                endif;

                // >= 1.0.7
                $ip_whitelist_table_name = $wpdb->prefix . "bad_ip_whitelist";
                $ip_whitelist_table = $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $ip_whitelist_table_name ) );
                
                $ip_blacklist_table_name = $wpdb->prefix . "bad_ip_blacklist";
                $ip_blacklist_table = $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $ip_blacklist_table_name ) );

                $query_whitelist_table_name = $wpdb->prefix . "bad_ip_query_whitelist";
                $query_whitelist_table = $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $query_whitelist_table_name ) );

                if (!$ip_whitelist_table) {
                    $table_ip_whitelist = $wpdb->prefix . 'bad_ip_whitelist';
                    $sql_ip_whitelist = "CREATE TABLE IF NOT EXISTS $table_ip_whitelist (
                    id mediumint(9) NOT NULL AUTO_INCREMENT,
                    ip varchar(20) NOT NULL,
                    PRIMARY KEY id (id),
                    UNIQUE KEY ip (ip)
                    ) $charset_collate;";
                    
                    dbDelta( $sql_ip_whitelist );
                    $status = 'Update successful';
                }
                if (!$ip_blacklist_table) {
                    $table_ip_blacklist = $wpdb->prefix . 'bad_ip_blacklist';
                    $sql_ip_blacklist = "CREATE TABLE IF NOT EXISTS $table_ip_blacklist (
                    id mediumint(9) NOT NULL AUTO_INCREMENT,
                    ip varchar(20) NOT NULL,
                    PRIMARY KEY id (id),
                    UNIQUE KEY ip (ip)
                    ) $charset_collate;";

                    dbDelta( $sql_ip_blacklist );
                    $status = 'Update successful';
                }
                if (!$query_whitelist_table) {
                    $table_query_whitelist = $wpdb->prefix . 'bad_ip_query_whitelist';
                    $sql_query_whitelist = "CREATE TABLE IF NOT EXISTS $table_query_whitelist (
                    id mediumint(9) NOT NULL AUTO_INCREMENT,
                    query varchar(255) NOT NULL,
                    PRIMARY KEY id (id)
                    ) $charset_collate;";

                    dbDelta( $sql_query_whitelist );
                    $status = 'Update successful';
                }

                return $status;


        }


        /**
         * used for adding and removing queries from whitelist
         */
        function handleQueryWhitelist($action, $query) {
            global $wpdb;

            if (!isset($action, $query) && empty($action) && empty($query)){
                return 'Error processing query whitelisting';
            }

            // $queryListArr = getQueryWhitelist();
            $table_query_whitelist = $wpdb->prefix . 'bad_ip_query_whitelist';

            if ($action == 'add') {
                // if (!in_array($query, $queryListArr)) {
                if (!checkQuery($query)) {
                    // $queryListArr[] = $query;
                    // file_put_contents(BAD_IP_WP_DIR.'/query_whitelist.bin', serialize($queryListArr));

                    $queryListArrInsert['query'] = $query;
                    $wpdb->insert( $table_query_whitelist, $queryListArrInsert);

                    return 'Selected query successfully whitelisted';
                } else {
                    return 'Selected query already whitelisted';
                }
            }

            if ($action == 'rm') {
                $done = false;
                // foreach($queryListArr as $k => &$val) { 
                //     if($val == $query) { 
                //         unset($queryListArr[$k]);
                //         $done = true; 
                //     } 
                // }
                $queryListArrInsert['query'] = $query;
                if ($wpdb->delete( $table_query_whitelist, $queryListArrInsert )) {
                    $done = true;
                }

                if ($done) {
                    // file_put_contents(BAD_IP_WP_DIR.'/query_whitelist.bin', serialize($queryListArr));
                    return 'Selected query successfully removed from whitelist';
                } else {
                    return 'Error removing query, selected query was not whitelisted';
                }
            }

        }


        /**
         * checks if database needs upgrade
         */
        function needUpgrade() {
            
            global $wpdb;
            $dbname = $wpdb->dbname;
            $settings_table = $wpdb->prefix . "bad_ip_settings";

            // @since 1.0.1
            $is_bot_access_col = $wpdb->get_results(  "SELECT `COLUMN_NAME` FROM `INFORMATION_SCHEMA`.`COLUMNS`    WHERE `table_name` = '{$settings_table}' AND `TABLE_SCHEMA` = '{$dbname}' AND `COLUMN_NAME` = 'bot_access'"  );
            if( empty($is_bot_access_col) ) {
                return true;
            }

            // >= 1.0.7
            $ip_whitelist_table_name = $wpdb->prefix . "bad_ip_whitelist";
            // $ip_whitelist_table = $wpdb->get_results(  "SELECT COUNT(1) FROM `INFORMATION_SCHEMA`.`TABLES` WHERE `TABLE_SCHEMA`='{$dbname}' AND `table_name`='{$ip_whitelist_table_name}';"  );
            $ip_whitelist_table = $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $ip_whitelist_table_name ) );
            
            $ip_blacklist_table_name = $wpdb->prefix . "bad_ip_blacklist";
            // $ip_blacklist_table = $wpdb->get_results(  "SELECT COUNT(1) FROM `INFORMATION_SCHEMA`.`TABLES` WHERE `TABLE_SCHEMA`='{$dbname}' AND `table_name`='{$ip_blacklist_table_name}';"  );
            $ip_blacklist_table = $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $ip_blacklist_table_name ) );

            $query_whitelist_table_name = $wpdb->prefix . "bad_ip_query_whitelist";
            // $query_whitelist_table = $wpdb->get_results(  "SELECT COUNT(1) FROM `INFORMATION_SCHEMA`.`TABLES` WHERE `TABLE_SCHEMA`='{$dbname}' AND `table_name`='{$query_whitelist_table_name}';"  );
            $query_whitelist_table = $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $query_whitelist_table_name ) );

            if (!$ip_whitelist_table || !$ip_blacklist_table || !$query_whitelist_table ) {
                return true;
            }

            return false;

        }


        function time_elapsed_bad_ip($datetime, $full = false) {
            $now = new DateTime;
            $ago = new DateTime($datetime);
            $diff = $now->diff($ago);

            $diff->w = floor($diff->d / 7);
            $diff->d -= $diff->w * 7;

            $string = array(
                'y' => 'year',
                'm' => 'month',
                'w' => 'week',
                'd' => 'day',
                'h' => 'hour',
                'i' => 'minute',
                's' => 'second',
            );
            foreach ($string as $k => &$v) {
                if ($diff->$k) {
                    $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
                } else {
                    unset($string[$k]);
                }
            }

            if (!$full) $string = array_slice($string, 0, 1);
            return $string ? implode(', ', $string) . ' ago' : 'just now';
        }


        function getQueryWhitelist() {
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
            $queryListArr = getQueryWhitelist();
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
            $ipArr = getIPWhitelist();
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
            $ipArr = getIPBlacklist();
            return true ? in_array($ip, $ipArr) : false;
        }