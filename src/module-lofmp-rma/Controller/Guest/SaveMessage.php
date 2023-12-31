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

class SaveMessage extends \Lofmp\Rma\Controller\Guest
{
    public function __construct(
        \Magento\Framework\Registry $registry,
        \Lofmp\Rma\Api\Repository\RmaRepositoryInterface $rmaRepository,
        \Lofmp\Rma\Api\Repository\MessageRepositoryInterface $messageRepository,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Session\SessionManagerInterface      $sessionObj,
        \Magento\Framework\Event\ManagerInterface               $eventManager,
        \Magento\Framework\App\Action\Context $context
    ) {
        $this->registry             = $registry;
        $this->rmaRepository        = $rmaRepository;
        $this->messageRepository     = $messageRepository;
        $this->resultFactory        = $context->getResultFactory();
         $this->eventManager         = $eventManager;
        $this->customerSession      = $customerSession;

        parent::__construct($sessionObj, $customerSession, $context);
    }


    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        try {
            $data = $this->getRequest()->getParams();
            //---
            $messageSuccess = isset($data['shipping_confirmation']) ? 'Shipping confirm was successfuly' : 'Your message was successfuly added';
            //---
            $id = (isset($data['id']) && $data['id'] !== null) ? (int)$data['id'] : 0;
            if ($data && $id) {
                $rma = $this->rmaRepository->getById($id);
                $customer_email = $this->getSessionEmail();
                if ($rma->getCustomerEmail() != $customer_email) {
                    return $resultRedirect->setPath('*/*/rmalist');
                }
                if (!$this->registry->registry('current_rma')) {
                    $this->registry->register('current_rma', $rma);
                }
                if (isset($data['shipping_confirmation'])) {
                    //$messagetext = $data['replyConfirmShipping'];
                    $messagetext = __('I confirm that I have sent the package to the RMA department.');
                    $rma->setStatusId(4); //4: package sent;

                } else {
                    $messagetext = isset($data['reply']) ? $data['reply'] : '';
                }

//                $messagetext = $data['reply']; //---

                if ($messagetext) {
                    $data = [
                        'isNotifyAdmin' => 1,
                        'isNotified'    => 0,
                    ];

                    $message = $this->messageRepository->create();
                    $message->setRmaId($id)
                        ->setText($messagetext, false);


                    if (!isset($data['isNotified'])) {
                        $data['isNotified'] = 1;
                    }
                    if (!isset($data['isVisible'])) {
                        $data['isVisible'] = 1;
                    }
                        $message->setIsCustomerNotified($data['isNotified']);
                        $message->setIsVisibleInFrontend($data['isVisible']);
                        $message->setCustomerId(0)
                            ->setCustomerEmail($customer_email)
                            ->setCustomerName($customer_email);


                    $this->messageRepository->save($message);

                    $rma->setLastReplyName($customer_email)
                        ->setIsAdminRead(0);

                    $this->rmaRepository->save($rma);
                    $this->eventManager->dispatch(
                        'rma_guest_add_message_after',
                        ['rma'=> $rma, 'message' => $message, 'customer_email' => $customer_email, 'params' => $data]
                    );
                                $this->messageManager->addSuccessMessage(__($messageSuccess));
                }
            }

            return $resultRedirect->setPath('*/guest/view', ['id' => $id, '_nosid' => true]);
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            return $resultRedirect->setPath('*/*/index');
        }
    }
}
