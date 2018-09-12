<?php

namespace Emartech\Emarsys\Model\Data;

use Magento\Framework\DataObject;

use Emartech\Emarsys\Api\Data\ImagesInterface;

/**
 * Class Images
 * @package Emartech\Emarsys\Model\Data
 */
class Images extends DataObject implements ImagesInterface
{
    /**
     * @return string
     */
    public function getImage(): string
    {
        return $this->getData(self::IMAGE_KEY);
    }

    /**
     * @return string
     */
    public function getSmallImage(): string
    {
        return $this->getData(self::SMALL_IMAGE_KEY);
    }

    /**
     * @return string
     */
    public function getThumbnail(): string
    {
        return $this->getData(self::THUMBNAIL_KEY);
    }

    /**
     * @param string $image
     *
     * @return $this
     */
    public function setImage($image): ImagesInterface
    {
        $this->setData(self::IMAGE_KEY, $image);

        return $this;
    }

    /**
     * @param string $smallImage
     *
     * @return $this
     */
    public function setSmallImage($smallImage): ImagesInterface
    {
        $this->setData(self::SMALL_IMAGE_KEY, $smallImage);

        return $this;
    }

    /**
     * @param string $thumbnail
     *
     * @return $this
     */
    public function setThumbnail($thumbnail): ImagesInterface
    {
        $this->setData(self::THUMBNAIL_KEY, $thumbnail);

        return $this;
    }
}
