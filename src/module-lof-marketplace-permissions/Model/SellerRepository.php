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
 * @package    Lof_MarketPermissions
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lof\MarketPermissions\Model;

use Lof\MarketPermissions\Api\SellerRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Lof\MarketPermissions\Model\ResourceModel\Customer as CustomerResource;

/**
 * A repository class for seller entity that provides basic CRUD operations.
 */
class SellerRepository implements SellerRepositoryInterface
{
    /**
     * @var \Lof\MarketPermissions\Api\Data\SellerInterface[]
     */
    private $instances = [];

    /**
     * @var \Lof\MarketPermissions\Model\SellerFactory
     */
    private $sellerFactory;

    /**
     * @var \Lof\MarketPermissions\Model\Seller\Delete
     */
    private $sellerDeleter;

    /**
     * @var \Lof\MarketPermissions\Model\Seller\GetList
     */
    private $sellerListGetter;

    /**
     * @var \Lof\MarketPermissions\Model\Seller\Save
     */
    private $sellerSaver;

    /**
     * @param \Lof\MarketPermissions\Model\SellerFactory $sellerFactory
     * @param \Lof\MarketPermissions\Model\Seller\Delete $sellerDeleter
     * @param \Lof\MarketPermissions\Model\Seller\GetList $sellerListGetter
     * @param \Lof\MarketPermissions\Model\Seller\Save $sellerSaver
     */
    public function __construct(
        \Lof\MarketPermissions\Model\SellerFactory $sellerFactory,
        \Lof\MarketPermissions\Model\Seller\Delete $sellerDeleter,
        \Lof\MarketPermissions\Model\Seller\GetList $sellerListGetter,
        \Lof\MarketPermissions\Model\Seller\Save $sellerSaver
    ) {
        $this->sellerFactory = $sellerFactory;
        $this->sellerDeleter = $sellerDeleter;
        $this->sellerListGetter = $sellerListGetter;
        $this->sellerSaver = $sellerSaver;
    }

    /**
     * @inheritdoc
     */
    public function save(\Lof\MarketPermissions\Api\Data\SellerInterface $seller)
    {
        unset($this->instances[$seller->getSellerId()]);
        $this->sellerSaver->save($seller);
        return $seller;
    }

    /**
     * @inheritdoc
     */
    public function get($sellerId)
    {
        if (!isset($this->instances[$sellerId])) {
            /** @var \Lof\MarketPermissions\Model\Seller $seller */
            $seller = $this->sellerFactory->create();
            $seller->load($sellerId);
            if (!$seller->getSellerId()) {
                throw NoSuchEntityException::singleField('id', $sellerId);
            }
            $this->instances[$sellerId] = $seller;
        }
        return $this->instances[$sellerId];
    }

    /**
     * @inheritdoc
     */
    public function delete(\Lof\MarketPermissions\Api\Data\SellerInterface $seller)
    {
        $sellerId = $seller->getSellerId();
        try {
            unset($this->instances[$sellerId]);
            $this->sellerDeleter->delete($seller);
        } catch (\Exception $e) {
            throw new CouldNotDeleteException(
                __(
                    'Cannot delete seller with id %1',
                    $sellerId
                ),
                $e
            );
        }
        return true;
    }

    /**
     * @inheritdoc
     */
    public function deleteById($sellerId)
    {
        $seller = $this->get($sellerId);
        return $this->delete($seller);
    }

    /**
     * @inheritdoc
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $criteria)
    {
        return $this->sellerListGetter->getList($criteria);
    }
}
