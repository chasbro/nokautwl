<?php
namespace NokautWL\View\Products\ShortCode;

use NokautWL\Admin\Options;
use NokautWL\ApiKitFactory;
use NokautWL\Routing\Routing;
use NokautWL\Template\Renderer;

class ProductsBox
{
    const DEFAULT_PRODUCTS_LIMIT = 20;
    const DEFAULT_PRODUCTS_COLUMNS = 2;

    /**
     * @param $productsUrl
     * @param int $limit
     * @param int $columns
     * @internal param string $productUrl
     * @return string
     */
    public static function render($productsUrl, $limit = self::DEFAULT_PRODUCTS_LIMIT, $columns = self::DEFAULT_PRODUCTS_COLUMNS)
    {
        try {
            if (!$limit || !is_numeric($limit) || $limit < 0) {
                $limit = self::DEFAULT_PRODUCTS_LIMIT;
            }

            if (!$columns || $columns < 0 || !in_array($columns, array(1, 2, 3, 4, 6, 12))) {
                $columns = self::DEFAULT_PRODUCTS_COLUMNS;
            }

            if ($productsUrl) {
                $productsNokautUrl = Routing::getNokautProductsUrlFromWpUrl($productsUrl);
            } else {
                $productsNokautUrl = Routing::getProductsQuery();
                // doklejamy do urla informacje o porownywarce
                $productsUrl .= Options::getOption(Options::OPTION_URL_PART_PRODUCTS_PAGE);
            }

            if (!$productsNokautUrl) {
                throw new \Exception("No products url");
            }

            $apiKit = ApiKitFactory::getApiKit();
            $productRepository = $apiKit->getProductsRepository();
            $products = $productRepository->fetchProductsByUrl($productsNokautUrl, $productRepository::$fieldsForList, $limit);

            if (!count($products)) {
                throw new \Exception("Products not found for " . $productsNokautUrl);
            }

            return Renderer::render('products/short-code/products-box.twig',
                array('products' => $products, 'productsUrl' => $productsUrl, 'limit' => $limit, 'columns' => $columns)
            );
        } catch (\Exception $e) {
            error_log($e->getMessage() . ' in file ' . $e->getFile() . ':' . $e->getLine());
            return '';
        }
    }
}