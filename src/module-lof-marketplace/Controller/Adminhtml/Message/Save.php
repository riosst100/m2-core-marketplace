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
 * @package    Lof_MarketPlace
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lof\MarketPlace\Controller\Adminhtml\Message;

use Magento\Backend\Model\Auth\Session;

class Save extends \Magento\Backend\App\Action
{
    /**
     * @var \Lof\MarketPlace\Helper\Data
     */
    protected $helper;

    /**
     * @var Session
     */
    protected $authSession;

    /**
     * Main constructor.
     * @param \Lof\MarketPlace\Helper\Data $helper
     * @param \Magento\Backend\Block\Template\Context $context
     * @param Session $authSession
     */

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Lof\MarketPlace\Helper\Data $helper,
        Session $authSession
    ) {
        $this->helper = $helper;
        $this->authSession = $authSession;
        parent::__construct($context);
    }
    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        $userId = $this->authSession->getUser()->getData();
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            $model = $this->_objectManager->create(\Lof\MarketPlace\Model\MessageAdmin::class);
            $id = $this->getRequest()->getParam('message_id');
            if ($id) {
                $model->load($id);
                $_data = $model->getData();
                $_data['message'] = $data['message'];
                $model->setData($_data);
                $model->save();
            } else{
                $sellerEmail = $this->helper->getSellerById($data['partner_id'])->getData();
                $model->setAdminId($userId['user_id']);
                $model->setAdminName($userId['firstname'].$userId['lastname']);
                $model->setAdminEmail($userId['email']);
                $model->setSellerId($data['partner_id']);
                $model->setSellerEmail($sellerEmail['email']);
                $model->setSellerName($sellerEmail['name']);
                $model->setContent($data['message']);
                $model->setDescription($data['description']);
                $model->setSubject($data['subject']);
                $model->save();
            }


            $this->messageManager->addSuccessMessage(__('You saved this message.'));
            $this->_objectManager->get(\Magento\Backend\Model\Session::class)->setFormData(false);
            if ($this->getRequest()->getParam('back')) {
                return $resultRedirect->setPath('*/*/edit', ['message_id' => $model->getId(), '_current' => true]);
            }
            return $resultRedirect->setPath('*/*/');

//            $this->_getSession()->setFormData($data);
//            return $resultRedirect->setPath(
//                '*/*/edit',
//                ['message_id' => $this->getRequest()->getParam('message_id')]
//            );
        }
        return $resultRedirect->setPath('*/*/');
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Lof_MarketPlace::message_save');
    }
}
