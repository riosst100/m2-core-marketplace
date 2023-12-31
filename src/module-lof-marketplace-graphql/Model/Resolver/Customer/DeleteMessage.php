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
 * @package    Lof_SellerMessageGraphQl
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

declare(strict_types=1);

namespace Lof\MarketplaceGraphQl\Model\Resolver\Customer;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Exception\GraphQlAuthorizationException;
use Lof\MarketPlace\Api\CustomerMessageRepositoryInterface;

class DeleteMessage implements ResolverInterface
{
    /**
     * @var CustomerMessageRepositoryInterface
     */
    private $customerMessageRepository;

    /**
     * @param CustomerMessageRepositoryInterface $customerMessageRepository
     */
    public function __construct(
        CustomerMessageRepositoryInterface $customerMessageRepository
    ) {
        $this->customerMessageRepository = $customerMessageRepository;
    }

    /**
     * @inheritdoc
     */
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        if (!$context->getExtensionAttributes()->getIsCustomer()) {
            throw new GraphQlAuthorizationException(__('The current customer isn\'t authorized.'));
        }
        if (!($args['message_id']) || !isset($args['message_id'])) {
            throw new GraphQlInputException(__('"message_id" value should be specified'));
        }
        $data = $this->customerMessageRepository->deleteMessage($context->getUserId(), (int)$args['message_id']);

        return [
            "code" => $data ? 0 : 1,
            "message" => $data ? "The message was delete successfully!" : "Error when delete the message."
        ];
    }
}
