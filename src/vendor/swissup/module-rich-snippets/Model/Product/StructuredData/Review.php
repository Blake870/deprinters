<?php

namespace Swissup\RichSnippets\Model\Product\StructuredData;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\Exception\NotFoundException;
use Magento\Review\Model\ResourceModel\Review\CollectionFactory;

class Review
{
    /**
     * @var ProductInterface
     */
    protected $product;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @param ProductInterface                                   $product
     * @param \Magento\Store\Model\StoreManagerInterface         $storeManager
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param CollectionFactory                                  $collectionFactory
     */
    public function __construct(
        ProductInterface $product,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        CollectionFactory $collectionFactory
    ) {
        $this->product = $product;
        $this->storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * Get review data
     *
     * @return array
     */
    public function get()
    {
        if (!$this->product->getId()) {
            throw new NotFoundException(__('Product not found.'));
        }

        $collection = $this->collectionFactory->create()->addStoreFilter(
                $this->product->getStoreId()
            )->addStatusFilter(
                \Magento\Review\Model\Review::STATUS_APPROVED
            )->addEntityFilter(
                'product',
                $this->product->getId()
            )->setDateOrder()
            ->setPageSize(1)
            ->addRateVotes();
        // get latest review
        $item = $collection->getFirstItem();
        $votes = [];
        // check code/Magento/Review/view/frontend/templates/product/view/list.phtml
        // to figure out how to handle votes
        if (!$item->getRatingVotes()) {
            return [];
        }

        foreach ($item->getRatingVotes() as $vote) {
            $votes[] = $vote->getPercent();
        }

        $ratingValue = count($votes) ? (array_sum($votes) / count($votes)) : 0;
        if (!$ratingValue) {
            return [];
        }

        return [
            '@type' => 'Review',
            'reviewRating' => [
                '@type' => 'Rating',
                'ratingValue' => $ratingValue,
                'bestRating' => 100,
            ],
            'author' => [
                '@type' => 'Person',
                'name' => $item->getNickname()
            ]
        ];
    }
}
