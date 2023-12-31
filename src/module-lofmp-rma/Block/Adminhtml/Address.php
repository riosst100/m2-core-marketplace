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
 * @copyright  Copyright (c) 2020 Landofcoder (https://landofcoder.com/)
 * @license    https://landofcoder.com/LICENSE-1.0.html
 */


namespace Lofmp\Rma\Block\Adminhtml;

use Magento\Backend\Block\Widget\Grid\Container;

class Address extends Container
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_controller = 'adminhtml_address';
        $this->_blockGroup = 'Lofmp_Rma';
        $this->_headerText = __('Return Addresses');
        $this->_addButtonLabel = __('Add New Return Address');
    }

    /**
     * {@inheritdoc}
     */
    public function getCreateUrl()
    {
        return $this->getUrl('*/*/add');
    }
}
