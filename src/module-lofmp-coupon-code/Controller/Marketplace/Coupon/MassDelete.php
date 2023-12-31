<?php
/**
 * Landofcoder
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
 * @category   Landofcoder
 * @package    Lof_CouponCode
 * @copyright  Copyright (c) 2021 Landofcoder (https://landofcoder.com/)
 * @license    https://landofcoder.com/LICENSE-1.0.html
 */

namespace Lofmp\CouponCode\Controller\Marketplace\Coupon;

class MassDelete extends \Lofmp\CouponCode\Controller\Marketplace\Coupon
{
    /**
     * @var string
     */
    protected $_allowedKey = 'Lofmp_CouponCode::coupon_delete';

    /**
     * execute mass action
     *
     * @param string $type
     * @return void
     */
    protected function _executeMassAction($type = "status")
    {
        if ($type == "delete") {
            $collection = $this->filter->getCollection($this->_getCollection());
            $count = $collection->getSize();
            foreach ($collection as $rule) {
                $coupon_id = $rule->getCouponId();
                $rule->delete();
                if($coupon_id) {
                    $model_sale_coupon = $this->_objectManager->create('Magento\SalesRule\Model\Coupon');
                    $model_sale_coupon->load($coupon_id);
                    $model_sale_coupon->delete();
                }
            }
            $this->messageManager->addSuccess(
                __('%1 items have been deleted.', $count)
            );
        } else {
            return parent::_executeMassAction($type);
        }
    }
}
