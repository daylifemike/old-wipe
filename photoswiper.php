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
        PhotoSwiper::print_options();

        wp_register_script( 'angularjs', 'http://ajax.googleapis.com/ajax/libs/angularjs/1.0.7/angular.min.js', array('jquery') );

        wp_register_script( 'photoswiper_schema', plugins_url('schema.js', __FILE__ ), array( 'angularjs' ) );
        wp_register_script( 'photoswiper_admin', plugins_url('admin-app.js', __FILE__ ), array( 'angularjs', 'photoswiper_schema' ) );
        wp_register_script( 'photoswiper_frontsite', plugins_url('frontsite-app.js', __FILE__ ), array( 'angularjs', 'photoswipe_js', 'photoswiper_schema' ) );

        wp_register_script( 'photoswipe_klass', plugins_url('photoswipe/lib/klass.min.js', __FILE__ ), array( 'jquery' ) );
        wp_register_script( 'photoswipe_js', plugins_url('photoswipe/code.photoswipe.jquery-3.0.5.min.js', __FILE__ ), array( 'jquery', 'photoswipe_klass' ), '3.0.5' );

        if ( is_admin() && isset($_GET['page']) ) {
            if ( $_GET['page'] == "photoswiper.php" || $_GET['page'] == "photoswiper" ) {
                wp_enqueue_script( 'photoswiper_admin' );
            }
        } else {
            wp_enqueue_script( 'photoswiper_frontsite' );
            wp_enqueue_style( 'photoswipe_css', plugins_url('photoswipe/photoswipe.css', __FILE__ ) );
        }
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

    public static function print_options() {
        $options = get_option('photoswiper');
        echo('
            <script>
                (function (window) {
                    if (!window.PhotoSwiper) {
                        window.PhotoSwiper = {};
                    }

                    window.PhotoSwiper.saved_data = '. $options .';
                }(window));
            </script>
        ');
    }
} // end PhotoSwiper
?>