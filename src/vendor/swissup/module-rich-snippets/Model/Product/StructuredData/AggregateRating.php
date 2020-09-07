<?php

namespace Swissup\RichSnippets\Model\Product\StructuredData;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\Exception\NotFoundException;

class AggregateRating extends AbstractData
{
    /**
     * @var ProductInterface
     */
    protected $product;

    /**
     * @var \Magento\Review\Model\Review\SummaryFactory
     */
    protected $reviewSummaryFactory;

    /**
     * @param ProductInterface                            $product
     * @param \Magento\Review\Model\Review\SummaryFactory $reviewSummaryFactory
     * @param \Magento\Catalog\Helper\Output              $attributeOutput
     */
    public function __construct(
        ProductInterface $product,
        \Magento\Review\Model\Review\SummaryFactory $reviewSummaryFactory,
        \Magento\Catalog\Helper\Output $attributeOutput
    ) {
        $this->product = $product;
        $this->reviewSummaryFactory = $reviewSummaryFactory;
        parent::__construct($attributeOutput);
    }

    /**
     * Get 'aggregateRating' for product structured data
     *
     * @param  array  $dataMap [description]
     * @return array
     */
    public function get(array $dataMap = [])
    {
        if (!$this->product->getId()) {
            throw new NotFoundException(__('Product not found.'));
        }

        $summaryData = $this->reviewSummaryFactory->create()
            ->setStoreId($this->product->getStoreId())
            ->load($this->product->getId());
        $collectedData = [
            '@type' => 'AggregateRating',
            'bestRating' => '100',
            'worstRating' => '0',
            'ratingValue' => $summaryData->getRatingSummary(),
            'reviewCount' => $summaryData->getReviewsCount(),
            'ratingCount' => $summaryData->getReviewsCount()
        ];
        $data = $this->buildAttributeBasedData($dataMap, $this->product);
        $data = $data + $collectedData;

        if ((int)$data['ratingCount'] > 0 && $data['ratingValue'] > 0) {
            return $data;
        }

        return [];
    }
}
