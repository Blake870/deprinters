<?php

namespace Swissup\Askit\Block\Question;

use Swissup\Askit\Api\Data\MessageInterface;
use Swissup\Askit\Block\Question\AbstractBlock;

use Magento\Catalog\Model\Product;
use Magento\Customer\Model\Context;
use Magento\Customer\Model\Url;
use Magento\Store\Model\Store;

// use Magento\Review\Model\ResourceModel\Rating\Collection as RatingCollection;

class Listing extends AbstractBlock
{
    const DEFAULT_QUESTION_VIEW_TEMPLATE = 'question/view.phtml';
    const DEFAULT_ANSWER_FORM_TEMPLATE = 'question/answer/form.phtml';
    const DEFAULT_ANSWER_VIEW_TEMPLATE = 'question/answer/view.phtml';

   /**
    *
    * @var \Swissup\Askit\Model\ResourceModel\Question\CollectionFactory
    */
    protected $collectionFactory;

    /**
     *
     * @var \Swissup\Askit\Model\ResourceModel\Question\Collection
     */
    protected $collection;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\SessionFactory $customerSessionFactory
     * @param \Swissup\Askit\Helper\Config $configHelper
     * @param \Swissup\Askit\Helper\Url $urlHelper
     * @param \Magento\Framework\Data\Helper\PostHelper $postDataHelper
     * @param \Swissup\Askit\Model\Vote\Factory $voteFactory
     * @param \Swissup\Askit\Model\ResourceModel\Question\CollectionFactory $collectionFactory

     * @param array $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\SessionFactory $customerSessionFactory,
        \Swissup\Askit\Helper\Config $configHelper,
        \Swissup\Askit\Helper\Url $urlHelper,
        \Magento\Framework\Data\Helper\PostHelper $postDataHelper,
        \Swissup\Askit\Model\Vote\Factory $voteFactory,
        \Swissup\Askit\Model\ResourceModel\Question\CollectionFactory $collectionFactory,
        array $data = []
    ) {

        $this->collectionFactory = $collectionFactory;
        parent::__construct($context, $customerSessionFactory, $configHelper, $urlHelper, $postDataHelper, $voteFactory, $data);

        $this->setTabTitle();
    }

    protected function _prepareLayout()
    {
        $layout = $this->getLayout();

        $blockQuestionView = $layout
            ->createBlock('Swissup\Askit\Block\Question\View')
            ->setTemplate(self::DEFAULT_QUESTION_VIEW_TEMPLATE);
        if ($blockQuestionView) {
            $this->setChild('askit_question_view', $blockQuestionView);
        }

        $blockAnswerForm = $layout
            ->createBlock('Swissup\Askit\Block\Question\Answer\Form')
            ->setTemplate(self::DEFAULT_ANSWER_FORM_TEMPLATE);
        if ($blockAnswerForm) {
            $this->setChild('askit_answer_form', $blockAnswerForm);
        }

        $blockAnswerView = $layout
            ->createBlock('Swissup\Askit\Block\Question\Answer\View')
            ->setTemplate(self::DEFAULT_ANSWER_VIEW_TEMPLATE);
        if ($blockAnswerView) {
            $this->setChild('askit_answer_view', $blockAnswerView);
        }

        $pager = $this->getLayout()->createBlock('Magento\Theme\Block\Html\Pager');
        if ($pager) {
            $pager
                // ->setAvailableLimit([1 => 1, 10 => 10, 20 => 20, 50 => 50])
                ->setLimit(10)
                ->setCollection($this->getCollection());
            $this->setChild('pager', $pager);
        }

        return parent::_prepareLayout();
    }

    /**
     *
     * @return \Swissup\Askit\Model\ResourceModel\Question\Collection
     */
    public function getCollection()
    {
        if (empty($this->collection)) {
            $storeId = $this->_storeManager->getStore()->getId();

            $collection = $this->collectionFactory->create()
                ->addStatusFilter([MessageInterface::STATUS_APPROVED, MessageInterface::STATUS_CLOSE])
                ->addStoreFilter([$storeId, Store::DEFAULT_STORE_ID])
                ->addQuestionFilter(0)
                ->addHintOrder()
                ->addCreatedTimeOrder()
                ;
            $type = $this->getItemTypeId();
            switch ($type) {
                case MessageInterface::TYPE_CATALOG_CATEGORY:
                    $itemId = $this->getCategoryId();
                    $collection->addCategoryFilter($itemId);
                    break;
                case MessageInterface::TYPE_CMS_PAGE:
                    $itemId = $this->getPageId();
                    $collection->addPageFilter($itemId);
                    break;
                case MessageInterface::TYPE_CATALOG_PRODUCT:
                    $itemId = $this->getProductId();
                    $collection->addProductFilter($itemId);
                    break;
                case MessageInterface::TYPE_UNKNOWN:
                default:
                    break;
            }

            if ($this->isCustomerLoggedIn()) {
                $customerId = (int) $this->getCustomerSession()->getId();
                $collection->addPrivateFilter($customerId);
            } else {
                $collection->addPrivateFilter();
            }
            $this->collection = $collection;
        }
        return $this->collection;
    }

    /**
     * Set tab title
     *
     * @return void
     */
    public function setTabTitle()
    {
        $title = $this->getCollectionSize()
            ? __('Questions %1', '<span class="counter">' . $this->getCollectionSize() . '</span>')
            : __('Questions');
        $this->setTitle($title);
    }

    /**
     * Get size of questions collection
     *
     * @return int
     */
    public function getCollectionSize()
    {
        return $this->getCollection()->getSize();
    }
}
