<?php
namespace Lofmp\CouponCode\Block;
class Index extends \Magento\Framework\View\Element\Template
{
     protected $_customerSession;
     protected $_gridFactory;
     protected $_helperData;
     protected $_sellers = [];

     public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\SessionFactory $customerSession,
        \Lofmp\CouponCode\Model\CouponFactory $gridFactory,
        \Lof\MarketPlace\Model\SellerFactory $sellerFactory,
        \Magento\Framework\App\Request\Http $request,
        \Lofmp\CouponCode\Helper\Data $helperData,
        array $data = []
     ) {
        $this->_gridFactory = $gridFactory;
        $this->_customerSession = $customerSession;
        $this->_sellerFactory = $sellerFactory;
        $this->request = $request;
        $this->_helperData = $helperData;
        parent::__construct($context, $data);
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $this->pageConfig->getTitle()->set(__('My Coupon Code'));

        $this->initCollection();
        if($this->getCollection()) {
            // create pager block for collection
            $pager = $this->getLayout()->createBlock(
                'Magento\Theme\Block\Html\Pager',
                'lof.couponcode.record.pager'
            )->setCollection(
                $this->getCollection() // assign collection to pager
            );
            $this->setChild('pager', $pager);// set pager block in layout
        }
        return $this;
    }

    public function initCollection(){
        $filterType = $this->request->getParam('filter');
        $customer_id = $this->_customerSession->create()->getCustomer()->getId();
        $collection = $this->_gridFactory->create()->getCollection();
        $collection->addFieldToFilter('customer_id', $customer_id);
        $collection->joinSalesruleCoupon();
        $today = $this->_helperData->getTimezoneDateTime();
        switch ($filterType) {
            case "expired":
                $collection->addFieldToFilter("expiration_date", ["lteq" => $today]);
                break;
            case "available":
                $collection->addFieldToFilter('expiration_date', [
                    ['gt' => $today],
                    ['null' => true]
                ]);
                $collection->getSelect()
                            ->where('`times_used` < `usage_per_customer` OR `usage_per_customer` = 0');
                //$collection->addFieldToFilter("times_used", ["eq" => 0]);
                break;
            case "used":
                $collection->addFieldToFilter("times_used", ["gt" => 0]);
                break;
            case "all":
            default:
                break;
        }
        $this->setCollection($collection);
    }

    public function getActiveClass($status = "all")
    {
        $filterType = $this->request->getParam('filter');
        if ($filterType == $status) {
            return "actived";
        }
        return "";
    }
    /**
     * @return string
     */
    // method for get pager html
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    public function getSellerById($sellerid) {
        if(!isset($this->_sellers[$sellerid])){
            $this->_sellers[$sellerid] = $this->_sellerFactory->create()->load ( $sellerid, 'seller_id' );
        }
        return $this->_sellers[$sellerid];
    }
}
?>
