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



namespace Lofmp\Rma\Controller\Adminhtml\Resolution;

use Magento\Framework\Controller\ResultFactory;
use \Lofmp\Rma\Api\Data\ResolutionInterface;

class MassDelete extends \Lofmp\Rma\Controller\Adminhtml\Resolution
{
    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        $ids = $this->getRequest()->getParam('resolution_id');
        if (!is_array($ids)) {
            $this->messageManager->addErrorMessage(__('Please select Resolution(s)'));
            return $resultRedirect->setPath('*/*/index');
        }
        try {
            $resolutionAmount = count($ids);
            foreach ($ids as $id) {
                if (in_array($id, ResolutionInterface::RESERVED_IDS)) {
                    $this->messageManager->addWarningMessage(
                        __(
                            'This resolution "%1" is reserved. You can only deactivate it',
                            $this->resolutionFactory->create()->load($id)->getName()
                        )
                    );
                    $resolutionAmount--;
                    continue;
                }
                $this->resolutionRepository->deleteById($id);
            }
            if ($resolutionAmount) {
                $this->messageManager->addSuccessMessage(
                    __(
                        'Total of %1 record(s) were successfully deleted',
                        $resolutionAmount
                    )
                );
            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        return $resultRedirect->setPath('*/*/index');
    }
}
