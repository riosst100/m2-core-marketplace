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
 * @package    Lof_Quickrfq
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lof\Quickrfq\Model\Config\Source;

use Lof\Quickrfq\Model\Quickrfq;
/**
 * Class Status
 * @package Lof\Quickrfq\Model\Config\Source
 */
class Status implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @return array|mixed
     */
    public function toOptionArray()
    {
        return [
            ['value' =>  Quickrfq::STATUS_NEW , 'label' => __('New')],
            ['value' =>  Quickrfq::STATUS_PROCESSING , 'label' => __('Processing')],
            ['value' =>  Quickrfq::STATUS_APPROVE , 'label' => __('Approved')],
            ['value' =>  Quickrfq::STATUS_EXPIRY , 'label' => __('Expired')],
            ['value' =>  Quickrfq::STATUS_DONE,'label' => __('Done')],
            ['value' =>  Quickrfq::STATUS_RE_NEW , 'label' => __('Re-New')]
        ];
    }
}
