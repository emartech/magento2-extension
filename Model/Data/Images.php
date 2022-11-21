<?php

namespace Emartech\Emarsys\Model\Data;

use Emartech\Emarsys\Api\Data\ImagesInterface;
use Magento\Framework\DataObject;

class Images extends DataObject implements ImagesInterface
{
    /**
     * GetImage
     *
     * @return string
     */
    public function getImage(): string
    {
        return (string) $this->getData(self::IMAGE_KEY);
    }

    /**
     * GetSmallImage
     *
     * @return string
     */
    public function getSmallImage(): string
    {
        return (string) $this->getData(self::SMALL_IMAGE_KEY);
    }

    /**
     * GetThumbnail
     *
     * @return string
     */
    public function getThumbnail(): string
    {
        return (string) $this->getData(self::THUMBNAIL_KEY);
    }

    /**
     * SetImage
     *
     * @param string $image
     *
     * @return ImagesInterface
     */
    public function setImage(string $image): ImagesInterface
    {
        $this->setData(self::IMAGE_KEY, $image);

        return $this;
    }

    /**
     * SetSmallImage
     *
     * @param string $smallImage
     *
     * @return ImagesInterface
     */
    public function setSmallImage(string $smallImage): ImagesInterface
    {
        $this->setData(self::SMALL_IMAGE_KEY, $smallImage);

        return $this;
    }

    /**
     * SetThumbnail
     *
     * @param string $thumbnail
     *
     * @return ImagesInterface
     */
    public function setThumbnail(string $thumbnail): ImagesInterface
    {
        $this->setData(self::THUMBNAIL_KEY, $thumbnail);

        return $this;
    }
}
