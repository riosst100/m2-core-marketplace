<?php
/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * http://www.landofcoder.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Landofcoder
 * @package    Lofmp_FavoriteSeller
 * @copyright  Copyright (c) 2018 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */
namespace Lofmp\FavoriteSeller\Model\Config\Source;
 
use Magento\Config\Model\Config\Source\Email\Template as MailTemplate;

class EmailTemplate extends MailTemplate
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        $options = parent::toOptionArray();
        $options[] = [
            'value' => 'favoriteseller_config_email_email_template',
            'label' => __('Base Seller Send Email To Subscriber template'),
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