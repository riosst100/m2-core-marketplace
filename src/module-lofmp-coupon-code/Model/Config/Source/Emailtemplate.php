<?php
/**
 * Venustheme
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * https://landofcoder.com/license
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category   Venustheme
 * @package    Lofmp_CouponCode
 * @copyright  Copyright (c) 2021 Landofcoder (https://landofcoder.com/)
 * @license    https://landofcoder.com/LICENSE-1.0.html
 */
namespace Lofmp\CouponCode\Model\Config\Source;
 
use Magento\Config\Model\Config\Source\Email\Template as MailTemplate;

class Emailtemplate extends MailTemplate
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        $options = parent::toOptionArray();
        $options[] = [
            'value' => 'sent_mail_with_visitor',
            'label' => __('Base Coupon Code Generation for Visitor template'),
        ];
        array_unshift(
            $options,
            [
                'value' => 'none',
                'label' => __('- Disable these emails -'),
            ]
        );

        return $options;
    }
}