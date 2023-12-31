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

/**
 * Process seller user save.
 */
class SellerSuperUserSave
{
    /**
     * @var \Lof\MarketPermissions\Model\Customer\SellerAttributes
     */
    private $sellerAttributes;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var \Magento\Customer\Api\AccountManagementInterface
     */
    private $customerManager;

    /**
     * @var \Lof\MarketPermissions\Model\Email\Sender
     */
    private $sellerEmailSender;

    /**
     * @var \Lof\MarketPermissions\Model\Action\Seller\ReplaceSuperUser
     */
    private $replaceSuperUser;

    /**
     * @param \Lof\MarketPermissions\Model\Customer\SellerAttributes $sellerAttributes
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Magento\Customer\Api\AccountManagementInterface $customerManager
     * @param \Lof\MarketPermissions\Model\Email\Sender $sellerEmailSender
     * @param \Lof\MarketPermissions\Model\Action\Seller\ReplaceSuperUser $replaceSuperUser
     */
    public function __construct(
        \Lof\MarketPermissions\Model\Customer\SellerAttributes $sellerAttributes,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Customer\Api\AccountManagementInterface $customerManager,
        \Lof\MarketPermissions\Model\Email\Sender $sellerEmailSender,
        \Lof\MarketPermissions\Model\Action\Seller\ReplaceSuperUser $replaceSuperUser
    ) {
        $this->sellerAttributes = $sellerAttributes;
        $this->customerRepository = $customerRepository;
        $this->customerManager = $customerManager;
        $this->sellerEmailSender = $sellerEmailSender;
        $this->replaceSuperUser = $replaceSuperUser;
    }

    /**
     * Save customer, send emails.
     *
     * @param \Magento\Customer\Api\Data\CustomerInterface $customer
     * @param \Magento\Customer\Api\Data\CustomerInterface|null $currentSuperUser [optional]
     * @param int|null $sellerStatus [optional]
     * @param bool $keepActive [optional]
     * @return \Magento\Customer\Api\Data\CustomerInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\State\InputMismatchException
     */
    public function saveCustomer(
        \Magento\Customer\Api\Data\CustomerInterface $customer,
        \Magento\Customer\Api\Data\CustomerInterface $currentSuperUser = null,
        $sellerStatus = null,
        $keepActive = true
    ) {
        $savedCustomer = $this->saveCustomerAccount($customer);
        $oldSuperUserId = $currentSuperUser ? $currentSuperUser->getId() : null;
        $this->replaceSuperUser->execute($savedCustomer, $oldSuperUserId, $keepActive);
        if ((!$customer->getId() || (int)$customer->getId() != $oldSuperUserId)
            && ($sellerStatus === \Lof\MarketPermissions\Api\Data\SellerInterface::STATUS_ENABLED)
        ) {
            $sellerAttributes = $this->sellerAttributes->getSellerAttributesByCustomer($customer);
            if ($sellerAttributes === null) {
                throw new \Magento\Framework\Exception\NoSuchEntityException(
                    __('No such entity with id = %idValue', ['idValue' => $customer->getId()])
                );
            }
            $seller = $sellerAttributes->getSellerId();
//            $this->sendEmails($seller, $savedCustomer, $oldSuperUserId, $keepActive);
        }
        return $savedCustomer;
    }

    /**
     * Create customer account (if account new) or update existing.
     *
     * @param \Magento\Customer\Api\Data\CustomerInterface $customer
     * @return \Magento\Customer\Api\Data\CustomerInterface
     * @throws \Magento\Framework\Exception\State\InputMismatchException
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function saveCustomerAccount(\Magento\Customer\Api\Data\CustomerInterface $customer)
    {
        $savedCustomer = $customer->getId()
            ? $this->customerRepository->save($customer)
            : $this->customerManager->createAccount($customer);
        return $savedCustomer;
    }

    /**
     * Send super user assign, unassign, inactivate email notifications.
     *
     * @param int $seller
     * @param \Magento\Customer\Api\Data\CustomerInterface $savedCustomer
     * @param int $oldSuperUser
     * @param bool $keepActive
     * @return void
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function sendEmails(
        $seller,
        \Magento\Customer\Api\Data\CustomerInterface $savedCustomer,
        $oldSuperUser,
        $keepActive
    ) {
        if ($seller !== null) {
            if ($oldSuperUser) {
                $oldCustomer = $this->customerRepository->getById($oldSuperUser);
                if ($keepActive) {
                    $this->sellerEmailSender->sendRemoveSuperUserNotificationEmail(
                        $oldCustomer,
                        $seller
                    );
                } else {
                    $this->sellerEmailSender->sendInactivateSuperUserNotificationEmail(
                        $oldCustomer,
                        $seller
                    );
                }
            }
        }
    }
}
