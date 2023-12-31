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

namespace Lofmp\CouponCode\Controller\Adminhtml\Rule;

use Magento\Framework\Controller\ResultFactory;

class MassDelete extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Ui\Component\MassAction\Filter
     */
    protected $filter;

    /**
     * @var \Lofmp\CouponCode\Model\ResourceModel\Rule\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @param \Magento\Backend\App\Action\Context                             $context           
     * @param \Magento\Ui\Component\MassAction\Filter                         $filter            
     * @param \Lof\RewardPoints\Model\ResourceModel\Earning\CollectionFactory $collectionFactory 
     * @param \Lof\RewardPoints\Helper\Data                                   $rewardsData       
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Ui\Component\MassAction\Filter $filter,
        \Lofmp\CouponCode\Model\ResourceModel\Rule\CollectionFactory $collectionFactory
    ) {
        parent::__construct($context);
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
        /*$collection = $this->filter->getCollection($this->collectionFactory->create());
        foreach ($collection as $item) {
            $item->delete();
        }
        $this->messageManager->addSuccess(__('A total of %1 record(s) have been deleted.', $collection->getSize()));*/
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $model_sale_rule = $objectManager->create('Magento\SalesRule\Model\Rule');
        $i = 0 ; 
        foreach ($collection as $item) {
            $sale_rule_id = $item->getData("rule_id");
            $item->delete();
            $model_sale_rule->load($sale_rule_id);
            $model_sale_rule->delete();
            $i++;
        }
        $this->messageManager->addSuccess(__('A total of %1 record(s) have been deleted.', $i));
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/');
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Lofmp_CouponCode::rule_delete');
    }
}
