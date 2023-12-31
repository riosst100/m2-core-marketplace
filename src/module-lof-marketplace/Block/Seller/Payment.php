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

use Magento\Framework\Pricing\PriceCurrencyInterface;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Payment extends \Magento\Framework\View\Element\Template
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Lof\MarketPlace\Model\SellerFactory
     */
    protected $_sellerFactory;

    /**
     * @var \Lof\MarketPlace\Model\Payment
     */
    protected $payment;

    /**
     * @var \Lof\MarketPlace\Helper\Data
     */
    protected $_helper;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $_resource;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var PriceCurrencyInterface
     */
    protected $_priceCurrency;

    /**
     * @var \Lof\MarketPlace\Model\Amount
     */
    protected $amount;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $session;

    /**
     * @var PriceCurrencyInterface
     */
    protected $priceFormatter;

    /**
     * @var int|float|null
     */
    protected $_sellerBalance = null;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Lof\MarketPlace\Model\SellerFactory $sellerFactory
     * @param \Lof\MarketPlace\Model\Payment $payment
     * @param \Lof\MarketPlace\Helper\Data $helper
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param PriceCurrencyInterface $priceCurrency
     * @param \Lof\MarketPlace\Model\Amount $amount
     * @param \Magento\Customer\Model\Session $customerSession
     * @param PriceCurrencyInterface $priceFormatter
     * @param array $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Lof\MarketPlace\Model\SellerFactory $sellerFactory,
        \Lof\MarketPlace\Model\Payment $payment,
        \Lof\MarketPlace\Helper\Data $helper,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Lof\MarketPlace\Model\Amount $amount,
        \Magento\Customer\Model\Session $customerSession,
        PriceCurrencyInterface $priceFormatter,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->request = $context->getRequest();
        $this->payment = $payment;
        $this->_helper = $helper;
        $this->_coreRegistry = $registry;
        $this->_sellerFactory = $sellerFactory;
        $this->_resource = $resource;
        $this->_priceCurrency = $priceCurrency;
        $this->session = $customerSession;
        $this->amount = $amount;
        $this->priceFormatter = $priceFormatter;
    }

    /**
     * @return int
     */
    public function getSellerId()
    {
        return (int)$this->getSeller()->getId();
    }

    /**
     * @return \Lof\MarketPlace\Model\Seller
     */
    public function getSeller()
    {
        $customerSession = $this->session;
        $customerId = $customerSession->getId();
        return $this->_sellerFactory->create()->load($customerId, 'customer_id');
    }

    /**
     * @param $price
     * @param $base_currency_code
     * @return string
     */
    public function getPriceFomat($price, $base_currency_code)
    {
        $currencyCode = isset($base_currency_code) ? $base_currency_code : null;
        return $this->priceFormatter->format(
            $price,
            false,
            2,
            null,
            $currencyCode
        );
    }

    /**
     * @return mixed
     */
    public function getCurrentCurrencyCode()
    {
        return $this->_priceCurrency->getCurrency()->getCurrencyCode();
    }

    /**
     * @return int
     */
    public function getPaymentId()
    {
        return (int)$this->getPayment()->getPaymentId();
    }

    /**
     * @return int|float
     */
    public function getAmount()
    {
        if ($this->_sellerBalance == null) {
            $balance = 0;
            $collection = $this->amount->getCollection()->addFieldToFilter('seller_id', $this->getSellerId());
            //$collection->getSelect()->sort("main_table.updated_at DESC");
            $foundItem = $collection->getFirstItem();
            $balance = $foundItem ? (float)$foundItem->getAmount() : 0;
            $this->_sellerBalance = $balance;
        }
        return $this->_sellerBalance;
    }

    /**
     * @return array|mixed|null
     */
    public function getPayment()
    {
        if (!$this->hasData('mp_current_payment')) {
            $this->setData('mp_current_payment', $this->_coreRegistry->registry('mp_current_payment'));
        }
        return $this->getData('mp_current_payment');
    }

    /**
     * @return Payment
     */
    public function _prepareLayout()
    {
        $this->pageConfig->getTitle()->set(__('Payment'));
        return parent::_prepareLayout();
    }
}
