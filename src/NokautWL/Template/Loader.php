<?php
namespace NokautWL\Template;

use NokautWL\Routing\Routing;

class Loader
{
    public static function init()
    {
        add_filter('template_include', array(__CLASS__, 'templateLoader'));
    }

    /**
     * @param $template
     * @return string
     */
    public static function templateLoader($template)
    {
        $find = array();
        $file = '';

        if (Routing::isProductPage()) {
            $file = 'nokaut-wl-product.php';
            $find[] = 'nokaut-wl/templates/wordpress/' . $file;
        } elseif (Routing::getWpCategoryPath()) {
            $file = 'nokaut-wl-products.php';
            $find[] = 'nokaut-wl/templates/wordpress/' . $file;
        }

        if ($file) {
            $template = locate_template($find);
            if (!$template) {
                $template = NOKAUTWL_PLUGIN_TEMPLATE_DIR . 'wordpress/' . $file;
            }
        }

        return $template;
    }
}

