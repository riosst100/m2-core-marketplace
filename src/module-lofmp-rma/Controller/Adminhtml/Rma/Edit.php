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



namespace Lofmp\Rma\Controller\Adminhtml\Rma;

use Magento\Framework\Controller\ResultFactory;
use Lofmp\Rma\Controller\Adminhtml\Rma;

class Edit extends Rma
{
    public function __construct(
        \Lofmp\Rma\Api\Repository\RmaRepositoryInterface $rmaRepository,
        \Magento\Framework\Registry $registry,
        \Magento\Backend\App\Action\Context $context
    ) {
        $this->registry      = $registry;
        $this->rmaRepository = $rmaRepository;

        parent::__construct($context);
    }


    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $rma = $this->rmaRepository->getById($this->getRequest()->getParam('id'));
        $rma->setIsAdminRead(true);
        $this->registry->register('current_rma', $rma);
        $this->initPage($resultPage)->getConfig()
            ->getTitle()->prepend(__(__('RMA #%1', $rma->getIncrementId())));
        $this->_addContent($resultPage->getLayout()->createBlock('\Lofmp\Rma\Block\Adminhtml\Rma\Edit'))->_addLeft($resultPage->getLayout()->createBlock('\Lofmp\Rma\Block\Adminhtml\Rma\Edit\Tabs'));
        return $resultPage;
    }
}
