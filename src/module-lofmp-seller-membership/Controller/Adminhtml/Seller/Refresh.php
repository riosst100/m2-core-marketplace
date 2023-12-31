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
 * @package    Lofmp_SellerMembership
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lofmp\SellerMembership\Controller\Adminhtml\Seller;

use Lofmp\SellerMembership\Model\Membership;

class Refresh extends \Lofmp\SellerMembership\Controller\Adminhtml\Seller
{
    /**
     * @var  \Lofmp\SellerMembership\Model\ResourceModel\Membership\CollectionFactory $_membershipCollectionFactory
     */
    protected $_membershipCollectionFactory;

    /**
     * @var  \Magento\Ui\Component\MassAction\Filter $_filer
     */
    protected $_filer;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Ui\Component\MassAction\Filter $filter
     * @param \Lofmp\SellerMembership\Model\ResourceModel\Membership\CollectionFactory $_membershipCollectionFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Ui\Component\MassAction\Filter $filter,
        \Lofmp\SellerMembership\Model\ResourceModel\Membership\CollectionFactory $_membershipCollectionFactory
    ) {
        $this->_membershipCollectionFactory = $_membershipCollectionFactory;
        $this->_filer = $filter;
        parent::__construct($context, $coreRegistry);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        try {
            $collection = $this->_filer->getCollection($this->_membershipCollectionFactory->create());
            $count = 0;

            /**
             * @var \Lofmp\SellerMembership\Model\Membership $item
             */
            foreach ($collection->getItems() as $item) {
                $status = $item->getStatus();
                if ($status == Membership::ENABLE) {
                    $item->setStatus(Membership::DISABLE);
                } else {
                    $item->setStatus(Membership::ENABLE);
                }
                $item->save();
                $count++;
            }
            $this->messageManager->addSuccess(
                __('A total of %1 record(s) have been changed.', $count)
            );
        } catch (\Exception $exception) {
            $this->messageManager->addError($exception->getMessage());
        }

        return $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT)->setPath('*/*/index');
    }
}
