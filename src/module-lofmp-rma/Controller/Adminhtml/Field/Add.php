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



namespace Lofmp\Rma\Controller\Adminhtml\Field;

use Magento\Framework\Controller\ResultFactory;

class Add extends \Lofmp\Rma\Controller\Adminhtml\Field
{
    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);

        $this->_initField();

        $this->initPage($resultPage);
        $resultPage->getConfig()->getTitle()->prepend(__('New Field'));
        $this->_addBreadcrumb(
            __('Field  Manager'),
            __('Field Manager'),
            $this->getUrl('*/*/')
        );
        $this->_addBreadcrumb(__('Add Field '), __('Add Field'));

        $resultPage->getLayout()
            ->getBlock('head')
            ;
        $this->_addContent($resultPage->getLayout()->createBlock('\Lofmp\Rma\Block\Adminhtml\Field\Edit'));

        return $resultPage;
    }
}
