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

use Lofmp\SellerMembership\Api\ProductMembershipRepositoryInterface;
use Lofmp\SellerMembership\Api\Data\ProductMembershipSearchResultsInterfaceFactory;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Lofmp\SellerMembership\Helper\Data as HelperData;
use Lofmp\SellerMembership\Model\CancelrequestFactory;
use Lofmp\SellerMembership\Model\MembershipFactory;
use Magento\Catalog\Model\ProductFactory;

class ProductMembershipRepository implements ProductMembershipRepositoryInterface
{
    /**
     * @var \Lofmp\SellerMembership\Model\MembershipFactory
     */
    protected $_membershipFactory;

    /**
     * @var ProductFactory
     */
    protected $_productFactory;

    /**
     * @var CollectionProcessorInterface
     */
    protected $_collectionProcessor;

    /**
     * @var ProductMembershipSearchResultsInterfaceFactory
     */
    protected $_searchResultsFactory;

    /**
     * @var HelperData
     */
    protected $_helperData;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * @var null
     */
    public $_productCollection = null;

    /**
     * ProductMembershipRepository constructor.
     * @param ProductFactory $productFactory
     * @param \Lofmp\SellerMembership\Model\MembershipFactory $membershipFactory
     * @param CollectionProcessorInterface $collectionProcessor
     * @param ProductMembershipSearchResultsInterfaceFactory $searchResultsFactory
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Magento\Catalog\Model\Product\Visibility $productVisibility
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param HelperData $helperData
     */
    public function __construct(
        ProductFactory $productFactory,
        MembershipFactory $membershipFactory,
        CollectionProcessorInterface $collectionProcessor,
        ProductMembershipSearchResultsInterfaceFactory $searchResultsFactory,
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Catalog\Model\Product\Visibility $productVisibility,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        HelperData $helperData
    ) {
        $this->_productFactory = $productFactory;
        $this->_membershipFactory = $membershipFactory;
        $this->_collectionProcessor = $collectionProcessor;
        $this->_searchResultsFactory = $searchResultsFactory;
        $this->_helperData = $helperData;
        $this->productVisibility = $productVisibility;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->catalogConfig = $context->getCatalogConfig();
    }

    /**
     * {@inheritdoc}
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria)
    {
        $product_collection = $this->productCollectionFactory->create();
        $product_collection->addAttributeToFilter('type_id', 'customer_membership');
        $product_collection->addAttributeToSelect($this->catalogConfig->getProductAttributes())
            ->addMinimalPrice()
            ->addFinalPrice()
            ->addTaxPercents()
            ->setVisibility($this->productVisibility->getVisibleInCatalogIds());

        $this->_collectionProcessor->process($searchCriteria, $product_collection);
        $searchResults = $this->_searchResultsFactory->create();
        
        $searchResults->setSearchCriteria($searchCriteria);

        $product_array = [];
       
        foreach ($product_collection as $membership_model) {
            $this->_helperData->setDurationArray($membership_model);

            $product_array[] = $membership_model;
        }

        $searchResults->setItems($product_array);
        $searchResults->setTotalCount($product_collection->getSize());

        return $searchResults;
    }

    /**
     * {@inheritdoc}
     */
    public function getByCustomer($customerId, $storeId = null)
    {
        $result = null;

        $membership_model = $this->_membershipFactory->create();
        $membership_model->load($customerId, 'customer_id');
        if (!$membership_model->getId()) {
            throw new NoSuchEntityException(__('Customer Id "%1" does not have any membership.', $customerId));
        }

        $product_id = $membership_model->getData('product_id');
        $product_collection = $this->productCollectionFactory->create();
        $product_collection->addAttributeToFilter('type_id', 'customer_membership');
        $product_collection->addAttributeToSelect($this->catalogConfig->getProductAttributes())
            ->addMinimalPrice()
            ->addFinalPrice()
            ->addTaxPercents()
            ->setVisibility($this->productVisibility->getVisibleInCatalogIds());

        $product_model = $product_collection->addFieldToFilter('entity_id', ['in' => [$product_id]])->getFirstItem();

        $this->_helperData->setDurationArray($product_model);

        if ($product_model->getId()) {
            $result = $product_model;
        } else {
            throw new NoSuchEntityException(__('Customer Id "%1" does not have any membership.', $customerId));
        }

        return $result;
    }
}
