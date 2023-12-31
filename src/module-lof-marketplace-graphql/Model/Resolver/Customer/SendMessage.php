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
 * @package    Lof_MarketplaceGraphQl
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

declare(strict_types=1);

namespace Lof\MarketplaceGraphQl\Model\Resolver\Customer;

use Lof\MarketPlace\Api\CustomerMessageRepositoryInterface;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\GraphQl\Exception\GraphQlAuthorizationException;
use Lof\MarketPlace\Model\ResourceModel\Seller\CollectionFactory;


class SendMessage implements ResolverInterface
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
        if (!($args['input']) || !isset($args['input'])) {
            throw new GraphQlInputException(__('"input" value should be specified'));
        }

        $input = $args['input'];
        $sellerUrl = isset($input['seller_url']) ? $input['seller_url'] : "";
        $content = isset($input['content']) ? $input['content'] : "";
        $subject = isset($input['subject']) ? $input['subject'] : "";

        if (empty($sellerUrl) || empty($content)) {
            throw new GraphQlInputException(__('"seller_url" and "content" value should be specified'));
        }
        return $this->customerMessageRepository->sendMessage($context->getUserId(), $sellerUrl, $subject, $content);
    }
}
