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

namespace Lof\MarketPlace\Block\Sale;

use Magento\Sales\Api\InvoiceRepositoryInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;

class Invoice extends \Magento\Framework\View\Element\Html\Link
{
    /**
     * @var \Lof\MarketPlace\Model\ResourceModel\SellerInvoice\Grid\Collection
     */
    protected $invoice;

    /**
     *
     * @var Magento\Framework\App\Action\Session
     */
    protected $session;

    /**
     * @var \Lof\MarketPlace\Model\Seller
     */
    protected $seller;

    /**
     * @var string[]
     */
    protected $states;

    /**
     * @var PriceCurrencyInterface
     */
    protected $priceFormatter;

    /**
     * @var \Lof\MarketPlace\Helper\Data
     */
    protected $helper;

    /**
     * Invoice constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Lof\MarketPlace\Model\ResourceModel\SellerInvoice\Grid\Collection $invoice
     * @param \Lof\MarketPlace\Model\Seller $seller
     * @param \Lof\MarketPlace\Helper\Data $helper
     * @param InvoiceRepositoryInterface $invoiceRepository
     * @param PriceCurrencyInterface $priceFormatter
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Lof\MarketPlace\Model\ResourceModel\SellerInvoice\Grid\Collection $invoice,
        \Lof\MarketPlace\Model\Seller $seller,
        \Lof\MarketPlace\Helper\Data $helper,
        InvoiceRepositoryInterface $invoiceRepository,
        PriceCurrencyInterface $priceFormatter,
        array $data = []
    ) {
        $this->helper = $helper;
        $this->states = $invoiceRepository->create()->getStates();
        $this->priceFormatter = $priceFormatter;
        $this->invoice = $invoice;
        $this->seller = $seller;
        $this->session = $customerSession;
        parent::__construct($context, $data);
    }

    /**
     * @return \Lof\MarketPlace\Model\ResourceModel\SellerInvoice\Grid\Collection
     */
    public function getInvoiceCollection()
    {
        return $this->invoice->addFieldToFilter('seller_id', $this->helper->getSellerId());
    }

    /**
     * @param $status
     * @return mixed
     */
    public function getStatus($status)
    {
        return isset($this->states[$status]) ? $this->states[$status]->getText() : $status;
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
            null,
            null,
            $currencyCode
        );
    }

    /**
     * @return mixed
     */
    public function isSeller()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $customerSession = $objectManager->create(\Magento\Customer\Model\Session::class);
        if ($customerSession->isLoggedIn()) {
            $customerId = $customerSession->getId();
            $status = $this->seller->create()->load($customerId, 'customer_id')->getStatus();
            return $status;
        }

        return false;
    }

    /**
     * @return string
     */
    public function getCurrentUrl()
    {
        return $this->_urlBuilder->getCurrentUrl();
    }

    /**
     * @return Invoice
     */
    public function _prepareLayout()
    {
        $this->pageConfig->getTitle()->set(__('Invoice'));
        return parent::_prepareLayout();
    }
}
