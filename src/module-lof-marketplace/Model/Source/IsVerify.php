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
 * @package    Lof_MarketPlace
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lof\MarketPlace\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;

class IsVerify implements OptionSourceInterface
{
    /**
     * @var \Lof\MarketPlace\Model\Seller
     */
    protected $sellerModel;

    /**
     * Constructor
     *
     * @param \Lof\MarketPlace\Model\Seller $sellerModel
     */
    public function __construct(\Lof\MarketPlace\Model\Seller $sellerModel)
    {
        $this->sellerModel = $sellerModel;
    }

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options[] = ['label' => '', 'value' => ''];
        $availableOptions = $this->sellerModel->getAvailableVerifyStatuses();

        foreach ($availableOptions as $key => $value) {
            $options[] = [
                'label' => $value,
                'value' => $key,
            ];
        }

        return $options;
    }
}
