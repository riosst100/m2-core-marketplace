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

namespace Lofmp\SellerMembership\Model;

use Lofmp\SellerMembership\Api\CancelrequestRepositoryInterface;
use Lofmp\SellerMembership\Api\Data\CancelrequestSearchResultsInterfaceFactory;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Lofmp\SellerMembership\Helper\Data as HelperData;

class CancelrequestRepository implements CancelrequestRepositoryInterface
{
    /**
     * @var MembershipFactory
     */
    protected $_membershipFactory;

    /**
     * @var CancelrequestFactory
     */
    protected $_cancelrequestFactory;

    /**
     * @var CollectionProcessorInterface
     */
    protected $_collectionProcessor;

    /**
     * @var CancelrequestSearchResultsInterfaceFactory
     */
    protected $_searchResultsFactory;

    /**
     * @var HelperData
     */
    protected $_helperData;

    /**
     * CancelrequestRepository constructor.
     * @param MembershipFactory $membershipFactory
     * @param CancelrequestFactory $cancelrequestFactory
     * @param CollectionProcessorInterface $collectionProcessor
     * @param CancelrequestSearchResultsInterfaceFactory $searchResultsFactory
     * @param HelperData $helperData
     */
    public function __construct(
        MembershipFactory $membershipFactory,
        CancelrequestFactory $cancelrequestFactory,
        CollectionProcessorInterface $collectionProcessor,
        CancelrequestSearchResultsInterfaceFactory $searchResultsFactory,
        HelperData $helperData
    ) {
        $this->_membershipFactory = $membershipFactory;
        $this->_cancelrequestFactory = $cancelrequestFactory;
        $this->_collectionProcessor = $collectionProcessor;
        $this->_searchResultsFactory = $searchResultsFactory;
        $this->_helperData = $helperData;
    }

    /**
     * {@inheritdoc}
     */
    public function save(\Lofmp\SellerMembership\Api\Data\CancelrequestInterface $cancelrequest_data)
    {
        try {
            if ($cancelrequest_data->getId()) {
                $cancelrequest_model = $this->_cancelrequestFactory->create()->load($cancelrequest_data->getId());
                $cancelrequest_model->addData($cancelrequest_data->getData());
            } else {
                $cancelrequest_model = $this->_cancelrequestFactory->create();
                $cancelrequest_model->addData($cancelrequest_data->getData());
            }

            $cancelrequest_model->save();
            $Cancelrequest_id = $cancelrequest_model->getId();
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__('Could not save the membership: %1', $exception->getMessage()));
        }

        return $this->_cancelrequestFactory->create()->load($Cancelrequest_id);
    }

    /**
     * {@inheritdoc}
     */
    public function getById($cancelrequest_id)
    {
        $cancelrequest_model = $this->_cancelrequestFactory->create();
        $cancelrequest_model->load($cancelrequest_id);

        if (!$cancelrequest_model->getId()) {
            throw new NoSuchEntityException(__('Cancel Request with id "%1" does not exist.', $cancelrequest_id));
        } else {
//            $this->_helperData->setMembershipMoreData($membership_model);
        }

        return $cancelrequest_model;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria)
    {
        $cancelrequest_collection = $this->_cancelrequestFactory->create()->getCollection();
        $this->_collectionProcessor->process($searchCriteria, $cancelrequest_collection);
        $searchResults = $this->_searchResultsFactory->create();

        $searchResults->setSearchCriteria($searchCriteria);

        $cancelrequest_array = [];
        foreach ($cancelrequest_collection as $membership_model) {
//            $this->_helperData->setMembershipMoreData($membership_model);
            $cancelrequest_array[] = $membership_model;
        }
        $searchResults->setItems($cancelrequest_array);

        $searchResults->setTotalCount($cancelrequest_collection->getSize());
        return $searchResults;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(\Lofmp\SellerMembership\Api\Data\CancelrequestInterface $cancelrequest_data)
    {
        try {
            $cancelrequest_resource = $this->_cancelrequestFactory->create()->getResource();
            $cancelrequest_resource->delete($cancelrequest_data);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__('Could not delete the Cancel Request : %1', $exception->getMessage()));
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($cancelrequest_id)
    {
        try {
            $cancelrequest_model = $this->_cancelrequestFactory->create();

            $cancelrequest_model->load($cancelrequest_id);

            if (!$cancelrequest_model->getId()) {
                throw new NoSuchEntityException(__('Cancel Request with id "%1" does not exist.', $cancelrequest_id));
            }

            $cancelrequest_model->delete();
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__('Could not delete the Cancel Request: %1', $exception->getMessage()));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function saveByCustomer($customerId, \Lofmp\SellerMembership\Api\Data\CancelrequestInterface $cancelrequest_data)
    {
        $cancelrequest_id = 0;
        try {
            # Load membership is active by customer
            $exist = $this->getCancelRequestPending($customerId);
            if (!$exist) {
                $membership_model = $this->getMembershipByCustomer($customerId);

                $cancelrequest_data->unsetData('entity_id');
                $cancelrequest_data->unsetData('admin_comment');
                $cancelrequest_data->setData('membership_id', $membership_model->getData('membership_id'));
                $cancelrequest_data->setData('status', \Lofmp\SellerMembership\Model\Cancelrequest::PENDING);
                $cancelrequest_data->setData('product_id', $membership_model->getData('product_id'));
                $data = $cancelrequest_data->getData();
                if( isset($data['entity_id'])) {
                    unset($data['entity_id']);
                }
                $cancelrequest_model = $this->_cancelrequestFactory->create();
                $cancelrequest_model->addData($data);
                $cancelrequest_model->save();
                $cancelrequest_id = $cancelrequest_model->getId();
            }

        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__('Could not create the Cancel Request: %1', $exception->getMessage()));
        }

        return $this->_cancelrequestFactory->create()->load($cancelrequest_id);
    }

    /**
     * @param $customerId
     * @return bool
     * @throws NoSuchEntityException
     */
    public function getCancelRequestPending($customerId)
    {
        # Load membership by customer_id
        $membership_model = $this->_membershipFactory->create();
        $membership_model->load($customerId, 'customer_id');
        if (!$membership_model->getId()) {
            throw new NoSuchEntityException(__('Customer Id "%1" does not have any membership.', $customerId));
        }


        # Load cancel_request pending by membership_id
        $membership_id = $membership_model->getData('membership_id');
        $cancelrequest_collection = $this->_cancelrequestFactory->create()->getCollection()
            ->addFieldToFilter('membership_id', $membership_id)
            ->addFieldToFilter('status', \Lofmp\SellerMembership\Model\Cancelrequest::PENDING);

        if ($cancelrequest_collection->getSize()) {
            # Customer not have cancelrequest peding
            throw new NoSuchEntityException(__('Customer Id have %1 Cancel Request (Pending).', $cancelrequest_collection->getSize()));
            return true;
        }
        return false;
    }

    /**
     * @param $customerId
     * @return Membership
     * @throws NoSuchEntityException
     */
    public function getMembershipByCustomer($customerId)
    {
        # Load membership by customer_id
        $membership_model = $this->_membershipFactory->create();
        $membership_model->load($customerId, 'customer_id');
        if (!$membership_model->getId()) {
            throw new NoSuchEntityException(__('Customer Id "%1" does not have any membership.', $customerId));
        }
        return $membership_model;
    }
}
