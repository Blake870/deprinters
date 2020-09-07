<?php
/**
 * Added ability to:
 *     1. Read _current_page, used in widgets
 *     2. Disable sorting for random products widget
 */
namespace Swissup\Highlight\Block\ProductList;

class Toolbar extends \Magento\Catalog\Block\Product\ProductList\Toolbar
{
    /**
     * Set collection to pager
     *
     * @param \Magento\Framework\Data\Collection $collection
     * @return $this
     */
    public function setCollection($collection)
    {
        $currentOrder = $this->getData('_current_grid_order');

        if ($this->getSkipOrder()) {
            // prevent order in parent method
            $this->setData('_current_grid_order', false);
        }

        $return = parent::setCollection($collection);

        // Add order by id to guarantee different products per page
        $collection->addOrder('entity_id', 'DESC');

        // restore original value
        if ($this->getSkipOrder()) {
            $this->setData('_current_grid_order', $currentOrder);
        }

        return $return;
    }

    /**
     * Return current page from request
     *
     * @return int
     */
    public function getCurrentPage()
    {
        $page = $this->_getData('_current_page');
        if ($page) {
            return $page;
        }
        return parent::getCurrentPage();
    }

    /**
     * Get grit products sort order field
     *
     * @return string
     */
    public function getCurrentOrder()
    {
        $order = $this->_getData('_current_grid_order');
        if (false === $order || $order) {
            // ability to disable sort for random products widget
            return $order;
        }
        return parent::getCurrentOrder();
    }

    /**
     * Rewritten to change default sort order on highlight pages
     *
     * @param array $customOptions Optional parameter for passing custom selectors from template
     * @return string
     */
    public function getWidgetOptionsJson(array $customOptions = [])
    {
        if ($this->_orderField) {
            $customOptions['orderDefault'] = $this->_orderField;
        }
        return parent::getWidgetOptionsJson($customOptions);
    }
}
