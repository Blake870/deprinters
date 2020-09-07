<?php
namespace Swissup\Askit\Ui\DataProvider\Product;

use Magento\Ui\DataProvider\AbstractDataProvider;
use Magento\Framework\App\RequestInterface;
use Swissup\Askit\Model\ResourceModel\Message\Collection;

/**
 * Class MessageDataProvider
 *
 * @method Collection getCollection()
 */
class MessageDataProvider extends AbstractDataProvider
{
    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        // $customerId = $this->request->getParam('current_customer_id', false);
        // if ($customerId) {
        //     $this->getCollection()->addCustomerFilter($customerId);
        // }

        $productId = $this->request->getParam('current_product_id', false);
        if ($productId) {
            $this->getCollection()->addProductFilter($productId);
        }

        $categoryId = $this->request->getParam('current_category_id', false);

        if ($categoryId) {
            $this->getCollection()->addCategoryFilter($categoryId);
        }

        $pageId = $this->request->getParam('current_page_id', false);
        if ($pageId) {
            $this->getCollection()->addPageFilter($pageId);
        }

        $arrItems = [
            'totalRecords' => $this->getCollection()->getSize(),
            'items' => [],
        ];

        foreach ($this->getCollection() as $item) {
            $arrItems['items'][] = $item->toArray([]);
        }

        return $arrItems;
    }
}
