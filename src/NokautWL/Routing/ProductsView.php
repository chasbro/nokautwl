<?php
namespace NokautWL\Routing;

class ProductsView
{
    const VIEW_KEYWORD = 'view';
    const VIEW_LIST = 'list';
    const VIEW_BOX = 'box';

    /**
     * @var string
     */
    private $view = self::VIEW_BOX;

    /**
     * @param string $view
     */
    public function __construct($view)
    {
        $this->setView($view);
    }

    /**
     * @param string $view
     */
    public function setView($view)
    {
        if (!in_array($view, array(self::VIEW_BOX, self::VIEW_LIST))) {
            $view = self::VIEW_BOX;
        }
        $this->view = $view;
    }

    /**
     * @return string
     */
    private function getView()
    {
        return $this->view;
    }

    /**
     * @return bool
     */
    public function isBoxView()
    {
        return ($this->getView() == self::VIEW_BOX);
    }

    /**
     * @return bool
     */
    public function isListView()
    {
        return ($this->getView() == self::VIEW_LIST);
    }

    /**
     * @return string
     */
    public function getBoxViewUrl()
    {
        return Routing::getUrl() . '?' . self::VIEW_KEYWORD . '=' . self::VIEW_BOX;
    }

    /**
     * @return string
     */
    public function getListViewUrl()
    {
        return Routing::getUrl() . '?' . self::VIEW_KEYWORD . '=' . self::VIEW_LIST;
    }
}