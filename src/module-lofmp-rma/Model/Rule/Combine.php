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



namespace Lofmp\Rma\Model\Rule;

class Combine extends \Magento\Rule\Model\Condition\Combine
{
    /**
     * {@inheritdoc}
     */
    public function getNewChildSelectOptions()
    {
        $actions = parent::getNewChildSelectOptions();
        $actions = array_merge_recursive($actions, [
            [
                'value' => 'rma/rule_action_product',
                'label' => __('Update the Product'), ],
        ]);

        return $actions;
    }
}
