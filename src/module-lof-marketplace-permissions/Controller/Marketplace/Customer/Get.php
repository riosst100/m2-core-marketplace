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

use Lof\MarketPermissions\Api\AclInterface;
use Lof\MarketPermissions\Model\Seller\Structure;
use Lof\MarketPermissions\Model\SellerContext;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\Customer;
use Magento\Eav\Model\Config as EavConfig;
use Magento\Eav\Model\Entity\Attribute;
use Magento\Eav\Model\Entity\Attribute\AbstractAttribute;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface as HttpGetActionInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Exception\LocalizedException;
use Psr\Log\LoggerInterface;

/**
 * Controller for retrieving customer info on the frontend.
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Get extends \Lof\MarketPermissions\Controller\Marketplace\AbstractAction implements HttpGetActionInterface
{
    /**
     * Authorization level of a seller session.
     */
    const SELLER_RESOURCE = 'Lof_MarketPermissions::users_edit';

    /**
     * @var AclInterface
     */
    private $acl;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var EavConfig
     */
    private $eavConfig;

    /**
     * @var Structure
     */
    private $structureManager;

    /**
     * Get constructor.
     * @param Context $context
     * @param SellerContext $sellerContext
     * @param \Magento\Framework\Url $frontendUrl
     * @param LoggerInterface $logger
     * @param CustomerRepositoryInterface $customerRepository
     * @param Structure $structureManager
     * @param AclInterface $acl
     * @param EavConfig|null $eavConfig
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Lof\MarketPermissions\Model\SellerContext $sellerContext,
        \Magento\Framework\Url $frontendUrl,
        \Psr\Log\LoggerInterface $logger,
        CustomerRepositoryInterface $customerRepository,
        Structure $structureManager,
        AclInterface $acl,
        ?EavConfig $eavConfig = null
    ) {
        parent::__construct($context, $sellerContext, $frontendUrl, $logger);
        $this->acl = $acl;
        $this->customerRepository = $customerRepository;
        $this->structureManager = $structureManager;
        $this->eavConfig = $eavConfig ?: ObjectManager::getInstance()
            ->get(EavConfig::class);
    }

    /**
     * Get customer action.
     *
     * @return Json
     */
    public function execute()
    {
        $request = $this->getRequest();

        $allowedIds = $this->structureManager->getAllowedIds($this->sellerContext->getCustomerId());
        $customerId = $request->getParam('customer_id');

        if (!in_array($customerId, $allowedIds['users'])) {
            return $this->jsonError(__('You are not allowed to do this.'));
        }

        try {
            $customer = $this->customerRepository->getById($customerId);
            $sellerAttributes = null;
            if ($customer->getExtensionAttributes() !== null
                && $customer->getExtensionAttributes()->getSellerAttributes() !== null
            ) {
                $sellerAttributes = $customer->getExtensionAttributes()->getSellerAttributes();
            }
            $this->setCustomerCustomDateAttribute($customer);
        } catch (LocalizedException $e) {
            return $this->handleJsonError($e->getMessage());
        } catch (\Exception $e) {
            $this->logger->critical($e);

            return $this->handleJsonError();
        }

        $customerData = $customer->__toArray();
        if ($sellerAttributes !== null) {
            $customerData['extension_attributes[seller_attributes][job_title]'] = $sellerAttributes->getJobTitle();
            $customerData['extension_attributes[seller_attributes][telephone]'] = $sellerAttributes->getTelephone();
            $customerData['extension_attributes[seller_attributes][status]'] = $sellerAttributes->getStatus();
        }
        $roles = $this->acl->getRolesByUserId($customerId);
        if (count($roles)) {
            foreach ($roles as $role) {
                $customerData['role'] = $role->getId();
                break;
            }
        }
        return $this->jsonSuccess($customerData);
    }

    /**
     * Get attribute type for upcoming validation.
     *
     * @param AbstractAttribute|Attribute $attribute
     * @return string
     */
    private function getAttributeType(AbstractAttribute $attribute): string
    {
        $frontendInput = $attribute->getFrontendInput();
        if ($attribute->usesSource() && in_array($frontendInput, ['select', 'multiselect', 'boolean'])) {
            return $frontendInput;
        } elseif ($attribute->isStatic()) {
            return $frontendInput == 'date' ? 'datetime' : 'varchar';
        } else {
            return $attribute->getBackendType();
        }
    }

    /**
     * Set customer custom date attribute
     *
     * @param CustomerInterface $customer
     * @throws LocalizedException
     */
    private function setCustomerCustomDateAttribute(CustomerInterface $customer): void
    {
        if ($customer->getCustomAttributes() !== null) {
            $customAttributes = $customer->getCustomAttributes();
            foreach ($customAttributes as $customAttribute) {
                $attributeCode = $customAttribute->getAttributeCode();
                $attribute = $this->eavConfig->getAttribute(Customer::ENTITY, $attributeCode);
                $attributeType = $this->getAttributeType($attribute);
                if ($attributeType === 'datetime') {
                    $date = new \DateTime($customAttribute->getValue());
                    $customAttribute->setValue($date->format('m/d/yy'));
                }
                $customAttribute->setData('attributeType', $attributeType);
            }
        }
    }
}
