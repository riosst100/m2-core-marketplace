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
 * @package    Lof_CustomerMembership
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lofmp\SellerMembership\Controller\Adminhtml\Cancelrequest;

use Lofmp\SellerMembership\Helper\Data as HelperData;
use Lofmp\SellerMembership\Model\MembershipFactory;
use Lofmp\SellerMembership\Model\CancelrequestFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;

class MassDeclide extends \Magento\Backend\App\Action
{
    /**
     * @param HelperData
     */
    protected $_helperData;

    /**
     * @var MembershipFactory
     */
    protected $_membershipFactory;

    /**
     * @var CancelrequestFactory
     */
    protected $_cancelrequestFactory;

    /**
     * @var Filter
     */
    protected $_filter;


    /**
     * @var \Lof\MarketPlace\Model\SellerFactory
     */
    protected $_sellerFactory;

    /**
     * @var \Lof\MarketPlace\Model\ResourceModel\Seller
     */
    protected $_resource;

    /**
     * @var \Lof\MarketPlace\Model\ResourceModel\Seller\CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @var \Lofmp\SellerMembership\Helper\Email
     */
    protected $email;

    /**
     * @param Context
     * @param Filter
     * @param HelperData $helperData
     * @param MembershipFactory $membershipFactory
     * @param CancelrequestFactory $cancelrequestFactory
     * @param \Lof\MarketPlace\Model\SellerFactory $sellerFactory
     * @param \Lof\MarketPlace\Model\ResourceModel\Seller $resource
     * @param \Lof\MarketPlace\Model\ResourceModel\Seller\CollectionFactory $collectionFactory
     * @param \Lofmp\SellerMembership\Helper\Email $email
     */
    public function __construct(
        Context $context,
        Filter $filter,
        HelperData $helperData,
        MembershipFactory $membershipFactory,
        CancelrequestFactory $cancelrequestFactory,
        \Lof\MarketPlace\Model\SellerFactory $sellerFactory,
        \Lof\MarketPlace\Model\ResourceModel\Seller\CollectionFactory $collectionFactory,
        \Lof\MarketPlace\Model\ResourceModel\Seller $resource,
        \Lofmp\SellerMembership\Helper\Email $email
    ) {
        $this->_filter = $filter;
        $this->_helperData = $helperData;
        $this->_membershipFactory = $membershipFactory;
        $this->_cancelrequestFactory = $cancelrequestFactory;
        $this->_sellerFactory = $sellerFactory;
        $this->_resource = $resource;
        $this->_collectionFactory = $collectionFactory;
        $this->email = $email;
        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Lof_CustomerMembership::cancelrequest_save');
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute()
    {
        $status = \Lofmp\SellerMembership\Model\Cancelrequest::DECLIDED;
        $size = 0;

        $cancelrequest_collection = $this->_filter->getCollection($this->_cancelrequestFactory->create()->getCollection());
        $size = $cancelrequest_collection->getSize();

        try {
            foreach ($cancelrequest_collection as $cancelrequest_model) {
                if ($cancelrequest_model->getData('status') == \Lofmp\SellerMembership\Model\Cancelrequest::PENDING) {
                    $cancelrequest_model->setData('status', $status)->save();
                    $size++;
                    $this->updateMembership($cancelrequest_model->getData('membership_id'));
                }
            }
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('Something went wrong while saving.'));
            $this->messageManager->addError($e->getMessage());
        }

        $this->messageManager->addSuccess(__('A total of %1 record(s) have been declided.', $size));

        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/');
    }

    /**
     * @param $membership_id
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function updateMembership($membership_id)
    {
        $membership_model = $this->_membershipFactory->create()->load($membership_id);
        if ($membership_model->getId()) {
            $seller = $this->_collectionFactory->create()->addFieldToFilter('seller_id',
                $membership_model->getData('seller_id'))->getFirstItem();

            # Send email
            $this->email->sendDeclideCancelRequestEmail($seller, $membership_model);
        }
    }
}
