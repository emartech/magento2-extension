<?php

namespace Emartech\Emarsys\Api\Data;

interface ImagesInterface
{
    public const IMAGE_KEY       = 'image';
    public const SMALL_IMAGE_KEY = 'small_image';
    public const THUMBNAIL_KEY   = 'thumbnail';

    /**
     * GetImage
     *
     * @return string|null
     */
    public function getImage(): ?string;

    /**
     * GetSmallImage
     *
     * @return string|null
     */
    public function getSmallImage(): ?string;

    /**
     * GetThumbnail
     *
     * @return string|null
     */
    public function getThumbnail(): ?string;

    /**
     * SetImage
     *
     * @param string|null $image
     *
     * @return \Emartech\Emarsys\Api\Data\ImagesInterface
     */
    public function setImage(?string $image = null): ImagesInterface;

    /**
     * SetSmallImage
     *
     * @param string|null $smallImage
     *
     * @return \Emartech\Emarsys\Api\Data\ImagesInterface
     */
    public function setSmallImage(?string $smallImage = null): ImagesInterface;

    /**
     * SetThumbnail
     *
     * @param string|null $thumbnail
     *
     * @return \Emartech\Emarsys\Api\Data\ImagesInterface
     */
    public function setThumbnail(?string $thumbnail = null): ImagesInterface;
}
