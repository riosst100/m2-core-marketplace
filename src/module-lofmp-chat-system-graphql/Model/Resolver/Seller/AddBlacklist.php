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
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Lofmp\ChatSystemGraphQl\Model\Reply\AddReplies;

class AddBlacklist implements ResolverInterface
{
      /**
     * @var AddReplies
     */
    private $addReplies;

    public function __construct(
        AddReplies $addReplies
    ) {
        $this->addReplies = $addReplies;
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

        $data = [
            'message_id' => $input['message_id'],
            'content' => $input['content'],
        ];

        $messageModel = $this->addReplies->execute($data);

        return $messageModel;
    }
}
