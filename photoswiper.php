<?php
/*
Plugin Name: PhotoSwiper
Plugin URI: 
Description: Makes PhotoSwipe (an image gallery for mobile and touch devices) available to your posts
Author: makfak
Author URI: http://www.codecanyon.net/user/makfak?ref=makfak
Version: 0.1
*/

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { 
    die('Illegal Entry');
}

add_action('init', array('PhotoSwiper', 'init'));

class PhotoSwiper {

    public static function version () {
        return '0.1';
    }

    public static function init() {
        ob_start();
        $options = get_option('photoswiper_options');

        add_action( 'admin_menu', array( __CLASS__, 'add_admin_page' ) );
        add_action( 'admin_init', array( __CLASS__, 'options_init' ) );

        add_action( 'wp_enqueue_scripts', array( __CLASS__, 'load_scripts' ) );
        add_action( 'admin_enqueue_scripts', array( __CLASS__, 'load_scripts' ) );

        add_action( 'wp_ajax_photoswiper_save', array( __CLASS__, 'save_options' ) );

        add_filter( 'plugin_action_links', array( __CLASS__, 'plugin_action_links' ), 10, 2);
    }

    public static function add_admin_page() {
        add_options_page( 'PhotoSwiper Settings', 'PhotoSwiper Settings', 'manage_options', 'photoswiper', array( __CLASS__, 'create_admin_page' ) );
    }

    public static function create_admin_page() {
        include( dirname(__file__) . '/form.php' );
    }

    public static function options_init(){
        register_setting( 'photoswiper_options', 'photoswiper' );
    }

    public static function load_scripts() {
        wp_enqueue_script( 'jquery' );
        wp_register_script( 'angularjs', 'http://ajax.googleapis.com/ajax/libs/angularjs/1.0.7/angular.min.js', array('jquery'), '1.0.7', true );
        wp_enqueue_script( 'angularjs' );

        if ( is_admin() && isset($_GET['page']) ) {
            if ( $_GET['page'] == "photoswiper.php" || $_GET['page'] == "photoswiper" ) {
                wp_enqueue_script( 'photoswiper_admin_form', plugins_url('app.js', __FILE__ ), array( 'angularjs' ) );
            }
        } else {
            // frontsite
        }
    }

    public static function save_options() {
        // error_log(print_r($_POST, TRUE), 0);
        print_r( json_encode($_POST) );
        echo "<br>";
        print_r($_REQUEST);
        update_option( 'photoswiper', json_encode( $_POST['photoswiper'] ) );
        echo "kafka";
        die();
    }

    public static function plugin_action_links($links, $file) {
        // http://wp.smashingmagazine.com/2011/03/08/ten-things-every-wordpress-plugin-developer-should-know/
        static $this_plugin;

        if (!$this_plugin) {
            $this_plugin = plugin_basename(__FILE__);
        }

        if ($file == $this_plugin) {
            $settings_link = '<a href="' . get_bloginfo('wpurl') . '/wp-admin/options-general.php?page=photoswiper">Settings</a>';
            array_unshift($links, $settings_link);
        }

        return $links;
    }
} // end PhotoSwiper
?>