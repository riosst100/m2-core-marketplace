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

namespace Lof\MarketPermissions\Model\Email;

use Lof\MarketPermissions\Api\SellerRepositoryInterface;
use Lof\MarketPermissions\Model\Config\EmailTemplate as EmailTemplateConfig;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Api\CustomerNameGenerationInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Sending seller related emails.
 */
class Sender
{
    /**
     * Email template for identity.
     */
    private $xmlPathRegisterEmailIdentity = 'customer/create_account/email_identity';

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var \Lof\MarketPermissions\Model\Email\Transporter
     */
    private $transporter;

    /**
     * @var \Magento\Customer\Api\CustomerNameGenerationInterface
     */
    private $customerViewHelper;

    /**
     * @var \Lof\MarketPermissions\Model\Email\CustomerData
     */
    private $customerData;

    /**
     * @var \Lof\MarketPermissions\Model\Config\EmailTemplate
     */
    private $emailTemplateConfig;

    /**
     * @var \Lof\MarketPermissions\Api\SellerRepositoryInterface
     */
    private $sellerRepository;

    /**
     * @param StoreManagerInterface $storeManager
     * @param ScopeConfigInterface $scopeConfig
     * @param Transporter $transporter
     * @param CustomerNameGenerationInterface $customerViewHelper
     * @param CustomerData $customerData
     * @param EmailTemplateConfig $emailTemplateConfig
     * @param SellerRepositoryInterface $sellerRepository
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        ScopeConfigInterface $scopeConfig,
        Transporter $transporter,
        CustomerNameGenerationInterface $customerViewHelper,
        CustomerData $customerData,
        EmailTemplateConfig $emailTemplateConfig,
        SellerRepositoryInterface $sellerRepository
    ) {
        $this->storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
        $this->transporter = $transporter;
        $this->customerViewHelper = $customerViewHelper;
        $this->customerData = $customerData;
        $this->emailTemplateConfig = $emailTemplateConfig;
        $this->sellerRepository = $sellerRepository;
    }

    /**
     * Send email to customer after assign seller to him.
     *
     * @param \Magento\Customer\Api\Data\CustomerInterface $customer
     * @param int $sellerId
     * @return $this
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function sendCustomerSellerAssignNotificationEmail(
        \Magento\Customer\Api\Data\CustomerInterface $customer,
        $sellerId
    ) {
        $customerName = $this->customerViewHelper->getCustomerName($customer);
        $sellerIdSuperUser = $this->customerData->getDataObjectSuperUser($sellerId);
        $mergedCustomerData = $this->customerData->getDataObjectByCustomer($customer, $sellerId);

        if ($sellerIdSuperUser && $mergedCustomerData) {
            $sender = [
                'name' => $sellerIdSuperUser->getName(),
                'email' => $sellerIdSuperUser->getEmail()
            ];

            $mergedCustomerData->setData('sellerAdminEmail', $sellerIdSuperUser->getEmail());
            $this->sendEmailTemplate(
                $customer->getEmail(),
                $customerName,
                $this->emailTemplateConfig->getSellerCustomerAssignUserTemplateId(
                    ScopeInterface::SCOPE_STORE,
                    $customer->getStoreId()
                ),
                $sender,
                ['customer' => $mergedCustomerData],
                $customer->getStoreId()
            );
        }

        return $this;
    }

    /**
     * Send email to customer with remove message.
     *
     * @param CustomerInterface $customer
     * @param int $sellerId
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function sendRemoveSuperUserNotificationEmail(CustomerInterface $customer, $sellerId)
    {
        $storeId = $customer->getStoreId();
        if (!$storeId) {
            $storeId = $this->getWebsiteStoreId($customer);
        }
        $customerEmailData = $this->customerData->getDataObjectByCustomer($customer, $sellerId);
        if ($customerEmailData !== null) {
            $this->sendEmailTemplate(
                $customer->getEmail(),
                $this->customerViewHelper->getCustomerName($customer),
                $this->emailTemplateConfig->getCustomerRemoveSuperUserTemplateId(
                    ScopeInterface::SCOPE_STORE,
                    $storeId
                ),
                $this->xmlPathRegisterEmailIdentity,
                ['customer' => $customerEmailData],
                $storeId
            );
        }
        return $this;
    }

    /**
     * Send email to customer with inactivate message.
     *
     * @param CustomerInterface $customer
     * @param int $sellerId
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function sendInactivateSuperUserNotificationEmail(CustomerInterface $customer, $sellerId)
    {
        $storeId = $customer->getStoreId();
        if (!$storeId) {
            $storeId = $this->getWebsiteStoreId($customer);
        }
        $customerEmailData = $this->customerData->getDataObjectByCustomer($customer, $sellerId);
        if ($customerEmailData !== null) {
            $this->sendEmailTemplate(
                $customer->getEmail(),
                $this->customerViewHelper->getCustomerName($customer),
                $this->emailTemplateConfig->getCustomerInactivateSuperUserTemplateId(
                    ScopeInterface::SCOPE_STORE,
                    $storeId
                ),
                $this->xmlPathRegisterEmailIdentity,
                ['customer' => $customerEmailData],
                $storeId
            );
        }
        return $this;
    }

    /**
     * Get either first store ID from a set website or the provided as default.
     *
     * @param CustomerInterface $customer
     * @return int
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getWebsiteStoreId(CustomerInterface $customer)
    {
        $defaultStoreId = \Magento\Store\Model\Store::DEFAULT_STORE_ID;
        if ($customer->getWebsiteId() != 0) {
            $storeIds = $this->storeManager->getWebsite($customer->getWebsiteId())->getStoreIds();
            reset($storeIds);
            $defaultStoreId = current($storeIds);
        }
        return $defaultStoreId;
    }

    /**
     * Send email to sales representative.
     *
     * @param int $sellerId
     * @param int $salesRepresentativeId [optional]
     * @return $this
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function sendSalesRepresentativeNotificationEmail($sellerId, $salesRepresentativeId = 0)
    {
        $salesRepresentativeDataObject = $this->customerData
            ->getDataObjectSalesRepresentative($sellerId, $salesRepresentativeId);
        if ($salesRepresentativeDataObject !== null) {
            $this->sendEmailTemplate(
                $salesRepresentativeDataObject->getEmail(),
                $salesRepresentativeDataObject->getName(),
                $this->emailTemplateConfig->getSalesRepresentativeUserTemplateId(),
                $this->xmlPathRegisterEmailIdentity,
                ['customer' => $salesRepresentativeDataObject],
                $this->customerData->getDataObjectSuperUser($sellerId)
                    ->getStoreId()
            );
        }

        return $this;
    }


    /**
     * Notify admin about new seller.
     *
     * @param CustomerInterface $customer
     * @param string $sellerName
     * @param string $sellerUrl
     * @return $this
     */
    public function sendAdminNotificationEmail(CustomerInterface $customer, $sellerName, $sellerUrl)
    {
        $toCode = $this->emailTemplateConfig->getSellerCreateRecipient(ScopeInterface::SCOPE_STORE);
        $toEmail = $this->scopeConfig->getValue('trans_email/ident_' . $toCode . '/email', ScopeInterface::SCOPE_STORE);
        $toName = $this->scopeConfig->getValue('trans_email/ident_' . $toCode . '/name', ScopeInterface::SCOPE_STORE);

        $copyTo = $this->emailTemplateConfig->getSellerCreateCopyTo(ScopeInterface::SCOPE_STORE);
        $copyMethod = $this->emailTemplateConfig->getSellerCreateCopyMethod(ScopeInterface::SCOPE_STORE);
        $storeId = $customer->getStoreId() ?: $this->getWebsiteStoreId($customer);

        $sendTo = [];
        if ($copyTo && $copyMethod == 'copy') {
            $sendTo = explode(',', $copyTo);
        }
        array_unshift($sendTo, $toEmail);

        foreach ($sendTo as $recipient) {
            $this->sendEmailTemplate(
                $recipient,
                $toName,
                $this->emailTemplateConfig->getSellerCreateNotifyAdminTemplateId(),
                [
                    'email' => $customer->getEmail(),
                    'name' => $this->customerViewHelper->getCustomerName($customer)
                ],
                [
                    'customer' => $customer->getFirstname(),
                    'seller' => $sellerName,
                    'admin' => $toName,
                    'seller_url' => $sellerUrl
                ],
                $storeId,
                ($copyTo && $copyMethod == 'bcc') ? explode(',', $copyTo) : []
            );
        }

        return $this;
    }

    /**
     * Notify seller admin of seller status change.
     *
     * @param CustomerInterface $customer
     * @param int $sellerId
     * @param string $templatePath
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function sendSellerStatusChangeNotificationEmail(CustomerInterface $customer, $sellerId, $templatePath)
    {
        $storeId = $customer->getStoreId();
        if (!$storeId) {
            $storeId = $this->getWebsiteStoreId($customer);
        }

        $copyTo = $this->emailTemplateConfig->getSellerStatusChangeCopyTo(ScopeInterface::SCOPE_STORE);
        $copyMethod = $this->emailTemplateConfig->getSellerStatusChangeCopyMethod(ScopeInterface::SCOPE_STORE);

        $sendTo = [];
        if ($copyTo && $copyMethod == 'copy') {
            $sendTo = explode(',', $copyTo);
        }
        array_unshift($sendTo, $customer->getEmail());

        $customerEmailData = $this->customerData->getDataObjectByCustomer($customer, $sellerId);
        if ($customerEmailData !== null) {
            foreach ($sendTo as $recipient) {
                $this->sendEmailTemplate(
                    $recipient,
                    $this->customerViewHelper->getCustomerName($customer),
                    $this->scopeConfig->getValue($templatePath, ScopeInterface::SCOPE_STORE, $storeId),
                    $this->xmlPathRegisterEmailIdentity,
                    ['customer' => $customerEmailData],
                    $storeId,
                    ($copyTo && $copyMethod == 'bcc') ? explode(',', $copyTo) : []
                );
            }
        }
        return $this;
    }

    /**
     * Send email to customer with status update message.
     *
     * @param CustomerInterface $customer
     * @param int $status
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function sendUserStatusChangeNotificationEmail(CustomerInterface $customer, $status)
    {
        $storeId = $customer->getStoreId();
        if (!$storeId) {
            $storeId = $this->getWebsiteStoreId($customer);
        }
        $templateId = $status
            ? $this->emailTemplateConfig->getActivateCustomerTemplateId(ScopeInterface::SCOPE_STORE, $storeId)
            : $this->emailTemplateConfig->getInactivateCustomerTemplateId(ScopeInterface::SCOPE_STORE, $storeId);
        $customerEmailData = $this->customerData->getDataObjectByCustomer($customer);
        if ($customerEmailData !== null) {
            $this->sendEmailTemplate(
                $customer->getEmail(),
                $this->customerViewHelper->getCustomerName($customer),
                $templateId,
                $this->xmlPathRegisterEmailIdentity,
                ['customer' => $customerEmailData],
                $storeId
            );
        }
        return $this;
    }

    /**
     * Send corresponding email template.
     *
     * @param string $customerEmail
     * @param string $customerName
     * @param string $templateId
     * @param string|array $sender configuration path of email identity
     * @param array $templateParams [optional]
     * @param int|null $storeId [optional]
     * @param array $bcc [optional]
     * @return void
     */
    private function sendEmailTemplate(
        $customerEmail,
        $customerName,
        $templateId,
        $sender,
        array $templateParams = [],
        $storeId = null,
        array $bcc = []
    ) {
        $from = $sender;
        if (is_string($sender)) {
            $from = $this->scopeConfig->getValue($sender, ScopeInterface::SCOPE_STORE, $storeId);
        }
        $this->transporter->sendMessage(
            $customerEmail,
            $customerName,
            $from,
            $templateId,
            $templateParams,
            $storeId,
            $bcc
        );
    }
}
