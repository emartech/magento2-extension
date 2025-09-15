<?php

namespace Emartech\Emarsys\Model\Data;

use Emartech\Emarsys\Api\Data\ImagesInterface;
use Magento\Framework\DataObject;

class Images extends DataObject implements ImagesInterface
{
    /**
     * GetImage
     *
     * @return string|null
     */
    public function getImage(): ?string
    {
        return $this->getData(self::IMAGE_KEY);
    }

    /**
     * GetSmallImage
     *
     * @return string|null
     */
    public function getSmallImage(): ?string
    {
        return $this->getData(self::SMALL_IMAGE_KEY);
    }

    /**
     * GetThumbnail
     *
     * @return string|null
     */
    public function getThumbnail(): ?string
    {
        return $this->getData(self::THUMBNAIL_KEY);
    }

    /**
     * SetImage
     *
     * @param string|null $image
     *
     * @return ImagesInterface
     */
    public function setImage(?string $image = null): ImagesInterface
    {
        $this->setData(self::IMAGE_KEY, $image);

        return $this;
    }

    /**
     * SetSmallImage
     *
     * @param string|null $smallImage
     *
     * @return ImagesInterface
     */
    public function setSmallImage(?string $smallImage = null): ImagesInterface
    {
        $this->setData(self::SMALL_IMAGE_KEY, $smallImage);

        return $this;
    }

    /**
     * SetThumbnail
     *
     * @param string|null $thumbnail
     *
     * @return ImagesInterface
     */
    public function setThumbnail(?string $thumbnail = null): ImagesInterface
    {
        $this->setData(self::THUMBNAIL_KEY, $thumbnail);

        return $this;
    }
}
