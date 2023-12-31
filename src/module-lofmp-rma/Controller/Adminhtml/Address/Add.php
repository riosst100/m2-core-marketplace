<?php
/**
 * LandOfCoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * http://www.landofcoder.com/license-agreement.html
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

namespace Lofmp\Rma\Controller\Adminhtml\Address;

use Magento\Framework\Controller\ResultFactory;

class Add extends \Lofmp\Rma\Controller\Adminhtml\Address
{
    /**
     * Authorization level of a basic admin session
     */
    const ADMIN_RESOURCE = 'Lofmp_Rma::rma_address_save';

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $this->_initAddress();

        $this->initPage($resultPage);
        $resultPage->getConfig()->getTitle()->prepend(__('New Return Address'));
        $this->_addBreadcrumb(
            __('Return Address Manager'),
            __('Return Address Manager'),
            $this->getUrl('*/*/')
        );
        $this->_addBreadcrumb(__('Add Return Address '), __('Add Return Address'));

        $resultPage->getLayout()
            ->getBlock('head')
            ;
        $this->_addContent($resultPage->getLayout()->createBlock('\Lofmp\Rma\Block\Adminhtml\Address\Edit'));

        return $resultPage;
    }
}
