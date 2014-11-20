<?php
namespace NokautWL\Template;

class Helper
{
    /**
     * @param string $title
     * @param int $length
     * @return string
     */
    public static function shortTitle($title, $length = 80)
    {
        if (strlen($title) > $length) {
            $title = substr($title, 0, $length) . '...';
        }

        return $title;
    }

    /**
     * @param float $price
     * @return string
     */
    public static function price($price)
    {
        if ($price != (int)$price) {
            $price = sprintf("%01.2f", $price);
        }

        return str_replace(".", ",", $price);
    }
}