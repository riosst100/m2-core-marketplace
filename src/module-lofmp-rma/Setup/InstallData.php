<?php
/**
 * LandOfCoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * https://landofcoder.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   LandOfCoder
 * @package    Lofmp_Rma
 * @copyright  Copyright (c) 2021 Landofcoder (https://landofcoder.com/)
 * @license    https://landofcoder.com/LICENSE-1.0.html
 */



namespace Lofmp\Rma\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Eav\Setup\EavSetupFactory;

/**
 * @codeCoverageIgnore
 */
class InstallData implements InstallDataInterface
{
    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface   $context
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @return void
     */
    public function __construct(
        EavSetupFactory $eavSetupFactory
    ) {
        $this->eavSetupFactory = $eavSetupFactory;
    }

    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {

        $setup->startSetup();
         $select = $setup->getConnection()->select()
                ->from(
                    $setup->getTable('sales_order'),
                    [
                        'entity_id',
                        'status',
                        'created_at'
                    ]
                );
            $select = $setup->getConnection()->insertFromSelect(
                $select,
                $setup->getTable('lofmp_rma_order_status_history'),
                [
                    'order_id',
                    'status',
                    'created_at'
                ]
            );
            $setup->getConnection()->query($select);

            $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
        $data = [
            'group' => 'General',
                    'type' => 'int',
                    'backend' => '',
                    'frontend' => '',
                    'label' => 'Not Allow Rma',
                    'input' => 'boolean',
                    'class' => '',
                    'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                    'visible' => true,
                    'required' => false,
                    'user_defined' => false,
                    'default' => '1',
                    'searchable' => false,
                    'filterable' => false,
                    'comparable' => false,
                    'visible_on_front' => false,
                    'used_in_product_listing' => false,
                    'unique' => false,
                    'apply_to' => ''
            ];
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'product_rma',
            $data
        );
        
        
        $data = [
            [
                'condition_id' => 1,
                'name'         => 'Unopened',
                'sort_order'   => 10,
                'is_active'    => 1,
            ],
            [
                'condition_id' => 2,
                'name'         => 'Opened',
                'sort_order'   => 20,
                'is_active'    => 1,
            ],
            [
                'condition_id' => 3,
                'name'         => 'Damaged',
                'sort_order'   => 30,
                'is_active'    => 1,
            ],
        ];
        foreach ($data as $row) {
            $setup->getConnection()->insertForce($setup->getTable('lofmp_rma_condition'), $row);
        }

        $data = [
            [
                'reason_id'  => 1,
                'name'       => 'Out of Service',
                'sort_order' => 10,
                'is_active'  => 1,
            ],
            [
                'reason_id'  => 2,
                'name'       => 'Wrong Item',
                'sort_order' => 20,
                'is_active'  => 1,
            ],
            [
                'reason_id'  => 3,
                'name'       => 'Wrong color',
                'sort_order' => 30,
                'is_active'  => 1,
            ],
            [
                'reason_id'  => 5,
                'name'       => 'Wrong size',
                'sort_order' => 40,
                'is_active'  => 1,
            ],
            [
                'reason_id'  => 6,
                'name'       => 'Other',
                'sort_order' => 50,
                'is_active'  => 1,
            ],
        ];
        foreach ($data as $row) {
            $setup->getConnection()->insertForce($setup->getTable('lofmp_rma_reason'), $row);
        }

        $data = [
            [
                'resolution_id' => 1,
                'name'          => 'Exchange',
                'sort_order'    => 10,
                'is_active'     => 1,
                'code'          => 'exchange',
            ],
            [
                'resolution_id' => 2,
                'name'          => 'Refund',
                'sort_order'    => 20,
                'is_active'     => 1,
                'code'          => 'refund',
            ],
            [
                'resolution_id' => 3,
                'name'          => 'Store Credit',
                'sort_order'    => 30,
                'is_active'     => 1,
                'code'          => 'credit',
            ],
        ];
        foreach ($data as $row) {
            $setup->getConnection()->insertForce($setup->getTable('lofmp_rma_resolution'), $row);
        }
// @codingStandardsIgnoreStart
        $data = [
            [
                'status_id'        => 1,
                'name'             => 'Pending Approval',
                'sort_order'       => 10,
                'is_active'        => 1,
                'code'             => 'pending',
                'is_show_shipping'  => 0,
                'customer_message' =>
"Dear {{var customer.name}},
<br><br>\n\nYour Return request has been received.
You will be notified when your request is reviewed.",
'admin_message'    => "RMA #{{var rma.increment_id}} has been created.",
'history_message'  => "Return request has been received.
You will be notified when your request is reviewed.",
            ],
            [
                'status_id'        => 2,
                'name'             => 'Approved',
                'sort_order'       => 20,
                'is_active'        => 1,
                'code'             => 'approved',
                'is_show_shipping'  => 1,
                'customer_message' => "Dear {{var customer.name}},
<br><br>\n\nYour Return request has been approved.\n
<br>\nPlease, print RMA Packing Slip and RMA Shipping Label
\nand send package to:<br>\n{{var rma.return_address_html | raw}}",
'admin_message'    => '',
'history_message'  => "Your Return request has been approved.\n
<br>\nPlease, print RMA Packing Slip ,RMA Shipping Label
\nand send package to:<br>\n{{var rma.return_address | raw}}",
            ],
            [
                'status_id'        => 3,
                'name'             => 'Rejected',
                'sort_order'       => 30,
                'is_active'        => 1,
                'code'             => 'rejected',
                'is_show_shipping'  => 0,
                'customer_message' => "Dear {{var customer.name}},<br><br>\n\nReturn request has been rejected.",
                'admin_message'    => '',
                'history_message'  => 'Return request has been rejected.',
            ],
            [
                'status_id'        => 4,
                'name'             => 'Package Sent',
                'sort_order'       => 25,
                'is_active'        => 1,
                'code'             => 'package_sent',
                'is_show_shipping'  => 0,
                'customer_message' => '',
                'admin_message'    => 'Package is sent.',
                'history_message'  => '',
            ],
            [
                'status_id'        => 5,
                'name'             => 'Closed',
                'sort_order'       => 10,
                'is_active'        => 1,
                'code'             => 'closed',
                'is_show_shipping'  => 0,
                'customer_message' => 'Dear {{var customer.name}},<br><br>\n\nYour Return request has been closed.',
                'admin_message'    => '',
                'history_message'  => 'Return request has been closed.',
            ],
        ];
// @codingStandardsIgnoreEnd
        foreach ($data as $row) {
            $setup->getConnection()->insertForce($setup->getTable('lofmp_rma_status'), $row);
        }
        $setup->endSetup();
    }
}
