<?php

namespace Gudoguy\DeprintersSync\Setup;

use Magento\Catalog\Model\Category;
use Magento\Eav\Model\Entity\Attribute as EavAttribute;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class UpgradeData implements UpgradeDataInterface
{
    /**
     * $eavSetupFactory
     *
     * @var EavSetupFactory
     */
    protected $eavSetupFactory;

    /**
     * $eavAttribute
     *
     * @var EavAttribute
     */
    protected $eavAttribute;

    /**
     * __construct
     *
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(
        EavSetupFactory $eavSetupFactory,
        EavAttribute $eavAttribute
    ) {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->eavAttribute = $eavAttribute;
    }

    /**
     * upgrade
     *
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        if ($context->getVersion() && version_compare($context->getVersion(), '1.1', '<')) {
            $this->addSkuAttribute($setup);
        }
    }

    /**
     * @param $eavSetup
     */
    public function addSkuAttribute(ModuleDataSetupInterface $setup): void
    {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
        $eavSetup->addAttribute(Category::ENTITY, 'deprinters_sku', [
            'type'         => 'varchar',
            'label'        => 'Deprinters Sku',
            'input'        => 'text',
            'sort_order'   => 100,
            'source'       => '',
            'global'       => 1,
            'visible'      => true,
            'required'     => false,
            'user_defined' => false,
            'default'      => null,
            'group'        => 'Content',
            'backend'      => ''
        ]);
    }
}
