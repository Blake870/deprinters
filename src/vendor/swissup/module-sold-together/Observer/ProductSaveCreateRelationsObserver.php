<?php
namespace Swissup\SoldTogether\Observer;

class ProductSaveCreateRelationsObserver extends AbstractObserver
{
    /**
     * Create order relations
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $controller    = $observer->getEvent()->getController();
        $linksData     = $controller->getRequest()->getParam('links');
        if (!$linksData) {
            return $this;
        }

        $productId     = $controller->getRequest()->getParam('id');
        $productParams = $controller->getRequest()->getParam('product');
        $productName   = $productParams['name'];

        $orderData = isset($linksData['sold_order']) ? $linksData['sold_order'] : [];
        $this->orderModel->updateProductRelations($orderData, $productId, $productName);

        $customerData = isset($linksData['sold_customer']) ? $linksData['sold_customer'] : [];
        $this->customerModel->updateProductRelations($customerData, $productId, $productName);

        return $this;
    }
}
