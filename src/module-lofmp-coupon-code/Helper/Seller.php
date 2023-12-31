<?php
/**
 * LandofCoder
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * https://landofcoder.com/license
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category   LandofCoder
 * @package    Lofmp_CouponCode
 * @copyright  Copyright (c) 2016 Landofcoder (https://landofcoder.com/)
 * @license    https://landofcoder.com/LICENSE-1.0.html
 */
namespace Lofmp\CouponCode\Helper;

class Seller extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * Seller state const
     */
    const STATE_NOT_LOGGED_IN = "not_loggin";
    const STATE_APPROVED = "approved";
    const STATE_NEED_APPROVAL = "need_approval";

    /**
     * @var \Lof\MarketPlace\Helper\Data
     */
    protected $marketplaceHelperData;

    /** 
    *@var \Magento\Store\Model\StoreManagerInterface 
    */
    protected $_storeManager;

    protected $_scopeConfig;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Lof\MarketPlace\Helper\Data $marketplaceHelperData
        ) {
        parent::__construct($context);
        $this->_scopeConfig    = $context->getScopeConfig();
        $this->_storeManager   = $storeManager;
        $this->marketplaceHelperData = $marketplaceHelperData;
    }

    /**
     * get marketplace helper
     * 
     * @return \Lof\MarketPlace\Helper\Data
     */
    public function getMarketplaceHelper() 
    {
        return $this->marketplaceHelperData;
    }

    /**
     * get seller by customer id
     * 
     * @param int $customerId
     * @return \Lof\MarketPlace\Model\Seller
     */
    public function getSeller( $customerId )
    {
        return $this->marketplaceHelperData->getSellerByCustomerId($customerId);
    }

    /**
     * Get current actived seller account
     * 
     * @param int $customerId
     * @return \Lof\MarketPlace\Model\Seller|bool
     */
    public function getActiveSeller ( $customerId )
    {
        $seller = $this->getSeller($customerId);
        if ($this->isActiveSeler($seller)) {
            return $seller;
        }
        return false;
    }

    protected function getSellerState($seller = null){
        if($seller && $seller->getId()){
            return 1 == $seller->getStatus()?self::STATE_APPROVED:self::STATE_NEED_APPROVAL;
        }
        return self::STATE_NOT_LOGGED_IN;
    }

    public function isActiveSeler($seller = null) {
        $sellerState = $this->getSellerState( $seller );
        switch($sellerState){
            case "approved":
                return true;
                break;
            case "not_loggin":
                return false;
                break;
            case "need_approval":
            default:
                return false;
                break;
        }
        return false;
    }
}