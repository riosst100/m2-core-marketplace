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
namespace Lof\Quickrfq\Model\Quickrfq\Source;

use Lof\Quickrfq\Model\Quickrfq;
use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class IsActive
 */
class Status implements OptionSourceInterface
{

    /**
     * @var Quickrfq
     */
    protected $_rfq_model;

    /**
     * Status constructor.
     * @param Quickrfq $_rfq_model
     */
    public function __construct(Quickrfq $_rfq_model)
    {

        $this->_rfq_model = $_rfq_model;
    }

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {

        $availableOptions = $this->_rfq_model->getAvailableStatuses();

        $options = [];
        foreach ($availableOptions as $key => $value) {
            $options[] = [
                'label' => $value,
                'value' => $key,
            ];
        }
        return $options;
    }
}
