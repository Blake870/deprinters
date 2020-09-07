<?php

namespace Swissup\SeoImages\Model;

use Magento\Framework\Exception\RuntimeException;

class NameGenerator
{
    /**
     * @var \Swissup\SeoImages\Model\EntityFactory
     */
    protected $seoImageFactory;

    /**
     * @var \Swissup\SeoImages\Model\Filter\Product
     */
    protected $processor;

    /**
     * @var string[]
     */
    protected $names = [];

    /**
     * @param \Swissup\SeoImages\Model\EntityFactory  $seoImageFactory
     * @param \Swissup\SeoImages\Model\Filter\Product $processor
     */
    public function __construct(
        \Swissup\SeoImages\Model\EntityFactory $seoImageFactory,
        \Swissup\SeoImages\Model\Filter\Product $processor,
        \Swissup\SeoImages\Helper\Data $helper
    ) {
        $this->seoImageFactory = $seoImageFactory;
        $this->processor = $processor;
        $this->helper = $helper;
    }

    /**
     * Generate file name for product.
     *
     * @param  \Magento\Framework\DataObject $product
     * @param  string                        $originalFile
     * @return string
     */
    public function generate(
        \Magento\Framework\DataObject $product,
        $originalFile
    ) {
        if (!isset($this->names[$originalFile][$product->getId()])) {
            $file = '';
            if ($name = $this->processTemplate($product)) {
                $i = 0;
                $extension = $this->getFileExtension($originalFile);
                // Figure out new name for SEO image
                do {
                    $file = '/' . $name . ($i ? "-{$i}" : '') . '.' . $extension;
                    $i++;
                    if ($i > 100) {
                        throw new RuntimeException(__('Looks like Magento went into infinit loop... Product ID - %1. Image - %2', $product->getId(), $originalFile));
                    }

                    $seoImage = $this->seoImageFactory->create()
                        ->load($file, 'target_file');
                } while ($seoImage->getFileKey()
                    && $seoImage->getOriginalFile() !== $originalFile
                );
            }

            $this->names[$originalFile][$product->getId()] = $file;
        }

        return $this->names[$originalFile][$product->getId()];
    }

    /**
     * Get file extension.
     *
     * @param  string $fileName
     * @return string
     */
    public function getFileExtension($fileName)
    {
        return pathinfo($fileName, PATHINFO_EXTENSION);
    }

    /**
     * Process template of file name.
     *
     * @param  \Magento\Framework\DataObject $product
     * @return string
     */
    protected function processTemplate(
        \Magento\Framework\DataObject $product
    ) {
        $template = $this->helper->getSeoImageNameTemplate();
        $result = $this->processor->setScope($product)->filter($template);
        return ltrim($result, '/');
    }
}
