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
 * @package    Lofmp_SellerBadge
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lofmp\SellerBadge\Controller\Adminhtml\SellerBadge;

use Magento\Framework\Exception\LocalizedException;

class Save extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Lofmp_SellerBadge::SellerBadge_save';

    /**
     * @var \Magento\Framework\App\Request\DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
    ) {
        $this->dataPersistor = $dataPersistor;
        parent::__construct($context);
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
        $data = $this->getRequest()->getPostValue();
        if ($data) {
            $id = $this->getRequest()->getParam('badge_id');

            $model = $this->_objectManager->create(\Lofmp\SellerBadge\Model\SellerBadge::class)->load($id);
            if (!$model->getId() && $id) {
                $this->messageManager->addErrorMessage(__('This Seller badge no longer exists.'));
                return $resultRedirect->setPath('*/*/');
            }

            $postData = $this->prepareData($data);

            unset($postData['conditions_serialized']);
            unset($postData['actions_serialized']);

            $model->loadPost($postData);

            try {
                $model->save();
                $this->messageManager->addSuccessMessage(__('You saved the Seller badge.'));
                $this->dataPersistor->clear('lofmp_sellerbadge_badge');

                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['badge_id' => $model->getId()]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage(
                    $e,
                    __('Something went wrong while saving the Seller badge.')
                );
            }

            $this->dataPersistor->set('lofmp_sellerbadge_badge', $postData);
            return $resultRedirect->setPath('*/*/edit', ['badge_id' => $this->getRequest()->getParam('badge_id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }

    /**
     * @param $data
     * @return array|mixed
     */
    private function prepareData($data)
    {
        if (isset($data['general']) && is_array($data['general'])) {
            if (isset($data['general']['image']) && is_array($data['general']['image'])) {
                if (isset($data['general']['image'][0]['name'])) {
                    $data['general']['image'] = $data['general']['image'][0]['name'];
                } else {
                    unset($data['general']['image']);
                }
            } else {
                $data['general']['image'] = null;
            }

            if (isset($data['rule']['conditions'])) {
                $data['general']['conditions'] = $data['rule']['conditions'];
                unset($data['rule']);
            }

            $data = $data['general'];
            unset($data['general']);
        }
        return $data;
    }
}
