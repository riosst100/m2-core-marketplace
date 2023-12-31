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


namespace Lofmp\Rma\Controller\Attachment;

use Magento\Framework\Controller\ResultFactory;

class Download extends \Magento\Framework\App\Action\Action
{
    public function __construct(
        \Lofmp\Rma\Model\AttachmentFactory $attachmentFactory,
        \Magento\Framework\App\Action\Context $context
    ) {
        $this->attachmentFactory = $attachmentFactory;
        $this->context = $context;
        $this->resultFactory = $context->getResultFactory();
        parent::__construct($context);
    }


    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Raw $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_RAW);

        $uid = $this->getRequest()->getParam('uid');
        try {
            $attachment = $this->attachmentFactory->create()->getCollection()
                ->addFieldToFilter('uid', $uid)
                ->getFirstItem();

            if (!$attachment->getId()) {
                throw NoSuchEntityException::singleField('uid', $uid);
            }
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            return $resultPage->setContents('wrong URL');
        }

        // give our picture the proper headers...otherwise our page will be confused
        $resultPage->setHeader("Content-Disposition", "attachment; filename={$attachment->getName()}");
        $resultPage->setHeader("Content-length", $attachment->getSize());
        $resultPage->setHeader("Content-type", $attachment->getType());
        $resultPage->setContents($attachment->getBody());
        return $resultPage;
    }
}
