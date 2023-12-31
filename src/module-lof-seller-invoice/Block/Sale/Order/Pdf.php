<?php

namespace Lof\SellerInvoice\Block\Sale\Order;

class Pdf extends \Magento\Framework\View\Element\Template
{
	protected $_objectManager;
	protected $_sellerOrder;
	protected $_sellerModel;

	public function __construct(
		\Magento\Framework\View\Element\Template\Context $context,
		\Lof\MarketPlace\Model\Order 					 $sellerOrder,
		\Lof\MarketPlace\Model\Seller 					 $sellerModel,
        array $data = []
    ) {
        parent::__construct($context);
        $this->_sellerOrder   = $sellerOrder;
        $this->_sellerModel   = $sellerModel;
    }
	public function getPreviewPdfUrl($sellerId){
        $orderId      = $this->getRequest()->getParam("order_id");
        return $this->getUrl("sellerinvoice/exportfile/downloadfile", ["order_id"=>$orderId, "seller_id"=>$sellerId]);
    }

    public function getSellers()
    {
        $orderId      	= $this->getRequest()->getParam("order_id");
        $orderModel     = $this->_sellerOrder->getCollection();
        $sellers        = $orderModel->addFieldToFilter("order_id", $orderId);
        $res            = [];

        foreach ($sellers as $seller) {
            $sellersData = $this->_sellerModel->load($seller->getData("seller_id"));
            $res[$seller->getData("seller_id")]["seller_name"] = $sellersData->getData("name");
            $res[$seller->getData("seller_id")]["seller_id"]   = $seller->getData("seller_id");
            $res[$seller->getData("seller_id")]["order_id"]	   = $orderId;
        }
        return $res;
    }
}
