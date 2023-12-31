<?php
/**
 * LandofCoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * https://landofcoder.com/license
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   LandofCoder
 * @package    Lofmp_CouponCode
 * @copyright  Copyright (c) 2016 Landofcoder (https://landofcoder.com/)
 * @license    https://landofcoder.com/LICENSE-1.0.html
 */

namespace Lofmp\CouponCode\Controller\Adminhtml\Coupon;

use Magento\Framework\Controller\ResultFactory;

class MassDelete extends \Lofmp\CouponCode\Controller\Adminhtml\Coupon
{
    /**
     * @var \Magento\Ui\Component\MassAction\Filter
     */
    protected $filter;

    /**
     * @var \Lofmp\CouponCode\Model\ResourceModel\Coupon\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @param \Magento\Backend\App\Action\Context                             $context
     * @param \Magento\Ui\Component\MassAction\Filter                         $filter
     * @param \Lofmp\CouponCode\Model\ResourceModel\Coupon\CollectionFactory    $collectionFactory
     * @param  \Magento\Framework\Registry                                    $coreRegistry
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Ui\Component\MassAction\Filter $filter,
        \Lofmp\CouponCode\Model\ResourceModel\Coupon\CollectionFactory $collectionFactory,
        \Magento\Framework\Registry $coreRegistry
    ) {
        parent::__construct($context, $coreRegistry);
        $this->filter             = $filter;
        $this->collectionFactory  = $collectionFactory;
    }

    /**
     * Execute action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     * @throws \Magento\Framework\Exception\LocalizedException|\Exception
     */
    public function execute()
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        foreach ($collection as $rule) {
            $coupon_id = $rule->getCouponId();
            $rule->delete();
            if($coupon_id) {
                $model_sale_coupon = $this->_objectManager->create('Magento\SalesRule\Model\Coupon');
                $model_sale_coupon->load($coupon_id);
                $model_sale_coupon->delete();
            }
        }
        $this->messageManager->addSuccess(__('A total of %1 record(s) have been deleted.', $collection->count()));
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/');
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Lofmp_CouponCode::coupon_delete');
    }
}
