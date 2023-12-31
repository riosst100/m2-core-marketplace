<?php

namespace Lof\SellerInvoice\Block\Adminhtml\Invoice;

use Magento\Customer\Model\Context;
use Magento\Customer\Api\CustomerRepositoryInterface as CustomerRepository;

class InvoicePdf extends \Magento\Framework\View\Element\Template
{

    protected $_objectManager = null;
    protected $_directoryData = null;
    protected $customerRepository = null;
    protected $urlHelper = null;

    protected $_sellerData = null;
    protected $_invoiceData = null;

    /**
     * construct
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Directory\Block\Data $directoryData
     * @param CustomerRepository $customerRepository
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\Url\Helper\Data $urlHelper
     * @param array $data = []
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Directory\Block\Data $directoryData,
        CustomerRepository $customerRepository,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\Url\Helper\Data $urlHelper,
        array $data = []
    ) {
        $this->_coreRegistry      = $coreRegistry;
        parent::__construct($context, $data);
        $this->_directoryData     = $directoryData;
        $this->customerRepository = $customerRepository;
        $this->urlHelper          = $urlHelper;
    }

    /**
     * Get logo path
     *
     * @param string $imageUrl
     * @param mixed|null $invoice
     * @return string
     */
    public function getLogoPath($imageUrl, $invoice = null) {
        if(!$invoice){
            $invoice = $this->getInvoiceData();
        }
        $_store = $invoice->getStore();
        $_baseImageUrl = $_store->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        $image_path = $_baseImageUrl."lof/sellerinvoice/".$imageUrl;
        return $image_path;
    }

    /**
     * Get object manager
     *
     * @return mixed
     */
    public function getObjectManager(){
        if(!$this->_objectManager){
            $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        }
        return $this->_objectManager;
    }

    /**
     * Get invoice data
     *
     * @return \Magento\Sales\Model\Order\Invoice|mixed|null
     */
    public function getInvoiceData()
    {
        $id     = $this->getMageInvoice()->getId();
        if ($id && !$this->_invoiceData) {
            $model  = $this->getObjectManager()->create('Magento\Sales\Model\Order\Invoice');
            $this->_invoiceData   = $model->load($id);
        }

        return $this->_invoiceData;
    }

    /**
     * get seller product
     *
     * @return string[]|int[]|mixed
     */
    public function getSellerProduct()
    {
        $sellerId = $this->getSellerId();
        $productIds = [];
        if($sellerId){
            $productModel = $this->getObjectManager()->get("Lof\MarketPlace\Model\SellerProduct")->getCollection();
            $products = $productModel->addFieldToFilter("seller_id",$sellerId)->load();
            foreach ($products as $product) {
                array_push($productIds, $product->getData("product_id"));
            }
        }
        return $productIds;
    }

    /**
     * Get seller Data
     *
     * @return \Lof\MarketPlace\Model\Seller|null
     */
    public function getSellerData()
    {
        $sellerId    = $this->getSellerId();
        if ($sellerId && !$this->_sellerData) {
            $sellerModel = $this->getObjectManager()->get("Lof\MarketPlace\Model\Seller");
            $this->_sellerData  = $sellerModel->load($sellerId);
        }
        return $this->_sellerData;
    }
}
