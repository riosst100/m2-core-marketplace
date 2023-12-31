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

declare(strict_types=1);

namespace Lof\MarketPermissions\Model;

use Lof\MarketPermissions\Api\SellerManagementInterface;
use Lof\MarketPermissions\Api\SellerUserManagerInterface;
use Lof\MarketPermissions\Api\Data\SellerCustomerInterface;
use Lof\MarketPermissions\Model\Action\Customer\Assign;
use Lof\MarketPermissions\Model\Email\CustomerData;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * @inheritdoc
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class SellerUserManager implements SellerUserManagerInterface
{
    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var EncryptorInterface
     */
    private $encryptor;

    /**
     * @var TransportBuilder
     */
    private $transportBuilder;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var ScopeConfigInterface
     */
    private $config;

    /**
     * @var SellerManagementInterface
     */
    private $management;

    /**
     * @var Assign
     */
    private $roleAssigner;

    /**
     * @var CustomerData
     */
    private $customerData;

    /**
     * @param CustomerRepositoryInterface $customerRepository
     * @param EncryptorInterface $encryptor
     * @param TransportBuilder $transportBuilder
     * @param StoreManagerInterface $storeManager
     * @param ScopeConfigInterface $config
     * @param SellerManagementInterface $management
     * @param Assign $roleAssigner
     * @param CustomerData $customerData
     */
    public function __construct(
        CustomerRepositoryInterface $customerRepository,
        EncryptorInterface $encryptor,
        TransportBuilder $transportBuilder,
        StoreManagerInterface $storeManager,
        ScopeConfigInterface $config,
        SellerManagementInterface $management,
        Assign $roleAssigner,
        CustomerData $customerData
    ) {
        $this->customerRepository = $customerRepository;
        $this->encryptor = $encryptor;
        $this->transportBuilder = $transportBuilder;
        $this->storeManager = $storeManager;
        $this->config = $config;
        $this->management = $management;
        $this->roleAssigner = $roleAssigner;
        $this->customerData = $customerData;
    }

    /**
     * @inheritDoc
     */
    public function acceptInvitation(
        string $invitationCode,
        SellerCustomerInterface $attributes,
        ?string $roleId
    ): void {
        $customer = $this->customerRepository->getById($attributes->getCustomerId());

        if ($customer->getExtensionAttributes()->getSellerAttributes()
            && $customer->getExtensionAttributes()->getSellerAttributes()->getSellerId()
        ) {
            throw new CouldNotSaveException(__('Already assigned to a seller'));
        }
        $this->validateCode($invitationCode, $attributes);
        $customer->getExtensionAttributes()->setSellerAttributes($attributes);
        try {
            $this->customerRepository->save($customer);
            if ($roleId) {
                $this->roleAssigner->assignCustomerRole($customer, $roleId);
            }
        } catch (\Throwable $exception) {
            throw new CouldNotSaveException(__('Could not assign to the seller'), $exception);
        }
    }

    /**
     * Validate invitation.
     *
     * @param string $code
     * @param SellerCustomerInterface $data
     * @throws CouldNotSaveException
     * @return void
     */
    private function validateCode(string $code, SellerCustomerInterface $data): void
    {
        $flat = $this->flattenCustomerData($data);
        if (!$this->encryptor->isValidHash($flat, $code)) {
            throw new CouldNotSaveException(__('Invalid code provided'));
        }
    }

    /**
     * @inheritDoc
     */
    public function sendInvitation(SellerCustomerInterface $forCustomer, ?string $roleId): void
    {
        $code = $this->createCode($forCustomer);
        $customer = $this->customerRepository->getById($forCustomer->getCustomerId());
        $admin = $this->management->getAdminBySellerId($forCustomer->getSellerId());
        $seller = $this->management->getByCustomerId($admin->getId());
        $customerData = $this->customerData->getDataObjectByCustomer($customer);
        $adminData = $this->customerData->getDataObjectSuperUser($forCustomer->getSellerId());
        $storeId = $customer->getStoreId();
        if (!$storeId && $customer->getWebsiteId()) {
            $stores = $this->storeManager->getWebsite($customer->getWebsiteId())->getStoreIds();
            $storeId = reset($stores);
        }

        $transport = $this->transportBuilder->setTemplateIdentifier('seller_invite_existing_customer_template')
            ->setTemplateOptions(['area' => 'frontend', 'store' => $storeId])
            ->setTemplateVars(
                [
                    'code' => $code,
                    'sellerAttributes' => $forCustomer->getData(),
                    'customer' => $customer,
                    'admin' => $admin,
                    'seller' => $seller,
                    'roleId' => $roleId,
                    'customerData' => $customerData,
                    'adminData' => $adminData,
                    'sellerData' => [
                        'name' => $seller->getSellerName()
                    ]
                ]
            )
            ->setFrom(
                $this->config->getValue(
                    'customer/create_account/email_identity',
                    ScopeInterface::SCOPE_STORE,
                    $storeId
                )
            )
            ->addTo($customer->getEmail(), $customer->getFirstname())
            ->getTransport();
        $transport->sendMessage();
    }

    /**
     * Flatten array to string.
     *
     * @param SellerCustomerInterface $data
     * @return string
     */
    private function flattenCustomerData(SellerCustomerInterface $data): string
    {
        $data = $data->getData();
        ksort($data);

        return implode('|', $data);
    }

    /**
     * Create secret code to validate invitation.
     *
     * @param SellerCustomerInterface $customerData
     * @return string
     */
    private function createCode(SellerCustomerInterface $customerData): string
    {
        return $this->encryptor->hash($this->flattenCustomerData($customerData));
    }
}
