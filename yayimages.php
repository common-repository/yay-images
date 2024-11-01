<?php
/*
Plugin Name: YAY Images
Plugin URI: http://yayimages.com
Description: Add YAY Images media directly into your post content
Version: 1.1.0
Author: YAY Images
Author URI: http://yayimages.com
License: GPLv2 or later
*/

if ( ! defined('YAY_IMAGES_FILE')) {
    define('YAY_IMAGES_FILE', __FILE__);
}

if ( ! defined('YAY_IMAGES_PATH')) {
    define('YAY_IMAGES_PATH', plugin_dir_path(YAY_IMAGES_FILE));
}

require_once YAY_IMAGES_PATH.'controllers/YayImage.php';

function yayStartSession()
{
    if (!session_id()) {
        session_start();
    }
}

function yayEndSession()
{
    session_destroy();
}

register_activation_hook( __FILE__, array( 'YayimagesPlugin', 'activation_check' ) );

class YayimagesPlugin
{

    // default settings
    protected $default_settings;

    // Define and register singleton
    private static $instance = false;

    public static function instance()
    {
        if (!self::$instance)
            self::$instance = new YayimagesPlugin;

        return self::$instance;
    }


    private function __clone()
    {
    }

    /**
     * Register actions and filters
     *
     * @uses add_action, add_filter
     * @return null
     */
    private function __construct()
    {
        add_action( 'admin_init', array( $this, 'check_version' ) );

        // Don't run anything else in the plugin, if we're on an incompatible WordPress version
        if ( ! self::compatible_version() ) {
            return;
        }

        add_action('wp_enqueue_scripts', array($this, 'frontend_scripts'));
        add_action('admin_enqueue_scripts', array($this, 'admin_enqueue'));
        add_action('media_buttons', array($this, 'media_buttons'), 20);
        add_action('admin_menu', array($this, 'admin_menu'));

        $this->default_settings = array(
            'yay_image_caption' => 'copyright'
        );

        $this->_url = plugins_url('', __FILE__);
    }

    public static function compatible_version()
    {
        return function_exists('curl_version');
    }

    static function activation_check()
    {
        if ( ! self::compatible_version()) {
            deactivate_plugins(plugin_basename(__FILE__));
            wp_die(
                __(
                    'YAY Images plugin requires cURL and the curl php extension to be installed and enabled.',
                    'yay-images'
                )
            );
        }
    }

    function check_version()
    {
        if ( ! self::compatible_version()) {
            if (is_plugin_active(plugin_basename(__FILE__))) {
                deactivate_plugins(plugin_basename(__FILE__));
                add_action('admin_notices', [$this, 'disabled_notice']);
                if (isset($_GET['activate'])) {
                    unset($_GET['activate']);
                }
            }
        }
    }

    function disabled_notice()
    {
        echo '<strong>'.esc_html__(
                'YAY Images plugin requires cURL and the curl php extension to be installed and enabled.',
                'yay-images'
            ).'</strong>';
    }

    function frontend_scripts()
    {
        wp_enqueue_script('jquery-ui-core');
        wp_enqueue_script("jquery-ui-slider");

    }

    function admin_menu()
    {
        add_filter('plugin_action_links_' . plugin_basename(__FILE__), array($this, 'filter_plugin_actions'), 10, 2);
        add_options_page(__('Yay Images', "yayimages"), __('Yay Images', "yayimages"), 'manage_options', __class__, array($this, 'options_page'));
    }

    /**
     * Enqueue all assets used for admin view. Localize scripts.
     */
    function admin_enqueue($hook)
    {
        global $pagenow;

        // Only operate on edit post pages
        if ($pagenow != 'post.php' && $pagenow != 'post-new.php')
            return;

        //if ($hook == 'settings_page_YayimagesPlugin') {


        //}


        // Ensure all the files required by the media manager are present
        wp_enqueue_media();

        wp_enqueue_script('yayimages-cssjs', plugins_url('/js/css.js', __FILE__), array(), 1, true);
        wp_enqueue_script('yayimages-sprintfjs', plugins_url('/js/sprintf.js', __FILE__), array(), 1, true);
        wp_enqueue_script('yayimages', plugins_url('/js/yayimages-views.js', __FILE__), array(
            'jquery',
            'jquery-ui-core',
            'jquery-ui-widget',
            'jquery-ui-button',
            'jquery-ui-slider',
            'jquery-ui-draggable',
            'jquery-ui-droppable'
        ), 1, true);


        wp_enqueue_style('yayimages-style', plugins_url('/css/yayimages.css', __FILE__));
        wp_enqueue_style(
            'yayimages-admin-ui-css',
            'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.21/themes/smoothness/jquery-ui.css',
            false,
            PLUGIN_VERSION,
            false
        );

        wp_enqueue_script('aviary', 'http://feather.aviary.com/js/feather.js', array('jquery'), 1, true);
        wp_enqueue_script('yay-aviary', plugins_url('/js/aviary.js', __FILE__), array(), 1, true);

        add_action('print_media_templates', array($this, 'print_media_templates'));


    }

    function media_buttons($editor_id = 'content')
    {
        ?>
        <a href="#" id="insert-yayimages-button" class="button yayimages add_media"
           data-editor="<?php echo esc_attr($editor_id); ?>"
           title="<?php esc_attr_e("YAY Images", 'yayimages'); ?>"><span
                class="yay-media-buttons-icon"></span><?php esc_html_e("YAY Images", 'yayimages'); ?></a>
    <?php
    }

    function options_page()
    {
        wp_enqueue_style(__class__ . '_styles', $this->_url . '/css/options.css', array(), 1, 'all');
        wp_enqueue_script(__class__, $this->_url . '/js/options.js', array('jquery'), 1);

        $options_url = admin_url('options-general.php?page=' . __class__);
        $settings = get_option(__class__ . '_settings', array());
        require_once YAY_IMAGES_PATH . '/views/admin/options.php';

    }

    function print_media_templates()
    {
        require_once YAY_IMAGES_PATH.'/yayimages-templates.php';
    }


    function filter_plugin_actions($l, $file)
    {
        $settings_link = '<a href="options-general.php?page=' . __class__ . '">' . __('Settings') . '</a>';
        array_unshift($l, $settings_link);
        return $l;

    }

}

YayimagesPlugin::instance();


add_filter('wp_get_attachment_image_attributes', 'yay_attachment_image_attributes', 10, 2);
add_filter('delete_post_metadata', 'delete_yay_thumbnail', 10, 5);

add_action('wp_ajax_my_action', 'my_action_callback');

function my_action_callback()
{
    $method = $_POST["method"];

    $ym = new YayImage();
    $methodName = "action" . ucfirst($method);
    $post = isset($_POST) ? $_POST : array();
    $ym->$methodName($_POST, $post);

    die();
}

add_action('init', 'yayStartSession', 1);
add_action('wp_logout', 'yayEndSession');
add_action('wp_login', 'yayEndSession');

//add_filter('genesis_get_image', 'genesis_compat_url');
//function genesis_compat_url($src)
//{
//    global $post;
//    $thumburl = get_post_meta($post->ID, 'yayimages_featured_url', true);
//    if ($thumburl != '') {
//        $html = "<img src = '$thumburl'/>";
//    }
//    return $html;
//}

/**
 * @param array $attributes
 * @param int $attachment
 *
 * @return array
 */
function yay_attachment_image_attributes($attributes, $attachment)
{
    $meta = get_post_meta($attachment->ID, 'yay_streaming_url', true);

    if ($meta) {
        $attributes['src'] = $meta;
    }

    return $attributes;
}

function delete_yay_thumbnail($sth, $post_id, $metaKey)
{
    if ($metaKey == '_thumbnail_id') {
        $yayUrl = get_post_meta($post_id, 'yayimages_featured_url', true);
        if ($yayUrl) {
            delete_post_meta($post_id, 'yayimages_featured_url');
        }
    }
}

 