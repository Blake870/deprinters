<?php
namespace Swissup\QuantitySwitcher\Helper;

use Magento\CatalogInventory\Api\StockRegistryInterface as StockRegistry;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Registry;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractHelper
{
    /**
     * Global minimum allowed qty in shopping cart xpath config
     */
    const GLOBAL_MIN_QTY = 'cataloginventory/item_options/min_sale_qty';

    /**
     * Global maximum allowed qty in shopping cart xpath config
     */
    const GLOBAL_MAX_QTY = 'cataloginventory/item_options/max_sale_qty';

    /**
     * Global qty increments xpath config
     */
    const GLOBAL_QTY_INC = 'cataloginventory/item_options/qty_increments';

    /**
     * Quantity switcher type xpath config
     */
    const XML_PATH_QTY_TYPE = 'quantityswitcher/general/switcher_type';

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\CatalogInventory\Api\StockRegistryInterface
     */
    private $stockRegistry;

    /**
     * @param Context       $context
     * @param StockRegistry $stockRegistry
     * @param Registry      $registry
     */
    public function __construct(
        Context $context,
        StockRegistry $stockRegistry,
        Registry $registry
    ) {
        $this->registry = $registry;
        $this->stockRegistry = $stockRegistry;

        parent::__construct($context);
    }

    protected function getConfig($key)
    {
        return $this->scopeConfig->getValue(
            $key,ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get global minimum allowed sale qty config
     *
     * @return int
     */
    public function getGlobalMinQty()
    {
        return abs((int)$this->getConfig(self::GLOBAL_MIN_QTY));
    }

    /**
     * Get global maximum allowed sale qty config
     *
     * @return int
     */
    public function getGlobalMaxQty()
    {
        return abs((int)$this->getConfig(self::GLOBAL_MAX_QTY));
    }

    /**
     * Get global qty increments config
     *
     * @return int
     */
    public function getGlobalQtyInc()
    {
        return abs((int)$this->getConfig(self::GLOBAL_QTY_INC));
    }

    /**
     * Get minimum allowed sale qty config for specific product
     *
     * @param Magento\CatalogInventory\Model\Stock\Item $stockItem
     * @return float
     */
    public function getMinQty($stockItem)
    {
        return (float)$stockItem->getMinSaleQty();
    }

    /**
     * Get maximum allowed sale qty config for specific product
     *
     * @param Magento\CatalogInventory\Model\Stock\Item $stockItem
     * @return float
     */
    public function getMaxQty($stockItem)
    {
        $manageStock = $stockItem->getManageStock();
        $maxSaleQty = $stockItem->getMaxSaleQty();
        $backOrders = $stockItem->getBackorders();
        $qty = $stockItem->getQty();

        $maxQty = $maxSaleQty;

        if ($manageStock) {
            $maxQty = $maxSaleQty > $qty && !$backOrders ? $qty : $maxSaleQty;
        }

        return $maxQty;
    }

    /**
     * Get qty increments config for specific product
     *
     * @param Magento\CatalogInventory\Model\Stock\Item $stockItem
     * @return int
     */
    public function getQtyIncrements($stockItem)
    {
        $qtyIncEnabled = $stockItem->getEnableQtyIncrements();
        $qtyInc = $stockItem->getQtyIncrements();

        return $qtyIncEnabled ? $qtyInc : 1;
    }

    /**
     * Get stock config array for specific product
     *
     * @return array
     */
    public function getStockConfig()
    {
        $product = $this->registry->registry('product');
        $productId = $product->getId();
        $productType = $product->getTypeId();
        $config[0] = [
            'type' => $productType,
            'switcher' => $this->getConfig(self::XML_PATH_QTY_TYPE)
        ];

        if ($productType == 'configurable') {
            $items = $product->getTypeInstance()->getUsedProducts($product);

            foreach($items as $item) {
                $id = $item->getId();
                $stockItem = $this->stockRegistry->getStockItem($id);

                $config[] = array (
                    "id" => $id,
                    "minQty" => $this->getMinQty($stockItem),
                    "maxQty" => $this->getMaxQty($stockItem),
                    "qtyInc" => $this->getQtyIncrements($stockItem)
                );
            }
        } elseif ($productType == 'bundle' || $productType == 'grouped') {
            $config[] = array (
                "minQty" => $this->getGlobalMinQty(),
                "maxQty" => $this->getGlobalMaxQty(),
                "qtyInc" => $this->getGlobalQtyInc()
            );
        } else {
            $stockItem = $this->stockRegistry->getStockItem($productId);

            $config[] = array (
                "minQty" => $this->getMinQty($stockItem),
                "maxQty" => $this->getMaxQty($stockItem),
                "qtyInc" => $this->getQtyIncrements($stockItem)
            );
        }

        return $config;
    }
}
