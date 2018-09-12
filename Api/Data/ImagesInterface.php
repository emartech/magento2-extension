<?php

namespace Emartech\Emarsys\Api\Data;

/**
 * Interface ImagesInterface
 * @package Emartech\Emarsys\Api\Data
 */
interface ImagesInterface
{
    const IMAGE_KEY       = 'image';
    const SMALL_IMAGE_KEY = 'small_image';
    const THUMBNAIL_KEY   = 'thumbnail';

    /**
     * @return string
     */
    public function getImage(): string;

    /**
     * @return string
     */
    public function getSmallImage(): string;

    /**
     * @return string
     */
    public function getThumbnail(): string;

    /**
     * @param string $image
     *
     * @return $this
     */
    public function setImage($image): ImagesInterface;

    /**
     * @param string $smallImage
     *
     * @return $this
     */
    public function setSmallImage($smallImage): ImagesInterface;

    /**
     * @param string $thumbnail
     *
     * @return $this
     */
    public function setThumbnail($thumbnail): ImagesInterface;
}
