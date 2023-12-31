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
 * @package    Lof_Auction
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */
namespace Lof\Auction\Block\Adminhtml\System\Config\Form\Field;

use Lof\Auction\Block\Adminhtml\System\Config\Form\Field\TaxColumn;
use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;

/**
 * Class Ranges
 */
class IncrementalPriceRange extends AbstractFieldArray
{

    /**
     * Prepare rendering the new field by adding all the needed columns
     */
    protected function _prepareToRender()
    {
        $this->addColumn('from', ['label' => __('From'), 'class' => 'required-entry']);
        $this->addColumn('to', ['label' => __('To'), 'class' => 'required-entry']);
        $this->addColumn('increment', ['label' => __('Increment'), 'class' => 'required-entry']);
        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add');
    }
}
