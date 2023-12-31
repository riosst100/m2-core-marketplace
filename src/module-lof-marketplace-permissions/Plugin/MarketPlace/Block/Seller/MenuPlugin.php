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
 * @package    Lofmp_Permissions
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lof\MarketPermissions\Plugin\MarketPlace\Block\Seller;

use Lof\MarketPermissions\Model\SellerContext;
use Magento\Framework\Locale\ResolverInterface;

class MenuPlugin
{

    /**
     * @var ResolverInterface
     */
    protected $_localeResolver;

    /**
     * @var SellerContext
     */
    protected $sellerContext;

    /**
     * MenuPlugin constructor.
     * @param ResolverInterface $localeResolver
     * @param SellerContext $sellerContext
     */
    public function __construct(
        ResolverInterface $localeResolver,
        SellerContext $sellerContext
    ) {
        $this->sellerContext = $sellerContext;
        $this->_localeResolver = $localeResolver;
    }

    /**
     * @param \Lof\MarketPlace\Block\Seller\Menu $subject
     * @param \Closure $proceed
     * @return array
     */
    public function aroundGetCacheKeyInfo(
        \Lof\MarketPlace\Block\Seller\Menu $subject,
        \Closure $proceed
    ) {
        if (!$this->sellerContext->isModuleActive()) {
            return $proceed();
        }

        $cacheKeyInfo = [
            'seller_top_nav',
            $subject->getActive(),
            $this->sellerContext->getCustomerId(),
            $this->_localeResolver->getLocale(),
        ];

        $newCacheKeyInfo = $subject->getAdditionalCacheKeyInfo();
        if (is_array($newCacheKeyInfo) && !empty($newCacheKeyInfo)) {
            $cacheKeyInfo = array_merge($cacheKeyInfo, $newCacheKeyInfo);
        }
        return $cacheKeyInfo;
    }
}
