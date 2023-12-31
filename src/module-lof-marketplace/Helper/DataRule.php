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
 * @package    Lof_MarketPlace
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lof\MarketPlace\Helper;

use Lof\MarketPlace\Model\SellerFactory;
use Magento\Customer\Api\CustomerRepositoryInterface;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class DataRule extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Lof\MarketPlace\Model\Condition\Sql\Builder
     */
    protected $sqlBuilder;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * @var \Magento\Checkout\Model\Cart
     */
    protected $cart;

    /**
     * @var \Magento\Quote\Model\QuoteFactory
     */
    protected $quoteFactory;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\Collection
     */
    protected $_collection;

    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_session;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Lof\MarketPlace\Model\ResourceModel\Commission\CollectionFactory
     */
    protected $commission;

    /**
     * @var \Lof\MarketPlace\Model\ResourceModel\ShippingCommission\CollectionFactory
     */
    protected $shippingCommission;

    /**
     * @var SellerFactory
     */
    protected $sellerFactory;

    /**
     * DataRule constructor.
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param CustomerRepositoryInterface $customerRepository
     * @param \Magento\Sales\Model\ResourceModel\Order\Collection $collection
     * @param \Lof\MarketPlace\Model\Condition\Sql\Builder $sqlBuilder
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param \Magento\Quote\Model\QuoteFactory $quoteFactory
     * @param \Magento\Checkout\Model\Cart $cart
     * @param \Magento\Checkout\Model\Session $session
     * @param SellerFactory $sellerFactory
     * @param \Lof\MarketPlace\Model\ResourceModel\Commission\CollectionFactory $commission
     * @param \Lof\MarketPlace\Model\ResourceModel\ShippingCommission\CollectionFactory $shippingCommission
     * @param \Magento\Customer\Model\Session $customerSession
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        CustomerRepositoryInterface $customerRepository,
        \Magento\Sales\Model\ResourceModel\Order\Collection $collection,
        \Lof\MarketPlace\Model\Condition\Sql\Builder $sqlBuilder,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Quote\Model\QuoteFactory $quoteFactory,
        \Magento\Checkout\Model\Cart $cart,
        \Magento\Checkout\Model\Session $session,
        SellerFactory $sellerFactory,
        \Lof\MarketPlace\Model\ResourceModel\Commission\CollectionFactory $commission,
        \Lof\MarketPlace\Model\ResourceModel\ShippingCommission\CollectionFactory $shippingCommission,
        \Magento\Customer\Model\Session $customerSession
    ) {
        parent::__construct($context);
        $this->cart = $cart;
        $this->sqlBuilder = $sqlBuilder;
        $this->storeManager = $storeManager;
        $this->customerSession = $customerSession;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->quoteFactory = $quoteFactory;
        $this->_collection = $collection;
        $this->customerRepository = $customerRepository;
        $this->_session = $session;
        $this->commission = $commission;
        $this->shippingCommission = $shippingCommission;
        $this->sellerFactory = $sellerFactory;
    }

    /**
     * @param string $storeId
     * @return \Magento\Store\Api\Data\StoreInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getStore($storeId = '')
    {
        return $this->storeManager->getStore($storeId);
    }

    /**
     * @return \Magento\Customer\Model\Customer
     */
    public function getCustomer()
    {
        return $this->customerSession->getCustomer();
    }

    /**
     * @return \Lof\MarketPlace\Model\ResourceModel\Commission\Collection
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getRules()
    {
        return $this->commission->create();
    }

    /**
     * @return \Lof\MarketPlace\Model\ResourceModel\ShippingCommission\Collection
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getShippingCommissionRules()
    {
        return $this->shippingCommission->create();
    }

    /**
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    public function getProductCollection()
    {
        $collection = $this->productCollectionFactory->create();
        $collection->addMinimalPrice()
            ->addFinalPrice()
            ->addTaxPercents()
            ->addUrlRewrite()
            ->addStoreFilter();

        return $collection;
    }

    /**
     * @param int $sellerId
     * @param int $entityId
     * @return mixed|null
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function getRuleProducts($sellerId, $entityId)
    {
        $seller = $this->sellerFactory->create();
        $storeId = $this->storeManager->getStore()->getId();
        $seller = $seller->load($sellerId);
        $today = (new \DateTime())->format('Y-m-d');
        $rules = $this->getRules()->addFieldToFilter('is_active', 1)->setOrder('priority', 'ASC');
        $rules->getSelect()
            ->where(
                '(from_date IS NULL OR from_date<=?) AND (to_date IS NULL OR to_date>=?)',
                $today,
                $today
            )->order("priority ASC");
        $commissionRules = $rules;
        try {
            $foundRules = [];
            //Step1: Find match commission rule by apply condition rules
            foreach ($commissionRules as $commissionRule) {
                $group_ids = $commissionRule->getGroupId();
                $group_ids = is_array($group_ids) ? $group_ids : [(int)$group_ids];
                $store_ids = $commissionRule->getData('store_id');
                $store_ids = is_array($store_ids) ? $store_ids : [(int)$store_ids];
                if (in_array($seller->getGroupId(), $group_ids)
                ) {
                    if ((in_array($storeId, $store_ids)
                            || in_array(0, $store_ids))
                    ) {
                        $collection = $this->getProductCollection();
                        $collection->getSelect()->reset(\Magento\Framework\DB\Select::WHERE);
                        $conditions = $commissionRule->getActions();
                        $conditions->collectValidatedAttributes($collection);
                        $collection->getSelect()->where('e.entity_id IN (?) ', $entityId);
                        $this->sqlBuilder->attachConditionToCollection($collection, $conditions);

                        if (count($collection->getData()) > 0 && !isset($foundRules[$commissionRule->getId()])) {
                            $foundRules[$commissionRule->getId()] = $commissionRule;
                            if ($commissionRule->getData('stop_rules_processing')) {
                                break;
                            }
                        }
                    }
                }
            }
            //Step2: if not found rule when apply condition rule will get default commission rule
            if (!$foundRules) {
                foreach ($rules as $_rules) {
                    $group_ids = $_rules->getGroupId();
                    $group_ids = is_array($group_ids) ? $group_ids : [(int)$group_ids];
                    $store_ids = $_rules->getData('store_id');
                    $store_ids = is_array($store_ids) ? $store_ids : [(int)$store_ids];

                    if ((in_array($storeId, $store_ids) || in_array(0, $store_ids))
                        && in_array($seller->getGroupId(), $group_ids)
                    ) {
                        $foundRules[$_rules->getId()] = $_rules;
                        if ($_rules->getData('stop_rules_processing')) {
                            break;
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            throw $e;
        }
        return $foundRules && count($foundRules) > 0 ? array_slice($foundRules, 0, 1)[0] : null;
    }

    /**
     * @param int $entityId
     * @return mixed|null
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function getRuleShippingCommission($sellerId)
    {
        $seller = $this->sellerFactory->create();
        $storeId = $this->storeManager->getStore()->getId();
        $seller = $seller->load($sellerId);
        $today = (new \DateTime())->format('Y-m-d');
        $rules = $this->getShippingCommissionRules()->addFieldToFilter('is_active', 1)->setOrder('priority', 'ASC');
        $rules->getSelect()
            ->where(
                '(from_date IS NULL OR from_date<=?) AND (to_date IS NULL OR to_date>=?)',
                $today,
                $today
            );

        $commissionRules = $rules;
        $foundRules = [];
        foreach ($commissionRules as $commissionRule) {
            $group_ids = $commissionRule->getGroupId();
            $group_ids = is_array($group_ids) ? $group_ids : [(int)$group_ids];
            $store_ids = $commissionRule->getData('store_id');
            $store_ids = is_array($store_ids) ? $store_ids : [(int)$store_ids];

            if (in_array($seller->getGroupId(), $group_ids)
                && (in_array($storeId, $store_ids) || in_array(0, $store_ids))
            ) {
                $foundRules[$commissionRule->getId()] = $commissionRule;
                // foreach ($rules as $_rules) {
                //     if ((in_array($storeId, $_rules->getData('store_id')) || in_array(0, $_rules->getData('store_id')))
                //         && $_rules
                //         && count($_rules->getData()) > 0
                //         && in_array($seller->getGroupId(), $_rules->getGroupId())
                //     ) {
                //         return $_rules;
                //     }
                // }
            }
        }
        return $foundRules && count($foundRules) > 0 ? array_slice($foundRules, 0, 1)[0] : null;
    }
}
