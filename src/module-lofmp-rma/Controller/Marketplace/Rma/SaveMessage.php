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

namespace Lofmp\Rma\Controller\Marketplace\Rma;

use Magento\Framework\Controller\ResultFactory;

class SaveMessage extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Lof\MarketPlace\Model\SellerFactory
     */
    protected $sellerFactory;

    /**
     * @var \Lofmp\Rma\Api\Repository\RmaRepositoryInterface
     */
    protected $rmaRepository;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Lofmp\Rma\Api\Repository\MessageRepositoryInterface
     */
    protected $messageRepository;

    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $eventManager;

    /**
     * SaveMessage constructor.
     *
     * @param \Magento\Framework\Registry $registry
     * @param \Lofmp\Rma\Api\Repository\RmaRepositoryInterface $rmaRepository
     * @param \Lofmp\Rma\Api\Repository\MessageRepositoryInterface $messageRepository
     * @param \Lof\MarketPlace\Helper\Data $helper
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Lof\MarketPlace\Model\SellerFactory $sellerFactory
     */
    public function __construct(
        \Magento\Framework\Registry $registry,
        \Lofmp\Rma\Api\Repository\RmaRepositoryInterface $rmaRepository,
        \Lofmp\Rma\Api\Repository\MessageRepositoryInterface $messageRepository,
        \Lof\MarketPlace\Helper\Data $helper,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\App\Action\Context $context,
        \Lof\MarketPlace\Model\SellerFactory $sellerFactory
    ) {
        $this->registry = $registry;
        $this->helper = $helper;
        $this->rmaRepository = $rmaRepository;
        $this->messageRepository = $messageRepository;
        $this->resultFactory = $context->getResultFactory();
        $this->eventManager = $eventManager;
        $this->customerSession = $customerSession;
        $this->sellerFactory = $sellerFactory;
        parent::__construct($context);
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
            $id = (int)$data['id'];
            $customerId = $this->customerSession->getCustomer()->getId();
            $seller = $this->sellerFactory->create()->load($customerId, 'customer_id');
            $rma = $this->rmaRepository->getById($id);
            $rma->setStatusId($data['status_id'])
                ->setReturnAddress($data['return_address']);

            $this->rmaRepository->save($rma);
            if (!$this->registry->registry('current_rma')) {
                $this->registry->register('current_rma', $rma);
            }
            $files = $this->getRequest()->getFiles();
            if ((isset($data['reply']) && $data['reply'] != '') || count($files->toArray()) > 0) {
                $message = $this->messageRepository->create();
                $message->setRmaId($rma->getId())
                    ->setText($data['reply']);
                if (isset($data['internalcheck'])) {
                    $message->setInternal($data['internalcheck'])
                        ->setIsCustomerNotified(false)
                        ->setSellerId($seller->getData('seller_id'));
                } else {
                    $message->setInternal(0)
                        ->setIsCustomerNotified(true)
                        ->setSellerId($seller->getData('seller_id'));
                }

                $this->messageRepository->save($message);
                $rma->setLastReplyName($seller->getData('name'));
                $this->rmaRepository->save($rma);
                $this->eventManager->dispatch(
                    'rma_add_message_after',
                    ['rma' => $rma, 'message' => $message, 'user' => $seller, 'params' => $data]
                );
            }
            $this->eventManager->dispatch('rma_update_rma_after', ['rma' => $rma, 'user' => $seller]);
            $this->messageManager->addSuccessMessage(__('Your message was successfuly added'));
            return $resultRedirect->setPath('*/rma/view', ['id' => $rma->getId(), '_nosid' => true]);
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            return $resultRedirect->setPath('*/*/index');
        }
    }
}
