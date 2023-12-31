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

class BecomesellerLink extends \Magento\Framework\View\Element\Html\Link
{
    /**
     * @var \Lof\MarketPlace\Helper\Url
     */
    protected $_helperUrl;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var MarketplaceSession
     */
    protected $_marketplaceSession;

    /**
     * @var \Lof\MarketPlace\Model\SellerFactory
     */
    protected $sellerFactory;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Lof\MarketPlace\Helper\Url $helperUrl
     * @param MarketplaceSession $marketplaceSession
     * @param \Lof\MarketPlace\Model\SellerFactory $sellerFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Lof\MarketPlace\Helper\Url $helperUrl,
        MarketplaceSession $marketplaceSession,
        \Lof\MarketPlace\Model\SellerFactory $sellerFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_helperUrl = $helperUrl;
        $this->_marketplaceSession = $marketplaceSession;
        $this->sellerFactory = $sellerFactory;
    }

    /**
     * @return string
     */
    public function getHref()
    {
        return $this->_helperUrl->getBecomesellerUrl();
    }

    /**
     * {@inheritdoc}
     */
    protected function _toHtml()
    {
        $seller = $this->_marketplaceSession->getSeller();
        $isSeller = $seller && $seller->getId() ? true : false;
        
        if ($this->_marketplaceSession->isLoggedIn() && $isSeller) {
            return '';
        }
        return parent::_toHtml();
    }
}
