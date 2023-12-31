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



namespace Lofmp\Rma\Controller\Guest;

use Magento\Framework\Controller\ResultFactory;

class PrintLabel extends \Lofmp\Rma\Controller\Guest
{
    public function __construct(
        \Lofmp\Rma\Api\Repository\RmaRepositoryInterface $rmaRepository,
        \Lofmp\Rma\Helper\Data                            $datahelper,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Session\SessionManagerInterface      $sessionObj,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Action\Context $context
    ) {
        $this->rmaRepository        = $rmaRepository;
        $this->registry             = $registry;
        $this->datahelper           = $datahelper;
        parent::__construct($sessionObj, $customerSession, $context);
    }

    

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        if (!$this->isLoggedIn()) {
            return $resultRedirect->setPath('*/*/login');
        }
        $customer_email = $this->getSessionEmail();
        $id = $this->getRequest()->getParam('id');
        $rma = $this->rmaRepository->getById($id);
        if (!$rma) {
            return $resultRedirect->setPath('/');
        }
        if ($rma->getCustomerEmail() != $customer_email) {
            return $resultRedirect->setPath('*/*/rmalist');
        }
        $attachments = $this->datahelper->getAttachments('return_label', $rma->getId());
        if ($label = array_shift($attachments)) {
            return $resultRedirect->setPath('*/attachment/download', ['uid' => $label->getUid()]);
        } else {
            $this->_forward('no_rote');
        }
    }
}
