<?php
/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * http://landofcoder.com/license
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Landofcoder
 * @package    Lofmp_PriceComparison
 * @copyright  Copyright (c) 2017 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */

namespace Lofmp\PriceComparison\Plugin\Sales\Block\Reorder;

use Magento\Sales\Block\Reorder\Sidebar as SidebarBlock;

class Sidebar
{
    /**
     * @var \Lofmp\PriceComparison\Helper\Data
     */
    protected $helperData;

    /**
     * @param \Lofmp\PriceComparison\Helper\Data $helper
     */
    public function __construct(\Lofmp\PriceComparison\Helper\Data $helper)
    {
        $this->helperData = $helper;
    }

    public function afterGetFormActionUrl(SidebarBlock $subject, $result)
    {
        if ($this->helperData->isEnabled()) {
            return $subject->getUrl('lofmppricecomparison/cart/addgroup', ['_secure' => true]);
        }
        return $result;
    }
}
