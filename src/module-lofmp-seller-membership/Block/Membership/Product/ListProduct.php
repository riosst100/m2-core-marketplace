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

namespace Lofmp\SellerMembership\Block\Membership\Product;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Lof\MarketPlace\Model\Commission as CommissionRule;

class ListProduct extends \Magento\Catalog\Block\Product\ListProduct
{
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $_productCollectionFactory;

    /**
     * Catalog product visibility
     *
     * @var \Magento\Catalog\Model\Product\Visibility
     */
    protected $productVisibility;

    /**
     * @var \Lof\MarketPlace\Model\Group
     */
    protected $group;

    /**
     * @var CommissionRule
     */
    protected $commission;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $_resource;

    /**
     * ListProduct constructor.
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Magento\Framework\Data\Helper\PostHelper $postDataHelper
     * @param \Magento\Catalog\Model\Layer\Resolver $layerResolver
     * @param CategoryRepositoryInterface $categoryRepository
     * @param \Magento\Framework\Url\Helper\Data $urlHelper
     * @param CollectionFactory $productCollectionFactory
     * @param \Magento\Catalog\Model\Product\Visibility $productVisibility
     * @param \Lof\MarketPlace\Model\Group $group
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param CommissionRule $commission
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\Data\Helper\PostHelper $postDataHelper,
        \Magento\Catalog\Model\Layer\Resolver $layerResolver,
        CategoryRepositoryInterface $categoryRepository,
        \Magento\Framework\Url\Helper\Data $urlHelper,
        CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Model\Product\Visibility $productVisibility,
        \Lof\MarketPlace\Model\Group $group,
        \Magento\Framework\App\ResourceConnection $resource,
        \Lof\MarketPlace\Model\Commission $commission,
        array $data = []
    ) {
        $this->group = $group;
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->productVisibility = $productVisibility;
        $this->commission = $commission;
        $this->_resource = $resource;
        $this->catalogConfig = $context->getCatalogConfig();

        parent::__construct(
            $context,
            $postDataHelper,
            $layerResolver,
            $categoryRepository,
            $urlHelper,
            $data
        );
    }

    /**
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection|\Magento\Eav\Model\Entity\Collection\AbstractCollection
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _getProductCollection()
    {
        if ($this->_productCollection === null) {
            $this->_productCollection = $this->_productCollectionFactory->create();
            $this->_productCollection->addAttributeToFilter('type_id', 'seller_membership');
            $this->_productCollection->addAttributeToSelect($this->catalogConfig->getProductAttributes())
                ->addMinimalPrice()
                ->addFinalPrice()
                ->addTaxPercents()
                ->setVisibility($this->productVisibility->getVisibleInCatalogIds());
        }

        return $this->_productCollection;
    }

    /**
     * @param $groupId
     * @return \Magento\Framework\DataObject
     */
    public function getGroup($groupId)
    {
        $group = $this->group->getCollection()->addFieldToFilter('group_id', $groupId)->getFirstItem();
        return $group;
    }

    /**
     * @param $groupId
     * @return array
     */
    public function getOption($groupId)
    {
        $option = [];
        $group = $this->getGroup($groupId)->getData();

        if (is_array($group) && count($group) > 0) {
            if ($group['can_add_product'] == 1) {
                $option[] = __('Can add product');
            }
            if ($group['can_cancel_order'] == 1) {
                $option[] = __('Can cancel order');
            }
            if ($group['can_create_invoice'] == 1) {
                $option[] = __('Can create invoice');
            }
            if ($group['can_create_shipment'] == 1) {
                $option[] = __('Can create shipment');
            }
            if ($group['hide_payment_info'] == 1) {
                $option[] = __('Hide payment info');
            }
            if ($group['hide_customer_email'] == 1) {
                $option[] = __('Hide customer email');
            }
            if ($group['can_use_shipping'] == 1) {
                $option[] = __('Can Use Shipping');
            }
            if ($group['can_submit_order_comments'] == 1) {
                $option[] = __('Can submit order comments');
            }
            if ($group['can_use_message'] == 1) {
                $option[] = __('Can use message');
            }
            if ($group['can_use_review'] == 1) {
                $option[] = __('Can use review');
            }
            if ($group['can_use_rating'] == 1) {
                $option[] = __('Can use rating');
            }
            if ($group['can_use_import_export'] == 1) {
                $option[] = __('Can import/export product');
            }
            if ($group['can_use_vacation'] == 1) {
                $option[] = __('Can use vacation');
            }
            if ($group['can_use_report'] == 1) {
                $option[] = __('Can use report');
            }
            if ($group['can_use_withdrawal'] == 1) {
                $option[] = __('Can use withdrawal');
            }
            return $option;
        }
    }

    /**
     * @param $groupId
     * @return array
     */
    public function getExtraOptions($groupId)
    {
        return [];
    }

    /**
     * Get store ids to which specified item is assigned
     *
     * @param int $commission_id
     * @return array
     */
    public function lookupGroupIds($commission_id)
    {
        $connection = $this->_resource->getConnection();
        $table = $this->_resource->getTableName('lof_marketplace_commission_group');
        $select = $connection->select('group_id')->from(
            $table
        )
            ->where(
                'commission_id = ?',
                (int)$commission_id
            );
        $groups = [];
        foreach ($connection->fetchAll($select) as $key => $commission) {
            $groups[] = $commission['group_id'];
        }
        return $groups;
    }

    /**
     * @param $groupId
     * @return string
     */
    public function getFeeCommission($groupId)
    {
        if ($this->getCommission($groupId)) {
            $commission = $this->getCommission($groupId)->getData();
        } else {
            $commission = 0;
        }
        if (is_array($commission)) {
            switch ($commission['commission_by']) {
                case CommissionRule::COMMISSION_BY_FIXED_AMOUNT:
                    $_commission = $this->marketHelper->getPriceFomat($commission['commission_amount']) . __('fee for each sales');
                    break;
                case CommissionRule::COMMISSION_BY_PERCENT_PRODUCT_PRICE:
                    $_commission = $commission['commission_amount'] * 100 / 100 . '% ' . __('fee for each sales');
                    break;
            }
            return $_commission;
        }
    }

    /**
     * @param $groupId
     * @return mixed
     */
    public function getCommission($groupId)
    {
        $commission = $this->commission->getCollection();
        foreach ($commission as $key => $_commission) {
            $groups = $this->lookupGroupIds($_commission->getId());

            if (in_array($groupId, $groups)) {
                return $_commission;
            }
        }
    }

    /**
     * @param array $data
     * @return $this
     */
    public function setWidgetData($data = [])
    {
        if ($data) {
            foreach ($data as $key => $val) {
                $this->setData($key, $val);
            }
        }
        return $this;
    }
}
