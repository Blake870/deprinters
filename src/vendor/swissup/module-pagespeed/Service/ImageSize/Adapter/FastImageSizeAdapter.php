<?php

namespace Swissup\Pagespeed\Service\ImageSize\Adapter;

use FastImageSize\FastImageSize;
use Swissup\Pagespeed\Service\ImageSize\Adapter\ImageSizeInterface;

class FastImageSizeAdapter implements ImageSizeInterface
{
    /**
     * @url https://github.com/marc1706/fast-image-size
     * @var \FastImageSize\FastImageSiz\FastImageSize\FastImageSize
     */
    private $fastImageSize;

    /**
     * @param FastImageSize $fastImageSize
     */
    public function __construct(FastImageSize $fastImageSize)
    {
        $this->fastImageSize = $fastImageSize;
    }

    /**
     * Get image dimensions of supplied image
     *
     * @inherit
     */
    public function getDimensions($path)
    {
        return $this->fastImageSize->getImageSize($path);
    }
}
