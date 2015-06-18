<?php
namespace NokautWL\Routing;

use Nokaut\ApiKit\Entity\Product;

class Anchor
{
    /**
     * @param string $offerUrl
     * @return string
     */
    public static function offerAnchorAttributes($offerUrl)
    {
        return 'href="http://nokaut.click' . $offerUrl . '" target="_blank" rel="nofollow"';
    }

    /**
     * @param string $productUrl
     * @return string
     */
    public static function productAnchorAttributes($productUrl)
    {
        return 'href="' . ProductsUrl::productUrl($productUrl) . '" rel="follow"';
    }

    /**
     * @param Product $product
     * @return string
     */
    public static function productDefaultAnchorAttributes(Product $product)
    {
        if ($product->getClickUrl()) {
            return self::offerAnchorAttributes($product->getClickUrl());
        } else {
            return self::productAnchorAttributes($product->getUrl());
        }
    }
}