<?php
namespace NokautWL\Routing;

use Nokaut\ApiKit\Ext\Data;
use NokautWL\Admin\Options;

class ProductsUrl
{
    /**
     * @var string
     */
    private static $baseUrl;

    /**
     * @var string
     */
    private static $defaultProductsQuery;

    /**
     * @return string
     */
    private static function getBaseUrl()
    {
        if (!self::$baseUrl) {
            self::$baseUrl = Routing::getBaseUrl();
        }

        return self::$baseUrl;
    }

    /**
     * @return string
     */
    private static function getDefaultProductQuery()
    {
        if (!self::$defaultProductsQuery) {
            self::$defaultProductsQuery = Routing::getDefaultProductQuery();
        }

        return self::$defaultProductsQuery;
    }

    /**
     * @param string $nokautProductsUrl
     * @return string
     */
    public static function productsUrl($nokautProductsUrl)
    {
        // usuwanie domyslnych dla kategorii wordpress kategorii nokaut w url
        $nokautProductsUrl = preg_replace('/' . preg_quote(self::getDefaultProductQuery(), '/') . '/', '', $nokautProductsUrl);

        $productsUrl = self::getBaseUrl() . ltrim($nokautProductsUrl, '/');

        if (isset($_GET[ProductsView::VIEW_KEYWORD])) {
            $productsUrl .= '?' . ProductsView::VIEW_KEYWORD . '=' . $_GET[ProductsView::VIEW_KEYWORD];
        }

        return $productsUrl;
    }

    /**
     * @param $nokautProductUrl
     * @return string
     */
    public static function productUrl($nokautProductUrl)
    {
        return '/' . Options::getOption(Options::OPTION_URL_PART_PRODUCT_PAGE) . '/' . $nokautProductUrl.'.html';
    }

    /**
     * @param Data\Collection\Filters\Categories $filtersCategories
     * @return bool
     */
    public static function showProductsCategoryFacet(Data\Collection\Filters\Categories $filtersCategories)
    {
        if (count(Routing::getNokautCategoryIds()) == 1
            && count($filtersCategories) == 1
            && current(Routing::getNokautCategoryIds()) == $filtersCategories->getLast()->getId()
        ) {
            return false;
        }

        return true;
    }

    /**
     * @param \Nokaut\ApiKit\Ext\Data\Collection\Filters\Categories $filtersSelectedCategories
     * @return bool
     */
    public static function showProductsCategoryFilter(Data\Collection\Filters\Categories $filtersSelectedCategories)
    {
        if (count(Routing::getNokautCategoryIds()) == 1
            && count($filtersSelectedCategories) == 1
            && current(Routing::getNokautCategoryIds()) == $filtersSelectedCategories->getLast()->getId()
        ) {
            return false;
        }

        if (count(Routing::getNokautCategoryIds()) > 1
            and count(Routing::getNokautCategoryIds()) == count($filtersSelectedCategories)
        ) {
            return false;
        }

        foreach ($filtersSelectedCategories as $filter) {
            if (!$filter->getTotal()) {
                return false;
            }
        }

        return true;
    }
}