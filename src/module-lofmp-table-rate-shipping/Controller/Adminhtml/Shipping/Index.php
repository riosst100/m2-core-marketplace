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
 * @package    Lofmp_TableRateShipping
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lofmp\TableRateShipping\Controller\Adminhtml\Shipping;

use Magento\Framework\Controller\ResultFactory;

class Index extends \Lofmp\TableRateShipping\Controller\Adminhtml\Shipping
{
    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Lofmp_TableRateShipping::mpshipping');
        $resultPage->getConfig()->getTitle()->prepend(__('Manage Table Rates'));
        $resultPage->addBreadcrumb(
            __('Manage Table Rates'),
            __('Manage Table Rates')
        );
        return $resultPage;
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Lofmp_TableRateShipping::shipping');
    }
}
