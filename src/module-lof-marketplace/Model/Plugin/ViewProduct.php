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
 * @package    Lof_MarketPlace
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lof\MarketPlace\Model\Plugin;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;

class ViewProduct
{
    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @var \Magento\Framework\App\Response\RedirectInterface
     */
    protected $_redirect;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var \Lof\MarketPlace\Helper\Data
     */
    protected $sellerHelper;

    /**
     * @var \Lof\MarketPlace\Model\Seller
     */
    protected $seller;

    /**
     * ViewProduct constructor.
     * @param ProductRepositoryInterface $productRepository
     * @param \Lof\MarketPlace\Helper\Data $helper
     * @param \Lof\MarketPlace\Model\Seller $seller
     */
    public function __construct(
        ProductRepositoryInterface $productRepository,
        \Lof\MarketPlace\Helper\Data $helper,
        \Lof\MarketPlace\Model\Seller $seller
    ) {
        $this->seller = $seller;
        $this->productRepository = $productRepository;
        $this->sellerHelper = $helper;
    }

    /**
     * Get Not active seller ids
     *
     * @return array
     */
    public function getNotActiveSellerIds()
    {
        $collection = $this->seller->getCollection();
        $collection->addFieldToFilter('status', ['neq' => 1]);
        return $collection->getAllIds();
    }

    /**
     * @return int[]
     */
    public function getAllowedApprovalStatus()
    {
        return [
            0,
            2,
        ];
    }

    /**
     * @param \Magento\Catalog\Helper\Product $subject
     * @param \Closure $proceed
     * @param $product
     * @return bool|mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundCanShow(
        \Magento\Catalog\Helper\Product $subject,
        \Closure $proceed,
        $product
    ) {
        if (is_int($product)) {
            try {
                $product = $this->productRepository->getById($product);
            } catch (NoSuchEntityException $e) {
                return false;
            }
        } else {
            if (!$product->getId()) {
                return false;
            }
        }

        $notActiveSellerIds = $this->getNotActiveSellerIds();

        if (!in_array($product->getData('approval'), $this->getAllowedApprovalStatus())
            || in_array($product->getData('seller_id'), $notActiveSellerIds)
        ) {
            return false;
        }

        return $proceed($product);
    }
}
