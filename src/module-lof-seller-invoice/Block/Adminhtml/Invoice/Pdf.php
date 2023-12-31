<?php
/**
 * Landofcoder
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * http://landofcoder.com/license
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category   Landofcoder
 * @package    Lof_RequestForInvoice
 * @copyright  Copyright (c) 2017 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */

namespace Lof\SellerInvoice\Block\Adminhtml\Invoice;

use Magento\Customer\Model\Context;
use Magento\Customer\Api\CustomerRepositoryInterface as CustomerRepository;

class Pdf extends \Magento\Framework\View\Element\Template
{
	public function __construct(\Magento\Backend\Block\Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context);
    }
	public function getPreviewPdfUrl($sellerId){
        $invoiceId      = $this->getRequest()->getParam("invoice_id");
        return $this->getUrl("sellerinvoice/invoice/exportfiles", ["invoice_id"=>$invoiceId, "seller_id"=>$sellerId]);
    }

    public function getSellerIds()
    {
        $invoiceId      = $this->getRequest()->getParam("invoice_id");
        $objectManager  = \Magento\Framework\App\ObjectManager::getInstance ();
        $sellerModel    = $objectManager->get("Lof\MarketPlace\Model\Invoice")->getCollection();
        $sellers        = $sellerModel->addFieldToFilter("invoice_id",$invoiceId)->load();
        $sellerModel    = $objectManager->get("Lof\MarketPlace\Model\Seller");  
        $res            = [];
        foreach ($sellers as $seller) {
            $sellersData = $sellerModel->load($seller->getData("seller_id"));
            $res[$seller->getData("seller_id")]["seller_name"] = $sellersData->getData("name");
            $res[$seller->getData("seller_id")]["seller_id"]   = $seller->getData("seller_id");
            $res[$seller->getData("seller_id")]["invoice_id"]  = $invoiceId;
        }
        return $res;
    }

}