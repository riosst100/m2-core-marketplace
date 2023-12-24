<?php
/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * https://landofcoder.com/license
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Landofcoder
 * @package    Lof_SmtpEmail
 *
 * @copyright  Copyright (c) 2016 Landofcoder (https://landofcoder.com/)
 * @license    https://landofcoder.com/LICENSE-1.0.html
 */
namespace Lof\SmtpEmail\Block\Adminhtml\Spam\Edit;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    protected function _construct()
    {
        parent::_construct();
        $this->setId('spam_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Information'));

        $this->addTab(
            'spam_information',
            [
            'label' => __('Spam Information'),
            'content' => $this->getLayout()->createBlock('Lof\SmtpEmail\Block\Adminhtml\Spam\Edit\Tab\Main')->toHtml()
            ]
            );
    }

}
