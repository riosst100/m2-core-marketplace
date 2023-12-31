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

namespace Lof\MarketPermissions\Model\SaveHandler;

use Lof\MarketPermissions\Model\SaveHandlerInterface;
use Lof\MarketPermissions\Api\Data\SellerCustomerInterfaceFactory;

/**
 * Super User save handler.
 */
class SuperUser implements SaveHandlerInterface
{
    /**
     * @var \Lof\MarketPermissions\Model\SellerSuperUserSave
     */
    private $sellerSuperUser;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var \Lof\MarketPermissions\Api\Data\SellerCustomerInterfaceFactory
     */
    private $sellerCustomerAttributes;

    /**
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Lof\MarketPermissions\Model\SellerSuperUserSave $sellerSuperUser
     * @param SellerCustomerInterfaceFactory $sellerCustomerAttributes
     */
    public function __construct(
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Lof\MarketPermissions\Model\SellerSuperUserSave $sellerSuperUser,
        SellerCustomerInterfaceFactory $sellerCustomerAttributes
    ) {
        $this->customerRepository = $customerRepository;
        $this->sellerSuperUser = $sellerSuperUser;
        $this->sellerCustomerAttributes = $sellerCustomerAttributes;
    }

    /**
     * Saves customer as a seller admin and sets all the related data like structure.
     *
     * @param  $seller
     * @param  $initialSeller
     * @return void
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\State\InputMismatchException
     */
    public function execute($seller, $initialSeller)
    {
        if ($seller->getCustomerId() != $initialSeller->getCustomerId()) {
            $admin = $this->customerRepository->getById($seller->getCustomerId());
            if ($admin->getExtensionAttributes()->getSellerAttributes() === null) {
                $sellerAttributes = $this->sellerCustomerAttributes->create();
                $admin->getExtensionAttributes()->setSellerAttributes($sellerAttributes);
            }
            $admin->getExtensionAttributes()->getSellerAttributes()->setSellerId($seller->getId());
            $this->customerRepository->save($admin);
            $initialAdmin = $initialSeller->getCustomerId()
                ? $this->customerRepository->getById($initialSeller->getCustomerId()) : null;
            $sellerStatus = $seller->getStatus() !== null ? (int)$seller->getStatus() : null;
            $this->sellerSuperUser->saveCustomer($admin, $initialAdmin, $sellerStatus);
        }
    }
}
