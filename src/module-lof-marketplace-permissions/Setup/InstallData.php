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

namespace Lof\MarketPermissions\Setup;

use Lof\MarketPermissions\Model\Customer\SellerAttributes;
use Lof\MarketPermissions\Model\SaveHandlerPool;
use Lof\MarketPlace\Model\SellerFactory;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Lof\MarketPlace\Model\ResourceModel\Seller\CollectionFactory as SellerCollectionFactory;

class InstallData implements \Magento\Framework\Setup\InstallDataInterface
{
    /**
     * @var SellerCollectionFactory
     */
    private $sellerCollectionFactory;

    /**
     * @var SaveHandlerPool
     */
    private $saveHandlerPool;

    /**
     * @var SellerFactory
     */
    protected $sellerFactory;

    /**
     * @var SellerAttributes
     */
    private $sellerAttributes;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * InstallData constructor.
     * @param SellerCollectionFactory $sellerCollectionFactory
     * @param SaveHandlerPool $saveHandlerPool
     * @param SellerFactory $sellerFactory
     * @param SellerAttributes $sellerAttributes
     * @param CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        SellerCollectionFactory $sellerCollectionFactory,
        SaveHandlerPool $saveHandlerPool,
        SellerFactory $sellerFactory,
        SellerAttributes $sellerAttributes,
        CustomerRepositoryInterface $customerRepository
    ) {
        $this->sellerFactory = $sellerFactory;
        $this->saveHandlerPool = $saveHandlerPool;
        $this->sellerCollectionFactory = $sellerCollectionFactory;
        $this->sellerAttributes = $sellerAttributes;
        $this->customerRepository = $customerRepository;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @SuppressWarnings(PHPMD)
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $sellerCollection = $this->sellerCollectionFactory->create();
        foreach ($sellerCollection as $seller) {
            $initialSeller = $this->sellerFactory->create();
            $customer = $this->customerRepository->getById($seller->getCustomerId());
            $sellerAttributes = $this->sellerAttributes->getSellerAttributesByCustomer($customer);
            if ($sellerAttributes->getSellerId() === null) {
                $this->saveHandlerPool->execute($seller, $initialSeller);
            }
        }
    }
}
