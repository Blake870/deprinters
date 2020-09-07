<?php

//place this before any script you want to calculate time
$time_start = microtime(true);

// MAGENTO START
require dirname(__FILE__) . '/../app/bootstrap.php';

use Magento\Framework\App\Bootstrap;

$bootstrap = Bootstrap::create(BP, $_SERVER);
$objectManager = $bootstrap->getObjectManager();

$state = $objectManager->get('Magento\Framework\App\State');
$state->setAreaCode('adminhtml');

$fileHandle = fopen(__DIR__ . '/../var/brochure.csv','r+');
$headers = fgetcsv($fileHandle);

$nonDropdownAttribute = ['sku', 'print_productnaam', 'price'];

$attributeData = [];
foreach ($headers as $index => $attributeName) {
    if (!in_array($attributeName, $nonDropdownAttribute)) {
        $attributeData[$attributeName] = [];
    }
}
$productData = [];

while ($row = fgetcsv($fileHandle)) {
    $productData[] = $row;
    foreach ($row as $index => $value) {
        $attributeName = $headers[$index];
        if (!in_array($attributeName, $nonDropdownAttribute)) {
            $attributeData[$attributeName][] = $value;
        }
    }
}

$eavSetup = $objectManager->create(\Magento\Eav\Setup\EavSetup::class);
$attributesByCode = [];
foreach ($attributeData as $attributeName => $options) {
    $attributeCode = preg_replace('/[\W]/', '_', $attributeName);
    $attribute = $eavSetup->getAttribute(\Magento\Catalog\Model\Product::ENTITY, $attributeCode);
    $attributeName = ucfirst(str_replace('_', ' ', $attributeName));
    if (!$attribute) {
        echo 'Creating: ' . $attributeName . PHP_EOL;
        $attribute = $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            $attributeCode,
            [
                'type' => 'int',
                'backend' => '',
                'frontend' => '',
                'label' => $attributeName,
                'input' => 'select',
                'class' => '',
                'global' => 1,
                'visible' => true,
                'required' => false,
                'user_defined' => true,
                'searchable' => true,
                'filterable' => true,
                'comparable' => false,
                'visible_on_front' => true,
                'used_in_product_listing' => true,
                'unique' => false,
                'apply_to' => '',
                'system' => 1,
                'group' => 'General',
            ]
        );
        $attribute = $eavSetup->getAttribute(\Magento\Catalog\Model\Product::ENTITY, $attributeCode);
    } else {
        echo 'Already exists: ' . $attributeName . PHP_EOL;
    }
    $attributesByCode[$attribute['attribute_code']] = $attribute;
    $optionData = array_unique($options);
//    asort($optionData);
    $configurableAttributes[] = $attribute['attribute_id'];
    foreach ($optionData as $index => $label) {
        echo 'Adding/updating attribute option: ' . $label . PHP_EOL;
        $option = [
            'store_id' => 0,
            'attribute_id' => $attribute['attribute_id'],
            'values' => [$label],
        ];
        $eavSetup->addAttributeOption($option);
    }
}

$cacheTypeList = $objectManager->create(\Magento\Framework\App\Cache\TypeListInterface::class);
$cacheFrontendPool = $objectManager->create(\Magento\Framework\App\Cache\Frontend\Pool::class);
foreach ($cacheTypeList->getTypes() as $type) {
    $cacheTypeList->cleanType($type->getId());
}
foreach ($cacheFrontendPool as $cacheFrontend) {
    $cacheFrontend->getBackend()->clean();
}

$keyData = array_flip($headers);
/** @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory */
$productCollectionFactory = $objectManager->create(\Magento\Catalog\Model\ResourceModel\Product\CollectionFactory::class);
/** @var \Magento\Catalog\Model\ProductFactory $productFactory */
$productFactory = $objectManager->create(\Magento\Catalog\Model\ProductFactory::class);
$productIds = [];
$batches = array_chunk($productData, 400);
foreach ($batches as $batch) {
    $skus = [];
    foreach ($batch as $row) {
        $skus[] = $row[$keyData['sku']];
    }

    $productCollection = $productCollectionFactory->create()->addFieldToFilter('sku', ['in' => $skus]);
    $existingSkus = $productCollection->getColumnValues('sku');
    $productIds = array_merge($productIds, $productCollection->getAllIds());
    foreach ($batch as $row) {
        if (!in_array($row[$keyData['sku']], $existingSkus)) {
            $product = $productFactory->create();
            $product->setAttributeSetId(4);
            $product->setStatus(1);
            $product->setVisibility(\Magento\Catalog\Model\Product\Visibility::VISIBILITY_NOT_VISIBLE);
            $product->setSku($row[$keyData['sku']]);
            $product->setPrice($row[$keyData['price']]);

            $product->setTypeId('simple');
            $product->setStockData([
                    'use_config_manage_stock' => 0, //'Use config settings' checkbox
                    'manage_stock' => 1, //manage stock
                    'is_in_stock' => 1, //Stock Availability
                    'qty' => 99999, //Stock Qty
                ]
            );
            $product->setName($row[$keyData['print_productnaam']] . ' - ' . $row[$keyData['sku']]);
            foreach ($headers as $rowIndex => $attributeName) {
                if (!in_array($attributeName, $nonDropdownAttribute)) {
                    $attributeCode = preg_replace('/[\W]/', '_', $attributeName);
                    $product->setData($attributeCode, getOptionId($row[$rowIndex], $attributesByCode[$attributeCode], $objectManager));
                }
            }

            $product->save();
            $productIds[] = $product->getId();
            echo 'Created product: ' . $product->getId() . PHP_EOL;
        }
    }
}

//$configurable_product = $productFactory->create();
//$configurable_product->setSku($productData[0][1]); // set sku
//$configurable_product->setName($productData[0][1]); // set name
//$configurable_product->setAttributeSetId(4);
//$configurable_product->setStatus(1);
//$configurable_product->setTypeId('configurable');
//$configurable_product->setPrice(0);
////$configurable_product->setWebsiteIds(array(1)); // set website
////$configurable_product->setCategoryIds(array(2)); // set category
//$configurable_product->setStockData(array(
//        'use_config_manage_stock' => 0, //'Use config settings' checkbox
//        'manage_stock' => 1, //manage stock
//        'is_in_stock' => 1, //Stock Availability
//    )
//);
//
//// super attribute
//$configurable_product->getTypeInstance()->setUsedProductAttributeIds($configurableAttributes, $configurable_product); //attribute ID of attribute 'size_general' in my store
//
//try {
//    $configurableAttributesData = $configurable_product->getTypeInstance()->getConfigurableAttributesAsArray($configurable_product);
//    $configurable_product->setCanSaveConfigurableAttributes(true);
//    $configurable_product->setConfigurableAttributesData($configurableAttributesData);
//    $configurableProductsData = array();
//    $configurable_product->setConfigurableProductsData($configurableProductsData);
//} catch (\Exception $e) {
//    echo $e->getTraceAsString();
//    echo $e->getMessage();
//}
//$configurable_product->save();
//
//$productId = $configurable_product->getId();
//
//$configurable_product = $objectManager->create('Magento\Catalog\Model\Product')->load($productId); // Load Configurable Product
//$configurable_product->setAssociatedProductIds($productIds); // Setting Associated Products
//$configurable_product->setCanSaveConfigurableAttributes(true);
//$configurable_product->save();

fclose($fileHandle);

$time_end = microtime(true);
//dividing with 60 will give the execution time in minutes other wise seconds
$execution_time = ($time_end - $time_start) / 60;

//execution time of the script
echo "<pre>";
echo 'Total Execution Time:</b> ' . $execution_time . ' Mins';

function getOptionId($label, $attribute, $objectManager)
{
    static $optionData = [];
    $attributeId = $attribute['attribute_id'];
    if (!array_key_exists($attributeId, $optionData)) {
        $optionCollectionFactory = $objectManager->create(\Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory::class);
        /** @var \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\Collection $optionCollection */
        $optionCollection = $optionCollectionFactory->create();
        $optionCollection->setPositionOrder('asc')
            ->setAttributeFilter($attributeId)
            ->setStoreFilter()
            ->load();

        $optionData[$attributeId] = [];
        foreach ($optionCollection->getItems() as $option) {
            $optionData[$attributeId][strtolower($option->getValue())] = $option->getId();
        }
    }

    return $optionData[$attributeId][strtolower($label)];
}
