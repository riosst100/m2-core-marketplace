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



namespace Lofmp\Rma\Controller\Adminhtml;

abstract class Reason extends \Magento\Backend\App\Action
{
    public function __construct(
        \Lofmp\Rma\Model\ReasonFactory $reasonFactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Framework\Registry $registry,
        \Magento\Backend\App\Action\Context $context
    ) {
        $this->reasonFactory  = $reasonFactory;
        $this->localeDate     = $localeDate;
        $this->registry       = $registry;
        $this->context        = $context;
        $this->backendSession = $context->getSession();
        $this->resultFactory  = $context->getResultFactory();

        parent::__construct($context);
    }

    /**
     * @param \Magento\Backend\Model\View\Result\Page $resultPage
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function initPage($resultPage)
    {
        $resultPage->setActiveMenu('Lofmp_Rma::rma_rma');
        $resultPage->getConfig()->getTitle()->prepend(__('RMA Reason'));
        return $resultPage;
    }

    /**
     * @return \Lofmp\Rma\Model\Reason
     */
    public function _initReason()
    {
        $reason = $this->reasonFactory->create();
        if ($this->getRequest()->getParam('id')) {
            $reason->load($this->getRequest()->getParam('id'));
            if ($storeId = (int) $this->getRequest()->getParam('store')) {
                $reason->setStoreId($storeId);
            }
        }

        $this->registry->register('current_reason', $reason);

        return $reason;
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->context->getAuthorization()->isAllowed('Lofmp_Rma::rma_dictionary_reason');
    }

    /************************/
}
