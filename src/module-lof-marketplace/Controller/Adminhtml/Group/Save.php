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

namespace Lof\MarketPlace\Controller\Adminhtml\Group;

use Magento\Backend\App\Action;
use Lof\MarketPlace\Model\Group;
use Lof\MarketPlace\Api\SellerGroupRepositoryInterface;
use Magento\Backend\App\Action\Context;

class Save extends Action
{
    /**
     * @var Context
     */
    protected $context;

    /**
     * @var Group
     */
    protected $sellerGroup;

    /**
     * @param Context $context
     * @param Group $sellerGroup
     */
    public function __construct(
        Context $context,
        Group $sellerGroup
    ) {
        $this->sellerGroup = $sellerGroup;
        parent::__construct($context);
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            $model = $this->sellerGroup;
            $id = $this->getRequest()->getParam('group_id');

            if ($data['url_key'] == '') {
                $data['url_key'] = $data['name'];
            }
            $url_key = $this->_objectManager->create(\Magento\Catalog\Model\Product\Url::class)
                ->formatUrlKey($data['url_key']);
            $data['url_key'] = $url_key;

            if ($id) {
                $model->load($id);
                $data["id"] = $id;
            }
            $model->setData($data);

            try {
                $model->save();
                $this->messageManager->addSuccessMessage(__('You saved this group.'));
                $this->_objectManager->get(\Magento\Backend\Model\Session::class)->setFormData(false);
                $this->_eventManager->dispatch(
                    'controller_action_marketplace_seller_group_save_entity_after',
                    ['controller' => $this, 'group' => $model]
                );

                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['group_id' => $model->getId(), '_current' => true]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the group.'));
            }

            $this->_getSession()->setFormData($data);
            return $resultRedirect->setPath('*/*/edit', ['group_id' => $this->getRequest()->getParam('group_id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Lof_MarketPlace::group_save');
    }
}
