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
class ViewTransaction extends \Magento\Framework\View\Element\Template
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Lof\MarketPlace\Model\Seller
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
     * @var \Lof\MarketPlace\Model\Withdrawal
     */
    protected $withdrawal;

    /**
     *
     * @var Magento\Framework\App\Action\Session
     */
    protected $session;

    /**
     * @var PriceCurrencyInterface
     */
    protected $priceFormatter;

    /**
     * @var \Lof\MarketPlace\Model\WithdrawalFactory
     */
    protected $withdrawalFactory;

    /**
     * ViewTransaction constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Lof\MarketPlace\Model\Seller $sellerFactory
     * @param \Lof\MarketPlace\Helper\Data $helper
     * @param \Lof\MarketPlace\Model\PaymentFactory $payment
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param PriceCurrencyInterface $priceCurrency
     * @param \Lof\MarketPlace\Model\Amount $amount
     * @param \Lof\MarketPlace\Model\Withdrawal $withdrawal
     * @param \Magento\Customer\Model\Session $customerSession
     * @param PriceCurrencyInterface $priceFormatter
     * @param \Lof\MarketPlace\Model\WithdrawalFactory $withdrawalFactory
     * @param array $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Lof\MarketPlace\Model\Seller $sellerFactory,
        \Lof\MarketPlace\Helper\Data $helper,
        \Lof\MarketPlace\Model\PaymentFactory $payment,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Lof\MarketPlace\Model\Amount $amount,
        \Lof\MarketPlace\Model\Withdrawal $withdrawal,
        \Magento\Customer\Model\Session $customerSession,
        PriceCurrencyInterface $priceFormatter,
        \Lof\MarketPlace\Model\WithdrawalFactory $withdrawalFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->withdrawal = $withdrawal;
        $this->request = $context->getRequest();
        $this->_helper = $helper;
        $this->_coreRegistry = $registry;
        $this->_sellerFactory = $sellerFactory;
        $this->_resource = $resource;
        $this->_priceCurrency = $priceCurrency;
        $this->session = $customerSession;
        $this->amount = $amount;
        $this->payment = $payment;
        $this->priceFormatter = $priceFormatter;
        $this->withdrawalFactory = $withdrawalFactory;
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
            4,
            null,
            $currencyCode
        );
    }

    /**
     * Get current currency code
     *
     * @return string
     */
    public function getCurrentCurrencyCode()
    {
        return $this->_priceCurrency->getCurrency()->getCurrencyCode();
    }

    /**
     * @return \Magento\Framework\Data\Collection\AbstractDb|\Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection|null
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getSellerCollection()
    {
        $sellerCollection = $this->_sellerFactory->getCollection();
        return $sellerCollection;
    }

    /**
     * @return mixed|string
     */
    public function getSellerId()
    {
        $seller_id = '';
        $seller = $this->_sellerFactory->getCollection()
            ->addFieldToFilter('customer_id', $this->session->getId())
            ->getData();

        foreach ($seller as $_seller) {
            $seller_id = $_seller['seller_id'];
        }

        return $seller_id;
    }

    /**
     * @return \Lof\MarketPlace\Model\Payment
     */
    public function getPayment()
    {
        return $this->payment->create()->load($this->getWithdrawal()->getData('payment_id'));
    }

    /**
     * @return \Lof\MarketPlace\Model\Withdrawal
     */
    public function getWithdrawal()
    {
        $withdrawal = $this->withdrawalFactory->create()->load($this->getWithdrawalId());
        return $withdrawal;
    }

    /**
     * @return mixed|string
     */
    public function getWithdrawalId()
    {
        $path = trim($this->request->getPathInfo(), '/');
        $params = explode('/', $path);
        return end($params);
    }

    /**
     * @return int|mixed
     */
    public function getAmount()
    {
        $balance = 0;
        $amount = $this->amount->getCollection()->addFieldToFilter('seller_id', $this->getSellerId())->getData();
        foreach ($amount as $_amount) {
            $balance = $_amount['amount'];
        }

        return $balance;
    }

    /**
     * @param $time
     * @return string
     */
    public function formatDateTime($time)
    {
        return $this->formatTime($time, \IntlDateFormatter::MEDIUM, true);
    }

    /**
     * @return ViewTransaction
     */
    public function _prepareLayout()
    {
        $this->pageConfig->getTitle()->set(__('Payment'));
        return parent::_prepareLayout();
    }
}
