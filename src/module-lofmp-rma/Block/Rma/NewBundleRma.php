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
 * @package    Lof_Rma
 * @copyright  Copyright (c) 2020 Landofcoder (http://www.LandOfCoder.com/)
 * @license    http://www.LandOfCoder.com/LICENSE-1.0.html
 */

namespace Lofmp\Rma\Block\Rma;

class NewBundleRma extends \Magento\Framework\View\Element\Template
{
    protected $_orderModel;
    protected $_orders = [];
    protected $calculate;
    protected $_sellerFactory;
    protected $_products = [];

    public function __construct(
        \Magento\Sales\Model\Order\Address\Renderer $addressRenderer,
        \Magento\Customer\Api\GroupRepositoryInterface $groupRepository,
        \Lofmp\Rma\Helper\Data         $rmaHelper,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Catalog\Helper\Image $imageHelper,
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Lof\MarketPlace\Model\CalculateCommission $calculate,
        \Lof\MarketPlace\Model\SellerFactory $sellerFactory,
        array $data = []
    ) {
        $this->addressRenderer = $addressRenderer;
        $this->groupRepository = $groupRepository;
        $this->rmaHelper             = $rmaHelper;
        $this->imageHelper            = $imageHelper;
        $this->objectManager         = $objectManager;
        $this->calculate       = $calculate;
        $this->request =  $context->getRequest();
        $this->context = $context;
        $this->_orderFactory = $orderFactory;
        $this->_productFactory = $productFactory;
        $this->_sellerFactory = $sellerFactory;
        parent::__construct($context, $data);
    }

    /**
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return void
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $this->pageConfig->getTitle()->set(__('Create RMA'));
        $pageMainTitle = $this->getLayout()->getBlock('page.main.title');
        if ($pageMainTitle) {
            $order_id = $this->getOrderId(0);
            $pageMainTitle->setPageTitle(__('New Bundle Return for Order #').$this->getOrder($order_id)->getIncrementId());
        }
    }
    public function getCountOrders()
    {
        $order_ids = $this->getOrderIds();
        return count($order_ids);
    }
    public function getOrder($order_id = 0)
    {
        if ($order_id) {
            if (!isset($this->_orders[$order_id])) {
                $this->_orders[$order_id] = $this->_orderFactory->create()->load($order_id);
            }
            return $this->_orders[$order_id];
        }
        return false;
    }
    public function getOrderIds()
    {
        if (!isset($this->_order_ids)) {
            $this->_order_ids = $this->request->getParam("order_id");
        }
        if (!$this->_order_ids) {
            $path = trim($this->request->getPathInfo(), '/');
            $params = explode('/', $path);
            $order_id = end($params);
            if ($order_id) {
                $order_id = (int)$order_id;
                $this->_order_ids = [$order_id];
            }
        }
        return $this->_order_ids;
    }
    public function getOrderId($index = 0)
    {
        $order_ids = $this->getOrderIds();
        if ($order_ids && is_array($order_ids) && isset($order_ids[$index])) {
            return (int)$order_ids[$index];
        }
        return 0;
    }
    public function getFormattedAddress($order_id)
    {
        if ($this->getOrder($order_id)->getShippingAddress()) {
            return $this->addressRenderer->format($this->getOrder($order_id)->getShippingAddress(), 'html');
        } else {
            return;
        }
    }

    public function getBillingAddress($order_id)
    {
        return $this->addressRenderer->format($this->getOrder($order_id)->getBillingAddress(), 'html');
    }

    public function getOrderDate($order_id)
    {
        return $this->formatDate(
            $this->getOrderAdminDate($this->getOrder($order_id)->getCreatedAt()),
            \IntlDateFormatter::MEDIUM,
            true
        );
    }
    /**
     * Get order store name
     *
     * @return null|string
     */
    public function getOrderStoreName($order_id)
    {
        if ($this->getOrder($order_id)) {
            $storeId = $this->getOrder($order_id)->getStoreId();
            if ($storeId === null) {
                $deleted = __(' [deleted]');
                return nl2br($this->getOrder($order_id)->getStoreName()) . $deleted;
            }
            $store = $this->_storeManager->getStore($storeId);
            $name = [$store->getWebsite()->getName(), $store->getGroup()->getName(), $store->getName()];
            return implode('<br/>', $name);
        }

        return null;
    }

    public function getQtyAvailable($item)
    {
        return $this->rmaHelper->getItemQuantityAvaiable($item);
    }

    /**
     * @return \Lofmp\Rma\Model\Field[]
     */
    public function getCustomFields()
    {
        return $this->rmaHelper->getVisibleFields('initial', true, true);
    }


        /**
         * @param \Lofmp\Rma\Model\Field $field
         *
         * @return string
         */
    public function getFieldInputHtml(\Lofmp\Rma\Model\Field $field)
    {
        $params = $this->rmaHelper->getInputParams($field, false);
        unset($params['label']);
        $className = '\Magento\Framework\Data\Form\Element\\'.ucfirst(strtolower($field->getType()));
        $element = $this->objectManager->create($className);
        $element->setData($params);
        $element->setForm(new \Magento\Framework\DataObject());
        $element->setId($field->getCode());
        $element->setNoSpan(true);
        $element->addClass($field->getType());
        $element->setType($field->getType());
        if ($field->IsCustomerRequired()) {
            $element->addClass('required-entry');
        }

        return $element->getDefaultHtml();
    }


    /**
     * Initialize Helper to work with Image
     *
     * @param mixed $product
     * @param string $imageId
     * @param array $attributes
     * @return \Magento\Catalog\Helper\Image|false
     */
    public function initImage($item, $imageId, $attributes = [])
    {
        $product = $this->getProductById((int)$item->getData('product_id'));
        if ($product) {
            return $this->imageHelper->init($product, $imageId, $attributes);
        }
        return false;
    }

    public function getProductImage($item, $imageId = 'product_thumbnail_image', $resize_width = 300)
    {
        $img_url = "";
        if ($item->getId()) {
            $img = $this->initImage($item, $imageId);
            if ($img) {
                $resize_width = $resize_width?(int)$resize_width:300;
                try {
                    $img_url = $img->resize($resize_width)->getUrl();
                } catch (\Exception $e) {
                    return $img->getDefaultPlaceholderUrl();
                }
            }
        }
        return $img_url;
    }

    public function getAttribute($item)
    {
        $product = $item->getProduct();
        $attribute = $product->getResource()->getAttribute('product_rma');
        $attribute_value = $attribute ->getFrontend()->getValue($product)->getText();
        return $attribute_value;
    }

    public function getProductById($product_id)
    {
        if (!isset($this->_products[$product_id])) {
            $this->_products[$product_id] = $this->_productFactory->create()->load((int)$product_id);
        }
        return $this->_products[$product_id];
    }

    public function getSellerIdByProductId($productid)
    {
        $product = $this->getProductById($productid);
        return $product?$product->getSellerId():0;
    }
    
    public function getSeller($sellerid)
    {
        $sellerDatas = $this->_sellerFactory->create()->load($sellerid, 'seller_id');
        return $sellerDatas;
    }

    public function getSellerId()
    {
        $seller_id = $this->request->getParam('seller_id');
        return $seller_id;
    }
}
