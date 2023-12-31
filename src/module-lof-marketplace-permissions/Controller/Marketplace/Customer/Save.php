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
use Magento\Framework\Exception\State\InputMismatchException;

/**
 * Update customer for seller structure.
 */
class Save extends \Lof\MarketPermissions\Controller\Marketplace\AbstractAction implements HttpPostActionInterface
{
    /**
     * Authorization level of a seller session.
     */
    const SELLER_RESOURCE = 'Lof_MarketPermissions::users_edit';

    /**
     * @var \Lof\MarketPermissions\Model\Action\SaveCustomer
     */
    private $customerAction;

    /**
     * @var \Lof\MarketPermissions\Model\Seller\Structure
     */
    private $structureManager;

    /**
     * Save constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Lof\MarketPermissions\Model\SellerContext $sellerContext
     * @param \Magento\Framework\Url $frontendUrl
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Lof\MarketPermissions\Model\Action\SaveCustomer $customerAction
     * @param \Lof\MarketPermissions\Model\Seller\Structure $structureManager
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Lof\MarketPermissions\Model\SellerContext $sellerContext,
        \Magento\Framework\Url $frontendUrl,
        \Psr\Log\LoggerInterface $logger,
        \Lof\MarketPermissions\Model\Action\SaveCustomer $customerAction,
        \Lof\MarketPermissions\Model\Seller\Structure $structureManager
    ) {
        parent::__construct($context, $sellerContext, $frontendUrl, $logger);
        $this->structureManager = $structureManager;
        $this->customerAction = $customerAction;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $request = $this->getRequest();

        $customerId = $request->getParam('customer_id');
        $allowedIds = $this->structureManager->getAllowedIds($this->sellerContext->getCustomerId());

        if (!in_array($customerId, $allowedIds['users'])) {
            throw new InputMismatchException(__('You are not allowed to do this.'));
        }

        try {
            $customer = $this->customerAction->update($request);
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            return $this->handleJsonError($e->getMessage());
        } catch (\Exception $e) {
            $this->logger->critical($e);

            return $this->handleJsonError();
        }

        return $this->handleJsonSuccess(__('The customer was successfully updated.'), $customer->__toArray());
    }
}
