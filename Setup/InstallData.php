<?php


namespace DoIRun\ShipperHQShipper\Setup;

use Magento\Catalog\Setup\CategorySetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class InstallData implements InstallDataInterface
{
    protected $categorySetupFactory;

    public function __construct(
        CategorySetupFactory $categorySetupFactory
    )
    {
        $this->categorySetupFactory = $categorySetupFactory;
    }

    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        $catalogSetup = $this->categorySetupFactory->create(['setup' => $setup]);

        $catalogSetup->addAttribute(\Magento\Catalog\Model\Product::ENTITY,'doirun_dim_use_options', [
            'type'                      => 'int',
            'input'                     => 'boolean',
            'label'                     => 'Use Product Options for Dimensions',
            'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
            'visible'                   => true,
            'required'                  => false,
            'visible_on_front'          => false,
            'is_html_allow_on_front'    => false,
            'searchable'                => false,
            'filterable'                => false,
            'comparable'                => false,
            'is_configurable'           => false,
            'unique'                    => false,
            'user_define'               => true,
            'used_in_product_listing'   => false
        ]);
        $catalogSetup->addAttribute(\Magento\Catalog\Model\Product::ENTITY,'doirun_dim_options_height', [
            'type'                      => 'varchar',
            'input'                     => 'text',
            'label'                     => 'Options Height',
            'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
            'visible'                   => true,
            'required'                  => false,
            'visible_on_front'          => false,
            'is_html_allow_on_front'    => false,
            'searchable'                => false,
            'filterable'                => false,
            'comparable'                => false,
            'is_configurable'           => false,
            'unique'                    => false,
            'user_define'               => true,
            'used_in_product_listing'   => false,
            'note'                      => 'This value only used when dim_use_options set to yes'
        ]);
        $catalogSetup->addAttribute(\Magento\Catalog\Model\Product::ENTITY,'doirun_dim_options_width', [
            'type'                      => 'varchar',
            'input'                     => 'text',
            'label'                     => 'Options Width',
            'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
            'visible'                   => true,
            'required'                  => false,
            'visible_on_front'          => false,
            'is_html_allow_on_front'    => false,
            'searchable'                => false,
            'filterable'                => false,
            'comparable'                => false,
            'is_configurable'           => false,
            'unique'                    => false,
            'user_define'               => true,
            'used_in_product_listing'   => false,
            'note'                      => 'This value only used when dim_use_options set to yes'
        ]);
        $catalogSetup->addAttribute(\Magento\Catalog\Model\Product::ENTITY,'doirun_dim_options_length', [
            'type'                      => 'varchar',
            'input'                     => 'text',
            'label'                     => 'Options Length',
            'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
            'visible'                   => true,
            'required'                  => false,
            'visible_on_front'          => false,
            'is_html_allow_on_front'    => false,
            'searchable'                => false,
            'filterable'                => false,
            'comparable'                => false,
            'is_configurable'           => false,
            'unique'                    => false,
            'user_define'               => true,
            'used_in_product_listing'   => false,
            'note'                      => 'This value only used when dim_use_options set to yes'
        ]);
        $entityTypeId = $catalogSetup->getEntityTypeId(\Magento\Catalog\Model\Product::ENTITY);
        $attributeSetArr = $catalogSetup->getAllAttributeSetIds($entityTypeId);
        $dimAttributeCodes = [
            'doirun_dim_use_options' => '30',
            'doirun_dim_options_height' => '31',
            'doirun_dim_options_width' => '32',
            'doirun_dim_options_length' => '33',
        ];
        foreach ($attributeSetArr as $attributeSetId) {
            $attributeGroupId = $catalogSetup->getAttributeGroupId($entityTypeId, $attributeSetId,'Dimensional Shipping');
            foreach ($dimAttributeCodes as $code => $sort) {
                $attributeId = $catalogSetup->getAttributeId($entityTypeId, $code);
                $catalogSetup->AddAttributeToGroup($entityTypeId, $attributeSetId, $attributeGroupId, $attributeId, $sort);
            }
        }
        $installer->endSetup();
    }
}