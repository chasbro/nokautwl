<?php
namespace NokautWL;

use NokautWL\View\Product\ShortCode\ProductBox;
use NokautWL\View\Product\ShortCode\TextLink;
use NokautWL\View\Products\ShortCode\ProductsBox;

class ShortCode
{
    /**
     * @param array $atts
     * @param string $content
     * @return string
     */
    public static function productTextLink($atts = array(), $content = '')
    {
        $a = shortcode_atts(array(
            'url' => null,
            'tooltip' => TextLink::TOOLTIP_SIMPLE,
        ), $atts);

        return TextLink::render($a['url'], $content, $a['tooltip']);
    }

    /**
     * @param array $atts
     * @return string
     */
    public static function productBox($atts = array())
    {
        $a = shortcode_atts(array(
            'url' => null
        ), $atts);

        return ProductBox::render($a['url']);
    }

    /**
     * @param array $atts
     * @return string
     */
    public static function productsBox($atts = array())
    {
        $a = shortcode_atts(array(
            'url' => null,
            'limit' => ProductsBox::DEFAULT_PRODUCTS_LIMIT,
            'columns' => ProductsBox::DEFAULT_PRODUCTS_COLUMNS
        ), $atts);

        return ProductsBox::render($a['url'], $a['limit'], $a['columns']);
    }
}