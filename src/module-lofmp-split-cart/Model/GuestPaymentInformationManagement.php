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
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Quote\Model\Quote;
use Magento\Checkout\Api\PaymentInformationManagementInterface;
use Magento\Checkout\Api\GuestPaymentInformationManagementInterface;
use Lof\MarketPlace\Model\ResourceModel\Seller\CollectionFactory as SellerCollectionFactory;
use Lof\MarketPlace\Model\Seller;
use Lofmp\SplitCart\Api\QuoteRepositoryInterface;

/**
 * Guest payment information management model.
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class GuestPaymentInformationManagement implements \Lofmp\SplitCart\Api\GuestPaymentInformationManagementInterface
{

    /**
     * @var \Magento\Quote\Api\GuestBillingAddressManagementInterface
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
     * @var \Magento\Checkout\Api\PaymentInformationManagementInterface
     */
    protected $paymentInformationManagement;

    /**
     * @var \Magento\Quote\Model\QuoteIdMaskFactory
     */
    protected $quoteIdMaskFactory;

    /**
     * @var CartRepositoryInterface
     */
    protected $cartRepository;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @var PaymentProcessingRateLimiterInterface
     */
    private $paymentsRateLimiter;

    /**
     * @var PaymentSavingRateLimiterInterface
     */
    private $savingRateLimiter;

    /**
     * @var SellerCollectionFactory
     */
    protected $sellerCollectionFactory;

    /**
     * @var QuoteRepositoryInterface
     */
    protected $splitQuoteRepository;

    /**
     * @var GuestPaymentInformationManagementInterface
     */
    protected $checkoutPaymentInformation;

    /**
     * @var bool
     */
    private $saveRateLimitDisabled = false;

    /**
     * @param \Magento\Quote\Api\GuestBillingAddressManagementInterface $billingAddressManagement
     * @param \Magento\Quote\Api\PaymentMethodManagementInterface $paymentMethodManagement
     * @param \Magento\Quote\Api\CartManagementInterface $cartManagement
     * @param \Magento\Checkout\Api\PaymentInformationManagementInterface $paymentInformationManagement
     * @param \Magento\Quote\Model\QuoteIdMaskFactory $quoteIdMaskFactory
     * @param CartRepositoryInterface $cartRepository
     * @param SellerCollectionFactory $sellerCollectionFactory
     * @param QuoteRepositoryInterface $splitQuoteRepository
     * @param GuestPaymentInformationManagementInterface $checkoutPaymentInformation
     * @param PaymentProcessingRateLimiterInterface|null $paymentsRateLimiter
     * @param PaymentSavingRateLimiterInterface|null $savingRateLimiter
     * @codeCoverageIgnore
     */
    public function __construct(
        \Magento\Quote\Api\GuestBillingAddressManagementInterface $billingAddressManagement,
        \Magento\Quote\Api\PaymentMethodManagementInterface $paymentMethodManagement,
        \Magento\Quote\Api\CartManagementInterface $cartManagement,
        \Magento\Checkout\Api\PaymentInformationManagementInterface $paymentInformationManagement,
        \Magento\Quote\Model\QuoteIdMaskFactory $quoteIdMaskFactory,
        CartRepositoryInterface $cartRepository,
        SellerCollectionFactory $sellerCollectionFactory,
        QuoteRepositoryInterface $splitQuoteRepository,
        GuestPaymentInformationManagementInterface $checkoutPaymentInformation,
        ?PaymentProcessingRateLimiterInterface $paymentsRateLimiter = null,
        ?PaymentSavingRateLimiterInterface $savingRateLimiter = null
    ) {
        $this->billingAddressManagement = $billingAddressManagement;
        $this->paymentMethodManagement = $paymentMethodManagement;
        $this->cartManagement = $cartManagement;
        $this->paymentInformationManagement = $paymentInformationManagement;
        $this->quoteIdMaskFactory = $quoteIdMaskFactory;
        $this->cartRepository = $cartRepository;
        $this->sellerCollectionFactory = $sellerCollectionFactory;
        $this->splitQuoteRepository = $splitQuoteRepository;
        $this->checkoutPaymentInformation = $checkoutPaymentInformation;
        $this->paymentsRateLimiter = $paymentsRateLimiter
            ?? ObjectManager::getInstance()->get(PaymentProcessingRateLimiterInterface::class);
        $this->savingRateLimiter = $savingRateLimiter
            ?? ObjectManager::getInstance()->get(PaymentSavingRateLimiterInterface::class);
    }

    /**
     * @inheritdoc
     */
    public function savePaymentInformationAndPlaceOrder(
        $sellerUrl,
        $cartId,
        $email,
        \Magento\Quote\Api\Data\PaymentInterface $paymentMethod,
        \Magento\Quote\Api\Data\AddressInterface $billingAddress = null
    ) {
        $splitQuote = $this->splitQuoteRepository->getSplitCartForGuest($cartId, $sellerUrl);
        if ($splitQuote && $splitQuote->getEntityId()) {
            $this->paymentsRateLimiter->limit();
            try {
                //Have to do this hack because of savePaymentInformation() plugins.
                $this->saveRateLimitDisabled = true;
                $this->savePaymentInformation($sellerUrl, $cartId, $email, $paymentMethod, $billingAddress);
            } finally {
                $this->saveRateLimitDisabled = false;
            }
            try {
                $orderId = $this->cartManagement->placeOrder($splitQuote->getQuoteId());
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->getLogger()->critical(
                    'Placing an order with quote_id ' . $cartId . ' is failed: ' . $e->getMessage()
                );
                throw new CouldNotSaveException(
                    __($e->getMessage()),
                    $e
                );
            } catch (\Exception $e) {
                $this->getLogger()->critical($e);
                throw new CouldNotSaveException(
                    __('An error occurred on the server. Please try to place the order again.'),
                    $e
                );
            }
            if ($orderId) {
                $this->splitQuoteRepository->updateSplitCart($splitQuote->getQuoteId());
            }
            return $orderId;
        } else {
            return $this->checkoutPaymentInformation->savePaymentInformationAndPlaceOrder($cartId, $email, $paymentMethod, $billingAddress);
        }
    }

    /**
     * @inheritdoc
     */
    public function savePaymentInformation(
        $sellerUrl,
        $cartId,
        $email,
        \Magento\Quote\Api\Data\PaymentInterface $paymentMethod,
        \Magento\Quote\Api\Data\AddressInterface $billingAddress = null
    ) {
        $splitQuote = $this->splitQuoteRepository->getSplitCartForGuest($cartId, $sellerUrl);
        if ($splitQuote && $splitQuote->getEntityId()) {
            $newCartId = $splitQuote->getQuoteId();
            if (!$this->saveRateLimitDisabled) {
                try {
                    $this->savingRateLimiter->limit();
                } catch (PaymentProcessingRateLimitExceededException $ex) {
                    //Limit reached
                    return false;
                }
            }
            /** @var Quote $quote */
            $quote = $this->cartRepository->getActive($newCartId);

            if ($billingAddress) {
                $billingAddress->setEmail($email);
                $quote->removeAddress($quote->getBillingAddress()->getId());
                $quote->setBillingAddress($billingAddress);
                $quote->setDataChanges(true);
            } else {
                $quote->getBillingAddress()->setEmail($email);
            }
            $this->limitShippingCarrier($quote);

            $this->paymentMethodManagement->set($newCartId, $paymentMethod);
            return true;
        } else {
            return $this->checkoutPaymentInformation->savePaymentInformation($cartId, $email);
        }
    }

    /**
     * @inheritdoc
     */
    public function getPaymentInformation($cartId, $sellerUrl)
    {
        $splitQuote = $this->splitQuoteRepository->getSplitCartForGuest($cartId, $sellerUrl);
        if ($splitQuote && $splitQuote->getEntityId()) {
            return $this->paymentInformationManagement->getPaymentInformation($splitQuote->getQuoteId());
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
            $this->logger = \Magento\Framework\App\ObjectManager::getInstance()->get(\Psr\Log\LoggerInterface::class);
        }
        return $this->logger;
    }

    /**
     * Limits shipping rates request by carrier from shipping address.
     *
     * @param Quote $quote
     *
     * @return void
     * @see \Magento\Shipping\Model\Shipping::collectRates
     */
    private function limitShippingCarrier(Quote $quote) : void
    {
        $shippingAddress = $quote->getShippingAddress();
        if ($shippingAddress && $shippingAddress->getShippingMethod()) {
            $shippingRate = $shippingAddress->getShippingRateByCode($shippingAddress->getShippingMethod());
            if ($shippingRate) {
                $shippingAddress->setLimitCarrier($shippingRate->getCarrier());
            }
        }
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
