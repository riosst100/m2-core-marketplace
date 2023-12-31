<?php
/**
 * Copyright © Landofcoder.com All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Lofmp\DeliverySlotGraphQl\Model\Resolver\DataProvider;

use Lofmp\DeliverySlot\Api\Data\SellerDeliverySlotInterface;
use Lofmp\DeliverySlot\Api\GuestSellerDeliverySlotInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Exception\GraphQlNoSuchEntityException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class GetDeliverySlots
{
    /**
     * @var SellerDeliverySlotInterface
     */
    private $sellerDeliverySlotRepository;

    /**
     * @var GuestSellerDeliverySlotInterface
     */
    private $guestDeliverySlotRepository;

    /**
     * @param SellerDeliverySlotInterface $sellerDeliverySlotRepository
     * @param GuestSellerDeliverySlotInterface $guestDeliverySlotRepository
     */
    public function __construct(
        SellerDeliverySlotInterface $sellerDeliverySlotRepository,
        GuestSellerDeliverySlotInterface $guestDeliverySlotRepository
    ) {
        $this->sellerDeliverySlotRepository = $sellerDeliverySlotRepository;
        $this->guestDeliverySlotRepository = $guestDeliverySlotRepository;
    }

    /**
     * @inheritdoc
     */
    public function getGetDeliverySlots(
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    )
    {
        $cart_id = isset($args["cart_id"])?trim($args["cart_id"]):"";
        $zip_code = isset($args["zip_code"])?trim($args["zip_code"]):"";
        $target_date = isset($args["target_date"])?trim($args["target_date"]):"";

        if (!$cart_id || !$zip_code || !$target_date) {
            throw new GraphQlInputException(__('Required parameter "cart_id" or "zip_code" or "target_date" is missing!'));
        }

        $dataModel = null;

        if (false === $context->getExtensionAttributes()->getIsCustomer()) {
            $context->getExtensionAttributes()->setIsCustomer(true);
            $dataModel = $this->guestDeliverySlotRepository->getConfig($cart_id, $zip_code, $target_code);
        } else {
            $dataModel = $this->sellerDeliverySlotRepository->getConfig($cart_id, $zip_code, $target_code);
        }

        $dataModel = $this->sellerDeliverySlotRepository->get($id, $storeId);
        if (!$dataModel) {
            throw new GraphQlInputException(__('Delivery Slots Request does not match any records.'));
        }
        return $dataModel->__toArray();
    }
}

