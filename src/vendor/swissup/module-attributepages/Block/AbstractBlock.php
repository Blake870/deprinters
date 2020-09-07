<?php
namespace Swissup\Attributepages\Block;

use Swissup\Attributepages\Model\Entity as AttributepagesModel;

class AbstractBlock extends \Magento\Framework\View\Element\Template implements
    \Magento\Framework\DataObject\IdentityInterface,
    \Magento\Widget\Block\BlockInterface
{
    /**
     * Attribute pages collection factory
     * @var \Swissup\Attributepages\Model\ResourceModel\Entity\CollectionFactory
     */
    public $attrpagesCollectionFactory;
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry = null;
    /**
     * Construct
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Swissup\Attributepages\Model\ResourceModel\Entity\CollectionFactory $attrpagesCollectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Swissup\Attributepages\Model\ResourceModel\Entity\CollectionFactory $attrpagesCollectionFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->coreRegistry = $coreRegistry;
        $this->attrpagesCollectionFactory = $attrpagesCollectionFactory;
    }

    public function getTitle()
    {
        return $this->getConfigurableParam('title');
    }

    public function getPageTitle()
    {
        $title = $this->_getData('title');
        if (null === $title) {
            $currentPage = $this->getCurrentPage();
            if ($currentPage) {
                $title = $currentPage->getPageTitle();
            }
        }
        return $title;
    }
    /**
     * Retrieve current category model object
     *
     * @return \Magento\Catalog\Model\Category
     */
    public function getCurrentPage()
    {
        if (!$this->hasData('current_page')) {
            if ($identifier = $this->getData('identifier')) { // parent page for option list
                $storeId = $this->_storeManager->getStore()->getId();
                $collection = $this->attrpagesCollectionFactory->create()
                    ->addFieldToFilter('identifier', $identifier)
                    ->addUseForAttributePageFilter() // enabled flag
                    ->addStoreFilter($storeId)
                    ->setOrder('store_id', 'DESC');
                $this->setData('current_page', $collection->getFirstItem());
            } else {
                $this->setData('current_page',
                    $this->coreRegistry->registry('attributepages_current_page')
                );
            }
        }
        return $this->getData('current_page');
    }

    protected function getConfigurableParam($key)
    {
        $data = $this->_getData($key);
        if (null === $data) {
            $currentPage = $this->getCurrentPage();
            if ($currentPage) {
                $this->setData($key, $currentPage->getData($key));
            }
        }
        return $this->_getData($key);
    }
    /**
     * Return identifiers for produced content
     *
     * @return array
     */
    public function getIdentities()
    {
        return [AttributepagesModel::CACHE_TAG . '_' . 'block'];
    }
}
