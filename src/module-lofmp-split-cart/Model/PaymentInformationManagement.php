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
 * @package    Lofmp_SplitCart
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */
declare(strict_types=1);

namespace Lofmp\SplitCart\Model;

use Magento\Checkout\Api\Exception\PaymentProcessingRateLimitExceededException;
use Magento\Checkout\Api\PaymentProcessingRateLimiterInterface;
use Magento\Checkout\Api\PaymentSavingRateLimiterInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Checkout\Api\PaymentInformationManagementInterface;
use Lof\MarketPlace\Model\ResourceModel\Seller\CollectionFactory as SellerCollectionFactory;
use Lof\MarketPlace\Model\Seller;
use Lofmp\SplitCart\Api\QuoteRepositoryInterface;

/**
 * Payment information management service.
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class PaymentInformationManagement implements \Lofmp\SplitCart\Api\PaymentInformationManagementInterface
{
    /**
     * @var \Magento\Quote\Api\BillingAddressManagementInterface
     * @deprecated 100.1.0 This call was substituted to eliminate extra quote::save call
     */
    protected $billingAddressManagement;

    /**
     * @var \Magento\Quote\Api\PaymentMethodManagementInterface
     */
    protected $paymentMethodManagement;

    /**
     * @var \Magento\Quote\Api\CartManagementInterface
     */
    protected $cartManagement;

    /**
     * @var PaymentDetailsFactory
     */
    protected $paymentDetailsFactory;

    /**
     * @var \Magento\Quote\Api\CartTotalRepositoryInterface
     */
    protected $cartTotalsRepository;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @var CartRepositoryInterface
     */
    private $cartRepository;

    /**
     * @var PaymentProcessingRateLimiterInterface
     */
    private $paymentRateLimiter;

    /**
     * @var PaymentSavingRateLimiterInterface
     */
    private $saveRateLimiter;

    /**
     * @var SellerCollectionFactory
     */
    protected $sellerCollectionFactory;

    /**
     * @var QuoteRepositoryInterface
     */
    protected $splitQuoteRepository;

    /**
     * @var PaymentInformationManagementInterface
     */
    protected $checkoutPaymentInformation;

    /**
     * @var bool
     */
    private $saveRateLimiterDisabled = false;

    /**
     * @param \Magento\Quote\Api\BillingAddressManagementInterface $billingAddressManagement
     * @param \Magento\Quote\Api\PaymentMethodManagementInterface $paymentMethodManagement
     * @param \Magento\Quote\Api\CartManagementInterface $cartManagement
     * @param PaymentDetailsFactory $paymentDetailsFactory
     * @param \Magento\Quote\Api\CartTotalRepositoryInterface $cartTotalsRepository
     * @param SellerCollectionFactory $sellerCollectionFactory
     * @param QuoteRepositoryInterface $splitQuoteRepository
     * @param PaymentInformationManagementInterface $checkoutPaymentInformation
     * @param PaymentProcessingRateLimiterInterface|null $paymentRateLimiter
     * @param PaymentSavingRateLimiterInterface|null $saveRateLimiter
     * @param CartRepositoryInterface|null $cartRepository
     * @codeCoverageIgnore
     */
    public function __construct(
        \Magento\Quote\Api\BillingAddressManagementInterface $billingAddressManagement,
        \Magento\Quote\Api\PaymentMethodManagementInterface $paymentMethodManagement,
        \Magento\Quote\Api\CartManagementInterface $cartManagement,
        \Magento\Checkout\Model\PaymentDetailsFactory $paymentDetailsFactory,
        \Magento\Quote\Api\CartTotalRepositoryInterface $cartTotalsRepository,
        SellerCollectionFactory $sellerCollectionFactory,
        QuoteRepositoryInterface $splitQuoteRepository,
        PaymentInformationManagementInterface $checkoutPaymentInformation,
        ?PaymentProcessingRateLimiterInterface $paymentRateLimiter = null,
        ?PaymentSavingRateLimiterInterface $saveRateLimiter = null,
        ?CartRepositoryInterface $cartRepository = null
    ) {
        $this->billingAddressManagement = $billingAddressManagement;
        $this->paymentMethodManagement = $paymentMethodManagement;
        $this->cartManagement = $cartManagement;
        $this->paymentDetailsFactory = $paymentDetailsFactory;
        $this->cartTotalsRepository = $cartTotalsRepository;
        $this->sellerCollectionFactory = $sellerCollectionFactory;
        $this->splitQuoteRepository = $splitQuoteRepository;
        $this->checkoutPaymentInformation = $checkoutPaymentInformation;
        $this->paymentRateLimiter = $paymentRateLimiter
            ?? ObjectManager::getInstance()->get(PaymentProcessingRateLimiterInterface::class);
        $this->saveRateLimiter = $saveRateLimiter
            ?? ObjectManager::getInstance()->get(PaymentSavingRateLimiterInterface::class);
        $this->cartRepository = $cartRepository
            ?? ObjectManager::getInstance()->get(CartRepositoryInterface::class);
    }

    /**
     * @inheritdoc
     */
    public function savePaymentInformationAndPlaceOrder(
        $sellerUrl,
        $cartId,
        \Magento\Quote\Api\Data\PaymentInterface $paymentMethod,
        \Magento\Quote\Api\Data\AddressInterface $billingAddress = null
    ) {
        $splitQuote = $this->splitQuoteRepository->getSplitCartForCustomer($cartId, $sellerUrl);
        if ($splitQuote && $splitQuote->getEntityId()) {
            $cartId = $splitQuote->getQuoteId();
        }
        $orderId = $this->checkoutPaymentInformation->savePaymentInformationAndPlaceOrder($cartId, $paymentMethod, $billingAddress);
        if ($orderId) {
            $this->splitQuoteRepository->updateSplitCart($cartId);
        }
        return $orderId;
    }

    /**
     * @inheritdoc
     */
    public function savePaymentInformation(
        $sellerUrl,
        $cartId,
        \Magento\Quote\Api\Data\PaymentInterface $paymentMethod,
        \Magento\Quote\Api\Data\AddressInterface $billingAddress = null
    ) {
        $splitQuote = $this->splitQuoteRepository->getSplitCartForCustomer($cartId, $sellerUrl);
        if ($splitQuote && $splitQuote->getEntityId()) {
            $cartId = $splitQuote->getQuoteId();
        }
        return $this->checkoutPaymentInformation->savePaymentInformation($cartId, $paymentMethod, $billingAddress);
    }

    /**
     * @inheritdoc
     */
    public function getPaymentInformation($sellerUrl, $cartId)
    {
        $splitQuote = $this->splitQuoteRepository->getSplitCartForCustomer($cartId, $sellerUrl);
        if ($splitQuote && $splitQuote->getEntityId()) {
            return $this->checkoutPaymentInformation->getPaymentInformation($splitQuote->getQuoteId());
        } else {
            return $this->checkoutPaymentInformation->getPaymentInformation($cartId);
        }
    }

    /**
     * Get logger instance
     *
     * @return \Psr\Log\LoggerInterface
     * @deprecated 100.1.8
     */
    private function getLogger()
    {
        if (!$this->logger) {
            $this->logger = ObjectManager::getInstance()->get(\Psr\Log\LoggerInterface::class);
        }
        return $this->logger;
    }

    /**
     * get seller by sellerUrl
     *
     * @param string $sellerUrl
     * @return \Lof\MarketPlace\Model\Seller
     */
    protected function getSellerByUrl($sellerUrl)
    {
        $seller = $this->sellerCollectionFactory->create()
            ->addFieldToFilter('url_key', ['eq' => $sellerUrl])
            ->addFieldToFilter("status", Seller::STATUS_ENABLED)
            ->getFirstItem();
        return $seller;
    }
}
