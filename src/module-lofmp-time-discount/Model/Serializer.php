<?php
/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * http://landofcoder.com/license
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Landofcoder
 * @package    Lof_CommonRules
 * @copyright  Copyright (c) 2020 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */

namespace Lofmp\TimeDiscount\Model;

use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Serialize\SerializerInterface;

/**
 * Class Serializer
 *
 * @package Lofmp\TimeDiscount\Model
 */
class Serializer
{
    /**
     * @var null|SerializerInterface
     */
    private $serializer;

    /**
     * construct class
     *
     * @param SerializerInterface $serializer
     */
    public function __construct(
        SerializerInterface $serializer
    ) {
        $this->serializer = $serializer;
    }

    /**
     * Serialize
     *
     * @param mixed|array|string
     * @return string
     */
    public function serialize($value)
    {
        try {
            return $this->serializer->serialize($value);
        } catch (\Exception $e) {
            return '{}';
        }
    }
    /**
     * UnSerialize
     *
     * @param string
     * @return mixed|array|bool
     */
    public function unserialize($value)
    {
        if (false === $value || null === $value || '' === $value) {
            return false;
        }
        try {
            return $this->serializer->unserialize($value);
        } catch (\InvalidArgumentException $exception) {
            return $value;
        }
    }
}