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
 * @copyright  Copyright (c) 2020 Landofcoder (https://landofcoder.com/)
 * @license    https://landofcoder.com/LICENSE-1.0.html
 */


namespace Lofmp\Rma\Controller\Adminhtml\Customer;

class Index extends \Magento\Catalog\Controller\Adminhtml\Product
{
    /**
     * @var \Magento\Framework\View\Result\LayoutFactory
     */
    protected $resultLayoutFactory;

    /**
     * @param \Magento\Backend\App\Action\Context
     * @param \Magento\Catalog\Controller\Adminhtml\Product\Builder
     * @param \Magento\Framework\View\Result\LayoutFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Catalog\Controller\Adminhtml\Product\Builder $productBuilder,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
    ) {
        parent::__construct($context, $productBuilder);
        $this->resultLayoutFactory = $resultLayoutFactory;
    }
    /**
     * Get post grid
     *
     * @return \Magento\Framework\View\Result\Layout
     */
    public function execute()
    {
        $id = $this->getRequest()->getparam('id');
        $customer = $this->_objectManager->create('Magento\Customer\Model\Customer');
        $customer->load($id);
        $registry = $this->_objectManager->get('Magento\Framework\Registry');
        $registry->register("current_customer", $customer);
        $this->productBuilder->build($this->getRequest());
        $resultLayout = $this->resultLayoutFactory->create();
        $resultLayout->getLayout()->getBlock('rma.customer.edit.tab.rma');
        return $resultLayout;
    }
}
