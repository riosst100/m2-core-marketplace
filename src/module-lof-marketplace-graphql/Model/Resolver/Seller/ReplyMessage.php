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

namespace Lof\MarketplaceGraphQl\Model\Resolver\Seller;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Lof\MarketPlace\Api\SellerMessageRepositoryInterface;
use Magento\Customer\Model\Session;


class ReplyMessage implements ResolverInterface
{

    /**
     * @var SellerMessageRepositoryInterface
     */
    private $sellerMessageRepository;

    /**
     * @var Session
     */
    protected $customerSession;

    /**
     * @param SellerMessageRepositoryInterface $sellerMessageRepository
     * @param Session $customerSession
     */

    public function __construct(
        SellerMessageRepositoryInterface $sellerMessageRepository,
        Session $customerSession
    ) {
        $this->sellerMessageRepository = $sellerMessageRepository;
        $this->customerSession = $customerSession;
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
        $input = $args['input'];
        $customerId = $this->customerSession->getCustomer()->getId();

        return $this->sellerMessageRepository->sellerReplyMessage((int) $customerId, $input['message_id'], $input['content'] );

    }
}
