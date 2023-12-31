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

class View extends \Lofmp\Rma\Controller\Rma
{
    /**
     * @var \Lofmp\Rma\Api\Repository\CustomerRmaRepositoryInterface
     */
    protected $rmaRepository;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    public function __construct(
        \Lofmp\Rma\Api\Repository\CustomerRmaRepositoryInterface $rmaRepository,
        \Magento\Framework\Registry $registry,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\App\Action\Context $context
    ) {
        $this->rmaRepository     = $rmaRepository;
        $this->registry          = $registry;
        parent::__construct($customerSession, $context);
    }
    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        try {
            $customerId = $this->customerSession->getCustomerId();
            $id = $this->getRequest()->getParam('id');
            $rma = $this->rmaRepository->getById($customerId, $id);
            if($rma && $rma->getId()){
                $this->registry->register('current_rma', $rma);
                /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
                $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
                $this->initPage($resultPage);
                return $resultPage;
            }
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            /** $e->getMessage() */
            $this->messageManager->addErrorMessage($e->getMessage());
        }
        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*');
    }
}
