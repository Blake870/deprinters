<?php

namespace Swissup\RichSnippets\Block;

use Magento\Framework\View\Element\Template;
use Swissup\RichSnippets\Model\Config\Source\StructuredDataFormat;
use Swissup\RichSnippets\Model\Product as ProductData;

class Product extends LdJson
{
    /**
     * @var array
     */
    protected $dataSnippetFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Magento\Catalog\Helper\Image
     */
    protected $_imageHelper;

    /**
     * @param ProductData\OffersFactory          $offersFactory
     * @param ProductData\BrandFactory           $brandFactory
     * @param ProductData\AggregateRatingFactory $aggregateRatingFactory
     * @param \Magento\Framework\Registry        $registry
     * @param \Magento\Catalog\Helper\Image      $imageHelper
     * @param Template\Context                   $context
     * @param array                              $data
     */
    public function __construct(
        \Swissup\RichSnippets\Model\Product\StructuredData $structuredData,
        \Magento\Framework\Registry $registry,
        Template\Context $context,
        array $data = []
    ) {
        $this->structuredData = $structuredData;
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        $dataFormat = $this->getStoreConfig('richsnippets/general/product_format');
        if ($dataFormat != StructuredDataFormat::MICRODATA) {
            // unset microdata attributes added in Magento_Catalog module
            $this->unsetLayoutBlockData('page.main.title', 'add_base_attribute')
                ->unsetLayoutBlockData('product.info.sku', 'add_attribute')
                ->unsetLayoutBlockData('product.info.overview', 'add_attribute');
            // Remove itemtype and itemscope attributes from body.
            // We have to use reflection because there is no method or layout
            // instruction for this
            $refProperty = new \ReflectionProperty($this->pageConfig, 'elements');
            $refProperty->setAccessible(true);
            $attributes = $refProperty->getValue($this->pageConfig);
            unset($attributes['body']['itemtype']);
            unset($attributes['body']['itemscope']);
            $refProperty->setValue($this->pageConfig, $attributes);
        }

        return parent::_prepareLayout();
    }

    /**
     * Unset data with $dataKey for block with name $blockName
     *
     * @param  string $blockName
     * @param  string $dataKey
     * @return $this
     */
    private function unsetLayoutBlockData($blockName, $dataKey)
    {
        if ($block = $this->getLayout()->getBlock($blockName)) {
            $block->unsetData($dataKey);
        }

        return $this;
    }

    /**
     * Get current product
     *
     * @return \Magento\Catalog\Model\Product
     */
    public function getProduct()
    {
        return $this->_coreRegistry->registry('product');
    }

    /**
     * {@inheritdoc}
     */
    public function getLdJson()
    {
        $product = $this->getProduct();
        $dataFormat = $this->getStoreConfig('richsnippets/general/product_format');
        if (!$product || $dataFormat != StructuredDataFormat::JSON_LD) {
            // product not found
            // or structured data format is not JSON-LD
            return '';
        }

        $data = $this->structuredData->get($product);
        $data = array_filter($data);
        return $this->prepareJsonString($data);
    }
}
