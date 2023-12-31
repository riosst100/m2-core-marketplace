<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Lof\MarketPlace\Block\Widget;

use Magento\Customer\Api\CustomerMetadataInterface;

/**
 * Customer Value Added Tax Widget
 *
 * @SuppressWarnings(PHPMD.DepthOfInheritance)
 */
class Taxvat extends \Magento\Customer\Block\Widget\Taxvat
{
    /**
     * Sets the template
     *
     * @return void
     */
    public function _construct()
    {
        parent::_construct();
        $this->setTemplate('Lof_MarketPlace::widget/taxvat.phtml');
    }
}
