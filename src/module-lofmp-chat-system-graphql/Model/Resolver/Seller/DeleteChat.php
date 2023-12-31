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

namespace Lofmp\ChatSystemGraphQl\Model\Resolver\Seller;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Lofmp\ChatSystemGraphQl\Api\MessageRepositoryInterface;
use Magento\Framework\GraphQl\Exception\GraphQlAuthorizationException;

class DeleteChat implements ResolverInterface
{

    /**
     * @var MessageRepositoryInterface
     */
    private $messageRepository;

    /**
      * @param MessageRepositoryInterface $messageRepository
     */

    public function __construct(
        MessageRepositoryInterface $messageRepository
    ) {

        $this->messageRepository = $messageRepository;
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
        $subject = $args['subject'];
        $message = $args['message'];

        return $this->messageRepository->sendMessageAdmin($message, $subject);

    }
}
