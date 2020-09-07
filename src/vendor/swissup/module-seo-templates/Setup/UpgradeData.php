<?php

namespace Swissup\SeoTemplates\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\App\Config\ValueFactory;

class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var ValueFactory
     */
    protected $configValueFactory;

    /**
     * @param ValueFactory $configValueFactory
     */
    public function __construct(ValueFactory $configValueFactory)
    {
        $this->configValueFactory = $configValueFactory;
    }

    /**
     * @inheritdoc
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.5.0', '<')) {
            $this->removeOldCronConfigs($setup);
        }

        $setup->endSetup();
    }

    /**
     * Cleanup config table. Remove old records that can cause conflicts.
     */
    public function removeOldCronConfigs()
    {
        $keys = [
            'swissup_seotemplates/cron_process/entity_type',
            'swissup_seotemplates/cron_process/page_size',
            'swissup_seotemplates/cron_process/cur_page',
            'swissup_seotemplates/cron_process/status',
            'swissup_seotemplates/cron_process/last_run',
            'crontab/default/jobs/swissup_seotemplates_generate/schedule/cron_expr',
            'crontab/default/jobs/swissup_seotemplates_generate/run/model'
        ];
        $config = $this->configValueFactory->create();
        foreach ($keys as $key) {
            $config->load($key, 'path')->delete();
        }
    }
}
