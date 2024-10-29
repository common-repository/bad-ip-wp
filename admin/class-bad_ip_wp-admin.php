<?php

require BAD_IP_WP_DIR.'/admin/partials/class-bad_ip_actions.php';



/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://iridiumintel.com
 * @since      1.0.0
 *
 * @package    bad_ip_wp
 * @subpackage bad_ip_wp/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * @package    bad_ip_wp
 * @subpackage bad_ip_wp/admin
 * @author     i--i <bad_ip@iridiumintel.com>
 */
class bad_ip_wp_Admin extends theActions {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

    /**
     * @param $needle
     * @param $haystack
     * @return bool
     */
    function contains($needle, $haystack) {
        return strpos($haystack, $needle) !== false;
    }




    /**
     * Register main bad_ip admin menu & page.
     *
     * @since    1.0.0
     */
    public function add_bad_ip_admin_menu() {

        add_menu_page(
            __('bad_ip'),// the page title
            __('bad_ip'),//menu title
            'manage_options',
            'bad-ip-dash.php',
            'bad_ip_dashboard_page',
            'dashicons-shield',
            6
        );
        add_submenu_page(
            'bad-ip-dash.php',
            'bad_ip - Settings', //page title
            'Settings', //menu title
            'manage_options', //capability,
            'bad_ip_settings',//menu slug
            'bad_ip_settings_page' //callback function
        );



        require BAD_IP_WP_DIR.'/admin/partials/bad_ip_admin_menu_actions.php';

        require BAD_IP_WP_DIR.'/admin/partials/bad_ip_admin_dashboard_page.php';
        require BAD_IP_WP_DIR.'/admin/partials/bad_ip_admin_settings_page.php';

            
        
    }


    


    /**
     * Hooks to WP head
     *
     * @since    1.0.0
     */
    public function hook_bad_ip_head() {

        function Redirect($url = BAD_IP_WP_JAIL_URL, $permanent = false)
        {
            if(headers_sent())
            {
                echo "<script>window.open('".$url."','_self');</script>";
            }
            else{
                exit( wp_redirect($url));
            }
        }

        require BAD_IP_WP_DIR.'/admin/partials/bad_ip_head_hook.php';

        ?>
        <script>
        //     console.log(`%c
        // Protected by
        //
        // 888                    888          d8b
        // 888                    888          Y8P
        // 888                    888
        // 88888b.   8888b.   .d88888          888 88888b.
        // 888 "88b     "88b d88" 888          888 888 "88b
        // 888  888 .d888888 888  888          888 888  888
        // 888 d88P 888  888 Y88b 888          888 888 d88P
        // 88888P"  "Y888888  "Y88888 88888888 888 88888P"
        //                                         888
        //                                         888
        //                                         888
        // `, "font-family:monospace");
            // console.log('<?php //echo $user_ip; ?>');
            // console.log('<?php //echo $_rsp->code; ?>');
            //console.error('<?php //echo $bodyRSP; ?>//');
            //console.error('<?php //echo json_encode($_rsp); ?>//');
            //console.error('<?php //echo json_encode($data); ?>//');
        </script>
        <?php
        
    }

    /**
     * Hooks to WP Login
     *
     * @since    1.0.0
     */
    public function hook_bad_ip_login_success() {
        session_start();
        //        if (isset($_SESSION['login_count'])) {
            unset($_SESSION['login_count']);
        //        }
    }

    /**
     * Hooks to WP Failed Login
     *
     * @since    1.0.0
     */
    public function hook_bad_ip_login() {
       
        require BAD_IP_WP_DIR.'/admin/partials/bad_ip_login_hook.php';


    }


    /**
     * Hooks to WP 404
     *
     * @since    1.0.0
     */
    public function hook_bad_ip_404() {

        require BAD_IP_WP_DIR.'/admin/partials/bad_ip_404_hook.php';
        

    }




	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

        $page_name = filter_input(INPUT_POST | INPUT_GET, 'page', FILTER_SANITIZE_SPECIAL_CHARS);
        if (isset($_GET['page']) && ($page_name == 'bad-ip-dash.php' || $page_name == 'bad_ip_settings')) {
            wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/bad_ip_wp-admin.css', array(), $this->version, 'all' );
            wp_enqueue_style( $this->plugin_name.'fontawesome', plugin_dir_url( __FILE__ ) . 'vendor/fontawesome-free/css/all.min.css', array(), $this->version, 'all' );
            wp_enqueue_style( $this->plugin_name.'-Nunito', '//fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i', array(), $this->version, 'all' );
            wp_enqueue_style( $this->plugin_name.'-sb-admin-2', plugin_dir_url( __FILE__ ) . 'css/sb-admin-2.min.css', array(), $this->version, 'all' );
        }
        

		
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

        $page_name = filter_input(INPUT_POST | INPUT_GET, 'page', FILTER_SANITIZE_SPECIAL_CHARS);
        if (isset($_GET['page']) && ($page_name == 'bad-ip-dash.php' || $page_name == 'bad_ip_settings')) {
            wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/bad_ip_wp-admin.js', array( 'jquery' ), $this->version, false );
            wp_enqueue_script( $this->plugin_name.'-jquery', plugin_dir_url( __FILE__ ) . 'vendor/jquery/jquery.min.js', array( 'jquery' ), $this->version, true );
            wp_enqueue_script( $this->plugin_name.'-bootstrap', plugin_dir_url( __FILE__ ) . 'vendor/bootstrap/js/bootstrap.bundle.min.js', array( 'jquery' ), $this->version, true );
            wp_enqueue_script( $this->plugin_name.'-jquery-easing', plugin_dir_url( __FILE__ ) . 'vendor/jquery-easing/jquery.easing.min.js', array( 'jquery' ), $this->version, true );
            wp_enqueue_script( $this->plugin_name.'-sb-admin-2', plugin_dir_url( __FILE__ ) . 'js/sb-admin-2.min.js', array( 'jquery' ), $this->version, true );
            wp_enqueue_script( $this->plugin_name.'-fancyTable', plugin_dir_url( __FILE__ ) . 'js/fancyTable.js', array( 'jquery' ), $this->version, true );
        }

		
	}

}
