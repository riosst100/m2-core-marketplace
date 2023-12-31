<?php
/**
 * Landofcoder
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * http://Landofcoder.com/license
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category   Landofcoder
 * @package    Lofmp_StoreLocator
 * @copyright  Copyright (c) 2016 Landofcoder (http://www.Landofcoder.com/)
 * @license    http://www.Landofcoder.com/LICENSE-1.0.html
 */
namespace Lofmp\StoreLocator\Block\StoreLocator;

use Magento\Framework\View\Element\Template\Context;
use Lofmp\StoreLocator\Helper\Data;

class Grid extends \Magento\Framework\View\Element\Template
{
    public function __construct(
        Context       $context,
        array         $data = []  
        ) {
       
        parent::__construct($context, $data);
     
    }
}