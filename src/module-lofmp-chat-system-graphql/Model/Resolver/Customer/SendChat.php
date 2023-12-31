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
 * @package    Lofmp_ChatSystemGraphQl
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

declare(strict_types=1);

namespace Lofmp\ChatSystemGraphQl\Model\Resolver\Customer;

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
     * @var CollectionFactory
     */
    protected $collectionFactory;


    /**
     * @param CollectionFactory $collectionFactory
     * @param CustomerMessageRepositoryInterface $customerMessageRepository
     */

    public function __construct(
        CustomerMessageRepositoryInterface $customerMessageRepository,
        CollectionFactory $collectionFactory
    ) {
        $this->customerMessageRepository = $customerMessageRepository;
        $this->collectionFactory = $collectionFactory;
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
        $args = $args['input'];
        $sellerUrl = $args['seller_url'];

        $seller = $this->collectionFactory->create()
                        ->addFieldToFilter("url_key", $sellerUrl)
                        ->getFirstItem();
        $content = $args['content'];
        // $subject = $args['subject'];

        return $this->customerMessageRepository->sendMessageSeller($content,(String) $seller->getSellerId());
    }
}
