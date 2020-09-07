<?php
/**
 * Copyright © 2016-2018 Swissup. All rights reserved.
 */
namespace Swissup\Ajaxpro\Block;

use Magento\Store\Model\ScopeInterface;
use Magento\Framework\View\Asset\File;

class Head extends \Magento\Framework\View\Element\Template
{
    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\View\Asset\GroupedCollection $pageAssets
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\View\Asset\GroupedCollection $pageAssets,
        array $data = []
    ) {
        $files = [
            'mage/gallery/gallery.css',
            'Magento_Swatches::css/swatches.css', // does not exist in magento 2.3
        ];

        $assetRepository = $context->getAssetRepository();
        foreach ($files as $identifier) {
            $asset = $assetRepository->createAsset($identifier);
            try {
                $asset->getSourceFile();
            } catch (\Exception $e) {
                continue;
            }
            $pageAssets->add($identifier, $asset);
        }

        parent::__construct($context, $data);
    }
}
