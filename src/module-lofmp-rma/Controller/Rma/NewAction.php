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



namespace Lofmp\Rma\Controller\Rma;

use Magento\Framework\Controller\ResultFactory;

class NewAction extends \Lofmp\Rma\Controller\Rma
{
    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $orderid = $this->getRequest()->getParam('order_id');
        if ($orderid) {
            $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
            $this->initPage($resultPage);
            return $resultPage;
        } else {
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $this->messageManager->addErrorMessage(__("Please select one or more orders to request return. Empty is not accepted!"));
            return $resultRedirect->setPath('*/*/sellect');
        }
    }
}
