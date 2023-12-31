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

namespace Lof\MarketPermissions\Plugin\Customer\Block\Widget;

class Name
{
    /**
     * @param \Magento\Customer\Block\Widget\Name $subject
     */
    public function after_construct(\Magento\Customer\Block\Widget\Name $subject)
    {
        if ($subject->getArea() === \Lof\MarketPlace\App\Area\FrontNameResolver::AREA_CODE) {
            $subject->setTemplate('Lof_MarketPermissions::widget/name.phtml');
        }
    }
}
