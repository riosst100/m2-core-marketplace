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

class View extends \Lofmp\Rma\Controller\Guest
{
    public function __construct(
        \Lofmp\Rma\Api\Repository\RmaRepositoryInterface $rmaRepository,
        \Magento\Framework\Registry $registry,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Session\SessionManagerInterface      $sessionObj,
        \Magento\Framework\App\Action\Context $context
    ) {
        $this->rmaRepository     = $rmaRepository;
        $this->registry          = $registry;

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
        try {
            $id = $this->getRequest()->getParam('id');
            $rma = $this->rmaRepository->getById($id);
            $customer_email = $this->getSessionEmail();
            if ($rma && $rma->getId() && $customer_email == $rma->getCustomerEmail()) {
                $this->registry->register('current_rma', $rma);
            /* $this->rmaManagement->markAsRead($rma);*/
                /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
                $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
                return $resultPage;
            } else {
                return $resultRedirect->setPath('*/*/rmalist');
            }
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            /** @var \Magento\Framework\Controller\Result\Forward $resultForward */
            $resultForward = $this->resultFactory->create(ResultFactory::TYPE_FORWARD);
            return $resultForward->forward('noroute');
        }
    }
}
