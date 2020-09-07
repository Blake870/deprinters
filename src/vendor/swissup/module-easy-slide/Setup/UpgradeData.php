<?php

namespace Swissup\EasySlide\Setup;

use Magento\Framework\DB\DataConverter\SerializedToJson;
use Magento\Framework\DB\AggregatedFieldDataConverter;
use Magento\Framework\DB\FieldToConvert;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Swissup\EasySlide\Model\ResourceModel\Slider as SliderResource;

class UpgradeData implements \Magento\Framework\Setup\UpgradeDataInterface
{
    /**
     * @var ProductMetadataInterface
     */
    private $magentoMetadata;

    /**
     * @var SliderResource
     */
    private $sliderResource;

    /**
     * @var AggregatedFieldDataConverter
     */
    private $aggregatedFieldConverter;

    /**
     * @param ProductMetadataInterface     $productMetadata
     * @param AggregatedFieldDataConverter $aggregatedFieldConverter
     * @param SliderResource               $sliderResource
     */
    public function __construct(
        ProductMetadataInterface $productMetadata,
        AggregatedFieldDataConverter $aggregatedFieldConverter,
        SliderResource $sliderResource
    ) {
        $this->magentoMetadata = $productMetadata;
        $this->aggregatedFieldConverter = $aggregatedFieldConverter;
        $this->sliderResource = $sliderResource;
    }

    /**
     * @inheritdoc
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.5.2', '<')
            && version_compare($this->magentoMetadata->getVersion(), '2.2.0', '>=')
        ) {
            $this->convertSerializedDataToJson($setup);
        }

        $setup->endSetup();
    }

    /**
     * Convert metadata from serialized to JSON format:
     *
     * @param ModuleDataSetupInterface $setup
     * @return void
     */
    public function convertSerializedDataToJson($setup)
    {
        $this->aggregatedFieldConverter->convert(
            [
                new FieldToConvert(
                    SerializedToJson::class,
                    $this->sliderResource->getMainTable(),
                    $this->sliderResource->getIdFieldName(),
                    'slider_config'
                ),
            ],
            $setup->getConnection()
        );
    }
}
