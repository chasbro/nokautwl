<?php

namespace ApiKitExt;

use Nokaut\ApiKit\ApiKit;
use Nokaut\ApiKit\Config;
use ApiKitExt\Repository\CategoriesRepository;

class ApiKitExt extends ApiKit
{

    public function __construct(Config $config)
    {
        parent::__construct($config);
    }

    /**
     * @param Config $config
     * @return CategoriesRepository
     */
    public function getCategoriesRepository(Config $config = null)
    {
        if (!$config) {
            $config = $this->config;
        }
        $this->validate($config);

        $restClientApi = $this->getClientApi($config);

        return new CategoriesRepository($config, $restClientApi);
    }
}