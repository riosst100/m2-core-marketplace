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

use Magento\Framework\Model\AbstractModel;

class Seller extends \Lofmp\SellerRule\Model\Rule\Condition\AbstractSellers
{
    /**
     * @param AbstractModel $model
     * @return bool
     */
    public function validate(AbstractModel $model): bool
    {
        $attrCode = $this->getAttribute();
        switch ($attrCode) {
            case 'groups':
                $validatedValue = $model->getData('group_id');
                $result = $this->validateAttribute($validatedValue);
                return (bool)$result;
            case 'specified':
                $validatedValue = $model->getId();
                $result = $this->validateAttribute($validatedValue);
                return (bool)$result;
            default:
                return parent::validate($model);
        }
    }
}
