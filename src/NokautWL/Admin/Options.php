<?php
namespace NokautWL\Admin;

use Nokaut\ApiKit\ClientApi\Rest\Query\ProductsQuery;
use NokautWL\ApiKitFactory;

class Options
{
    const OPTIONS_GROUP = 'nokautwl_options';
    const OPTIONS_SECTION_API = 'nokautwl_options_section_api';
    const OPTIONS_SECTION_TEST_API_CONNECTION = 'nokautwl_options_section_test_api_connection';
    const OPTIONS_SECTION_CATEGORIES = 'nokautwl_options_section_categories';
    const OPTIONS_SECTION_URL = 'nokautwl_options_section_url';
    const OPTION_APIKEY = 'nokautwl_apikey';
    const OPTION_APIURL = 'nokautwl_apiurl';
    const OPTION_CATEGORIES_KEY_PREFIX = 'wp_cat_';
    const OPTION_CATEGORIES = 'nokautwl_categories';
    const OPTION_URL_PART_PRODUCT_PAGE = 'nokautwl_url_part_product_page';
    const OPTION_URL_PART_PRODUCTS_PAGE = 'nokautwl_url_part_products_page';

    private static $optionsDefault = array(
        self::OPTION_APIKEY => '',
        self::OPTION_APIURL => '',
        self::OPTION_CATEGORIES => array(),
        self::OPTION_URL_PART_PRODUCT_PAGE => 'produkt',
        self::OPTION_URL_PART_PRODUCTS_PAGE => 'produkty',
    );

    public static function init()
    {
        register_setting(self::OPTIONS_GROUP, self::OPTIONS_GROUP, array(__CLASS__, 'validate'));

        add_settings_section(self::OPTIONS_SECTION_API, 'Dostęp do API', array(__CLASS__, 'sectionApiText'),
            Admin::NOKAUTWL_CONFIG_PAGE_UNIQUE_KEY);

        add_settings_field(self::OPTION_APIKEY, 'API KEY', array(__CLASS__, 'apiKeyInputText'),
            Admin::NOKAUTWL_CONFIG_PAGE_UNIQUE_KEY, self::OPTIONS_SECTION_API);

        add_settings_field(self::OPTION_APIURL, 'API URL', array(__CLASS__, 'apiUrlInputText'),
            Admin::NOKAUTWL_CONFIG_PAGE_UNIQUE_KEY, self::OPTIONS_SECTION_API);

        add_settings_section(self::OPTIONS_SECTION_TEST_API_CONNECTION, 'Test komunikacji z API', array(__CLASS__, 'sectionTestApiConnectionText'),
            Admin::NOKAUTWL_CONFIG_PAGE_UNIQUE_KEY);

        add_settings_section(self::OPTIONS_SECTION_URL, 'Adresy URL', array(__CLASS__, 'sectionUrlText'),
            Admin::NOKAUTWL_CONFIG_PAGE_UNIQUE_KEY);

        add_settings_field(self::OPTION_URL_PART_PRODUCTS_PAGE, 'Strona produktów kategorii', array(__CLASS__, 'urlPartProductsPageInputText'),
            Admin::NOKAUTWL_CONFIG_PAGE_UNIQUE_KEY, self::OPTIONS_SECTION_URL);

        add_settings_field(self::OPTION_URL_PART_PRODUCT_PAGE, 'Strona produktu', array(__CLASS__, 'urlPartProductPageInputText'),
            Admin::NOKAUTWL_CONFIG_PAGE_UNIQUE_KEY, self::OPTIONS_SECTION_URL);

        add_settings_section(self::OPTIONS_SECTION_CATEGORIES, 'Mapowanie kategorii', array(__CLASS__, 'categoriesSectionText'),
            Admin::NOKAUTWL_CONFIG_PAGE_UNIQUE_KEY);

        $categories = get_categories();
        foreach ($categories as $category) {
            add_settings_field(self::OPTION_CATEGORIES . $category->slug, $category->slug,
                function () use ($category) {
                    Options::nokautCategoriesInputSelect($category);
                },
                Admin::NOKAUTWL_CONFIG_PAGE_UNIQUE_KEY, self::OPTIONS_SECTION_CATEGORIES);
        }

    }

    public static function sectionApiText()
    {
        echo '<p>Wprowadź klucz dostępowy API oraz bazowy adres URL API (kończacy się slash\'em "/", baza do wywołania zasobów: products, offers, categories).</p>';
    }

    public static function sectionUrlText()
    {
        echo '<p>Skonfiguruj wygląd adresów URL strony produktów kategorii i strony produktu.</p>';
    }

    public static function categoriesSectionText()
    {
        echo '<p>Przypisz kategorie z serwisu Nokaut.pl do kategorii Twojego bloga. Możesz wybrać kategorie liście lub wyższe węzły z drzewa kategorii <a href="http://www.nokaut.pl/">nokaut.pl</a>. Kategorie bloga bez artykułów nie są widoczne.</p>';
    }

    public static function apiKeyInputText()
    {
        $value = self::getOption(self::OPTION_APIKEY);
        echo "<input id='" . self::OPTION_APIKEY . "' name='nokautwl_options[" . self::OPTION_APIKEY . "]' size='95' type='text' value='{$value}' />";
    }

    public static function apiUrlInputText()
    {
        $value = self::getOption(self::OPTION_APIURL);
        echo "<input id='" . self::OPTION_APIURL . "' name='nokautwl_options[" . self::OPTION_APIURL . "]' size='95' type='text' value='{$value}' />";
    }

    public static function sectionTestApiConnectionText()
    {
        try {
            error_reporting(E_ALL);
            ini_set('display_errors', 'On');
            $apiKit = ApiKitFactory::getApiKitInDebugMode();
            $productsRepository = $apiKit->getProductsRepository();
            $productsRepository->fetchProductsByCategory(array(127), 1, array('id'));
        } catch (\Exception $e) {
            echo '<p>' . $e->getMessage() . '</p>';
        }
    }

    public static function urlPartProductPageInputText()
    {
        $value = self::getOption(self::OPTION_URL_PART_PRODUCT_PAGE);
        echo "<input id='" . self::OPTION_URL_PART_PRODUCT_PAGE . "' name='nokautwl_options[" . self::OPTION_URL_PART_PRODUCT_PAGE . "]' size='95' type='text' value='{$value}' />";
    }

    public static function urlPartProductsPageInputText()
    {
        $value = self::getOption(self::OPTION_URL_PART_PRODUCTS_PAGE);
        echo "<input id='" . self::OPTION_URL_PART_PRODUCTS_PAGE . "' name='nokautwl_options[" . self::OPTION_URL_PART_PRODUCTS_PAGE . "]' size='95' type='text' value='{$value}' />";
    }

    public static function nokautCategoriesInputSelect($category)
    {
        $optionsCategories = self::getOption(self::OPTION_CATEGORIES);
        if (!isset($optionsCategories[self::OPTION_CATEGORIES_KEY_PREFIX . $category->cat_ID])) {
            $optionsCategories[self::OPTION_CATEGORIES_KEY_PREFIX . $category->cat_ID] = '';
        }
        echo "<input id='" . self::OPTION_CATEGORIES . "-" . $category->cat_ID . "'
        name='nokautwl_options[" . self::OPTION_CATEGORIES . "][" . self::OPTION_CATEGORIES_KEY_PREFIX . $category->cat_ID . "]' type='text'
        value='{$optionsCategories[self::OPTION_CATEGORIES_KEY_PREFIX . $category->cat_ID]}' />";
    }

    public static function validate($input)
    {
        $input[self::OPTION_URL_PART_PRODUCT_PAGE] = trim($input[self::OPTION_URL_PART_PRODUCT_PAGE]);
        $input[self::OPTION_URL_PART_PRODUCT_PAGE] = trim($input[self::OPTION_URL_PART_PRODUCT_PAGE], "/");
        $input[self::OPTION_URL_PART_PRODUCT_PAGE] = trim($input[self::OPTION_URL_PART_PRODUCT_PAGE]);

        $input[self::OPTION_URL_PART_PRODUCTS_PAGE] = trim($input[self::OPTION_URL_PART_PRODUCTS_PAGE]);
        $input[self::OPTION_URL_PART_PRODUCTS_PAGE] = trim($input[self::OPTION_URL_PART_PRODUCTS_PAGE], "/");
        $input[self::OPTION_URL_PART_PRODUCTS_PAGE] = trim($input[self::OPTION_URL_PART_PRODUCTS_PAGE]);

        $options = $input;
        return $options;
    }

    public static function form()
    {
        echo '<form method="post" action="options.php"> ';
        settings_fields(self::OPTIONS_GROUP);
        do_settings_sections(Admin::NOKAUTWL_CONFIG_PAGE_UNIQUE_KEY);
        submit_button();
        echo '</form>';
    }

    /**
     * @param string $key
     * @return mixed
     */
    public static function getOption($key)
    {
        $options = get_option(self::OPTIONS_GROUP);
        if (isset($options[$key])) {
            return $options[$key];
        } elseif (isset(self::$optionsDefault[$key])) {
            return self::$optionsDefault[$key];
        }
    }

    /**
     * @param int $nokautCategoryId
     * @return int
     */
    public static function getWordpressCategoryId($nokautCategoryId)
    {
        $categorySettings = self::getOption(self::OPTION_CATEGORIES);
        foreach (array_keys($categorySettings) as $categorySettingsKey) {
            $nokautCategoryIds = array_keys(self::getNokautCategoryData($categorySettingsKey));
            if (in_array($nokautCategoryId, $nokautCategoryIds)) {
                return (int)preg_replace("/" . self::OPTION_CATEGORIES_KEY_PREFIX . "/", '', $categorySettingsKey);
            }
        }
    }

    /**
     * @param int|null $wordpressCategoryId
     * @return array
     */
    public static function getNokautCategoryIds($wordpressCategoryId = null)
    {
        $ids = array_keys(self::getNokautCategoryData($wordpressCategoryId));
        sort($ids);
        return $ids;
    }

    /**
     * @param int|null $wordpressCategoryId
     * @return array
     */
    public static function getNokautCategoryUrls($wordpressCategoryId = null)
    {
        $urls = array_unique(array_values(self::getNokautCategoryData($wordpressCategoryId)));
        sort($urls);
        return $urls;
    }

    /**
     * @param $wordpressCategoryId
     * @return array [category id] => category url
     */
    public static function getNokautCategoryData($wordpressCategoryId)
    {
        $categorySettings = self::getOption(self::OPTION_CATEGORIES);
        $nokautCategoryData = array();

        if ($wordpressCategoryId) {
            if (is_numeric($wordpressCategoryId)) {
                $wordpressCategorySettingsKey = self::OPTION_CATEGORIES_KEY_PREFIX . $wordpressCategoryId;
            } else {
                $wordpressCategorySettingsKey = $wordpressCategoryId;
            }
            if (isset($categorySettings[$wordpressCategorySettingsKey])) {
                $nokautCategoriesDataArray = explode(",", $categorySettings[$wordpressCategorySettingsKey]);
                foreach ($nokautCategoriesDataArray as $nokautCategoryDataTmp) {
                    if (strstr($nokautCategoryDataTmp, ":")) {
                        list($key, $value) = explode(":", $nokautCategoryDataTmp);
                        $nokautCategoryData[$key] = $value;
                    }
                }
            }
        } else {
            foreach (array_keys($categorySettings) as $categorySettingsKey) {
                $nokautCategoryData = $nokautCategoryData + self::getNokautCategoryData($categorySettingsKey);
            }
        }

        return $nokautCategoryData;
    }
}