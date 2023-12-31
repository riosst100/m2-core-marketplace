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
class Withdrawals extends \Magento\Framework\View\Element\Template
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
     * @var \Lof\MarketPlace\Model\Amount
     */
    protected $amount;

    /**
     * @var \Lof\MarketPlace\Model\Withdrawal
     */
    protected $withdrawal;

    /**
     * @var \Lof\MarketPlace\Helper\Data
     */
    protected $_helper;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $_resource;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $session;

    /**
     * @var PriceCurrencyInterface
     */
    protected $_priceCurrency;

    /**
     * @var PriceCurrencyInterface
     */
    protected $priceFormatter;

    /**
     * Withdrawals constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Lof\MarketPlace\Model\Seller $sellerFactory
     * @param \Lof\MarketPlace\Model\Payment $payment
     * @param \Lof\MarketPlace\Helper\Data $helper
     * @param \Lof\MarketPlace\Model\Withdrawal $withdrawal
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param \Lof\MarketPlace\Model\Amount $amount
     * @param \Magento\Customer\Model\Session $customerSession
     * @param PriceCurrencyInterface $priceCurrency
     * @param PriceCurrencyInterface $priceFormatter
     * @param array $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Lof\MarketPlace\Model\Seller $sellerFactory,
        \Lof\MarketPlace\Model\Payment $payment,
        \Lof\MarketPlace\Helper\Data $helper,
        \Lof\MarketPlace\Model\Withdrawal $withdrawal,
        \Magento\Framework\App\ResourceConnection $resource,
        \Lof\MarketPlace\Model\Amount $amount,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        PriceCurrencyInterface $priceFormatter,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->withdrawal = $withdrawal;
        $this->amount = $amount;
        $this->payment = $payment;
        $this->_helper = $helper;
        $this->_coreRegistry = $registry;
        $this->_sellerFactory = $sellerFactory;
        $this->_resource = $resource;
        $this->session = $customerSession;
        $this->_storeManager = $context->getStoreManager();
        $this->_priceCurrency = $priceCurrency;
        $this->priceFormatter = $priceFormatter;
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
     * @return \Magento\Framework\Data\Collection\AbstractDb|\Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection|null
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getSellerCollection()
    {
        $sellerCollection = $this->_sellerFactory->getCollection();
        return $sellerCollection;
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
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCurrentCurrency()
    {
        return $this->_storeManager->getStore()->getCurrentCurrency()->getCode();
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
     * @return \Magento\Framework\Data\Collection\AbstractDb|\Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection|null
     */
    public function getPayment()
    {
        $payment = $this->payment->getCollection();
        return $payment;
    }

    /**
     * @return int|mixed
     */
    public function getTotalAmount()
    {
        $withdrawal = $this->withdrawal->getCollection()
            ->addFieldToFilter('seller_id', $this->getSellerId())
            ->addFieldToFilter('status', 1)
            ->addExpressionFieldToSelect('total_amount', 'SUM({{net_amount}})', 'net_amount')
            ->getFirstItem()->getData('total_amount');
        return $withdrawal;
    }

    /**
     * @return \Magento\Framework\Data\Collection\AbstractDb|\Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection|null
     */
    public function getWithdrawal()
    {
        $withdrawal = $this->withdrawal->getCollection()->addFieldToFilter('seller_id', $this->getSellerId());
        return $withdrawal;
    }

    /**
     * @param $status
     * @return string
     */
    public function getStatus($status)
    {
        $data = '';
        if ($status == 0) {
            $data = '<span class="btn btn-warning">' . __('Pending') . '</span>';
        } elseif ($status == 1) {
            $data = '<span class="btn btn-success">' . __('Completed') . '</span>';
        } elseif ($status == 2) {
            $data = '<span class="btn btn-danger">' . __('Cancel') . '</span>';
        }

        return $data;
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
     * @return Withdrawals
     */
    public function _prepareLayout()
    {
        $this->pageConfig->getTitle()->set(__('Withdrawals'));
        return parent::_prepareLayout();
    }
}
