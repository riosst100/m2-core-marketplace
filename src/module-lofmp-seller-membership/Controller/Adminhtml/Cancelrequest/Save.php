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
 * @package    Lof_CustomerMembership
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lofmp\SellerMembership\Controller\Adminhtml\Cancelrequest;

use Lofmp\SellerMembership\Model\CancelrequestFactory;

class Save extends \Magento\Backend\App\Action
{
    /**
     * @var CancelrequestFactory
     */
    protected $_cancelrequestFactory;

    /**
     * @param \Magento\Backend\App\Action\Context
     * @param \Lofmp\SellerMembership\Model\CancelrequestFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        CancelrequestFactory $cancelrequestFactory
    ) {
        $this->_cancelrequestFactory = $cancelrequestFactory;
        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Lof_CustomerMembership::cancelrequest_save');
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();

        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            $cancelrequest_model = $this->_cancelrequestFactory->create();
            $entity_id = $this->getRequest()->getParam('entity_id');
            if ($entity_id) {
                $cancelrequest_model->load($entity_id);
            } else {
                unset($data['entity_id']);
            }

            unset($data['seller_comment']);
            unset($data['creation_time']);
            unset($data['membership_id']);

            $cancelrequest_model->setData($data);

            try {
                $cancelrequest_model->save();
                $this->messageManager->addSuccess(__('You updated Cancel Request : ') . $cancelrequest_model->getId());
                $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['entity_id' => $cancelrequest_model->getId(), '_current' => true]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the Cancel Request.'));
                $this->messageManager->addError($e->getMessage());
            }
            $this->_getSession()->setFormData($data);
            return $resultRedirect->setPath('*/*/edit', ['entity_id' => $this->getRequest()->getParam('entity_id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }
}
