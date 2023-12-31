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

namespace Lof\MarketPermissions\Controller\Marketplace\Customer;

use Magento\Customer\Api\Data\CustomerInterface;
use Lof\MarketPermissions\Api\Data\SellerCustomerInterface;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Action\HttpPostActionInterface as HttpPostActionInterface;

/**
 * Class Check
 */
class Check extends \Lof\MarketPermissions\Controller\Marketplace\AbstractAction implements HttpPostActionInterface, HttpGetActionInterface
{
    /**
     * Authorization level of a seller session.
     */
    const SELLER_RESOURCE = 'Lof_MarketPermissions::users_edit';

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * Check constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Lof\MarketPermissions\Model\SellerContext $sellerContext
     * @param \Magento\Framework\Url $frontendUrl
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Lof\MarketPermissions\Model\SellerContext $sellerContext,
        \Magento\Framework\Url $frontendUrl,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
    ) {
        parent::__construct($context, $sellerContext, $frontendUrl, $logger);
        $this->customerRepository = $customerRepository;
    }

    /**
     * Check customer email.
     *
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        $email = $this->getRequest()->getParam('email');
        try {
            $customer = $this->customerRepository->get($email);
            $message = $this->getCustomerSellerErrorMessage($customer);
            if ($message) {
                return $this->jsonError($message);
            }
            $message = __(
                'A user with this email address already exists in the system. '
                . 'If you proceed, the user will be linked to your seller.'
            );
            return $this->jsonSuccess($this->getCustomerData($customer), $message);
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            // Do not remove this handle as it used to check that customer with this email not registered in the system
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            return $this->handleJsonError($e->getMessage());
        } catch (\Exception $e) {
            $this->logger->critical($e);

            return $this->handleJsonError();
        }

        return $this->jsonSuccess([]);
    }

    /**
     * @param CustomerInterface $customer
     * @return SellerCustomerInterface|null
     */
    private function getSellerAttributes(CustomerInterface $customer)
    {
        if ($customer->getExtensionAttributes() && $customer->getExtensionAttributes()->getSellerAttributes()) {
            return $customer->getExtensionAttributes()->getSellerAttributes();
        }
        return null;
    }

    /**
     * @param CustomerInterface $customer
     * @return array
     */
    private function getCustomerData(CustomerInterface $customer)
    {
        $customerData = [
            'firstname' => $customer->getFirstname(),
            'lastname' => $customer->getLastname()
        ];
        $sellerAttribute = $this->getSellerAttributes($customer);
        if ($sellerAttribute) {
            $customerData['extension_attributes[seller_attributes][job_title]'] = $sellerAttribute->getJobTitle()
                ?: '';
            $customerData['extension_attributes[seller_attributes][telephone]'] = $sellerAttribute->getTelephone()
                ?: '';
            $customerData['extension_attributes[seller_attributes][status]'] = $sellerAttribute->getStatus() ?: 1;
        }
        return $customerData;
    }

    /**
     * @param CustomerInterface $customer
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getCustomerSellerErrorMessage(CustomerInterface $customer)
    {
        $sellerAttribute = $this->getSellerAttributes($customer);
        $currentSeller = $this->customerRepository->getById($this->sellerContext->getCustomerId())
            ->getExtensionAttributes()->getSellerAttributes();
        $message = '';
        if ($sellerAttribute) {
            if ($sellerAttribute->getSellerId() == $currentSeller->getSellerId()) {
                $message = __('A user with this email address is already a member of your seller.');
            } elseif ((int)$sellerAttribute->getSellerId() > 0) {
                $message = __(
                    'A user with this email address already exists in the system. '
                    . 'Enter a different email address to create this user.'
                );
            }
        }
        return $message;
    }
}
