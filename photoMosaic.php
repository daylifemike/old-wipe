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
        add_action( 'wp_enqueue_scripts', array( __CLASS__, 'load_scripts' ) );
        add_action( 'admin_enqueue_scripts', array( __CLASS__, 'load_scripts' ) );

        // add_filter( 'the_posts', array( __CLASS__, 'the_posts' ) ); // :: conditionally enqueue JS & CSS
        // add_filter( 'post_gallery', array( __CLASS__, 'post_gallery' ), 1337, 2 );
        // add_filter( 'plugin_action_links', array( __CLASS__, 'plugin_action_links' ), 10, 2);
        
        
        // wp_register_script( 'photoswiper', plugins_url('/js/jquery.photoMosaic.js', __FILE__ ));
        // wp_enqueue_script('photoswiper');

        // wp_enqueue_style( 'photoswiper_base_css', plugins_url('/css/photoMosaic.css', __FILE__ ));

        if (!is_admin()) {
            // front-site
            //ajax.googleapis.com/ajax/libs/angularjs/1.0.7/angular.min.js

        } else {
            // if ( isset($_GET['page']) ) {
            //     if ( $_GET['page'] == "photoSwiper.php" || $_GET['page'] == "photoswiper" ) {
            //         wp_enqueue_script( 'photoswiper_admin_js', plugins_url('/js/jquery.photoMosaic.wp.admin.js', __FILE__ ), array('photomosaic'));
            //         wp_enqueue_style( 'photoswiper_admin_css', plugins_url('/css/photoMosaic.admin.css', __FILE__ ));
            //     }
            // }

            // wp_enqueue_style( 'menu', plugins_url('/css/photoMosaic.menu.css', __FILE__ ));
        }
    }

    public static function load_scripts() {
        wp_enqueue_script( 'jquery' );
        wp_register_script( 'angularjs', 'http://ajax.googleapis.com/ajax/libs/angularjs/1.0.7/angular.min.js', array('jquery'), '1.0.7', true );
        wp_enqueue_script( 'angularjs' );
    }

    public static function add_admin_page() {
        add_options_page( 'PhotoSwiper Settings', 'PhotoSwiper', 'manage_options', 'photoswiper', array( __CLASS__, 'create_admin_page' ) );
    }

    public static function create_admin_page() {
        $options = PhotoMosaic::getOptions();
        $options = PhotoMosaic::adjustDeprecatedOptions($options);
        $options = json_encode( $options );
        ?>
        <script>
            console.log( <?php echo $options ?> );
        </script>
        <div class="wrap">
            <h2>PhotoSwiper</h2>
        <?php
    }









    public static function getOptions() {
        $defaults = array(
            'padding' => 2,
            'columns' => 0,
            'width' => 0,
            'height' => 0,
            'links' => true,
            'order' => 'rows',
            'link_to_url' => false,
            'external_links' => false,
            'center' => true,
            'prevent_crop' => false,
            'show_loading' => false,
            'loading_transition' => 'fade',
            'responsive_transition' => true,
            'lightbox' => true,
            'lightbox_rel' => 'pmlightbox',
            'lightbox_group' => true,
            'custom_lightbox' => false,
            'custom_lightbox_name' => '',
            'custom_lightbox_params' => '{}'
        );

        $options = get_option('photomosaic_options');

        if (!is_array($options)) {
            $options = $defaults;
            update_option('photomosaic_options', $options);
        } else {
            $options = $options + $defaults; // "+" means dup keys aren't overwritten
        }

        return $options;
    }

    public static function ajaxHandler () {
        // not currently being used
        $options = PhotoMosaic::getOptions();
        die(json_encode($options));
    }

    public static function shortcode( $atts ) {
        global $post;
        $post_id = intval($post->ID);
        $base = array(
            'id'        => $post_id,
            'include'   => '',
            'exclude'   => '',
            'ids'       => ''
        );
        $options = PhotoMosaic::getOptions();
        $options = wp_parse_args($base, $options);
        $settings = shortcode_atts($options, $atts);

        $auto_settings = array(
            'height', 'width', 'columns'
        );
        $bool_settings = array(
            'center', 'prevent_crop', 'links', 'external_links', 'show_loading',
            'responsive_transition', 'lightbox', 'custom_lightbox', 'lightbox_group'
        );

        foreach ( $auto_settings as $key ) {
            if(intval($settings[$key]) == 0){
                $settings[$key] = "'auto'";
            } else {
                $settings[$key] = intval($settings[$key]);
            }
        }

        foreach ( $bool_settings as $key ) {
            if(intval($settings[$key])){
                $settings[$key] = "true";
            } else {
                $settings[$key] = "false";
            }
        }

        $unique = floor(((time() + rand(21,40)) * rand(1,5)) / rand(1,5));

        $output_buffer = '
            <!-- PhotoMosaic v'. PhotoMosaic::version() .' -->
            <script type="text/javascript" data-photomosaic-gallery="true">
                var PMalbum'.$unique.' = [';

        if ( !empty($atts['nggid']) ) {
            $output_buffer .= PhotoMosaic::galleryFromNextGen($atts['nggid'], $settings['link_to_url'], 'gallery');
        } else if ( !empty($atts['ngaid']) ) {
            $output_buffer .= PhotoMosaic::galleryFromNextGen($atts['ngaid'], $settings['link_to_url'], 'album');
        } else {
            $output_buffer .= PhotoMosaic::galleryFromWP($settings['id'], $settings['link_to_url'], $settings['include'], $settings['exclude'], $settings['ids']);
        }

        $output_buffer .='];
            </script>
            <script type="text/javascript" data-photomosaic-call="true">';

        $output_buffer .='
                JQPM(document).ready(function($) {
                    $("#photoMosaicTarget'.$unique.'").photoMosaic({
                        gallery: PMalbum'.$unique.',
                        padding: '. intval($settings['padding']) .',
                        columns: '. $settings['columns'] .',
                        width: '. $settings['width'] .',
                        height: '. $settings['height'] .',
                        center: '. $settings['center'] .',
                        prevent_crop: '. $settings['prevent_crop'] .',
                        links: '. $settings['links'] .',
                        external_links: '. $settings['external_links'] .',
                        show_loading: '. $settings['show_loading'] .',
                        loading_transition: "'. $settings['loading_transition'] .'",
                        responsive_transition: '. $settings['responsive_transition'] .',
                        modal_name: "' . $settings['lightbox_rel'] . '",
                        modal_group: ' . $settings['lightbox_group'] . ',
            ';

        $output_buffer .= PhotoMosaic::getSizeObj($atts);

        if( $settings['lightbox'] == 'true' || $settings['custom_lightbox'] == 'true' ) {
            if( $settings['lightbox'] == 'true' ) {
                $output_buffer .='
                        modal_ready_callback : function($photomosaic){
                            $("a[rel^=\''.$settings['lightbox_rel'].'\']", $photomosaic).prettyPhoto({
                                overlay_gallery: false,
                                slideshow: false,
                                theme: "pp_default",
                                deeplinking: false,
                                show_title: false,
                                social_tools: ""
                            });
                        },
                ';
            } elseif ( $settings['custom_lightbox'] == 'true' ) {
                $output_buffer .='
                        modal_ready_callback : function($photomosaic){
                            jQuery("a[rel^=\''.$settings['lightbox_rel'].'\']", $photomosaic).'.$settings['custom_lightbox_name'].'('.$settings['custom_lightbox_params'].');
                        },
                ';
            }
        } else if ( class_exists('Jetpack_Carousel') ) {
            // Jetpack :: Carousel support
            $output_buffer .='
                    modal_ready_callback : function($photomosaic){
                        var data;
                        var id;
                        var $fragment;
                        var $img;
                        var $a;
                        var self = this;

                        $("a", $photomosaic).each(function () {
                            $a = $(this);
                            $img = $a.find("img");
                            id = $img.attr("id");
                            data = self.deepSearch( self.images, "id", id );

                            $img.attr( data.jetpack );

                            $a.addClass("gallery-item");
                        });

                        $($photomosaic).parent().addClass("gallery");
                    },
            ';
        }

        $output_buffer .='
                        order: "'. $settings['order'] .'"
                    });
                });
            </script>';

        $gallery_div = '<div id="photoMosaicTarget'. $unique .'" data-version="'. PhotoMosaic::version() .'">';

        /* Jetpack :: Carousel hack - it needs an HTML string to append it's data */
        if ( class_exists('Jetpack_Carousel') ) {
            $gallery_style = "<style type='text/css'></style>";
            $output_buffer .= apply_filters( 'gallery_style', $gallery_style . "\n\t\t" . $gallery_div );
        } else {
            $output_buffer .= $gallery_div;
        }

        $output_buffer .='</div>';

        return preg_replace('/\s+/', ' ', $output_buffer);
    }

    public static function the_posts( $posts ) {
        // ok...
        // currently we load the JS and CSS on every non-admin page regardless if there is a mosaic
        // checks here could be used to only enqueue PM when needed
        // that's probably a good thing
        foreach( $posts as $post ) {
            if ( preg_match_all( '/\[gallery.*\]/', $post->post_content, $matches ) ) {
                foreach ( $matches[0] as $match ) {
                    // print_r($match);
                    // print_r('<br>');
                    if ( strpos( $match, 'photomosaic="true"' ) !== false || strpos( $match, "photomosaic='true'" ) !== false) {
                        // print_r('YUP!!! <br>');
                    }
                }
            }
        }
        return $posts;
    }

    public static function post_gallery( $empty = '', $atts = array() ) {
        global $post;

        $isPhotoMosaic = false;

        if ( isset($atts['photomosaic']) ) {
            if ( $atts['photomosaic'] === 'true' ) {
                $isPhotoMosaic = true;
            }
        } else if ( isset($atts['template']) ) {
            // deprecated in 2.4.1
            if ( $atts['template'] === 'photomosaic' ) {
                $isPhotoMosaic = true;
            }
        } else if ( isset($atts['theme']) ) {
            if ( $atts['theme'] === 'photomosaic' ) {
                $isPhotoMosaic = true;
            }
        }

        if ( !$isPhotoMosaic ) {
            return $empty;
        } else {
            $output = PhotoMosaic::shortcode($atts);
            return $output;
        }
    }

    public static function setupAdminPage() {
        if(isset($_POST['photomosaic_save'])) {
            $options = PhotoMosaic::getOptions();

            foreach ($options as $k => $v) {
                if ( !array_key_exists($k, $_POST) ) {
                    if (intval($options[$k]) || empty($options[$k])) {
                        $_POST[$k] = 0;
                    } else {
                        $_POST[$k] = $options[$k];
                    }
                }
                if (is_string($_POST[$k])) {
                    $options[$k] = trim( stripslashes( $_POST[$k] ) );
                } else {
                    $options[$k] = $_POST[$k];
                }
            }

            update_option('photomosaic_options', $options);

            $_POST['message'] = "Settings Updated";
        }

        add_menu_page(
            'PhotoMosaic v' . PhotoMosaic::version(),
            'PhotoMosaic',
            'update_plugins',
            'photomosaic', // basename(__FILE__) == 'photoMosaic.php'
            array('PhotoMosaic', 'renderAdminPage'),
            'div'
        );
    }

    public static function plugin_action_links($links, $file) {
        // http://wp.smashingmagazine.com/2011/03/08/ten-things-every-wordpress-plugin-developer-should-know/
        static $this_plugin;

        if (!$this_plugin) {
            $this_plugin = plugin_basename(__FILE__);
        }

        if ($file == $this_plugin) {
            $settings_link = '<a href="' . get_bloginfo('wpurl') . '/wp-admin/admin.php?page=photomosaic">Settings</a>';
            array_unshift($links, $settings_link);
        }

        return $links;
    }

    public static function renderAdminPage() {
        require_once( 'includes/Markdown.php' );
        $options = PhotoMosaic::getOptions();
        $options = PhotoMosaic::adjustDeprecatedOptions($options);
        ?>
        <script>
            if (!window.PhotoMosaic) {
                window.PhotoMosaic = {};
            }
        </script>
        <div class="wrap photomosaic">
            <h1>PhotoMosaic v<?php echo PhotoMosaic::version(); ?></h1>
            <?php
                $tabs = array(
                    // display name, id, file
                    array('Global Settings',   'form',        'global-settings.php'),
                    array('Usage',             'usage',       'usage.txt'),
                    array('Inline Attributes', 'inlineattrs', 'inline-attributes.txt'),
                    array('FAQ',               'faq',         'faq.php'),
                    array("What's New",        'whatsnew',    'whatsnew.txt')
                );
            ?>
            <h2 class="nav-tab-wrapper">
                <?php foreach ($tabs as $tab) : ?>
                    <a class="nav-tab" href="#tab-<?php echo $tab[1]; ?>"><?php echo $tab[0]; ?></a>
                <?php endforeach; ?>
            </h2>

            <?php foreach ($tabs as $tab) : ?>
                <div class="tab" id="tab-<?php echo $tab[1]; ?>">
                    <?php
                        $url = 'includes/admin-markup/' . $tab[2];

                        if ( strpos($tab[2], '.txt') === false) {
                            include( $url );
                        } else {
                            $text = file_get_contents( dirname(__file__) . '/' . $url );
                            echo Markdown($text);
                        }
                    ?>
                </div>
            <?php endforeach; ?>

        </div>
        <?php
    }

} // end PhotoSwiper
?>