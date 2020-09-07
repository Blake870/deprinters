<?php

namespace Swissup\EasySlide\Model\DataProviders;

class ImageUploadConfig
{
    /**
     * @var boolean
     */
    protected $isResizeEnabled = false;

    /**
     * Get slide image resize configuration
     *
     * @return int
     */
    public function getIsResizeEnabled()
    {
        return (int)$this->isResizeEnabled;
    }
}
