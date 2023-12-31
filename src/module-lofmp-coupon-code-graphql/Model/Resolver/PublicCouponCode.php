<?php
/**
 * Copyright © landofcoder.com All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Lofmp\CouponCodeGraphQl\Model\Resolver;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Exception\GraphQlNoSuchEntityException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class PublicCouponCode implements ResolverInterface
{

    /**
     * @var DataProvider\LofCouponCodes
     */
    private $dataRepository;

    /**
     * @param DataProvider\LofCouponCodes $dataRepository
     */
    public function __construct(
        DataProvider\LofCouponCodes $dataRepository
    ) {
        $this->dataRepository = $dataRepository;
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
    ) 
    {
        $couponcodeData = $this->dataRepository->getPublicCouponCodes(
            $args,
            $context
        );
        return $couponcodeData;
    }
}

