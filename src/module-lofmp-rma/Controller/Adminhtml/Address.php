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

abstract class Address extends \Magento\Backend\App\Action
{
    public function __construct(
        \Lofmp\Rma\Model\AddressFactory $addressFactory,
        \Lofmp\Rma\Api\Repository\AddressRepositoryInterface $addressRepository,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Framework\Registry $registry,
        \Magento\Backend\App\Action\Context $context
    ) {
        $this->addressFactory    = $addressFactory;
        $this->addressRepository = $addressRepository;
        $this->localeDate        = $localeDate;
        $this->registry          = $registry;
        $this->context           = $context;
        $this->backendSession    = $context->getSession();
        $this->resultFactory     = $context->getResultFactory();

        parent::__construct($context);
    }

    /**
     * @param \Magento\Backend\Model\View\Result\Page $resultPage
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function initPage($resultPage)
    {
        $resultPage->setActiveMenu('Lofmp_Rma::rma_rma');
        $resultPage->getConfig()->getTitle()->prepend(__('RMA Return Address'));
        return $resultPage;
    }

    /**
     * @return \Lofmp\Rma\Model\Address
     */
    public function _initAddress()
    {
        $address = $this->addressFactory->create();
        if ($this->getRequest()->getParam('id')) {
            $address->load($this->getRequest()->getParam('id'));
        }

        $this->registry->register('current_address', $address);

        return $address;
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->context->getAuthorization()->isAllowed('Lofmp_Rma::rma_return_addresses');
    }

    /************************/
}
