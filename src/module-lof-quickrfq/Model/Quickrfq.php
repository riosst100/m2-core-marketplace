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
 * @package    Lof_Quickrfq
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lof\Quickrfq\Model;

use Magento\Catalog\Model\Product;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;

/**
 * Steps: New > Processing > Approve|Close > Done > Re New
 */
/**
 * Class Quickrfq
 *
 * @package Lof\Quickrfq\Model
 */
class Quickrfq extends \Magento\Framework\Model\AbstractModel
{
    const STATUS_NEW = 'New';
    const STATUS_PROCESSING = 'Processing';
    const STATUS_DONE = 'Done';
    const STATUS_APPROVE = 'Approve'; //approve will create shopping cart and notifiy
    const STATUS_CLOSE = 'Close';
    const STATUS_RE_NEW = 'Re New';
    const STATUS_EXPIRY = 'Expiry'; //quote expired

    /**
     * @var \Magento\Catalog\Model\ProductRepository
     */
    protected $productRepository;

    /**
     * Quickrfq constructor.
     *
     * @param Context               $context
     * @param Registry              $registry
     * @param AbstractResource|null $resource
     * @param AbstractDb|null       $resourceCollection
     * @param Product               $product
     * @param array                 $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        AbstractDb $resourceCollection = null,
        AbstractResource $resource = null,
        array $data = []
    ) {
        $this->productRepository = $productRepository;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     *
     */
    protected function _construct()
    {
        $this->_init('Lof\Quickrfq\Model\ResourceModel\Quickrfq');
    }

    /**
     * @return string[]
     */
    public function getAvailableStatuses()
    {
        return [
            self::STATUS_NEW         => 'New',
            self::STATUS_PROCESSING => 'Processing',
            self::STATUS_APPROVE       => 'Approved',
            self::STATUS_EXPIRY       => 'Expiried',
            self::STATUS_DONE       => 'Done',
            self::STATUS_RE_NEW       => 'Re-New'
        ];
    }

    /**
     * @return string
     */
    public function getProductName()
    {
        $product = $this->getProduct($this->getProductId());

        if ($product) {
            return $product->getName();
        }

        return __('Unknown');
    }

    /**
     * @param $productId
     * @return bool|\Magento\Catalog\Api\Data\ProductInterface|mixed|null
     */
    public function getProduct($productId)
    {
        try {
            $product = $this->productRepository->getById($productId);

            return ! empty($product->getId()) ? $product : null;
        } catch (NoSuchEntityException $e) {
            return false;
        }
    }

    /**
     * Save Approve Cart
     * @param string|int $cartId
     * @param string|null $expiry
     * @return bool|string|int|mixed|null
     */
    public function saveApproveCart($cartId, $expiry = null)
    {
        try {
            return $this->getResource()->saveApproveCart($this->getId(), $cartId, $expiry);
        } catch (NoSuchEntityException $e) {
            return false;
        }
    }

    /**
     * Save Approve Cart
     * @param string|int $cartId
     * @param string|null $expiry
     * @return bool|string|int|mixed|null
     */
    public function updateApproveCartStatus($cartId, $status = 0)
    {
        try {
            return $this->getResource()->updateApproveCartStatus($this->getId(), $cartId, $status);
        } catch (NoSuchEntityException $e) {
            return false;
        }
    }
}
