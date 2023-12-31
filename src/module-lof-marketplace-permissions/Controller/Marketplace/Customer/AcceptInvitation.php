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

namespace Lof\MarketPermissions\Controller\Marketplace\Customer;

use Lof\MarketPermissions\Api\SellerUserManagerInterface;
use Lof\MarketPermissions\Api\Data\SellerCustomerInterface;
use Lof\MarketPermissions\Api\Data\SellerCustomerInterfaceFactory;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Framework\Api\DataObjectHelper;

/**
 * Accept invitation to a seller.
 */
class AcceptInvitation extends Action implements HttpGetActionInterface
{
    /**
     * @var DataObjectHelper
     */
    private $objectHelper;

    /**
     * @var SellerUserManagerInterface
     */
    private $userManager;

    /**
     * @var SellerCustomerInterfaceFactory
     */
    private $customerFactory;

    /**
     * @param Context $context
     * @param DataObjectHelper $dataObjectHelper
     * @param SellerUserManagerInterface $userManager
     * @param SellerCustomerInterfaceFactory $userFactory
     */
    public function __construct(
        Context $context,
        DataObjectHelper $dataObjectHelper,
        SellerUserManagerInterface $userManager,
        SellerCustomerInterfaceFactory $userFactory
    ) {
        parent::__construct($context);
        $this->objectHelper = $dataObjectHelper;
        $this->userManager = $userManager;
        $this->customerFactory = $userFactory;
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        /** @var HttpRequest $request */
        $request = $this->getRequest();
        $code = $request->get('code');
        $roleId = $request->get('role_id');
        if (empty($roleId)) {
            $roleId = null;
        }
        /** @var SellerCustomerInterface $customer */
        $customer = $this->customerFactory->create();
        $this->objectHelper->populateWithArray(
            $customer,
            $request->get('customer'),
            SellerCustomerInterface::class
        );

        try {
            $this->userManager->acceptInvitation($code, $customer, $roleId);
            $this->messageManager->addSuccessMessage(__('You have accepted the invitation to the seller'));
        } catch (\Throwable $exception) {
            $this->messageManager->addErrorMessage(
                __('Error occurred when trying to accept the invitation. Please try again later.')
            );
        }

        $result = $this->resultRedirectFactory->create();
        $result->setPath('/');
        return $result;
    }
}
