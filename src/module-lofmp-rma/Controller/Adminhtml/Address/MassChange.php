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

class MassChange extends \Lofmp\Rma\Controller\Adminhtml\Address
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
        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        $ids = $this->getRequest()->getParam('address_id');
        if (!is_array($ids)) {
            $this->messageManager->addErrorMessage(__('Please select Return Address(es)'));
        } else {
            try {
                $isActive = $this->getRequest()->getParam('is_active');
                foreach ($ids as $id) {
                    /** @var \Lofmp\Rma\Model\Address $address */
                    $address = $this->addressFactory->create()->load($id);
                    $address->setIsActive($isActive);
                    $address->save();
                }
                $this->messageManager->addSuccessMessage(
                    __(
                        'Total of %1 record(s) were successfully updated',
                        count($ids)
                    )
                );
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }
        }
        return $resultRedirect->setPath('*/*/index');
    }
}
