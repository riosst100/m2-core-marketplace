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


namespace Lofmp\Rma\Controller\Adminhtml\Condition;

use Magento\Framework\Controller\ResultFactory;

class Add extends \Lofmp\Rma\Controller\Adminhtml\Condition
{
    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $this->_initCondition();
        $this->initPage($resultPage);
        $resultPage->getConfig()->getTitle()->prepend(__('New Condition'));
        $this->_addBreadcrumb(
            __('Condition  Manager'),
            __('Condition Manager'),
            $this->getUrl('*/*/')
        );
        $this->_addBreadcrumb(__('Add Condition '), __('Add Condition'));
        $this->_addContent($resultPage->getLayout()->createBlock('\Lofmp\Rma\Block\Adminhtml\Condition\Edit'));

        return $resultPage;
    }
}
