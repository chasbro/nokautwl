<?php
namespace NokautWL\View\Products;

use Nokaut\ApiKit\Ext\Data;
use Nokaut\ApiKit\Collection\Products;
use NokautWL\ApiKitFactory;
use NokautWL\Routing\ProductsSearch;
use NokautWL\Routing\ProductsUrl;
use NokautWL\Routing\ProductsView;
use NokautWL\Routing\Routing;
use NokautWL\Template\Pagination;
use NokautWL\Template\Renderer;


class Page
{
    public static $products = array();

    /**
     * @return Products
     */
    public static function getProducts()
    {

        $productsQueryWpCategory = Routing::getProductsQuery();

        $key = md5($productsQueryWpCategory);
        if (isset(self::$products[$key])) {
            return self::$products[$key];
        }

        $apiKit = ApiKitFactory::getApiKit();
        $productRepository = $apiKit->getProductsRepository();

        $fields = array_merge($productRepository::$fieldsForList, array(
            '_categories.url_in',
            '_categories.url_out'
        ));

        self::$products[$key] = $productRepository->fetchProductsByUrl($productsQueryWpCategory, $fields);
        return self::$products[$key];
    }

    public static function canonical()
    {
        $products = self::getProducts();

        $canonical = $products->getMetadata()->getCanonical();
        if(!$canonical){
            $canonical = $products->getMetadata()->getUrl();
        }

        if($canonical){
            $canonical = ProductsUrl::productsUrl($canonical);
        }

        return $canonical;
    }

    public static function title()
    {
        $products = self::getProducts();
        $title = '';

        $categories = array();
        foreach ($products->getCategories() as $category) {
            if ($category->getIsFilter() and $category->getTotal()) {
                $categories[] = $category->getName();
            }
        }
        if ($categories) {
            $title .= implode(", ", $categories);
        }

        $shops = array();
        foreach ($products->getShops() as $shop) {
            if ($shop->getIsFilter() and $shop->getTotal()) {
                $shops[] = $shop->getName();
            }
        }
        if ($shops) {
            if (count($shops) == 1) {
                $title .= ' w sklepie ';
            } else {
                $title .= ' w sklepach ';
            }
            $title .= implode(", ", $shops);
        }

        $producers = array();
        foreach ($products->getProducers() as $producer) {
            if ($producer->getIsFilter() and $producer->getTotal()) {
                $producers[] = $producer->getName();
            }
        }
        if ($producers) {
            $title .= " (" . implode(", ", $producers) . ")";
        }

        return $title;
    }

    public static function render()
    {
        $wpCategory = Routing::getWpCategory();
        $products = self::getProducts();

        $pagination = new Pagination();
        $pagination->setCurrentPage($products->getMetadata()->getPaging()->getCurrent());
        $pagination->setUrlTemplate($products->getMetadata()->getPaging()->getUrlTemplate());
        $pagination->setLimit($products->getMetadata()->getQuery()->getLimit());
        $pagination->setTotal($products->getMetadata()->getTotal());

        $filtersCategoriesConverter = new Data\Converter\Filters\CategoriesConverter();
        $filtersCategories = $filtersCategoriesConverter->convert($products, array(
            new Data\Converter\Filters\Callback\Categories\SortByName(),
            new Data\Converter\Filters\Callback\Categories\SetIsNofollow(),
            new Data\Converter\Filters\Callback\Categories\SetIsActive(),
            new Data\Converter\Filters\Callback\Categories\SetIsExcluded(),
        ));

        $filtersSelectedCategoriesConverter = new Data\Converter\Filters\Selected\CategoriesConverter();
        $filtersSelectedCategories = $filtersSelectedCategoriesConverter->convert($products, array(
            new Data\Converter\Filters\Callback\Categories\SetIsNofollow()
        ));

        $filtersProducersConverter = new Data\Converter\Filters\ProducersConverter();
        $filtersProducers = $filtersProducersConverter->convert($products, array(
            new Data\Converter\Filters\Callback\Producers\SortByName(),
            new Data\Converter\Filters\Callback\Producers\SetIsNofollow(),
            new Data\Converter\Filters\Callback\Producers\SetIsActive(),
            new Data\Converter\Filters\Callback\Producers\SetIsExcluded(),
            new Data\Converter\Filters\Callback\Producers\SetIsPopular()
        ));
        $filtersSelectedProducersConverter = new Data\Converter\Filters\Selected\ProducersConverter();
        $filtersSelectedProducers = $filtersSelectedProducersConverter->convert($products, array(
            new Data\Converter\Filters\Callback\Producers\SetIsNofollow()
        ));

        $filtersShopsConverter = new Data\Converter\Filters\ShopsConverter();
        $filtersShops = $filtersShopsConverter->convert($products, array(
            new Data\Converter\Filters\Callback\Shops\SortByName(),
            new Data\Converter\Filters\Callback\Shops\SetIsNofollow(),
            new Data\Converter\Filters\Callback\Shops\SetIsActive(),
            new Data\Converter\Filters\Callback\Shops\SetIsExcluded(),
            new Data\Converter\Filters\Callback\Shops\SetIsPopular()
        ));
        $filtersSelectedShopsConverter = new Data\Converter\Filters\Selected\ShopsConverter();
        $filtersSelectedShops = $filtersSelectedShopsConverter->convert($products, array(
            new Data\Converter\Filters\Callback\Shops\SetIsNofollow()
        ));

        $filtersPriceRangesConverter = new Data\Converter\Filters\PriceRangesConverter();
        $filtersPriceRanges = $filtersPriceRangesConverter->convert($products, array(
            new Data\Converter\Filters\Callback\PriceRanges\SetIsNofollow()
        ));
        $filtersSelectedPriceRangesConverter = new Data\Converter\Filters\Selected\PriceRangesConverter();
        $filtersSelectedPriceRanges = $filtersSelectedPriceRangesConverter->convert($products, array(
                new Data\Converter\Filters\Callback\PriceRanges\SetIsNofollow()
            )
        );

        $filtersPropertiesConverter = new Data\Converter\Filters\PropertiesConverter();
        $filtersProperties = $filtersPropertiesConverter->convert($products, array(
            new Data\Converter\Filters\Callback\Property\SetIsNofollow(),
            new Data\Converter\Filters\Callback\Property\SetIsActive(),
            new Data\Converter\Filters\Callback\Property\SetIsExcluded(),
            new Data\Converter\Filters\Callback\Property\SortDefault()
        ));
        $filtersSelectedPropertiesConverter = new Data\Converter\Filters\Selected\PropertiesConverter();
        $filtersSelectedProperties = $filtersSelectedPropertiesConverter->convert($products, array(
            new Data\Converter\Filters\Callback\Property\SetIsNofollow(),
        ));

        $view = new ProductsView(
            isset($_GET[ProductsView::VIEW_KEYWORD]) ? $_GET[ProductsView::VIEW_KEYWORD] : ''
        );
        $search = new ProductsSearch();

        if (!count($products)) {
            return Renderer::render('products/not-found.twig', array(
                'search' => $search,
                'wordpressCategoryMode' => (bool)$wpCategory
            ));
        }

        return Renderer::render('products/page.twig', array(
            'products' => $products,
            'view' => $view,
            'search' => $search,
            'wordpressCategoryMode' => (bool)$wpCategory,
            'pagination' => $pagination,
            'filtersCategories' => $filtersCategories,
            'filtersSelectedCategories' => $filtersSelectedCategories,
            'filtersProducers' => $filtersProducers,
            'filtersSelectedProducers' => $filtersSelectedProducers,
            'filtersShops' => $filtersShops,
            'filtersSelectedShops' => $filtersSelectedShops,
            'filtersPriceRanges' => $filtersPriceRanges,
            'filtersSelectedPriceRanges' => $filtersSelectedPriceRanges,
            'filtersProperties' => $filtersProperties,
            'filtersSelectedProperties' => $filtersSelectedProperties,
        ));
    }
}