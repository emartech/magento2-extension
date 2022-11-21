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
     * @return string
     */
    public function getImage(): string;

    /**
     * GetSmallImage
     *
     * @return string
     */
    public function getSmallImage(): string;

    /**
     * GetThumbnail
     *
     * @return string
     */
    public function getThumbnail(): string;

    /**
     * SetImage
     *
     * @param string $image
     *
     * @return \Emartech\Emarsys\Api\Data\ImagesInterface
     */
    public function setImage(string $image): ImagesInterface;

    /**
     * SetSmallImage
     *
     * @param string $smallImage
     *
     * @return \Emartech\Emarsys\Api\Data\ImagesInterface
     */
    public function setSmallImage(string $smallImage): ImagesInterface;

    /**
     * SetThumbnail
     *
     * @param string $thumbnail
     *
     * @return \Emartech\Emarsys\Api\Data\ImagesInterface
     */
    public function setThumbnail(string $thumbnail): ImagesInterface;
}
