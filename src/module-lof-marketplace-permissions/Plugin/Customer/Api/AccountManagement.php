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

namespace Lof\MarketPermissions\Plugin\Customer\Api;

use Lof\MarketPermissions\Model\Customer\Seller;
use Lof\MarketPermissions\Model\Customer\SellerAttributes;
use Lof\MarketPermissions\Model\SellerContext;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Plugin for AccountManagement. Processing seller data.
 */
class AccountManagement
{
    /**
     * @var Http
     */
    private $request;

    /**
     * @var Seller
     */
    private $customerSeller;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var SellerContext
     */
    private $sellerContext;

    /**
     * @var SellerAttributes
     */
    private $sellerAttributes;

    /**
     * AccountManagement constructor
     *
     * @param Http $request
     * @param Seller $customerSeller
     * @param CustomerRepositoryInterface $customerRepository
     * @param SellerContext $sellerContext
     * @param SellerAttributes $sellerAttributes
     */
    public function __construct(
        Http $request,
        Seller $customerSeller,
        CustomerRepositoryInterface $customerRepository,
        SellerContext $sellerContext,
        SellerAttributes $sellerAttributes
    ) {
        $this->request = $request;
        $this->customerSeller = $customerSeller;
        $this->customerRepository = $customerRepository;
        $this->sellerContext = $sellerContext;
        $this->sellerAttributes = $sellerAttributes;
    }

    /**
     * Additional auth logic for seller customers.
     *
     * @param \Magento\Customer\Api\AccountManagementInterface $subject
     * @param string $username
     * @param string $password
     * @return \Magento\Customer\Api\Data\CustomerInterface
     * @throws \Magento\Framework\Exception\InvalidEmailOrPasswordException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeAuthenticate(\Magento\Customer\Api\AccountManagementInterface $subject, $username, $password)
    {
        try {
            $customer = $this->customerRepository->get($username);
        } catch (NoSuchEntityException $e) {
            throw new \Magento\Framework\Exception\InvalidEmailOrPasswordException(__('Invalid login or password.'));
        }
        if ($customer->getExtensionAttributes()
            && $customer->getExtensionAttributes()->getSellerAttributes()
            && $customer->getExtensionAttributes()->getSellerAttributes()->getStatus() == 0
        ) {
            throw new \Magento\Framework\Exception\LocalizedException(__('This account is locked.'));
        }

        return [$username, $password];
    }

    /**
     * Creating seller admin after authenticate.
     *
     * @param \Magento\Customer\Api\AccountManagementInterface $subject
     * @param $result
     * @return \Magento\Customer\Model\Data\Customer|mixed
     * @throws NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function afterAuthenticate(\Magento\Customer\Api\AccountManagementInterface $subject, $result)
    {
        if ($result && $result instanceof \Magento\Customer\Model\Data\Customer) {
            $sellerAttributes = $this->sellerAttributes->getSellerAttributesByCustomer($result);
            if ($sellerAttributes->getSellerId() === null) {
                $this->sellerContext->createSellerAdmin($result->getId());
            }
        }
        return $result;
    }

    /**
     * Creating seller profile after finished creating regular customer account.
     *
     * @param \Magento\Customer\Api\AccountManagementInterface $subject
     * @param \Magento\Customer\Api\Data\CustomerInterface $result
     * @return \Magento\Customer\Api\Data\CustomerInterface
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterCreateAccount(
        \Magento\Customer\Api\AccountManagementInterface $subject,
        \Magento\Customer\Api\Data\CustomerInterface $result
    ) {
        $isSeller = $this->request->getParam('is_seller');
        $sellerData = [];

        if ($isSeller === '1') {
            $sellerData = $this->request->getParams();
            if (isset($sellerData['status'])) {
                unset($sellerData['status']);
            }

            if (is_array($sellerData) && !empty($sellerData)) {
                $jobTitle = $sellerData['job_title'] ?? null;
                $this->customerSeller->createSeller($result, $sellerData, $jobTitle);
            }
        }

        return $result;
    }
}
