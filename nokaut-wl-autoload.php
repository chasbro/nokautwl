<?php
/**
 * Autoloaders for plugin and composer
 */
require_once dirname(__FILE__) . '/vendor/autoload.php';

function nokautwl_autoloader_namespace($class)
{
    if (preg_match("/^(ApiKitExt|NokautWL)/", $class)) {
        $class = ltrim($class, '\\');
        require_once 'src/' . str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';
    }
}

spl_autoload_register('nokautwl_autoloader_namespace');