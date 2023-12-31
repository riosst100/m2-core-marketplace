<?php
/**
 * Landofcoder
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
 * @category   Landofcoder
 * @package    Lofmp_FavoriteSeller
 * @copyright  Copyright (c) 2018 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */
namespace Lofmp\FavoriteSeller\Block\Marketplace\Index;

class Index extends \Magento\Framework\View\Element\Template {

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var \Lofmp\FavoriteSeller\Model\ResourceModel\Subscription\CollectionFactory
     */
    protected $subscriptionCollectionFactory;

    /**
     * @var \Magento\Framework\Api\FilterBuilder
     */
    protected $filterBuilder;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var \Lof\MarketPlace\Helper\Data $marketplaceHelper
     */
    public $marketplaceHelper;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Lofmp\FavoriteSeller\Model\ResourceModel\Subscription\CollectionFactory $subscriptionCollectionFactory
     * @param \Magento\Framework\Api\FilterBuilder $filterBuilder
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \Lof\MarketPlace\Helper\Data $marketplaceHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Lofmp\FavoriteSeller\Model\ResourceModel\Subscription\CollectionFactory $subscriptionCollectionFactory,
        \Magento\Framework\Api\FilterBuilder $filterBuilder,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Lof\MarketPlace\Helper\Data $marketplaceHelper,
        array $data = []
    ) {
        $this->customerRepository = $customerRepository;
        $this->subscriptionCollectionFactory = $subscriptionCollectionFactory;
        $this->filterBuilder = $filterBuilder;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->marketplaceHelper = $marketplaceHelper;
        parent::__construct($context, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->pageConfig->getTitle()->set(__('Subscription Customers'));
    }

    /**
     * @param $sellerId
     * @return \Magento\Customer\Api\Data\CustomerInterface[]|null
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getSubscriptionCustomers($sellerId) {
        $customerIds = $this->getSubscriptionCustomerIds($sellerId);
        if(!$customerIds) return null;

        $customerRepository = $this->customerRepository;

        $idFilter = $this->filterBuilder
                        ->setField('entity_id')
                        ->setValue($customerIds)
                        ->setConditionType('in')
                        ->create();

        $searchCriteria = $this->searchCriteriaBuilder
                        ->addFilter('entity_id', $customerIds, 'in')
                        ->create();

        $customerCollection = $customerRepository->getList($searchCriteria)->getItems();

        return count($customerCollection) != 0 ? $customerCollection : null;
    }

    /**
     * @param $sellerId
     * @return array|bool
     */
    public function getSubscriptionCustomerIds($sellerId){
        if (!$sellerId) {
            return false;
        }

        $subscriptionCollection = $this->subscriptionCollectionFactory->create();
        $customerIdSet = $subscriptionCollection->addFieldToFilter('seller_id', $sellerId)
            ->addFieldToSelect('customer_id')
            ->load();
        $customerIds = [];
        foreach($customerIdSet as $item){
            $customerIds[] = $item->getCustomerId();
        }
        return count($customerIds) != 0 ? $customerIds : false;
    }

    /**
     * @return int
     */
    public function getSellerId()
    {
        return $this->marketplaceHelper->getSellerId();
    }

    /**
     * @return \Lof\MarketPlace\Helper\Data
     */
    public function getMarketPlaceHelper(){
        return $this->marketplaceHelper;
    }

    /**
     * @param $customerId
     * @return string
     */
    public function getDeleteUrl($customerId){
        return $this->_urlBuilder->getUrl('favoriteseller/action/delete', ['id' => $customerId]);
    }

}