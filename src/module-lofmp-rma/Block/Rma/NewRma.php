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
namespace Lofmp\Rma\Block\Rma;

class NewRma extends \Magento\Framework\View\Element\Template
{
    protected $_orderFactory;
    protected $_productFactory;
    protected $_sellerFactory;
    protected $_products = [];

    public function __construct(
        \Magento\Sales\Model\Order\Address\Renderer $addressRenderer,
        \Magento\Customer\Api\GroupRepositoryInterface $groupRepository,
        \Lofmp\Rma\Helper\Data         $rmaHelper,
        \Lof\MarketPlace\Model\CalculateCommission $calculate,
        \Lof\MarketPlace\Model\SellerFactory $sellerFactory,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Catalog\Helper\Image $imageHelper,
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        array $data = []
    ) {
        $this->addressRenderer = $addressRenderer;
        $this->groupRepository = $groupRepository;
        $this->rmaHelper             = $rmaHelper;
        $this->calculate       = $calculate;
        $this->imageHelper            = $imageHelper;
        $this->objectManager         = $objectManager;
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
            $pageMainTitle->setPageTitle(__('New Return'));
        }
    }

    public function getOrder()
    {
        $order = $this->_orderFactory->create()->load($this->getOrderId());
        return $order;
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

    public function getOrderId()
    {
        $order_id = $this->request->getParam('order_id');
        return $order_id;
    }

    public function getSellerId()
    {
        $seller_id = $this->request->getParam('seller_id');
        return $seller_id;
    }

    public function getFormattedAddress()
    {
        if ($this->getOrder()->getShippingAddress()) {
            return $this->addressRenderer->format($this->getOrder()->getShippingAddress(), 'html');
        } else {
            return;
        }
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
     * @param \Magento\Catalog\Model\Product $product
     * @param string $imageId
     * @param array $attributes
     * @return \Magento\Catalog\Helper\Image
     */
    public function initImage($item, $imageId, $attributes = [])
    {
        $product = $this->getProductById((int)$item->getData('product_id'));
        if ($product) {
            return $this->imageHelper->init($product, $imageId, $attributes);
        }
        return false;
    }

    public function getProductById($product_id)
    {
        if (!isset($this->_products[$product_id])) {
            $this->_products[$product_id] = $this->_productFactory->create()->load((int)$product_id);
        }
        return $this->_products[$product_id];
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
}
