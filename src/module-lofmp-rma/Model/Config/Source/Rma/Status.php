<?php
/**
 * LandOfCoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * https://landofcoder.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   LandOfCoder
 * @package    Lofmp_Rma
 * @copyright  Copyright (c) 2021 Landofcoder (https://landofcoder.com/)
 * @license    https://landofcoder.com/LICENSE-1.0.html
 */


namespace Lofmp\Rma\Model\Config\Source\Rma;

class Status implements \Magento\Framework\Option\ArrayInterface
{
    public function __construct(
        \Lofmp\Rma\Helper\Data $rmaHelper
    ) {
        $this->rmaHelper = $rmaHelper;
    }

    /**
     * @var array
     */
    protected $options;

    /**
     * @return array
     */
    public function toOptionArray()
    {
        if (!$this->options) {
            $this->options = [];
            $statuses = $this->rmaHelper->getStatusList();
            foreach ($statuses as $status) {
                $this->options[] = ['value' => $status->getId(), 'label' => $status->getName()];
            }
        }

        return $this->options;
    }
}
