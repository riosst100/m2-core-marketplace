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

use \Lofmp\Rma\Api\Repository\ResolutionRepositoryInterface;

abstract class Resolution extends \Magento\Backend\App\Action
{
    public function __construct(
        \Lofmp\Rma\Model\ResolutionFactory $resolutionFactory,
        ResolutionRepositoryInterface $resolutionRepository,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Framework\Registry $registry,
        \Magento\Backend\App\Action\Context $context
    ) {
        $this->resolutionFactory    = $resolutionFactory;
        $this->resolutionRepository = $resolutionRepository;
        $this->localeDate           = $localeDate;
        $this->registry             = $registry;
        $this->context              = $context;
        $this->backendSession       = $context->getSession();
        $this->resultFactory        = $context->getResultFactory();

        parent::__construct($context);
    }

    /**
     * @param \Magento\Backend\Model\View\Result\Page $resultPage
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function initPage($resultPage)
    {
        $resultPage->setActiveMenu('Lofmp_Rma::rma_rma');
        $resultPage->getConfig()->getTitle()->prepend(__('RMA Resolution'));
        return $resultPage;
    }

    /**
     * @return \Lofmp\Rma\Model\Resolution
     */
    public function _initResolution()
    {
        $resolution = $this->resolutionFactory->create();
        if ($this->getRequest()->getParam('id')) {
            $resolution->load($this->getRequest()->getParam('id'));
            if ($storeId = (int) $this->getRequest()->getParam('store')) {
                $resolution->setStoreId($storeId);
            }
        }

        $this->registry->register('current_resolution', $resolution);

        return $resolution;
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->context->getAuthorization()->isAllowed('Lofmp_Rma::rma_dictionary_resolution');
    }

    /************************/
}
