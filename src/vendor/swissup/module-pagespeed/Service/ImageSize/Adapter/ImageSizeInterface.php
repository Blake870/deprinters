<?php

namespace Swissup\Pagespeed\Service\ImageSize\Adapter;

interface ImageSizeInterface
{
    /**
     * Get image dimensions of supplied image
     *
     * @param string $file Path to image that should be checked
     * @param string $type Mimetype of image
     * @return array|bool Array with image dimensions if successful, false if not
     */
    public function getDimensions($path);
}
