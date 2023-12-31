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

namespace Lof\MarketPermissions\ViewModel;

use Magento\Framework\DataObject;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Customer\Api\Data\CustomerInterfaceFactory;
use Magento\Framework\View\LayoutInterface;
use Magento\Customer\Block\Widget\Name;
use Magento\Framework\View\Element\BlockInterface;

/**
 * Seller customer view model
 */
class Customer extends DataObject implements ArgumentInterface
{
    /**
     * @var CustomerInterfaceFactory
     */
    private $customerInterfaceFactory;

    /**
     * @var LayoutInterface
     */
    private $layoutInterface;

    /**
     * @param CustomerInterfaceFactory $customerInterfaceFactory
     * @param LayoutInterface $layoutInterface
     */
    public function __construct(
        CustomerInterfaceFactory $customerInterfaceFactory,
        LayoutInterface $layoutInterface
    ) {
        parent::__construct();
        $this->customerInterfaceFactory = $customerInterfaceFactory;
        $this->layoutInterface = $layoutInterface;
    }

    /**
     * Get customer name html
     *
     * @return string
     */
    public function getCustomerNameHtml(): string
    {
        $customerData = $this->customerInterfaceFactory->create();
        /** @var BlockInterface $blockCustomerName */
        $blockCustomerName = $this->layoutInterface->createBlock(Name::class)
            ->setObject($customerData);

        return $blockCustomerName->toHtml();
    }
}
