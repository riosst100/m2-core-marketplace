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
 * @package    Lofmp_Auction
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lofmp\Auction\Block\Seller\Widget\Form;

/**
 * Adminhtml footer block
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Container extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * @var string
     */
    protected $_template = 'Lofmp_Auction::widget/form/container.phtml';

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->updateButton('save', 'class', 'btn-success');
        $this->updateButton('reset', 'class', 'btn-warning');
        $this->updateButton('delete', 'class', 'btn-danger');
        $this->updateButton('back', 'class', 'btn-github');
    }
}
