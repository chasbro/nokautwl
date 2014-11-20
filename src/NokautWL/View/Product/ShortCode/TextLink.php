<?php
namespace NokautWL\View\Product\ShortCode;

use NokautWL\ApiKitFactory;
use NokautWL\Routing\Routing;
use NokautWL\Template\Renderer;

class TextLink
{
    const TOOLTIP_OFF = 'off';
    const TOOLTIP_SIMPLE = 'simple';
    const TOOLTIP_ADVANCED = 'advanced';

    /**
     * @param string $productUrl
     * @param string $content
     * @param bool|string $tooltip
     * @return string
     */
    public static function render($productUrl, $content = '', $tooltip = self::TOOLTIP_SIMPLE)
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

            return Renderer::render('product/short-code/text-link.twig', array('product' => $product, 'content' => $content, 'tooltip' => $tooltip));
        } catch (\Exception $e) {
            error_log($e->getMessage() . ' in file ' . $e->getFile() . ':' . $e->getLine());
            return $content;
        }
    }
}