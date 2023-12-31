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
use Magento\QuoteGraphQl\Model\Cart\GetCartForUser;
use Lofmp\SplitCart\Api\QuoteRepositoryInterface;
use Lofmp\SplitCart\Helper\ConfigData;

/**
 * RemoveSplitCart data reslover
 */
class RemoveSplitCart implements ResolverInterface
{
    /**
     * @var GetCartForUser
     */
    private $getCartForUser;

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
     * @param QuoteRepositoryInterface $splitQuoteRepository
     * @param ConfigData $data
     */
    public function __construct(
        GetCartForUser $getCartForUser,
        QuoteRepositoryInterface $splitQuoteRepository,
        ConfigData $data
    ) {
        $this->getCartForUser = $getCartForUser;
        $this->splitQuoteRepository = $splitQuoteRepository;
        $this->dataHelper = $data;
    }

    /**
     * @inheritDoc
     */
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        if (empty($args['input']['cart_id'])) {
            throw new GraphQlInputException(__('Required parameter "cart_id" is missing'));
        }
        if ($this->dataHelper->isEnabled()) {
            $maskedCartId = $args['input']['cart_id'];
            $storeId = (int)$context->getExtensionAttributes()->getStore()->getId();
            $cart = $this->getCartForUser->execute($maskedCartId, $context->getUserId(), $storeId);

            if ((int)$context->getUserId() === 0) {
                if (!$cart->getCustomerEmail()) {
                    throw new GraphQlInputException(__("Guest email for cart is missing."));
                }
            }

            try {
                $cartId = $cart->getId();
                $response = $this->splitQuoteRepository->removeSplitCart($cartId);
                return $response;
            } catch (NoSuchEntityException $e) {
                throw new GraphQlNoSuchEntityException(__($e->getMessage()), $e);
            } catch (LocalizedException $e) {
                throw new GraphQlInputException(__('Unable to remove split cart: %message', ['message' => $e->getMessage()]), $e);
            }
        } else {
            throw new GraphQlInputException(__('Can remove split cart. The feature is not available.'));
        }
    }
}
