<?php
namespace Swissup\Attributepages\Block\Adminhtml\Page\Edit\Tab;

use \Swissup\Attributepages\Model\Entity as AttributepagesEntity;

class DisplaySettings
    extends \Swissup\Attributepages\Block\Adminhtml\Page\Edit\Tab\AbstractTab
    implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var \Magento\Framework\View\Model\PageLayout\Config\BuilderInterface
     */
    protected $pageLayoutBuilder;
    /**
     * @var \Magento\Theme\Model\Layout\Source\Layout
     */
    protected $pageLayout;
    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Framework\View\Model\PageLayout\Config\BuilderInterface $pageLayoutBuilder
     * @param \Magento\Theme\Model\Layout\Source\Layout $pageLayout
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Framework\View\Model\PageLayout\Config\BuilderInterface $pageLayoutBuilder,
        \Magento\Theme\Model\Layout\Source\Layout $pageLayout,
        array $data = []
    ) {
        $this->pageLayoutBuilder = $pageLayoutBuilder;
        $this->pageLayout = $pageLayout;
        parent::__construct($context, $registry, $formFactory, $data);
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

        $layoutFieldset = $form->addFieldset('layout_fieldset', [
            'legend' => __('Page Layout'),
            'class'  => 'fieldset-wide',
            'disabled' => $isElementDisabled
        ]);

        $layouts = $this->pageLayoutBuilder->getPageLayoutsConfig()->toOptionArray(true);
        $layoutFieldset->addField('root_template', 'select', [
            'label'  => __('Layout'),
            'title'  => __('Layout'),
            'name'   => 'root_template',
            'values' => $layouts,
            'disabled' => $isElementDisabled
        ]);
        if (!$model->getId()) {
            $model->setRootTemplate($this->pageLayout->getDefaultValue());
        }
        $layoutFieldset->addField('layout_update_xml', 'textarea', [
            'name'      => 'layout_update_xml',
            'label'     => __('Layout Update XML'),
            'style'     => 'height:12em;',
            'disabled'  => $isElementDisabled
        ]);

        $layoutFieldset = $form->addFieldset('display_fieldset', [
            'legend' => __('Display Settings'),
            'disabled' => $isElementDisabled
        ]);
        $layoutFieldset->addField('display_mode', 'select', [
            'label'  => __('Display Mode'),
            'title'  => __('Display Mode'),
            'name'   => 'display_mode',
            'value'  => AttributepagesEntity::DISPLAY_MODE_MIXED,
            'values' => [
                AttributepagesEntity::DISPLAY_MODE_MIXED
                    => __('Description and children'),
                AttributepagesEntity::DISPLAY_MODE_DESCRIPTION
                    => __('Description only'),
                AttributepagesEntity::DISPLAY_MODE_CHILDREN
                    => __('Children only')
            ],
            'disabled' => $isElementDisabled
        ]);
        if ($model->isAttributeBasedPage()) {
            $columnCountField = $layoutFieldset->addField('column_count', 'text', [
                'label' => __('Columns Count'),
                'title' => __('Columns Count'),
                'note'  => __('1 — 8 columns are supported'),
                'name'  => 'column_count',
                'value' => 4,
                'disabled' => $isElementDisabled
            ]);
            if (!$model->getId()) {
                $model->setColumnCount(4);
            }
            $layoutFieldset->addField('group_by_first_letter', 'select', [
                'label' => __('Group Options by First Letter'),
                'title' => __('Group Options by First Letter'),
                'name'  => 'group_by_first_letter',
                'value' => 0,
                'values' => [
                    '1' => __('Yes'),
                    '0' => __('No')
                ],
                'disabled' => $isElementDisabled
            ]);
            $layoutFieldset->addField('listing_mode', 'select', [
                'label'  => __('Listing Mode'),
                'title'  => __('Listing Mode'),
                'name'   => 'listing_mode',
                'value'  => AttributepagesEntity::LISTING_MODE_LINK,
                'values' => [
                    AttributepagesEntity::LISTING_MODE_IMAGE
                        => __('Images'),
                    AttributepagesEntity::LISTING_MODE_LINK
                        => __('Links')
                ],
                'disabled' => $isElementDisabled
            ]);
            if (!$model->getId()) {
                $model->setListingMode(AttributepagesEntity::LISTING_MODE_LINK);
            }
            $layoutFieldset->addField('image_width', 'text', [
                'label' => __('Image Width'),
                'title' => __('Image Width'),
                'name'  => 'image_width',
                'value' => 200,
                'disabled' => $isElementDisabled
            ]);
            if (!$model->getId()) {
                $model->setImageWidth(200);
            }
            $layoutFieldset->addField('image_height', 'text', [
                'label' => __('Image Height'),
                'title' => __('Image Height'),
                'name'  => 'image_height',
                'value' => 150,
                'disabled' => $isElementDisabled
            ]);
            if (!$model->getId()) {
                $model->setImageHeight(150);
            }

            // define field dependencies
            $this->setChild(
                'form_after',
                $this->getLayout()->createBlock(
                    'Magento\Backend\Block\Widget\Form\Element\Dependence'
                )->addFieldMap(
                    "page_image_width",
                    'page_image_width'
                )->addFieldMap(
                    "page_image_height",
                    'page_image_height'
                )->addFieldMap(
                    "page_listing_mode",
                    'page_listing_mode'
                )->addFieldDependence(
                    'page_image_width',
                    'page_listing_mode',
                    'image'
                )->addFieldDependence(
                    'page_image_height',
                    'page_listing_mode',
                    'image'
                )
            );
        }

        $form->setValues($model->getData());
        $form->setFieldNameSuffix('attributepage');
        $this->setForm($form);
        return parent::_prepareForm();
    }
     /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Display Settings');
    }
    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Display Settings');
    }
}
