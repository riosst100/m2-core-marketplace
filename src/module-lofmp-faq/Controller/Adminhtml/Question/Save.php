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
 * @package    Lofmp_Faq
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */
namespace Lofmp\Faq\Controller\Adminhtml\Question;

use Magento\Backend\App\Action;
use Magento\Framework\App\Filesystem\DirectoryList;

class Save extends \Lofmp\Faq\Controller\Adminhtml\Question
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Backend\Helper\Js $jsHelper
        )
    {
        $this->_coreRegistry = $coreRegistry;
        $this->jsHelper = $jsHelper;
        parent::__construct($context, $coreRegistry);
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        // check if data sent
        $data = $this->getRequest()->getPostValue();

        if ($data) {

            $links = $this->getRequest()->getPost('links');
            $links = is_array($links) ? $links : [];
            if(!empty($links) && isset($links['products'])){
                $questionProducts = $this->jsHelper->decodeGridSerializedInput($links['products']);
                $data['question_products'] = $questionProducts;
            }else{
                $data['question_products'] = [];
            }

            $id = $this->getRequest()->getParam('question_id');
            $model = $this->_objectManager->create('Lofmp\Faq\Model\Question')->load($id);
            if (!$model->getId() && $id) {
                $this->messageManager->addError(__('This question no longer exists.'));
                return $resultRedirect->setPath('*/*/');
            }
            if ($model->getId() && isset($data["creation_time"])) {
                unset($data["creation_time"]);
            }
            // init model and set data

            $model->setData($data);

            // try to save it
            try {
                // save the data
                $model->save();
                // display success message
                $this->messageManager->addSuccess(__('You saved the question.'));
                // clear previously saved data from session
                $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData(false);

                // check if 'Save and Continue'
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['question_id' => $model->getId()]);
                }
                // go to grid
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                // display error message
                $this->messageManager->addError($e->getMessage());
                // save data in session
                $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData($data);
                // redirect to edit form
                return $resultRedirect->setPath('*/*/edit', ['question_id' => $this->getRequest()->getParam('question_id')]);
            }
        }
        return $resultRedirect->setPath('*/*/');
    }
}
