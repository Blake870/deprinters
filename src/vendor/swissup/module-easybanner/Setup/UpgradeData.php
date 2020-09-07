<?php

namespace Swissup\Easybanner\Setup;

use Magento\Framework\DB\DataConverter\SerializedToJson;
use Magento\Framework\DB\AggregatedFieldDataConverter;
use Magento\Framework\DB\FieldToConvert;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\App\ProductMetadataInterface;
use Swissup\Easybanner\Model\ResourceModel\Banner as BannerResource;

class UpgradeData implements \Magento\Framework\Setup\UpgradeDataInterface
{
    /**
     * @var ProductMetadataInterface
     */
    private $magentoMetadata;

    /**
     * @var BannerResource
     */
    private $bannerResource;

    /**
     * @var AggregatedFieldDataConverter
     */
    private $aggregatedFieldConverter;

    /**
     * @param ProductMetadataInterface     $productMetadata
     * @param AggregatedFieldDataConverter $aggregatedFieldConverter
     * @param BannerResource                $bannerResource
     */
    public function __construct(
        ProductMetadataInterface $productMetadata,
        AggregatedFieldDataConverter $aggregatedFieldConverter,
        BannerResource $bannerResource
    ) {
        $this->magentoMetadata = $productMetadata;
        $this->aggregatedFieldConverter = $aggregatedFieldConverter;
        $this->bannerResource = $bannerResource;
    }

    /**
     * @inheritdoc
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.3.0', '<')
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
                    $this->bannerResource->getMainTable(),
                    $this->bannerResource->getIdFieldName(),
                    'conditions_serialized'
                )
            ],
            $setup->getConnection()
        );
    }
}
