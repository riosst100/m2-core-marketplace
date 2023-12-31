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

use Lofmp\SellerMembership\Api\MembershipRepositoryInterface;
use Lofmp\SellerMembership\Api\Data\MembershipSearchResultsInterfaceFactory;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Lofmp\SellerMembership\Helper\Data as HelperData;

class MembershipRepository implements MembershipRepositoryInterface
{
    /**
     * @var MembershipFactory
     */
    protected $_membershipFactory;

    /**
     * @var CollectionProcessorInterface
     */
    protected $_collectionProcessor;

    /**
     * @var MembershipSearchResultsInterfaceFactory
     */
    protected $_searchResultsFactory;

    /**
     * @var HelperData
     */
    protected $_helperData;

    /**
     * MembershipRepository constructor.
     * @param MembershipFactory $membershipFactory
     * @param CollectionProcessorInterface $collectionProcessor
     * @param MembershipSearchResultsInterfaceFactory $searchResultsFactory
     * @param HelperData $helperData
     */
    public function __construct(
        MembershipFactory $membershipFactory,
        CollectionProcessorInterface $collectionProcessor,
        MembershipSearchResultsInterfaceFactory $searchResultsFactory,
        HelperData $helperData
    ) {
        $this->_membershipFactory = $membershipFactory;
        $this->_collectionProcessor = $collectionProcessor;
        $this->_searchResultsFactory = $searchResultsFactory;
        $this->_helperData = $helperData;
    }

    /**
     * {@inheritdoc}
     */
    public function save(\Lofmp\SellerMembership\Api\Data\MembershipInterface $membership_data)
    {
        try {
            if ($membership_data->getId()) {
                $membership_model = $this->_membershipFactory->create()->load($membership_data->getId());
                $membership_model->addData($membership_data->getData());
            } else {
                $membership_model = $this->_membershipFactory->create();
                $membership_model->addData($membership_data->getData());
            }

            $membership_model->save();
            $membership_id = $membership_model->getId();
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__('Could not save the membership: %1', $exception->getMessage()));
        }

        return $this->_membershipFactory->create()->load($membership_id);
    }

    /**
     * {@inheritdoc}
     */
    public function getById($membershipId)
    {
        $membership_model = $this->_membershipFactory->create();
        $membership_model->load($membershipId);

        if (!$membership_model->getId()) {
            throw new NoSuchEntityException(__('Membership with id "%1" does not exist.', $membershipId));
        } else {
//            $this->_helperData->setMembershipMoreData($membership_model);
        }

        return $membership_model;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria)
    {
        $membership_collection = $this->_membershipFactory->create()->getCollection();
        $this->_collectionProcessor->process($searchCriteria, $membership_collection);
        $searchResults = $this->_searchResultsFactory->create();

        $searchResults->setSearchCriteria($searchCriteria);

        $membership_array = [];
        foreach ($membership_collection as $membership_model) {
//            $this->_helperData->setMembershipMoreData($membership_model);
            $membership_array[] = $membership_model;
        }
        $searchResults->setItems($membership_array);

        $searchResults->setTotalCount($membership_collection->getSize());
        return $searchResults;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(\Lofmp\SellerMembership\Api\Data\MembershipInterface $membership_data)
    {
        try {
            $membership_resource = $this->_membershipFactory->create()->getResource();
            $membership_resource->delete($membership_data);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__('Could not delete the membership : %1', $exception->getMessage()));
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($membershipId)
    {
        try {
            $membership_model = $this->_membershipFactory->create();

            $membership_model->load($membershipId);

            if (!$membership_model->getId()) {
                throw new NoSuchEntityException(__('Membership with id "%1" does not exist.', $membershipId));
            }

            $membership_model->delete();
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__('Could not delete the membership: %1', $exception->getMessage()));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getMyMembership($customerId)
    {
        $membership_model = $this->_membershipFactory->create();
        $membership_model->load($customerId, 'customer_id');
        if (!$membership_model->getId()) {
            throw new NoSuchEntityException(__('Customer Id "%1" does not have any membership.', $customerId));
        }
        return $membership_model;
    }
}
