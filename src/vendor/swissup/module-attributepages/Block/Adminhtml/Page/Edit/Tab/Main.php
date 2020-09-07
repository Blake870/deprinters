<?php
namespace Swissup\Attributepages\Block\Adminhtml\Page\Edit\Tab;

use Magento\Store\Model\Store;

class Main
    extends \Swissup\Attributepages\Block\Adminhtml\Page\Edit\Tab\AbstractTab
    implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $systemStore;
    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        array $data = []
    ) {
        $this->systemStore = $systemStore;
        parent::__construct($context, $registry, $formFactory, $data);
    }
    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setActive(true);
    }
    /**
     * Prepare form before rendering HTML
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        /* @var $model \Swissup\Attributepages\Model\Entity */
        $model = $this->getPage();
        /*
         * Checking if user have permissions to save information
         */
        if ($this->_isAllowedAction('save')) {
            $isElementDisabled = false;
        } else {
            $isElementDisabled = true;
        }

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('page_');
        $fieldset = $form->addFieldset('base_fieldset', ['legend' => $this->getTabLabel()]);

        if ($model->getEntityId()) {
            $fieldset->addField('entity_id', 'hidden', ['name' => 'entity_id']);
        }

        $fieldset->addField('attribute_id', 'hidden', [
            'required' => true,
            'name'     => 'attribute_id'
        ]);
        if ($model->isOptionBasedPage()) {
            $fieldset->addField('option_id', 'hidden', [
                'required' => true,
                'name'     => 'option_id'
            ]);
        }
        $fieldset->addField('name', 'text', [
            'name'     => 'name',
            'label'    => __('Name'),
            'title'    => __('Name'),
            'note'     => __('Used to identify page in backend grid'),
            'required' => true,
            'disabled' => $isElementDisabled
        ]);
        $fieldset->addField('title', 'text', [
            'name'     => 'title',
            'label'    => __('Title'),
            'title'    => __('Title'),
            'disabled' => $isElementDisabled
        ]);
        if (!$model->getId()) {
            $model->setData('title', $this->getDefaultPageTitle());
        }
        $fieldset->addField('identifier', 'text', [
            'name'     => 'identifier',
            'label'    => __('URL Key'),
            'title'    => __('URL Key'),
            'required' => true,
            'note'     => __('Relative to Website Base URL'),
            'disabled' => $isElementDisabled
        ]);
        if (!$model->getId()) {
            $model->setData('identifier', $this->getDefaultPageIdentifier());
        }
        if ($model->isOptionBasedPage()) {
            $this->_addElementTypes($fieldset); //register own image element
            $fieldset->addField('image', 'image', [
                'name'     => 'image',
                'label'    => __('Image'),
                'title'    => __('Image'),
                'disabled' => $isElementDisabled
            ]);
            $fieldset->addField('thumbnail', 'image', [
                'name'     => 'thumbnail',
                'label'    => __('Thumbnail'),
                'title'    => __('Thumbnail'),
                'disabled' => $isElementDisabled
            ]);
        }
        /* Check is single store mode */
        if (!$this->_storeManager->isSingleStoreMode()) {
            $field = $fieldset->addField(
                'store_id',
                'multiselect',
                [
                    'name' => 'stores[]',
                    'label' => __('Store View'),
                    'title' => __('Store View'),
                    'required' => true,
                    'values' => $this->systemStore->getStoreValuesForForm(false, true),
                    'disabled' => $isElementDisabled
                ]
            );
            $renderer = $this->getLayout()->createBlock(
                'Magento\Backend\Block\Store\Switcher\Form\Renderer\Fieldset\Element'
            );
            $field->setRenderer($renderer);
        } else {
            $fieldset->addField(
                'store_id',
                'hidden',
                ['name' => 'stores[]', 'value' => Store::DEFAULT_STORE_ID]
            );
            $model->setStoreId(Store::DEFAULT_STORE_ID);
        }
        $fieldset->addField('use_for_attribute_page', 'select', [
            'label'  => __('Enabled'),
            'title'  => __('Enabled'),
            'name'   => 'use_for_attribute_page',
            'values' => [
                '1' => __('Yes'),
                '0' => __('No')
            ],
            'disabled' => $isElementDisabled
        ]);
        if (!$model->getId()) {
            $model->setData('use_for_attribute_page', '1');
        }

        $form->setValues($model->getData());
        $form->setFieldNameSuffix('attributepage');
        $this->setForm($form);
        return parent::_prepareForm();
    }

    protected function _getAdditionalElementTypes()
    {
        return [
            'image' => 'Swissup\Attributepages\Block\Adminhtml\Page\Helper\Image'
        ];
    }
    /**
     * Prepare label for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        if ($this->getPage()->isAttributeBasedPage()) {
            return __('Page Information');
        } else {
            return __('Option Information');
        }
    }
    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        if ($this->getPage()->isAttributeBasedPage()) {
            return __('Page Information');
        } else {
            return __('Option Information');
        }
    }

    public function getDefaultPageTitle()
    {
        if ($this->getPage()->isAttributeBasedPage()) {
            return $this->getPage()->getAttribute()->getFrontendLabel();
        } else {
            return $this->getPage()->getOption()->getValue();
        }
    }

    public function getDefaultPageIdentifier()
    {
        if ($this->getPage()->isAttributeBasedPage()) {
            return $this->getPage()->getAttribute()->getAttributeCode();
        } else {
            return $this->getPage()->getOption()->getValue();
        }
    }
}
