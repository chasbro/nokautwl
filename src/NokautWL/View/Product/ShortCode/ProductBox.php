<?php
namespace NokautWL\View\Product\ShortCode;

use NokautWL\ApiKitFactory;
use NokautWL\Routing\Routing;
use NokautWL\Template\Renderer;

class ProductBox
{
    /**
     * @param string $productUrl
     * @return string
     */
    public static function render($productUrl)
    {
        try {
            $productNokautUrl = Routing::getNokautProductUrlFromWpUrl($productUrl);

            if (!$productNokautUrl) {
                throw new \Exception("No product url");
            }

            $apiKit = ApiKitFactory::getApiKit();
            $productRepository = $apiKit->getProductsRepository();
            $fields = array_merge($productRepository::$fieldsForProductPage, array('prices'));
            $product = $productRepository->fetchProductByUrl($productNokautUrl, $fields);

            if (!$product) {
                throw new \Exception("Product not found for " . $productNokautUrl);
            }

            return Renderer::render('product/short-code/product-box.twig', array('product' => $product));
        } catch (\Exception $e) {
            error_log($e->getMessage() . ' in file ' . $e->getFile() . ':' . $e->getLine());
            return '';
        }
    }
}