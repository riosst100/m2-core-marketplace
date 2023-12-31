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

use Lof\MarketPermissions\Model\Action\InviteConfirmationNeededException;
use Lof\MarketPermissions\Model\Seller\Structure;
use Magento\Framework\Exception\State\InputMismatchException;
use Lof\MarketPermissions\Model\Action\SaveCustomer as CustomerAction;

/**
 * Controller for creating a customer.
 */
class Create extends \Lof\MarketPermissions\Controller\Marketplace\AbstractAction
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
     * @var \Lof\MarketPermissions\Model\Action\SaveCustomer
     */
    private $customerAction;


    /**
     * Create constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Lof\MarketPermissions\Model\SellerContext $sellerContext
     * @param \Magento\Framework\Url $frontendUrl
     * @param \Psr\Log\LoggerInterface $logger
     * @param Structure $structureManager
     * @param CustomerAction $customerAction
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Lof\MarketPermissions\Model\SellerContext $sellerContext,
        \Magento\Framework\Url $frontendUrl,
        \Psr\Log\LoggerInterface $logger,
        Structure $structureManager,
        CustomerAction $customerAction
    ) {
        parent::__construct($context, $sellerContext, $frontendUrl, $logger);
        $this->structureManager = $structureManager;
        $this->customerAction = $customerAction;
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        $request = $this->getRequest();

        $targetId = $request->getParam('target_id');
        $allowedIds = $this->structureManager->getAllowedIds($this->sellerContext->getCustomerId());

        if ($targetId && !in_array($targetId, $allowedIds['structures'])) {
            return $this->handleJsonError(__('You are not allowed to do this.'));
        } elseif (!$targetId) {
            $structure = $this->structureManager
                ->getStructureByCustomerId($this->sellerContext->getCustomerId());
            if ($structure === null) {
                return $this->handleJsonError(__('Cannot create the customer.'));
            }
        }

        try {
            $customer = $this->customerAction->create($this->getRequest());
        } catch (InputMismatchException $e) {
            return $this->jsonError(
                __(
                    'A user with this email address already exists in the system. '
                    . 'Enter a different email address to create this user.'
                ),
                [
                    'field' => 'email'
                ]
            );
        } catch (InviteConfirmationNeededException $exception) {
            return $this->handleJsonSuccess($exception->getMessage(), $exception->getForCustomer()->__toArray());
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            return $this->handleJsonError($e->getMessage());
        } catch (\Throwable $e) {
            $this->logger->critical($e);

            return $this->handleJsonError();
        }

        return $this->handleJsonSuccess(__('The customer was successfully created.'), $customer->__toArray());
    }
}
