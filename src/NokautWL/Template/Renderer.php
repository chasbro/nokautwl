<?php
namespace NokautWL\Template;

class Renderer
{
    /**
     * @var \Twig_Environment
     */
    private static $twig;

    /**
     * @var array()
     */
    private static $templateBasePaths = array();

    /**
     * @param \Twig_Environment $twig
     */
    private static function setTwig($twig)
    {
        self::$twig = $twig;
    }

    /**
     * @return \Twig_Environment
     */
    private static function getTwig()
    {
        return self::$twig;
    }

    /**
     * @param array $templateBasePaths
     */
    public static function setTemplateBasePaths($templateBasePaths)
    {
        if (!is_array($templateBasePaths)) {
            $templateBasePaths = array($templateBasePaths);
        }

        self::$templateBasePaths = $templateBasePaths;
    }

    /**
     * @return array
     */
    private static function getTemplateBasePaths()
    {
        return self::$templateBasePaths;
    }

    /**
     * @param string $template
     * @param array $context
     * @return string
     */
    public static function render($template, $context)
    {
        if (!self::getTwig()) {
            $loader = new \Twig_Loader_Filesystem(self::getTemplateBasePaths());
            self::setTwig(new \Twig_Environment($loader));

            self::getTwig()->addFilter(new \Twig_SimpleFilter('photoUrl', array('NokautWL\\PhotoUrl', 'getPhotoUrl')));
            self::getTwig()->addFilter(new \Twig_SimpleFilter('shopLogoUrl', array('NokautWL\\PhotoUrl', 'getShopLogoUrl')));
            self::getTwig()->addFilter(new \Twig_SimpleFilter('offerAnchorAttributes', array('NokautWL\\Routing\\Anchor', 'offerAnchorAttributes')));
            self::getTwig()->addFilter(new \Twig_SimpleFilter('productAnchorAttributes', array('NokautWL\\Routing\\Anchor', 'productAnchorAttributes')));
            self::getTwig()->addFilter(new \Twig_SimpleFilter('productDefaultAnchorAttributes', array('NokautWL\\Routing\\Anchor', 'productDefaultAnchorAttributes')));
            self::getTwig()->addFilter(new \Twig_SimpleFilter('productsUrl', array('NokautWL\\Routing\\ProductsUrl', 'productsUrl')));
            self::getTwig()->addFilter(new \Twig_SimpleFilter('showProductsCategoryFacet', array('NokautWL\\Routing\\ProductsUrl', 'showProductsCategoryFacet')));
            self::getTwig()->addFilter(new \Twig_SimpleFilter('showProductsCategoryFilter', array('NokautWL\\Routing\\ProductsUrl', 'showProductsCategoryFilter')));
            self::getTwig()->addFilter(new \Twig_SimpleFilter('shortTitle', array('NokautWL\\Template\\Helper', 'shortTitle')));
            self::getTwig()->addFilter(new \Twig_SimpleFilter('price', array('NokautWL\\Template\\Helper', 'price')));
        }

        $template = self::getTwig()->loadTemplate($template);
        return $template->render($context);
    }
}