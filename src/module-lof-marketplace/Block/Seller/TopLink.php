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
 * @package    Lof_MarketPlace
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lof\MarketPlace\Block\Seller;

use Lof\MarketPlace\Model\Session as MarketplaceSession;

class TopLink extends \Magento\Framework\View\Element\Html\Link
{
    protected $_template = 'Lof_MarketPlace::account/top_link.phtml';

    /**
     * @var \Lof\MarketPlace\Helper\Data
     */
    protected $helper;

    /**
     * @var MarketplaceSession
     */
    protected $_marketplaceSession;

    /**
     * @var \Magento\Framework\App\Http\Context
     */
    protected $httpContext;

    /**
     * @var int|bool|null
     */
    protected $_isSellerLoggedIn = null;

    /**
     * @param \Magento\Framework\View\Element\Template\Context
     * @param \Lof\MarketPlace\Helper\Data
     * @param MarketplaceSession $marketplaceSession
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param array
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Lof\MarketPlace\Helper\Data $helper,
        MarketplaceSession $marketplaceSession,
        \Magento\Framework\App\Http\Context $httpContext,
        array $data = []
    ) {
        $this->helper = $helper;
        $this->_marketplaceSession = $marketplaceSession;
        $this->httpContext = $httpContext;
        parent::__construct($context, $data);
    }

    /**
     * Checking customer login status
     *
     * @return bool
     */
    public function customerLoggedIn()
    {
        return (bool)$this->httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_AUTH);
    }

    /**
     * check is seller logged in or not
     *
     * @return bool|int
     */
    public function isSellerLoggedIn()
    {
        if ($this->_isSellerLoggedIn == null) {
            if ($this->customerLoggedIn()) {
                $seller = $this->_marketplaceSession->getSeller();
                $this->_isSellerLoggedIn = $seller && $seller->getId() ? true : false;
            } else {
                $this->_isSellerLoggedIn = false;
            }
        }
        return $this->_isSellerLoggedIn;
    }

    /**
     * @return string
     */
    public function getHref()
    {
        if ($this->helper->getConfig('general_settings/enable') == 1) {

            if ($this->isSellerLoggedIn()) {
                $seller = $this->_marketplaceSession->getSeller();
                $status = (int)$seller->getStatus();
                $url = $status == 1 ? $this->getUrl('marketplace/catalog/dashboard') : "#";
            } else {
                $marketplaceUrl = $this->helper->getConfig('general_settings/change_route');
                $url = $this->getUrl($marketplaceUrl.'/seller/login');
            }
            return $url;
        }

        return '';
    }

    /**
     * @return string|void
     */
    protected function _toHtml()
    {
        if (!$this->helper->getConfig('general_settings/enable')) {
            return;
        }

        return parent::_toHtml();
    }

    /**
     * Function to Get Label on Top Link
     *
     * @return string
     */
    public function getLabel()
    {
        if ($this->isSellerLoggedIn()) {
            $seller = $this->_marketplaceSession->getSeller();
            $status = (int)$seller->getStatus();
            return $status == 1 ? __('Seller Dashboard ') : __('Seller Registration Under Review');
        } else {
            return __('Sell On Marketplace ');
        }
    }

    /**
     * Function to Get seller status
     *
     * @return int
     */
    public function getSellerStatus()
    {
        if ($this->isSellerLoggedIn()) {
            $seller = $this->_marketplaceSession->getSeller();
            return (int)$seller->getStatus();
        }
    }

    /**
     * Function to Get seller ID
     *
     * @return int
     */
    public function getSellerId()
    {
        if ($this->isSellerLoggedIn()) {
            $seller = $this->_marketplaceSession->getSeller();
            return (int)$seller->getSellerId();
        }
    }

    /**
     *
     * Get list seller links
     *
     * @return array
     */
    public function getSellerLinks()
    {
        return [
            "guest" => [
                "link" => $this->getUrl('lofmarketplace/seller/login'),
                "label" => __('Sell On Marketplace ')
            ],
            "sellerLoggedIn" => [
                "link" => $this->getUrl('marketplace/catalog/dashboard'),
                "label" => __('Seller Dashboard')
            ],
            "awaitSellerLoggedIn" => [
                "link" => $this->getUrl('lofmarketplace/seller/becomeseller/approval'),
                "label" => __('Seller Registration Under Review')
            ],
        ];
    }
}
