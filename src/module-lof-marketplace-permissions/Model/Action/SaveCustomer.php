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

namespace Lof\MarketPermissions\Model\Action;

use Lof\MarketPermissions\Api\SellerUserManagerInterface;
use Lof\MarketPermissions\Api\Data\SellerCustomerInterface;
use Lof\MarketPermissions\Model\SellerUser;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Lof\MarketPermissions\Api\SellerRepositoryInterface;

/**
 * Create or update customer from request.
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class SaveCustomer
{
    /**
     * @var \Lof\MarketPermissions\Model\Action\Customer\Populator
     */
    private $customerPopulator;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var \Lof\MarketPermissions\Api\SellerRepositoryInterface
     */
    private $sellerRepository;

    /**
     * @var \Lof\MarketPermissions\Model\Action\Customer\Assign
     */
    private $roleAssigner;

    /**
     * @var \Lof\MarketPermissions\Model\Action\Customer\Create
     */
    private $customerCreator;

    /**
     * @var SellerUserManagerInterface
     */
    private $userManager;

    /**
     * @var SellerUser
     */
    private $userHelper;

    /**
     * @param Customer\Populator $customerPopulator
     * @param CustomerRepositoryInterface $customerRepository
     * @param SellerRepositoryInterface $sellerRepository
     * @param Customer\Assign $roleAssigner
     * @param Customer\Create $customerCreator
     * @param SellerUserManagerInterface|null $userManager
     * @param SellerUser|null $SellerUser
     */
    public function __construct(
        Customer\Populator $customerPopulator,
        CustomerRepositoryInterface $customerRepository,
        SellerRepositoryInterface $sellerRepository,
        Customer\Assign $roleAssigner,
        Customer\Create $customerCreator,
        ?SellerUserManagerInterface $userManager = null,
        ?SellerUser $SellerUser = null
    ) {
        $this->customerPopulator = $customerPopulator;
        $this->customerRepository = $customerRepository;
        $this->sellerRepository = $sellerRepository;
        $this->roleAssigner = $roleAssigner;
        $this->customerCreator = $customerCreator;
        $this->userManager = $userManager ?? ObjectManager::getInstance()->get(SellerUserManagerInterface::class);
        $this->userHelper = $SellerUser ?? ObjectManager::getInstance()->get(SellerUser::class);
    }

    /**
     * Create customer from request.
     *
     * @param RequestInterface $request
     * @return CustomerInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws InviteConfirmationNeededException
     * @throws \Magento\Framework\Exception\State\InputMismatchException
     */
    public function create(RequestInterface $request)
    {
        try {
            $customer = $this->customerRepository->get($request->getParam('email'));
            if ($this->hasCustomerSellerId($customer)) {
                throw new \Magento\Framework\Exception\State\InputMismatchException(
                    __('A customer with the same email already assigned to seller.')
                );
            }
        } catch (NoSuchEntityException $e) {
            $customer = null;
        }

        $customer = $this->customerPopulator->populate($request->getParams(), $customer);
        if ($customer->getId()) {
            $this->sendInvitationToExisting($customer);
            throw new InviteConfirmationNeededException(
                __(
                    'Invitation was sent to an existing customer, '
                    .'they will be added to your organization once they accept the invitation.'
                ),
                $customer
            );
        }
        $targetId = $request->getParam('target_id');
        $customer = $this->customerCreator->execute($customer, $targetId);
        $this->roleAssigner->assignCustomerRole($customer, $request->getParam('role'));

        return $customer;
    }

    /**
     * Update customer from request.
     *
     * @param RequestInterface $request
     * @return CustomerInterface
     * @throws \Magento\Framework\Exception\State\InputMismatchException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function update(RequestInterface $request)
    {
        $customerId = $request->getParam('customer_id');

        $customer = $this->customerRepository->getById($customerId);
        $seller = $this->sellerRepository->get(
            $customer->getExtensionAttributes()->getSellerAttributes()->getSellerId()
        );
        $customer = $this->customerPopulator->populate(
            $request->getParams(),
            $customer
        );
        $this->customerRepository->save($customer);
        if ($seller->getCustomerId() != $customerId) {
            $this->roleAssigner->assignCustomerRole($customer, $request->getParam('role'));
        }

        return $customer;
    }

    /**
     * Has customer seller.
     *
     * @param CustomerInterface $customer
     * @return bool
     */
    private function hasCustomerSellerId(CustomerInterface $customer)
    {
        return $customer->getExtensionAttributes()
        && $customer->getExtensionAttributes()->getSellerAttributes()
        && (int)$customer->getExtensionAttributes()->getSellerAttributes()->getSellerId() > 0;
    }

    /**
     * When trying to assign existing customer then sending them an invite first.
     *
     * @param CustomerInterface $customer
     * @throws LocalizedException
     * @return void
     */
    private function sendInvitationToExisting(CustomerInterface $customer): void
    {
        if (!$sellerIdId = $this->userHelper->getCurrentSellerId()) {
            throw new \RuntimeException('Meant to be initiated by a seller customer');
        }
        /** @var SellerCustomerInterface $sellerIdAttributes */
        $sellerIdAttributes = $customer->getExtensionAttributes()->getSellerAttributes();
        $sellerIdAttributes->setCustomerId($customer->getId());
        $sellerIdAttributes->setSellerId($sellerIdId);
        $this->userManager->sendInvitation($sellerIdAttributes, null);
    }
}
