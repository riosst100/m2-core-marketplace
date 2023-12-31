<?php
/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * https://landofcoder.com/terms
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Landofcoder
 * @package    Lofmp_FlatRateShipping
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lofmp\FlatRateShipping\Controller\Adminhtml\Shipping;

use Lofmp\FlatRateShipping\Model\ShippingFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;

class Delete extends \Magento\Backend\App\Action
{
    /**
     * @var ShippingFactory
     */
    protected $shippingFactory;

    /**
     * @param Context $context
     * @param ShippingFactory $shippingFactory
     */
    public function __construct(
        Context $context,
        ShippingFactory $shippingFactory
    ) {
        parent::__construct($context);
        $this->shippingFactory = $shippingFactory;
    }

    /**
     * Execute action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     * @throws \Magento\Framework\Exception\LocalizedException|\Exception
     */
    public function execute()
    {
        $Id = $this->getRequest()->getParam('lofmpshipping_id');
        $shipping = $this->shippingFactory->create();
        $shipping->load($Id);
        $shipping->delete()->save();
        $this->messageManager->addSuccessMessage(__('A total of %1 record(s) have been deleted.'));

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/');
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Lofmp_FlatRateShipping::shipping');
    }
}
