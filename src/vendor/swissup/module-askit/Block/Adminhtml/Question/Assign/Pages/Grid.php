<?php
namespace Swissup\Askit\Block\Adminhtml\Question\Assign\Pages;

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
     * @var \Magento\Cms\Model\ResourceModel\Page\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var \Magento\Cms\Model\Page
     */
    protected $cmsPage;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Cms\Model\ResourceModel\Page\CollectionFactory $collectionFactory
     * @param \Magento\Cms\Model\Page $cmsPage
     * @param \Magento\Framework\Registry $coreRegistry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Cms\Model\ResourceModel\Page\CollectionFactory $collectionFactory,
        \Magento\Cms\Model\Page $cmsPage,
        \Magento\Framework\Registry $coreRegistry,
        array $data = []
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->cmsPage = $cmsPage;
        $this->coreRegistry = $coreRegistry;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('swissup_askit_question_assigned_pages');
        $this->setDefaultSort('page_id');
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
            $pageIds = $this->_getSelectedPages();
            if (empty($pageIds)) {
                $pageIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('page_id', ['in' => $pageIds]);
            } elseif (!empty($pageIds)) {
                $this->getCollection()->addFieldToFilter('page_id', ['nin' => $pageIds]);
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

        $this->setCollection($collection);

        if ($this->getPagesReadonly()) {
            $pageIds = $this->_getSelectedPages();
            if (empty($pageIds)) {
                $pageIds = 0;
            }
            $this->getCollection()->addFieldToFilter('page_id', ['in' => $pageIds]);
        }

        return parent::_prepareCollection();
    }

    /**
     * @return Extended
     */
    protected function _prepareColumns()
    {
        if (!$this->getPagesReadonly()) {
            $this->addColumn(
                'in_question',
                [
                    'type' => 'checkbox',
                    'name' => 'in_question',
                    'values' => $this->_getSelectedPages(),
                    'index' => 'page_id',
                    'header_css_class' => 'col-select col-massaction',
                    'column_css_class' => 'col-select col-massaction'
                ]
            );
        }
        $this->addColumn(
            'page_id',
            [
                'header' => __('ID'),
                'sortable' => true,
                'index' => 'page_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );
        $this->addColumn('title', ['header' => __('Title'), 'index' => 'title']);

        $this->addColumn('identifier', ['header' => __('URL Key'), 'index' => 'identifier']);

        $this->addColumn(
            'is_active',
            [
                'header' => __('Status'),
                'index' => 'is_active',
                'type' => 'options',
                'options' => $this->cmsPage->getAvailableStatuses()
            ]
        );

        return parent::_prepareColumns();
    }

    public function getPagesReadonly()
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
        return $this->getUrl('askit/assign/pagesGrid', ['_current' => true]);
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
    protected function _getSelectedPages()
    {
        $pages = $this->getRequest()->getPost('assign[pages]', null);
        if (!is_array($pages)) {
            $pages = $this->getAssignPages();
        }
        return $pages;
    }

    /**
     * @return array
     */
    public function getAssignPages()
    {
        $pages = $this->getRequest()->getPost('assign[pages]', null);
        if ($pages === null) {
            $pages = $this->getQuestion()->getAssignPages();
            return $pages;
        }
        return $pages;
    }
}
