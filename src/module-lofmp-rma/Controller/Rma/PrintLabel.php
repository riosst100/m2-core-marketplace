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



namespace Lofmp\Rma\Controller\Rma;

use Magento\Framework\Controller\ResultFactory;

class PrintLabel extends \Lofmp\Rma\Controller\Rma
{
    public function __construct(
        \Lofmp\Rma\Api\Repository\RmaRepositoryInterface $rmaRepository,
        \Lofmp\Rma\Helper\Data                            $datahelper,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Action\Context $context
    ) {
        $this->rmaRepository        = $rmaRepository;
        $this->registry             = $registry;
        $this->datahelper           = $datahelper;
        parent::__construct($customerSession, $context);
    }

    

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        $id = $this->getRequest()->getParam('id');
        $rma = $this->rmaRepository->getById($id);
        if (!$rma) {
            return $resultRedirect->setPath('/');
        }
        $attachments = $this->datahelper->getAttachments('return_label', $rma->getId());
        if ($label = array_shift($attachments)) {
            return $resultRedirect->setPath('*/attachment/download', ['uid' => $label->getUid()]);
        } else {
            $this->_forward('no_rote');
        }
    }
}
