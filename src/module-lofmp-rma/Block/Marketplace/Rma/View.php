<?php
/**
 * LandOfCoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * https://landofcoder.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   LandOfCoder
 * @package    Lofmp_Rma
 * @copyright  Copyright (c) 2021 Landofcoder (https://landofcoder.com/)
 * @license    https://landofcoder.com/LICENSE-1.0.html
 */



namespace Lofmp\Rma\Block\Marketplace\Rma;

class View extends \Magento\Framework\View\Element\Template
{
    protected $sellerFactory;
    protected $orderFactory;
    protected $productFactory;
    protected $_orders = [];
    protected $_sellers = [];
    protected $_sellerByProduct = [];
    protected $_products = [];

    public function __construct(
        \Magento\Sales\Model\Order\Address\Renderer $addressRenderer,
        \Magento\Customer\Api\GroupRepositoryInterface $groupRepository,
        \Lofmp\Rma\Helper\Data         $rmaHelper,
        \Lofmp\Rma\Model\Status $statusFactory,
        \Lof\MarketPlace\Model\Orderitems $orderitems,
        \Lof\MarketPlace\Model\Order $orders,
        \Lof\MarketPlace\Model\SellerFactory $sellerFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        array $data = []
    ) {
        $this->addressRenderer = $addressRenderer;
        $this->groupRepository = $groupRepository;
        $this->rmaHelper             = $rmaHelper;
        $this->orderitems = $orderitems;
        $this->orders = $orders;
        $this->request =  $context->getRequest();
        $this->registry                      = $registry;
        $this->status         = $statusFactory;
        $this->context = $context;
        $this->sellerFactory = $sellerFactory;
        $this->orderFactory = $orderFactory;
        $this->productFactory = $productFactory;
        parent::__construct($context, $data);
    }
    
    public function getOrder($order_id = 0)
    {
        $order_id = $order_id?(int)$order_id:$this->getOrderId();
        if (!isset($this->_orders[$order_id])) {
            $this->_orders[$order_id] = $this->orderFactory->create()->load($order_id);
        }
        return $this->_orders[$order_id];
    }

    public function getOrderId()
    {
        return $this->getRma()->getOrderId();
    }

    public function getFormattedAddress()
    {
        if ($this->getOrder()->getShippingAddress()) {
            return $this->addressRenderer->format($this->getOrder()->getShippingAddress(), 'html');
        } else {
            return;
        }
    }

    /**
     * Allow Create Credit Memo
     * @param int $order_id
     * @param int $seller_id
     * @return bool
     */
    public function allowCreateCreditmemo($order_id, $seller_id)
    {
        $order = $this->getOrder($order_id);
        $orderitemsCollection = $this->orderitems->getCollection();
        $orderitems = $orderitemsCollection->addFieldToFilter('order_id', $order_id)->addFieldToFilter('seller_id', $seller_id);
        $k=0;
        foreach ($orderitems as $orderitem) {
            if ($orderitem->getData('qty_invoiced')-$orderitem->getData('qty_refunded') > 0) {
                $k = 1;
            }
        }
        if ($k==0) {
            return false;
        }
        if (!$order->canCreditmemo()) {
            return false;
        }
        return true;
    }

    public function getBillingAddress()
    {
        return $this->addressRenderer->format($this->getOrder()->getBillingAddress(), 'html');
    }
    public function getOrderDate()
    {
        return $this->formatDate(
            $this->getOrderAdminDate($this->getOrder()->getCreatedAt()),
            \IntlDateFormatter::MEDIUM,
            true
        );
    }
    /**
     * Get order store name
     *
     * @return null|string
     */
    public function getOrderStoreName()
    {
        if ($this->getOrder()) {
            $storeId = $this->getOrder()->getStoreId();
            if ($storeId === null) {
                $deleted = __(' [deleted]');
                return nl2br($this->getOrder()->getStoreName()) . $deleted;
            }
            $store = $this->_storeManager->getStore($storeId);
            $name = [$store->getWebsite()->getName(), $store->getGroup()->getName(), $store->getName()];
            return implode('<br/>', $name);
        }

        return null;
    }

    /**
     * Return name of the customer group.
     *
     * @return string
     */
    public function getCustomerGroupName()
    {
        if ($this->getOrder()) {
            $customerGroupId = $this->getOrder()->getCustomerGroupId();
            try {
                if ($customerGroupId !== null) {
                    return $this->groupRepository->getById($customerGroupId)->getCode();
                }
            } catch (NoSuchEntityException $e) {
                return '';
            }
        }

        return '';
    }

    public function getProduct($productId)
    {
        if (!isset($this->_products[$productId])) {
            $this->_products[$productId] = $this->productFactory->create()->load($productId, 'entity_id');
        }
        return $this->_products[$productId];
    }

    public function getSellerId($productId)
    {
        $product = $this->getProduct($productId);
        return $product->getSellerId();
    }

    public function getSeller($productId)
    {
        if (!isset($this->_sellerByProduct[$productId])) {
            $this->_sellerByProduct[$productId] = $this->sellerFactory->create()->load($this->getSellerId($productId), 'seller_id');
        }
        return $this->_sellerByProduct[$productId];
    }

    public function getOrderItems($product_id)
    {
        $orderitems = $this->orderitems->getCollection()->addFieldToFilter('seller_id', $this->getSellerId($product_id))->addFieldToFilter('order_id', $this->getOrderId())->addFieldToFilter('product_id', $product_id)->getFirstItem();
        return $orderitems;
    }
    
    /**
     * @return \Lofmp\Rma\Model\Rma
     */
    public function getRma()
    {
        if ($this->registry->registry('current_rma') && $this->registry->registry('current_rma')->getId()) {
            return $this->registry->registry('current_rma');
        }
    }
    public function getQtyAvailable($item)
    {
        return $this->rmaHelper->getItemQuantityAvaiable($item);
    }
    public function getQtyRequest($item)
    {
        return $this->rmaHelper->getQtyReturnedRma($item, $this->getRma()->getId());
    }
    public function getRmaItemData($item)
    {
        return $this->rmaHelper->getRmaItemData($item, $this->getRma()->getId());
    }
     
    public function getAttachmentUrl($Uid)
    {
        $this->context->getUrlBuilder()->getUrl('rma/attachment/download', ['uid' => $Uid]);
    }
    public function getStatusname($id)
    {
        $status =  $this->status->load($id);
        return $status->getName();
    }
    public function getRmaDate()
    {
        return $this->formatDate(
            $this->getOrderAdminDate($this->getRma()->getCreatedAt()),
            \IntlDateFormatter::MEDIUM,
            true
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if ($rma = $this->getRma()) {
            $this->pageConfig->getTitle()->set(__('RMA #%1', $rma->getIncrementId()));
            $pageMainTitle = $this->getLayout()->getBlock('page.main.title');
            if ($pageMainTitle) {
                $pageMainTitle->setPageTitle(
                    __('RMA #%1 - %2', $rma->getIncrementId(), $this->getStatusname($rma->getStatusId()))
                );
            }
        }
    }
}
