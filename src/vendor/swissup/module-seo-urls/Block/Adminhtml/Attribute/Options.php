<?php

namespace Swissup\SeoUrls\Block\Adminhtml\Attribute;

class Options extends \Magento\Eav\Block\Adminhtml\Attribute\Edit\Options\Options
{
    /**
     * @var \Swissup\SeoUrls\Model\ResourceModel\Attribute\Action
     */
    protected $attributeAction;

    /**
     * @var \Swissup\SeoUrls\Model\Layer\PredefinedFilters
     */
    protected $predefinedFilters;

    /**
     * @var string
     */
    protected $_template = 'Swissup_SeoUrls::product/attribute/options.phtml';

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory $attrOptionCollectionFactory
     * @param \Magento\Framework\Validator\UniversalFactory $universalFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory $attrOptionCollectionFactory,
        \Magento\Framework\Validator\UniversalFactory $universalFactory,
        \Swissup\SeoUrls\Model\ResourceModel\Attribute\Action $attributeAction,
        \Swissup\SeoUrls\Model\Layer\PredefinedFilters $predefinedFilters,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $attrOptionCollectionFactory,
            $universalFactory,
            $data
        );
        $this->attributeAction = $attributeAction;
        $this->predefinedFilters = $predefinedFilters;
    }

    /**
     * Retrieve stores collection with default store
     *
     * @return \Magento\Store\Model\ResourceModel\Store\Collection
     */
    public function getStores()
    {
        if (!$this->hasStores()) {
            $stores = $this->_storeManager->getStores(true);
            ksort($stores);
            $this->setData('stores', $stores);
        }
        return $this->_getData('stores');
    }

    /**
     * @param \Magento\Eav\Model\Entity\Attribute\AbstractAttribute $attribute
     * @param array|\Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\Collection $optionCollection
     * @return array
     */
    protected function _prepareOptionValues(
        \Magento\Eav\Model\Entity\Attribute\AbstractAttribute $attribute,
        $optionCollection
    ) {
        $type = $attribute->getFrontendInput();
        if ($type === 'select' || $type === 'multiselect') {
            $defaultValues = explode(',', $attribute->getDefaultValue());
            $inputType = $type === 'select' ? 'radio' : 'checkbox';
        } else {
            $defaultValues = [];
            $inputType = '';
        }

        $values = [];
        $isSystemAttribute = is_array($optionCollection);
        foreach ($optionCollection as $option) {
            $bunch = $this->prepareAttributeOptionValues(
                $option,
                $inputType,
                $defaultValues
            );
            foreach ($bunch as $value) {
                $values[] = new \Magento\Framework\DataObject($value);
            }
        }

        return $values;
    }

    protected function prepareAttributeOptionValues($option, $inputType, $defaultValues)
    {
        $isSystemAttribute = is_array($option);
        $value['id'] = $isSystemAttribute
            ? $option['value']
            : $option->getId();
        $value['optionLabel'] = $isSystemAttribute
            ? $option['label']
            : $option->getValue();
        $urlValues = $this->attributeAction->getInUrlValues(
            $this->getAttributeObject()->getId(),
            $value['id']
        );
        foreach ($this->getStores() as $store) {
            $storeId = $store->getId();
            $value['store' . $storeId] = isset(
                $urlValues[$storeId]['url_value']
            ) ? $this->escapeHtml(
                $urlValues[$storeId]['url_value']
            ) : '';
        }

        return [$value];
    }

    /**
     * {@inheritdoc}
     */
    protected function _getOptionValuesCollection(
        \Magento\Eav\Model\Entity\Attribute\AbstractAttribute $attribute
    ) {
        $ratingFilter = $this->predefinedFilters->getRatingFilter();
        if ($ratingFilter
            && $attribute->getAttributeCode() == $ratingFilter->getAttributeCode()
        ) {
            // show custom options
            // when attribute is rating summary from Swissup ALN
            return [
                [
                    'value' => '80-100',
                    'label' => __('4 and up')
                ],
                [
                    'value' => '60-100',
                    'label' => __('3 and up')
                ],
                [
                    'value' => '40-100',
                    'label' => __('2 and up')
                ],
                [
                    'value' => '20-100',
                    'label' => __('1 and up')
                ],
                [
                    'value' => '0-100',
                    'label' => __('any')
                ]
            ];
        }

        return parent::_getOptionValuesCollection($attribute);
    }
}
