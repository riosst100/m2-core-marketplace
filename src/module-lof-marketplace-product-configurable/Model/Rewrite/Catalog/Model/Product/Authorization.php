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
 * @package    Lof_MarketProductConfigurable
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

declare(strict_types=1);

namespace Lof\MarketProductConfigurable\Model\Rewrite\Catalog\Model\Product;

use Magento\Catalog\Model\Product\Authorization as CatalogAuthorization;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\ProductFactory;
use Magento\Framework\AuthorizationInterface;
/**
 * Additional authorization for product operations.
 */
class Authorization extends CatalogAuthorization
{
    protected $sellerFactory;
    protected $customerSession;

    public function __construct(
        AuthorizationInterface $authorization,
        ProductFactory $factory,
        \Lof\MarketPlace\Model\SellerFactory $sellerFactory,
        \Magento\Customer\Model\Session $customerSession
        )
    {
        $this->sellerFactory = $sellerFactory;
        $this->customerSession = $customerSession;
        parent::__construct($authorization, $factory);
    }

    /**
     * {@inheritdoc}
     */
    public function authorizeSavingOf(ProductInterface $product): void
    {
        $sellerId = $this->getSellerId();//get current logged in seller account on frontend.
        if(!$sellerId){
            parent::authorizeSavingOf($product);
        }
    }

    public function getCustomerId()
    {
        $customer = $this->customerSession->getCustomer();
        return $customer->getId();
    }

    public function getSellerId()
    {
        $customerId = $this->getCustomerId();
        if($customerId){
            $seller = $this->sellerFactory->create()->load($customerId, 'customer_id');
            return $seller->getData('seller_id');
        }else {
            return 0;
        }
    }
}
