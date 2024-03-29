<?php

namespace CoreMarketplace\SellerMembership\Block\Membership\Product;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Lof\MarketPlace\Model\Commission as CommissionRule;

class ListProduct extends \Lofmp\SellerMembership\Block\Membership\Product\ListProduct
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
     * @var \Lofmp\SellerMembership\Helper\Data
     */
    protected $membershipHelper;

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
     * @param \Lofmp\SellerMembership\Helper\Data $membershipHelper
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
        \Lofmp\SellerMembership\Helper\Data $membershipHelper,
        array $data = []
    ) {
        $this->group = $group;
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->productVisibility = $productVisibility;
        $this->commission = $commission;
        $this->_resource = $resource;
        $this->catalogConfig = $context->getCatalogConfig();
        $this->membershipHelper = $membershipHelper;

        parent::__construct(
            $context,
            $postDataHelper,
            $layerResolver,
            $categoryRepository,
            $urlHelper,
            $productCollectionFactory,
            $productVisibility,
            $group,
            $resource,
            $commission,
            $data
        );
    }

    public function filterMembershipDurationByWebsite($duration) 
    {
        return $this->membershipHelper->filterMembershipDurationByWebsite($duration);
    }

    public function getCurrentWebsiteId() 
    {
        return $this->membershipHelper->getCurrentWebsiteId();
    }

    /**
     * Need use as _prepareLayout - but problem in declaring collection from another block.
     * (was problem with search result)
     *
     * @return $this
     */
    protected function _beforeToHtml()
    {
        $collection = $this->_getProductCollection();

        $this->addToolbarBlock($collection);

        if (!$collection->isLoaded()) {
            $collection->load();
        }

        return parent::_beforeToHtml();
    }

    /**
     * Get toolbar block from layout
     *
     * @return bool|Toolbar
     */
    private function getToolbarFromLayout()
    {
        $blockName = $this->getToolbarBlockName();

        $toolbarLayout = false;

        if ($blockName) {
            $toolbarLayout = $this->getLayout()->getBlock($blockName);
        }

        return $toolbarLayout;
    }

    private function addToolbarBlock($collection)
    {
        $toolbarLayout = $this->getToolbarFromLayout();

        if ($toolbarLayout) {
            $this->configureToolbar($toolbarLayout, $collection);
        }
    }
    
    private function configureToolbar($toolbar, $collection)
    {
        // use sortable parameters
        $orders = $this->getAvailableOrders();
        if ($orders) {
            $toolbar->setAvailableOrders($orders);
        }
        $sort = $this->getSortBy();
        if ($sort) {
            $toolbar->setDefaultOrder($sort);
        }
        $dir = $this->getDefaultDirection();
        if ($dir) {
            $toolbar->setDefaultDirection($dir);
        }
        $modes = $this->getModes();
        if ($modes) {
            $toolbar->setModes($modes);
        }
        // set collection to toolbar and apply sort
        $toolbar->setCollection($collection);
        $this->setChild('toolbar', $toolbar);
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
