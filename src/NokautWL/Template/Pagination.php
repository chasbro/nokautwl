<?php
namespace NokautWL\Template;

class Pagination
{
    /**
     * @var int
     */
    protected $currentPage = 1;
    /**
     * @var int
     */
    protected $limit = 20;
    /**
     * @var int
     */
    protected $total;
    /**
     * @var int
     */
    protected $delta = 5;

    /**
     * @var string
     */
    protected $url_template;

    /**
     * @param string $url_template
     */
    public function setUrlTemplate($url_template)
    {
        $this->url_template = $url_template;
    }

    /**
     * @return string
     */
    public function getUrlTemplate()
    {
        return $this->url_template;
    }


    /**
     * @param $page
     * @return string
     */
    public function getUrl($page)
    {
        return sprintf($this->getUrlTemplate(), $page);
    }

    /**
     * @param int $limit
     */
    public function setLimit($limit)
    {
        $this->limit = $limit;
    }

    /**
     * @return int
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * @param int $pageNumber
     */
    public function setCurrentPage($pageNumber)
    {
        $this->currentPage = $pageNumber;
    }

    /**
     * @return int
     */
    public function getCurrentPage()
    {
        return $this->currentPage;
    }

    /**
     * @return int
     */
    public function getPreviousPage()
    {
        return max(1, $this->getCurrentPage() - 1);
    }

    /**
     * @return int
     */
    public function getNextPage()
    {
        return min($this->getTotal(), $this->getCurrentPage() + 1);
    }

    /**
     * @param int $total
     */
    public function setTotal($total)
    {
        $this->total = $total;
    }

    /**
     * @return int
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * @return int
     */
    public function getDelta()
    {
        return $this->delta;
    }

    /**
     * @param int $delta
     */
    public function setDelta($delta)
    {
        $this->delta = $delta;
    }

    /**
     * @return int
     */
    public function calculateOffset()
    {
        if ($this->currentPage == 1) {
            return 0;
        }
        return ($this->currentPage - 1) * $this->limit;
    }

    public function isFirstPage()
    {
        if ($this->currentPage == 1) {
            return true;
        }
        return false;
    }

    public function getLastPageNum()
    {
        return ceil($this->total / $this->limit);
    }

    public function isLastPage()
    {
        return $this->getLastPageNum() == $this->currentPage;
    }

    /**
     * @return array
     */
    public function getDeltaPagesNum()
    {
        $middleNumber = $this->calculateMiddleNumber();
        $lastPageNum = $this->getLastPageNum();

        if (($this->currentPage - $middleNumber < 0) || ($this->delta >= $lastPageNum)) {
            return $this->getPutOrderPageNumbers(1);
        }
        if ($this->currentPage + $middleNumber > $lastPageNum) {
            return $this->getPutOrderPageNumbers($lastPageNum - $this->delta + 1);
        }

        return $this->getPutOrderPageNumbers($this->currentPage - $middleNumber + 1);
    }

    /**
     * @param int $numberFrom
     * @return array
     */
    protected function getPutOrderPageNumbers($numberFrom)
    {
        $result = array();

        $increasesLimit = min($this->delta, $this->getLastPageNum());
        for ($i = 0; $i < $increasesLimit; ++$i) {
            $result[] = $numberFrom + $i;
        }

        return $result;
    }

    protected function calculateMiddleNumber()
    {
        $half = $this->delta / 2;
        if ($this->delta % 2) {
            return floor($half) + 1;
        }
        return $half;
    }

    public function showLastPage()
    {
        $lastPageNum = $this->getLastPageNum();
        return $this->currentPage <= ($lastPageNum - $this->calculateMiddleNumber()) && $this->delta < $lastPageNum;
    }

    public function showFirstPage()
    {
        $lastPageNum = $this->getLastPageNum();
        return $this->currentPage > $this->calculateMiddleNumber() && $this->delta < $lastPageNum;
    }
}