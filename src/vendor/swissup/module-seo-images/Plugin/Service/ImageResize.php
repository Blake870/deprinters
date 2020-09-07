<?php

namespace Swissup\SeoImages\Plugin\Service;

class ImageResize
{
    /**
     * @var \Swissup\SeoImages\Model\EntityFactory
     */
    private $seoImageFactory;

    /**
     * @var \Swissup\SeoImages\Helper\Data
     */
    private $helper;

    /**
     * @param \Swissup\SeoImages\Helper\Data         $helper
     * @param \Swissup\SeoImages\Model\EntityFactory $seoImageFactory
     */
    public function __construct(
        \Swissup\SeoImages\Helper\Data $helper,
        \Swissup\SeoImages\Model\EntityFactory $seoImageFactory
    ) {
        $this->helper = $helper;
        $this->seoImageFactory = $seoImageFactory;
    }

    /**
     * Before plugin to replace file key with target file. It is enoght to
     * generate image with requested name.
     * \Swissup\SeoImages\Model\Product\Media\Config will do the rest.
     *
     * @param  \Magento\MediaStorage\Service\ImageResize $subject
     * @param  string                                    $originalImageName
     * @return string
     */
    public function beforeResizeFromImageName(
        \Magento\MediaStorage\Service\ImageResize $subject,
        $originalImageName
    ) {
        if (!$this->helper->isEnabled()) {
            return null;
        }

        $seoImage = $this->seoImageFactory->create()
            ->load(urldecode($originalImageName), 'file_key');

        return $seoImage->getTargetFile() ? [$seoImage->getTargetFile()] : null;
    }
}
