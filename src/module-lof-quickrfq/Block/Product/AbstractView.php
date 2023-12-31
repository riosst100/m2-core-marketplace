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

namespace Lof\Quickrfq\Block\Product;

use Lof\Quickrfq\Helper\Data;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template\Context;

/**
 * Class AbstractView
 *
 * @package Lof\Quickrfq\Block\Product
 */
abstract class AbstractView extends \Magento\Framework\View\Element\Template
{

    /**
     * @var Registry
     */
    protected $_coreRegistry;

    /**
     * AbstractView constructor
     * @param Context $context
     * @param Registry $registry
     * @param Data $helperConfig
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        Data $helperConfig,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_helperConfig = $helperConfig;
        $this->_coreRegistry  = $registry;
    }
}
