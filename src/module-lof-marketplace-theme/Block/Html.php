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
 * @package    Lof_MarketTheme
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lof\MarketTheme\Block;

class Html extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Lof\MarketPlace\Helper\Data
     */
    protected $_helperData;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context  
     * @param \Lof\MarketPlace\Helper\Data $vesTheme 
     * @param array $data     
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Lof\MarketPlace\Helper\Data $helperData,
        array $data = []
        ) {
        parent::__construct($context, $data);
        $this->_helperData = $helperData;
    }
    /**
     * Prepare global layout
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        $rtl_layout = $this->_helperData->getConfig('design/rtl_layout');
        if($rtl_layout){
            $this->pageConfig->addBodyClass("rtl");
        }
        return parent::_prepareLayout();
    }
}