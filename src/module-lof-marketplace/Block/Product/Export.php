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

namespace Lof\MarketPlace\Block\Product;

class Export extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\ImportExport\Model\Source\Export\FormatFactory
     */
    protected $_formatFactory;

    /**
     * Export constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\ImportExport\Model\Source\Export\FormatFactory $formatFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\ImportExport\Model\Source\Export\FormatFactory $formatFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->_formatFactory = $formatFactory;
    }

    /**
     * @return array
     */
    public function getFomat()
    {
        return $this->_formatFactory->create()->toOptionArray();
    }
}
