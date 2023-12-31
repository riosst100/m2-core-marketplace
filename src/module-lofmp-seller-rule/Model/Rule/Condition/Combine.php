<?php
/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * https://landofcoder.com/terms
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Landofcoder
 * @package    Lofmp_SellerRule
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lofmp\SellerRule\Model\Rule\Condition;

use Magento\Rule\Model\Condition\Context;

class Combine extends \Magento\Rule\Model\Condition\Combine
{
    /**
     * @var \Lofmp\SellerRule\Model\Rule\Condition\SellerAttributes
     */
    protected $sellerAttributes;

    /**
     * Combine constructor.
     * @param Context $context
     * @param \Lofmp\SellerRule\Model\Rule\Condition\SellerAttributes $sellerAttributes
     * @param array $data
     */
    public function __construct(
        Context $context,
        \Lofmp\SellerRule\Model\Rule\Condition\SellerAttributes $sellerAttributes,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->setType(\Lofmp\SellerRule\Model\Rule\Condition\Combine::class);
        $this->sellerAttributes = $sellerAttributes;
    }

    /**
     * @return array
     */
    public function getNewChildSelectOptions(): array
    {
        $conditions = parent::getNewChildSelectOptions();
        try {
            return array_merge_recursive(
                $conditions,
                [
                    [
                        'label' => __('Conditions Combination'),
                        'value' => \Lofmp\SellerRule\Model\Rule\Condition\Combine::class,
                    ],
                    [
                        'label' => __('Seller'),
                        'value' => $this->getSellerCondOptions()
                    ],
                    [
                        'label' => __('Seller Attributes'),
                        'value' => $this->getSellerAttributeCondOptions()
                    ],
                    [
                        'label' => __('Seller Usage'),
                        'value' => $this->getSellerUsageCondOptions()
                    ]
                ]
            );
        } catch (\Exception $e) {
            return array_merge_recursive(
                $conditions,
                [
                    [
                        'label' => __('Conditions Combination'),
                        'value' => Combine::class,
                    ]
                ]
            );
        }
    }

    /**
     * @return array[]
     */
    private function getSellerUsageCondOptions(): array
    {
        $options = [];
        $sellerUsages = $this->sellerAttributes->getSellerUsage();
        foreach ($sellerUsages as $usage => $usageLabel) {
            $options[] = [
                'value' => 'Lofmp\SellerRule\Model\Rule\Condition\Seller|' . $usage,
                'label' => $usageLabel,
            ];
        }
        return $options;
    }

    /**
     * @return array[]
     */
    private function getSellerCondOptions(): array
    {
        return [
            [
                'value' => 'Lofmp\SellerRule\Model\Rule\Condition\Seller|groups',
                'label' => __('Seller Group')
            ],
            [
                'value' => 'Lofmp\SellerRule\Model\Rule\Condition\Seller|specified',
                'label' => __('Specified Sellers')
            ]
        ];
    }

    /**
     * @return array
     */
    private function getSellerAttributeCondOptions(): array
    {
        $attributes = [];
        $sellerAttributes = $this->sellerAttributes->getSellerAllAttributes();
        foreach ($sellerAttributes as $attribute => $attributeLabel) {
            $attributes[] = [
                'value' => 'Lofmp\SellerRule\Model\Rule\Condition\Seller|' . $attribute,
                'label' => $attributeLabel,
            ];
        }
        return $attributes;
    }
}
