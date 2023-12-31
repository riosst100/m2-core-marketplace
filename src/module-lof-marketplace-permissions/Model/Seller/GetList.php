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
 * @package    Lof_MarketPermissions
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lof\MarketPermissions\Model\Seller;

/**
 * Class for retrieveing lists of seller model entities based on a given search criteria.
 */
class GetList
{
    /**
     * @var \Lof\MarketPlace\Model\ResourceModel\Seller\CollectionFactory
     */
    private $sellerCollectionFactory;

    /**
     * @var \Lof\MarketPermissions\Api\Data\SellerSearchResultsInterfaceFactory
     */
    private $searchResultsFactory;

    /**
     * @var \Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface
     */
    private $extensionAttributesJoinProcessor;

    /**
     * @var \Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface
     */
    private $collectionProcessor;

    /**
     * @param \Lof\MarketPlace\Model\ResourceModel\Seller\CollectionFactory $sellerCollectionFactory
     * @param \Lof\MarketPermissions\Api\Data\SellerSearchResultsInterfaceFactory $searchResultsFactory
     * @param \Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param \Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface $collectionProcessor
     */
    public function __construct(
        \Lof\MarketPlace\Model\ResourceModel\Seller\CollectionFactory $sellerCollectionFactory,
        \Lof\MarketPermissions\Api\Data\SellerSearchResultsInterfaceFactory $searchResultsFactory,
        \Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface $extensionAttributesJoinProcessor,
        \Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface $collectionProcessor
    ) {
        $this->sellerCollectionFactory = $sellerCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->collectionProcessor = $collectionProcessor;
    }

    /**
     * Gets a list of sellers.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $criteria
     * @return \Lof\MarketPermissions\Api\Data\SellerSearchResultsInterface
     * @throws \InvalidArgumentException
     * @throws \LogicException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $criteria)
    {
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);

        /** @var \Lof\MarketPlace\Model\ResourceModel\Seller\Collection $collection */
        $collection = $this->sellerCollectionFactory->create();
        $this->extensionAttributesJoinProcessor->process($collection);

        $this->collectionProcessor->process($criteria, $collection);

        $searchResults->setTotalCount($collection->getSize());
        $searchResults->setItems($collection->getItems());
        return $searchResults;
    }
}
