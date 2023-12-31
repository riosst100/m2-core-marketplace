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
 * @package    Lof_MarketPlace
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lof\MarketPlace\Plugin\Sales\Controller\Adminhtml\Order;

class View
{
    /**
     * @param \Magento\Sales\Controller\Adminhtml\Order\View $subject
     * @param $result
     * @return mixed|string
     */
    public function afterExecute(\Magento\Sales\Controller\Adminhtml\Order\View $subject, $result)
    {
        $backUrl = $subject->getRequest()->getParam('back_url');
        if ($backUrl === 'lofmarketplace_order') {
            if ($result instanceof \Magento\Backend\Model\View\Result\Page) {
                return $result->setActiveMenu('Lof_MarketPlace::marketplace');
            }
        }

        return $result;
    }
}
