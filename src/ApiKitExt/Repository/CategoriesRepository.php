<?php

namespace ApiKitExt\Repository;

use Nokaut\ApiKit\ClientApi\Rest\Fetch\CategoriesFetch;
use Nokaut\ApiKit\ClientApi\Rest\Fetch\CategoryFetch;
use Nokaut\ApiKit\ClientApi\Rest\Query\CategoriesQuery;
use Nokaut\ApiKit\ClientApi\Rest\Query\CategoryQuery;
use Nokaut\ApiKit\ClientApi\Rest\Query\Filter;

class CategoriesRepository extends \Nokaut\ApiKit\Repository\CategoriesRepository
{
    protected static $fieldsHeader = array(
        'id',
        'title',
        'url'
    );

    /**
     * @param $phrase
     * @return \Nokaut\ApiKit\Collection\Categories
     */
    public function fetchHeaderCategoriesByTitlePhrase($phrase)
    {
        $query = new CategoriesQuery($this->apiBaseUrl);
        $query->setFields(self::$fieldsHeader);
        $query->setTitleLike($phrase);

        $fetch = new CategoriesFetch($query, $this->cache);
        $this->clientApi->send($fetch);
        return $fetch->getResult();
    }

    /**
     * @param $categoryId
     * @return \Nokaut\ApiKit\Entity\Category
     */
    public function fetchHeaderCategoryById($categoryId)
    {
        $query = new CategoryQuery($this->apiBaseUrl);
        $query->setFields(self::$fieldsHeader);
        $query->setId($categoryId);

        $fetch = new CategoryFetch($query, $this->cache);
        $this->clientApi->send($fetch);
        return $fetch->getResult();
    }
}