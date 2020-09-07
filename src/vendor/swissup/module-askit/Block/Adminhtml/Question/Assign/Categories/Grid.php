<?php
namespace Swissup\Askit\Block\Adminhtml\Question\Assign\Categories;

use Magento\Backend\Block\Widget\Grid as WidgetGrid;
use Magento\Backend\Block\Widget\Grid\Column;
use Magento\Backend\Block\Widget\Grid\Extended;

use Swissup\Askit\Api\Data\MessageInterface;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry = null;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $collectionFactory
     * @param \Magento\Framework\Registry $coreRegistry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $collectionFactory,
        \Magento\Framework\Registry $coreRegistry,
        array $data = []
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->coreRegistry = $coreRegistry;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('swissup_askit_question_assigned_categories');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('ASC');
        $this->setUseAjax(true);
    }

    /**
     * @param Column $column
     * @return $this
     */
    protected function _addColumnFilterToCollection($column)
    {
        // Set custom filter for in category flag
        if ($column->getId() == 'in_question') {
            $categoriesIds = $this->_getSelectedCategories();
            if (empty($categoriesIds)) {
                $categoriesIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('entity_id', ['in' => $categoriesIds]);
            } elseif (!empty($categoriesIds)) {
                $this->getCollection()->addFieldToFilter('entity_id', ['nin' => $categoriesIds]);
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    /**
     * @return Grid
     */
    protected function _prepareCollection()
    {
        $messageId = (int) $this->getRequest()->getParam('id', 0);
        if ($this->getQuestion()->getId()) {
            $this->setDefaultFilter(['in_question' => 1]);
        }

        $collection = $this->collectionFactory->create();
        /* @var $collection \Magento\Cms\Model\ResourceModel\Page\Collection */
        $collection
            ->addAttributeToSelect('*')
            ->joinUrlRewrite();

        $this->setCollection($collection);

        if ($this->getCategoriesReadonly()) {
            $categoriesIds = $this->_getSelectedCategories();
            if (empty($categoriesIds)) {
                $categoriesIds = 0;
            }
            $this->getCollection()->addFieldToFilter('entity_id', ['in' => $categoriesIds]);
        }

        return parent::_prepareCollection();
    }

    /**
     * @return Extended
     */
    protected function _prepareColumns()
    {
        if (!$this->getCategoriesReadonly()) {
            $this->addColumn(
                'in_question',
                [
                    'type' => 'checkbox',
                    'name' => 'in_question',
                    'values' => $this->_getSelectedCategories(),
                    'index' => 'entity_id',
                    'header_css_class' => 'col-select col-massaction',
                    'column_css_class' => 'col-select col-massaction'
                ]
            );
        }
        $this->addColumn(
            'entity_id',
            [
                'header' => __('ID'),
                'sortable' => true,
                'index' => 'entity_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );
        $this->addColumn('name', ['header' => __('Name'), 'index' => 'name']);

        $this->addColumn('request_path', ['header' => __('Path'), 'index' => 'request_path']);

        return parent::_prepareColumns();
    }

    public function getCategoriesReadonly()
    {
        return false;
    }

    /**
     * Rerieve grid URL
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('askit/assign/categoriesGrid', ['_current' => true]);
    }

    /**
     * @return Swissup\Askit\Model\Message|null
     */
    public function getQuestion()
    {
        return $this->coreRegistry->registry('askit_question');
    }

    /**
     * @return array
     */
    protected function _getSelectedCategories()
    {
        $assigns = $this->getRequest()->getPost('assign[categories]', null);
        if (!is_array($assigns)) {
            $assigns = $this->getAssignCategories();
        }
        return $assigns;
    }

    /**
     * @return array
     */
    public function getAssignCategories()
    {
        $assigns = $this->getRequest()->getPost('assign[categories]', null);
        if ($assigns === null) {
            $assigns = $this->getQuestion()->getAssignCategories();
            return $assigns;
        }
        return $assigns;
    }

    /**
     * Get children of specified item
     *
     * @param \Magento\Framework\DataObject $item
     * @return array
     */
    public function getMultipleRows($item)
    {
        // Fix because collection have a column children
        return null;
        // return $item->getChildren();
    }
}
