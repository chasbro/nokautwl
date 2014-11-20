<?php
namespace NokautWL;

class PhotoUrl
{
    const IMG_BASE_URL = 'http://img.nokaut.pl/';
    const IMG_SIZE500x500 = '500x500';
    const IMG_SIZE130x130 = '130x130';

    /**
     * @param string $shopUrlLogo
     * @return string
     */
    public static function getShopLogoUrl($shopUrlLogo)
    {
        return self::IMG_BASE_URL . ltrim($shopUrlLogo, "/");
    }

    /**
     * @param string $photoId
     * @param string $size
     * @param string $additionalUrlPart
     * @return string
     */
    public static function getPhotoUrl($photoId, $size = self::IMG_SIZE130x130, $additionalUrlPart = '')
    {
        return self::IMG_BASE_URL . ltrim(\Nokaut\ApiKit\Helper\PhotoUrl::prepare($photoId, $size, $additionalUrlPart), "/");
    }
}