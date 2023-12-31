<?php
/**
 * LandOfCoder
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
 * @category   LandOfCoder
 * @package    Lofmp_Rma
 * @copyright  Copyright (c) 2020 Landofcoder (http://www.LandOfCoder.com/)
 * @license    http://www.LandOfCoder.com/LICENSE-1.0.html
 */

namespace Lofmp\Rma\Controller\Guest\Newrma;

use Magento\Framework\Controller\ResultFactory;

class Bundle extends \Lofmp\Rma\Controller\Guest
{

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $order_id = $this->getRequest()->getParam("order_id");
        if ($order_id) {
            $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
            return $resultPage;
        } else {
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $this->messageManager->addErrorMessage(__("Please select one or more orders to request return. Empty is not accepted!"));
            return $resultRedirect->setPath('*/*/sellect');
        }
    }
}
