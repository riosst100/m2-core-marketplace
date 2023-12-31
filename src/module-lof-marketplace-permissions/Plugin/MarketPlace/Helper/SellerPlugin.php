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

namespace Lof\MarketPermissions\Plugin\MarketPlace\Helper;

use Lof\MarketPermissions\Model\SellerContext;
use Magento\Customer\Api\CustomerNameGenerationInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;

class SellerPlugin
{

    /**
     * @var SellerContext
     */
    private $sellerContext;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var CustomerNameGenerationInterface
     */
    private $customerViewHelper;

    /**
     * SellerPlugin constructor.
     * @param SellerContext $sellerContext
     * @param CustomerRepositoryInterface $customerRepository
     * @param CustomerNameGenerationInterface $customerViewHelper
     */
    public function __construct(
        SellerContext $sellerContext,
        CustomerRepositoryInterface $customerRepository,
        CustomerNameGenerationInterface $customerViewHelper
    ) {
        $this->customerViewHelper = $customerViewHelper;
        $this->customerRepository = $customerRepository;
        $this->sellerContext = $sellerContext;
    }

    /**
     * @param \Lof\MarketPlace\Helper\Seller $subject
     * @param $result
     * @return mixed
     */
    public function afterGetSellerByCustomer(\Lof\MarketPlace\Helper\Seller $subject, $result)
    {
        $sellerData = $result;
        $customerId = $this->sellerContext->getCustomerSession()->getCustomerId();
        if (!$this->sellerContext->getSellerAdminPermission()->isCurrentUserSellerAdmin()) {
            $customer = $this->customerRepository->getById($customerId);
            $sellerUserName = $this->customerViewHelper->getCustomerName($customer);
            $sellerData['name'] = $sellerUserName;
            return $sellerData;
        }
        return $result;
    }
}
