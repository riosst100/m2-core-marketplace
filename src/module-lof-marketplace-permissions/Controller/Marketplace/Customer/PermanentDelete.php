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

use Magento\Framework\App\Action\HttpPostActionInterface as HttpPostActionInterface;

/**
 * Delete seller user on storefront.
 */
class PermanentDelete extends \Lof\MarketPermissions\Controller\Marketplace\AbstractAction implements HttpPostActionInterface
{
    /**
     * Authorization level of a seller session.
     */
    const SELLER_RESOURCE = 'Lof_MarketPermissions::users_edit';

    /**
     * @var \Lof\MarketPermissions\Model\Seller\Structure
     */
    private $structureManager;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * PermanentDelete constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Lof\MarketPermissions\Model\SellerContext $sellerContext
     * @param \Magento\Framework\Url $frontendUrl
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Lof\MarketPermissions\Model\Seller\Structure $structureManager
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Lof\MarketPermissions\Model\SellerContext $sellerContext,
        \Magento\Framework\Url $frontendUrl,
        \Psr\Log\LoggerInterface $logger,
        \Lof\MarketPermissions\Model\Seller\Structure $structureManager,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
    ) {
        parent::__construct($context, $sellerContext, $frontendUrl, $logger);
        $this->structureManager = $structureManager;
        $this->customerRepository = $customerRepository;
    }

    /**
     * Delete customer action.
     *
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        $customerId = $this->getRequest()->getParam('customer_id');

        if ($customerId == $this->sellerContext->getCustomerId()) {
            return $this->jsonError(__('You cannot delete yourself.'));
        }

        $allowedIds = $this->structureManager->getAllowedIds($this->sellerContext->getCustomerId());

        if (!in_array($customerId, $allowedIds['users'])) {
            return $this->jsonError(__('You are not allowed to do this.'));
        }

        if ($this->structureManager->getStructureByCustomerId($customerId) === null) {
            return $this->jsonError(__('Cannot delete this user.'));
        }

        try {
            $this->customerRepository->deleteById($customerId);
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            return $this->handleJsonError($e->getMessage());
        } catch (\Exception $e) {
            $this->logger->critical($e);

            return $this->handleJsonError();
        }

        return $this->handleJsonSuccess(__('The customer was successfully deleted.'));
    }
}
