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

use Magento\Framework\UrlFactory;
use Magento\Customer\Model\Session;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\View\Result\PageFactory;

class AbstractBlock extends \Magento\Framework\View\Element\Template
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var UrlFactory
     */
    protected $urlModel;

    /**
     * @var Session
     */
    protected $session;

    /**
     * AbstractBlock constructor.
     * @param Context $context
     * @param Session $customerSession
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param UrlFactory $urlFactory
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        UrlFactory $urlFactory
    ) {
        $this->session = $customerSession;
        $this->urlModel = $urlFactory;
        $this->_objectManager = $objectManager;
        parent::__construct($context);
    }

    /**
     * Retrieve customer session model object
     *
     * @return Session
     */
    protected function _getSession()
    {
        return $this->session;
    }

    /**
     * Get seller ID
     *
     * @return int
     */
    public function getSellerId()
    {
        return $this->session->getSellerId();
    }

    /**
     * @return mixed
     */
    public function getSeller()
    {
        return $this->_objectManager->get(\Lof\MarketPlace\Model\Seller::class)->load($this->getSellerId());
    }

    /**
     * @return mixed
     */
    public function getCustomer()
    {
        return $this->_objectManager->get(\Magento\Customer\Model\Customer::class)->load($this->getCustomerId());
    }

    /**
     * Get customer ID
     *
     * @return int
     */
    public function getCustomerId()
    {
        return $this->session->getCustomerId();
    }

    /**
     * Get object Manager
     *
     * @return \Magento\Framework\ObjectManagerInterface
     */
    public function getObjectManager()
    {
        return $this->_objectManager;
    }

    /**
     * Get seller url in seller dashboard
     *
     * @return string
     */
    public function getSellerUrl()
    {
        return  $this->urlModel->create()->getUrl('lofmarketplace/seller/edit', ['_secure' => true]);
    }

    /**
     * Get back url in seller dashboard
     *
     * @return string
     */
    public function getBackUrl()
    {
        // the RefererUrl must be set in appropriate controller
        if ($this->getRefererUrl()) {
            return $this->getRefererUrl();
        }
        return  $this->urlModel->create()->getUrl('marketplace/catalog/dashboard', ['_secure' => true]);
    }
}
