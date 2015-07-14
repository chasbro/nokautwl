<?php
namespace NokautWL\Routing;

use NokautWL\Admin\Options;

class Routing
{
    const NOKAUT_PRODUCTS_CATEGORY_KEY = 'wlproductscategory';
    const NOKAUT_PRODUCTS_QUERY_KEY = 'wlproductsquery';
    const NOKAUT_PRODUCT_URL_KEY = 'wlproduct';

    public static function init()
    {
        add_action('init', array(__CLASS__, 'flushRewriteRules'));
        add_action('generate_rewrite_rules', array(__CLASS__, 'generateRewriteRules'));
        add_filter('query_vars', array(__CLASS__, 'queryVars'));
        add_action('wp', array(__CLASS__, 'addTitleFilters'));
        add_filter('redirect_canonical', array(__CLASS__, 'redirectCanonical'));
        if (function_exists('rel_canonical')) {
            remove_action('wp_head', 'rel_canonical');
        }
        add_action('wp_head', array(__CLASS__, 'customRelCanonical'));

    }

    /**
     * Flush the rewrite rules, which forces the regeneration with new rules.
     * return void.
     **/
    public static function flushRewriteRules()
    {
        global $wp_rewrite;
        $wp_rewrite->flush_rules();
    }

    /**
     * Generates custom rewrite rules
     *
     * @return array
     */
    public static function generateRewriteRules()
    {
        global $wp_rewrite;

        /**
         * Product page
         */
        $rewrite_tag = '%' . self::NOKAUT_PRODUCT_URL_KEY . '%';
        $wp_rewrite->add_rewrite_tag($rewrite_tag, '(.+?)', self::NOKAUT_PRODUCT_URL_KEY . '=');
        $rewrite_keywords_structure = $wp_rewrite->root . Options::getOption(Options::OPTION_URL_PART_PRODUCT_PAGE) . "/$rewrite_tag";
        $new_rule = $wp_rewrite->generate_rewrite_rules($rewrite_keywords_structure);
        $wp_rewrite->rules = $new_rule + $wp_rewrite->rules;

        /**
         * Products page
         */
        $new_rules = array();
        $category_base = get_option('category_base') ? get_option('category_base') : 'category';
        $productsUrlPart = Options::getOption(Options::OPTION_URL_PART_PRODUCTS_PAGE);

        // category compare page
        $new_rules[$wp_rewrite->root . '(.*)' . $category_base . '/(.*)/' . $productsUrlPart . '/(.*)']
            = 'index.php?' . self::NOKAUT_PRODUCTS_CATEGORY_KEY . '=$matches[2]&' . self::NOKAUT_PRODUCTS_QUERY_KEY . '=$matches[3]';
        $new_rules[$wp_rewrite->root . '(.*)' . $category_base . '/(.*)/' . $productsUrlPart] = 'index.php?' . self::NOKAUT_PRODUCTS_CATEGORY_KEY . '=$matches[2]';

        // main compare page
        $new_rules[$wp_rewrite->root . $productsUrlPart . '/(.*)'] = 'index.php?' . self::NOKAUT_PRODUCTS_CATEGORY_KEY . '=none&' . self::NOKAUT_PRODUCTS_QUERY_KEY . '=$matches[1]';
        $new_rules[$wp_rewrite->root . $productsUrlPart] = 'index.php?' . self::NOKAUT_PRODUCTS_CATEGORY_KEY . '=none';

        $wp_rewrite->rules = $new_rules + $wp_rewrite->rules;

        return $wp_rewrite->rules;
    }

    /**
     * @param array $public_query_vars
     * @return array
     */
    public static function queryVars($public_query_vars)
    {
        $public_query_vars[] = self::NOKAUT_PRODUCT_URL_KEY;
        $public_query_vars[] = self::NOKAUT_PRODUCTS_CATEGORY_KEY;
        $public_query_vars[] = self::NOKAUT_PRODUCTS_QUERY_KEY;

        return $public_query_vars;
    }

    /**
     * @param string $url
     * @return bool
     */
    public static function redirectCanonical($url)
    {
        if (strpos($url, ".html")) {
            return false;
        }
    }

    public static function customRelCanonical()
    {
        if (self::isProductPage()) {
            $canonical = \NokautWL\View\Product\Page::canonical();
        } elseif (self::getWpCategoryPath()) {
            $canonical = \NokautWL\View\Products\Page::canonical();
        } else {
            $canonical = null;
        }

        if ($canonical) {
            echo "<link rel='canonical' href='" . esc_url($canonical) . "' />\n";
        } else {
            rel_canonical();
        }
    }

    /**
     * @return bool
     */
    public static function isProductPage()
    {
        return (self::getNokautProductUrl()) ? true : false;
    }

    /**
     * @return string
     */
    public static function getNokautProductUrl()
    {
        global $wp_query, $wp_rewrite;
        $productUrl = '';

        if ($wp_rewrite->using_permalinks()) {
            if (isset($wp_query->query_vars[self::NOKAUT_PRODUCT_URL_KEY])) {
                $productUrl = $wp_query->query_vars[self::NOKAUT_PRODUCT_URL_KEY];
            }
        } elseif (isset($_GET[self::NOKAUT_PRODUCT_URL_KEY])) {
            $productUrl = $_GET[self::NOKAUT_PRODUCT_URL_KEY];
        }

        $productUrl = str_replace(".html", '', $productUrl);

        return $productUrl;
    }

    /**
     * @param $wpProductsUrl
     * @return string
     */
    public static function getNokautProductUrlFromWpUrl($wpProductsUrl)
    {
        $optionUrlPartProductPage = Options::getOption(Options::OPTION_URL_PART_PRODUCT_PAGE);
        $optionUrlPartProductPage = preg_quote($optionUrlPartProductPage, "/");

        if (preg_match("/.*{$optionUrlPartProductPage}(.*)(\.html)?\/?/", $wpProductsUrl, $matches)) {
            $productUrl = trim($matches[1], '/ ');
            $productUrl = str_replace(".html", '', $productUrl);
        } else {
            $productUrl = '';
        }

        return $productUrl;
    }

    /**
     * @param $wpProductsUrl
     * @return string
     */
    public static function getNokautProductsUrlFromWpUrl($wpProductsUrl)
    {
        $optionUrlPartProductsPage = Options::getOption(Options::OPTION_URL_PART_PRODUCTS_PAGE);
        $optionUrlPartProductsPage = preg_quote($optionUrlPartProductsPage, "/");

        if (preg_match("/.*{$optionUrlPartProductsPage}(.*)(\.html)?\/?/", $wpProductsUrl, $matches)) {
            $productsUrl = trim($matches[1], '/ ');

            if (!preg_match("/.+\/.+/", $productsUrl)) {
                $category_base = get_option('category_base') ? get_option('category_base') : 'category';
                if (preg_match("/{$category_base}\/(.+)\/{$optionUrlPartProductsPage}/", $wpProductsUrl, $matches)) {
                    $wpCategoryPath = $matches[1];
                    $productsUrl = ltrim(self::getDefaultProductQuery($wpCategoryPath), '/') . $productsUrl;
                } else {
                    $productsUrl = ltrim(self::getDefaultProductQuery(), '/') . $productsUrl;
                }
            }
        } else {
            $productsUrl = '';
        }

        return $productsUrl;
    }

    /**
     * @return string
     */
    public static function getWpCategoryPath()
    {
        global $wp_query, $wp_rewrite;
        $wpCategoryPath = '';

        if ($wp_rewrite->using_permalinks()) {
            if (isset($wp_query->query_vars[self::NOKAUT_PRODUCTS_CATEGORY_KEY])) {
                $wpCategoryPath = $wp_query->query_vars[self::NOKAUT_PRODUCTS_CATEGORY_KEY];
            }
        } elseif (isset($_GET[self::NOKAUT_PRODUCTS_CATEGORY_KEY])) {
            $wpCategoryPath = $_GET[self::NOKAUT_PRODUCTS_CATEGORY_KEY];
        }

        return $wpCategoryPath;
    }

    /**
     * @return mixed|string
     */
    public static function getProductsQuery()
    {
        global $wp_query, $wp_rewrite;
        $productsUrl = '';

        if ($wp_rewrite->using_permalinks()) {
            if (isset($wp_query->query_vars[self::NOKAUT_PRODUCTS_QUERY_KEY])) {
                $productsUrl = $wp_query->query_vars[self::NOKAUT_PRODUCTS_QUERY_KEY];
            }
        } elseif (isset($_GET[self::NOKAUT_PRODUCTS_QUERY_KEY])) {
            $productsUrl = $_GET[self::NOKAUT_PRODUCTS_QUERY_KEY];
        }

        if (strstr($productsUrl, ".html")) {
            $productsUrl = str_replace(".html", '', $productsUrl);

            if (!preg_match("/.+\/.+/", $productsUrl)) {
                $productsUrl = self::getDefaultProductQuery() . $productsUrl;
            }
        }

        if (!$productsUrl) {
            $productsUrl = self::getDefaultProductQuery();
        }

        return $productsUrl;
    }

    /**
     * @param bool $wpCategoryPath
     * @return null
     */
    public static function getWpCategory($wpCategoryPath = false)
    {
        $wpCategory = null;

        if (!$wpCategoryPath) {
            $wpCategoryPath = self::getWpCategoryPath();
        }

        if ($wpCategoryPath) {
            $wpCategory = get_category_by_path($wpCategoryPath);
        } else {
            $wpCategory = get_queried_object();
        }

        if ($wpCategory && isset($wpCategory->taxonomy) && $wpCategory->taxonomy == 'post') {
            $wpCategories = get_the_category();
            if (isset($wpCategories[0])) {
                $wpCategory = $wpCategories[0];
            }
        }

        return $wpCategory;
    }

    /**
     * @param bool $wpCategoryPath
     * @return string
     */
    public static function getDefaultProductQuery($wpCategoryPath = false)
    {
        $nokautCategoryUrls = self::getNokautCategoryUrls($wpCategoryPath);

        $productsCategories = array();
        foreach ($nokautCategoryUrls as $url) {
            $productsCategories[] = trim($url, "/");
        }

        if ($productsCategories) {
            return '/' . implode(",", $productsCategories) . '/';
        } else {
            return '';
        }
    }

    /**
     * @param bool $wpCategoryPath
     * @return array
     */
    public static function getNokautCategoryUrls($wpCategoryPath = false)
    {
        $wpCategory = self::getWpCategory($wpCategoryPath);

        if ($wpCategory && $wpCategory->cat_ID) {
            $nokautCategoryUrls = Options::getNokautCategoryUrls($wpCategory->cat_ID);
        } else {
            $nokautCategoryUrls = Options::getNokautCategoryUrls(null);
        }

        return $nokautCategoryUrls;
    }

    /**
     * @param bool $wpCategoryPath
     * @return array
     */
    public static function getNokautCategoryIds($wpCategoryPath = false)
    {
        $wpCategory = self::getWpCategory($wpCategoryPath);

        if ($wpCategory && $wpCategory->cat_ID) {
            $nokautCategoryUrls = Options::getNokautCategoryIds($wpCategory->cat_ID);
        } else {
            $nokautCategoryUrls = Options::getNokautCategoryIds(null);
        }

        return $nokautCategoryUrls;
    }

    /**
     * @return string
     */
    public static function getBaseUrl()
    {
        $wpCategory = self::getWpCategory();

        if (!$wpCategory) {
            return self::getBaseUrlWithoutCategory();
        } else {
            $productsUrlPart = Options::getOption(Options::OPTION_URL_PART_PRODUCTS_PAGE);
            return get_category_link($wpCategory->cat_ID) . $productsUrlPart . '/';
        }
    }

    /**
     * @return string
     */
    public static function getBaseUrlWithoutCategory()
    {
        $productsUrlPart = Options::getOption(Options::OPTION_URL_PART_PRODUCTS_PAGE);

        return '/' . $productsUrlPart . '/';
    }

    public static function getUrl()
    {
        self::getBaseUrl() . self::getProductsQuery();
    }

    /**
     * @param $nokautCategoryId
     * @return array
     */
    public static function getBreadcrumbLinks($nokautCategoryId)
    {
        $separator = '&raquo;';
        $wordpressCategoryId = Options::getWordpressCategoryId($nokautCategoryId);

        if (!$wordpressCategoryId) {
            return array();
        }
        $breadcrumb = explode($separator, get_category_parents($wordpressCategoryId, true, $separator));

        if (!$breadcrumb[count($breadcrumb) - 1]) {
            unset($breadcrumb[count($breadcrumb) - 1]);
        }

        return $breadcrumb;
    }

    public static function addTitleFilters()
    {
        if (self::isProductPage()) {
            add_filter('wp_title', array('NokautWL\\View\\Product\\Page', 'title'), 100, 2);
        } elseif (self::getWpCategoryPath()) {
            add_filter('wp_title', array('NokautWL\\View\\Products\\Page', 'title'), 100, 2);
        }
    }
}