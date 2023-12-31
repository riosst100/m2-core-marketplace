<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Lofmp\SplitCartGraphQl\Model\Resolver;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Exception\GraphQlNoSuchEntityException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Quote\Api\CartManagementInterface;
use Magento\Quote\Api\PaymentMethodManagementInterface;
use Magento\QuoteGraphQl\Model\Cart\GetCartForUser;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\QuoteGraphQl\Model\Cart\CheckCartCheckoutAllowance;
use Lofmp\SplitCart\Api\QuoteRepositoryInterface;
use Lofmp\SplitCart\Helper\ConfigData;

/**
 * @inheritdoc
 */
class PlaceOrder implements ResolverInterface
{
    /**
     * @var CartManagementInterface
     */
    private $cartManagement;

    /**
     * @var GetCartForUser
     */
    private $getCartForUser;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var CheckCartCheckoutAllowance
     */
    private $checkCartCheckoutAllowance;

    /**
     * @var PaymentMethodManagementInterface
     */
    private $paymentMethodManagement;

    /**
     * @var QuoteRepositoryInterface
     */
    protected $splitQuoteRepository;

    /**
     * @var ConfigData
     */
    protected $dataHelper;

    /**
     * @param GetCartForUser $getCartForUser
     * @param CartManagementInterface $cartManagement
     * @param OrderRepositoryInterface $orderRepository
     * @param CheckCartCheckoutAllowance $checkCartCheckoutAllowance
     * @param PaymentMethodManagementInterface $paymentMethodManagement
     * @param QuoteRepositoryInterface $splitQuoteRepository
     * @param ConfigData $data
     */
    public function __construct(
        GetCartForUser $getCartForUser,
        CartManagementInterface $cartManagement,
        OrderRepositoryInterface $orderRepository,
        CheckCartCheckoutAllowance $checkCartCheckoutAllowance,
        PaymentMethodManagementInterface $paymentMethodManagement,
        QuoteRepositoryInterface $splitQuoteRepository,
        ConfigData $data
    ) {
        $this->getCartForUser = $getCartForUser;
        $this->cartManagement = $cartManagement;
        $this->orderRepository = $orderRepository;
        $this->checkCartCheckoutAllowance = $checkCartCheckoutAllowance;
        $this->paymentMethodManagement = $paymentMethodManagement;
        $this->splitQuoteRepository = $splitQuoteRepository;
        $this->dataHelper = $data;
    }

    /**
     * @inheritdoc
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        if (empty($args['input']['cart_id']) || empty($args['sellerUrl'])) {
            throw new GraphQlInputException(__('Required parameter "cart_id" or "sellerUrl" is missing'));
        }
        if ($this->dataHelper->isEnabled()) {
            $maskedCartId = $args['input']['cart_id'];
            $sellerUrl = $args['sellerUrl'];

            $storeId = (int)$context->getExtensionAttributes()->getStore()->getId();
            $cart = $this->getCartForUser->execute($maskedCartId, $context->getUserId(), $storeId);
            $this->checkCartCheckoutAllowance->execute($cart);

            if ((int)$context->getUserId() === 0) {
                if (!$cart->getCustomerEmail()) {
                    throw new GraphQlInputException(__("Guest email for cart is missing."));
                }
                $cart->setCheckoutMethod(CartManagementInterface::METHOD_GUEST);
            }

            try {
                $cartId = $cart->getId();
                /** get split cart data */
                $splitQuote = $this->splitQuoteRepository->getSplitCartForCustomer($cartId, $sellerUrl);
                if ($splitQuote && $splitQuote->getEntityId()) {
                    $cartId = $splitQuote->getQuoteId();
                }

                $orderId = $this->cartManagement->placeOrder($cartId, $this->paymentMethodManagement->get($cartId));
                $order = $this->orderRepository->get($orderId);

                /** Update split cart after place order */
                if ($orderId) {
                    $this->splitQuoteRepository->updateSplitCart($cartId);
                }

                return [
                    'order' => [
                        'order_number' => $order->getIncrementId(),
                        // @deprecated The order_id field is deprecated, use order_number instead
                        'order_id' => $order->getIncrementId(),
                    ],
                ];
            } catch (NoSuchEntityException $e) {
                throw new GraphQlNoSuchEntityException(__($e->getMessage()), $e);
            } catch (LocalizedException $e) {
                throw new GraphQlInputException(__('Unable to place order: %message', ['message' => $e->getMessage()]), $e);
            }
        } else {
            throw new GraphQlInputException(__('Can not place order for split cart. The feature is not available.'));
        }
    }
}
