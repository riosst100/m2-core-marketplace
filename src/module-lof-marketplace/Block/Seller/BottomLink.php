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

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\UrlInterface;

class BottomLink extends \Magento\Framework\View\Element\Html\Link
{
    /**
     * @var ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var UrlInterface
     */
    protected $_urlInterface;

    /**
     * BottomLink constructor.
     *
     * @param Template\Context $context
     * @param array $data
     * @param ScopeConfigInterface $scopeConfig
     * @param \Magento\Customer\Model\Session $customerSession
     * @param UrlInterface $urlInterface
     */
    public function __construct(
        Template\Context $context,
        ScopeConfigInterface $scopeConfig,
        \Magento\Customer\Model\Session $customerSession,
        UrlInterface $urlInterface,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_scopeConfig = $scopeConfig;
        $this->customerSession = $customerSession;
        $this->_urlInterface = $urlInterface;
    }

    /**
     * @return string
     */
    protected function _toHtml()
    {
        $router = $this->getUrl('lofmarketplace/seller/login');
        return '<li><a href="' . $this->_urlInterface->getUrl($router) . '" >'
            . $this->escapeHtml($this->getLabel()) . '</a></li>';
    }
}
