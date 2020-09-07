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



/** @var \Magento\Catalog\Model\ProductFactory $productFactory */
$productFactory = $objectManager->create(\Magento\Catalog\Model\ProductFactory::class);
$fileHandle = fopen(__DIR__ . '/../var/brochure.csv','r+');
$headers = fgetcsv($fileHandle);

$nonDropdownAttribute = ['sku', 'print_productnaam', 'price'];
$nonConfigurableAttributes = ['print_bedrukking_inhoud', 'print_binding', 'print_formaat', 'print_omvang'];

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

$configurableAttributes = [];
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
    if (!in_array($attributeCode, $nonConfigurableAttributes)) {
        $configurableAttributes[] = $attribute['attribute_id'];
    }
}




$eavSetup = $objectManager->create(\Magento\Eav\Setup\EavSetup::class);
$optionCollectionFactory = $objectManager->create(\Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory::class);
$attribute = $eavSetup->getAttribute(\Magento\Catalog\Model\Product::ENTITY, 'print_formaat');
$attributeId = $attribute['attribute_id'];
/** @var \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\Collection $optionCollection */
$optionCollection = $optionCollectionFactory->create();
$optionCollection->setPositionOrder('asc')
    ->setAttributeFilter($attributeId)
    ->setStoreFilter()
    ->load();

$formaatData = [];
foreach ($optionCollection->getItems() as $option) {
    $formaatData[$option->getId()] = $option->getValue();
}

$attribute = $eavSetup->getAttribute(\Magento\Catalog\Model\Product::ENTITY, 'print_omvang');
$attributeId = $attribute['attribute_id'];
/** @var \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\Collection $optionCollection */
$optionCollection = $optionCollectionFactory->create();
$optionCollection->setPositionOrder('asc')
    ->setAttributeFilter($attributeId)
    ->setStoreFilter()
    ->load();

$omvangData = [];
foreach ($optionCollection->getItems() as $option) {
    $omvangData[$option->getId()] = $option->getValue();
}

foreach($formaatData as $formaatOption => $formaatValue) {
    foreach ($omvangData as $omvangOption => $omvangValue) {
        /** @var \Magento\Catalog\Model\ResourceModel\Product\Collection $productCollection */
        $productCollection = $objectManager->create(\Magento\Catalog\Model\ResourceModel\Product\Collection::class);
        $productCollection->addFieldToFilter('type_id', ['eq' => 'simple']);
        $productCollection->addFieldToFilter('print_formaat', ['eq' => $formaatOption]);
        $productCollection->addFieldToFilter('print_omvang', ['eq' => $omvangOption]);
        $productIds = $productCollection->getAllIds();

        $configurable_product = $productFactory->create();
        $configurable_product->setSku($productData[0][1] . ' ' . $formaatValue . ' ' . $omvangValue . 'x'); // set sku
        $configurable_product->setName($productData[0][1] . ' ' . $formaatValue . ' ' . $omvangValue . 'x'); // set name
        $configurable_product->setAttributeSetId(4);
        $configurable_product->setStatus(1);
        $configurable_product->setTypeId('configurable');
        $configurable_product->setPrice(0);
        $configurable_product->setWebsiteIds([1]); // set website
        $configurable_product->setCategoryIds([10]); // set category
        $configurable_product->setStockData(array(
                'use_config_manage_stock' => 0, //'Use config settings' checkbox
                'manage_stock' => 1, //manage stock
                'is_in_stock' => 1, //Stock Availability
            )
        );

    // super attribute
        $configurable_product->getTypeInstance()->setUsedProductAttributeIds($configurableAttributes, $configurable_product); //attribute ID of attribute 'size_general' in my store

        try {
            $configurableAttributesData = $configurable_product->getTypeInstance()->getConfigurableAttributesAsArray($configurable_product);
            $configurable_product->setCanSaveConfigurableAttributes(true);
            $configurable_product->setConfigurableAttributesData($configurableAttributesData);
            $configurableProductsData = array();
            $configurable_product->setConfigurableProductsData($configurableProductsData);
        } catch (\Exception $e) {
            echo $e->getTraceAsString();
            echo $e->getMessage();
        }
        $configurable_product->save();

        $productId = $configurable_product->getId();

        $configurable_product = $objectManager->create('Magento\Catalog\Model\Product')->load($productId); // Load Configurable Product
        $configurable_product->setAssociatedProductIds($productIds); // Setting Associated Products
        $configurable_product->setCanSaveConfigurableAttributes(true);
        $configurable_product->save();
    }
}

fclose($fileHandle);
