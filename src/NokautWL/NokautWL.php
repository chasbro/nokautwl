<?php
namespace NokautWL;

use NokautWL\Admin\Options;
use NokautWL\Routing\Routing;
use NokautWL\Template\Loader;
use NokautWL\Template\Renderer;

class NokautWL
{
    private static $initiated = false;

    public static function init()
    {
        if (!self::$initiated) {
            self::init_hooks();
        }
    }

    /**
     * Initializes WordPress hooks
     */
    private static function init_hooks()
    {
        self::$initiated = true;

        ApiKitFactory::setApiKey(Options::getOption(Options::OPTION_APIKEY));
        ApiKitFactory::setApiUrl(Options::getOption(Options::OPTION_APIURL));

        $templateDirs = array();
        if (file_exists(NOKAUTWL_THEME_TEMPLATE_DIR)) {
            $templateDirs[] = NOKAUTWL_THEME_TEMPLATE_DIR;
        }
        $templateDirs[] = NOKAUTWL_PLUGIN_TEMPLATE_DIR;

        Renderer::setTemplateBasePaths($templateDirs);

        add_action('init', array(__CLASS__, 'registerShortCodes'));

        wp_register_style('bootstrap.nokaut-wl.min.css', NOKAUTWL_PLUGIN_URL . 'public/vendor/bootstrap/css/bootstrap.nokaut-wl.min.css', array(), '3.1.1');
        wp_enqueue_style('bootstrap.nokaut-wl.min.css');

        wp_register_script('bootstrap.min.js', NOKAUTWL_PLUGIN_URL . 'public/vendor/bootstrap/js/bootstrap.min.js', array('jquery'), '3.1.1');
        wp_enqueue_script('bootstrap.min.js');

        wp_register_script('tinysort.min.js', NOKAUTWL_PLUGIN_URL . 'public/vendor/jquery.tinysort.min.js', array('jquery'), '1.5.6');
        wp_enqueue_script('tinysort.min.js');

        if (file_exists(NOKAUTWL_THEME_PUBLIC_DIR . 'js/nokaut-wl.js')) {
            wp_register_script('nokaut-wl.js', NOKAUTWL_THEME_PUBLIC_URL . 'js/nokaut-wl.js', array('jquery'), NOKAUTWL_VERSION);
        } else {
            wp_register_script('nokaut-wl.js', NOKAUTWL_PLUGIN_URL . 'public/js/nokaut-wl.js', array('jquery'), NOKAUTWL_VERSION);
        }
        wp_localize_script('nokaut-wl.js', 'ajax_object',
            array(
                'products_key' => Options::getOption(Options::OPTION_URL_PART_PRODUCTS_PAGE)
            ));
        wp_enqueue_script('nokaut-wl.js');

        if (file_exists(NOKAUTWL_THEME_PUBLIC_DIR . 'css/nokaut-wl.css')) {
            wp_register_style('nokaut-wl.css', NOKAUTWL_THEME_PUBLIC_URL . 'css/nokaut-wl.css', array(), NOKAUTWL_VERSION);
        } else {
            wp_register_style('nokaut-wl.css', NOKAUTWL_PLUGIN_URL . 'public/css/nokaut-wl.css', array(), NOKAUTWL_VERSION);
        }
        wp_enqueue_style('nokaut-wl.css');

        Routing::init();
        Loader::init();
    }

    public static function activate()
    {
        // nothing to do
    }

    public static function deactivate()
    {
        //tidy up
    }

    public static function registerShortCodes()
    {
        add_shortcode('nokautwl-product-text-link', array('\\NokautWL\\ShortCode', 'productTextLink'));
        add_shortcode('nokautwl-product-box', array('\\NokautWL\\ShortCode', 'productBox'));
        add_shortcode('nokautwl-products-box', array('\\NokautWL\\ShortCode', 'productsBox'));
    }

    public static function log($nokautwl_debug)
    {
        if (defined('WP_DEBUG_LOG') && WP_DEBUG_LOG) {
            //send message to debug.log when in debug mode
            error_log(print_r(compact('nokautwl_debug'), 1));
        }
    }
}
