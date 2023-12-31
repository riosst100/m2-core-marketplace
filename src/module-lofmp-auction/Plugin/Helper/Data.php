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
 * @package    Lofmp_Auction
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lofmp\Auction\Plugin\Helper;
use Lof\MarketPlace\Model\SellerFactory;
use Magento\Customer\Model\SessionFactory;
class Data
{
    protected $_customerSession;
    protected $_sellerFactory;
    
    public function __construct(
        SessionFactory $customerSession,
        SellerFactory $sellerFactory
    ) {
        $this->_customerSession = $customerSession;
        $this->_sellerFactory = $sellerFactory;
    }
    public function aroundCheckContinues(\Lof\Auction\Helper\Data $request, $proceed, $isAuto, $auctionData, $auctionConfig) 
	{
		if(isset($auctionData["seller_id"]) && $auctionData["seller_id"] && $this->_customerSession->create()->isLoggedIn()){
            $customerId = $this->_customerSession->create()->getCustomerId();
            $seller = $this->_sellerFactory->create()->load($customerId, 'customer_id');
            if($seller && $seller->getId() == $auctionData["seller_id"]){
                return false;
            }
        }
        return $proceed($isAuto, $auctionData, $auctionConfig);
	}
}
