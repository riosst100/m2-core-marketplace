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

namespace Lof\MarketPermissions\Block\Marketplace;

use Lof\MarketPermissions\Api\StatusServiceInterface;
use Lof\MarketPermissions\Model\SellerContext;
use Lof\MarketPermissions\Model\SellerRepository;
use Magento\Customer\Api\CustomerNameGenerationInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Template;

class Identifier extends \Magento\Framework\View\Element\Template
{

    /**
     * @var StatusServiceInterface
     */
    private $moduleConfig;

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
     * @var SellerRepository
     */
    private $sellerRepository;

    /**
     * Identifier constructor.
     * @param Template\Context $context
     * @param SellerContext $sellerContext
     * @param StatusServiceInterface $moduleConfig
     * @param CustomerRepositoryInterface $customerRepository
     * @param CustomerNameGenerationInterface $customerViewHelper
     * @param SellerRepository $sellerRepository
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        SellerContext $sellerContext,
        StatusServiceInterface $moduleConfig,
        CustomerRepositoryInterface $customerRepository,
        CustomerNameGenerationInterface $customerViewHelper,
        SellerRepository $sellerRepository,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->moduleConfig = $moduleConfig;
        $this->sellerContext = $sellerContext;
        $this->customerRepository = $customerRepository;
        $this->customerViewHelper = $customerViewHelper;
        $this->sellerRepository = $sellerRepository;
    }

    /**
     * @return string
     */
    protected function _toHtml()
    {
        if (!$this->moduleConfig->isActive()) {
            return '';
        }

        if ($this->sellerContext->getSellerAdminPermission()->isCurrentUserSellerAdmin()) {
            return '';
        }

        return parent::_toHtml();
    }

    /**
     * @return \Magento\Customer\Api\Data\CustomerInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCustomer()
    {
        return $this->customerRepository->getById($this->sellerContext->getCustomerId());
    }

    /**
     * @return mixed|string|null
     * @throws NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getSellerName()
    {
        $sellerId = $this->getCustomer()->getExtensionAttributes()->getSellerAttributes()->getSellerId();
        try {
            $seller = $this->sellerRepository->get($sellerId);
        } catch (NoSuchEntityException $e) {
            return null;
        }
        return $seller->getName();
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getCustomerName(): string
    {
        return $this->customerViewHelper->getCustomerName($this->getCustomer());
    }
}
