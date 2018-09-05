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
    public function getImage();

    /**
     * @return string
     */
    public function getSmallImage();

    /**
     * @return string
     */
    public function getThumbnail();

    /**
     * @param string $image
     *
     * @return $this
     */
    public function setImage($image);

    /**
     * @param string $smallImage
     *
     * @return $this
     */
    public function setSmallImage($smallImage);

    /**
     * @param string $thumbnail
     *
     * @return $this
     */
    public function setThumbnail($thumbnail);
}
