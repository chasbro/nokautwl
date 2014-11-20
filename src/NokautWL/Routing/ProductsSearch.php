<?php
namespace NokautWL\Routing;

class ProductsSearch
{
    /**
     * @return string
     */
    public static function getWpCategorySearchUrlTemplate()
    {
        return Routing::getBaseUrl() . 'produkt:%s.html';
    }

    /**
     * @return string
     */
    public static function getWpGlobalSearchUrlTemplate()
    {
        return Routing::getBaseUrlWithoutCategory() . 'produkt:%s.html';
    }
}