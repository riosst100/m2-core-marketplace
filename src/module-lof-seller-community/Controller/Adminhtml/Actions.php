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
 * @package    Lof_SellerCommunity
 * @copyright  Copyright (c) 2022 Landofcoder (https://landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lof\SellerCommunity\Controller\Adminhtml;

use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Ui\Component\MassAction\Filter;

/**
 * Abstract admin controller
 */
abstract class Actions extends \Magento\Backend\App\Action
{
    /**
     * Form session key
     * @var string
     */
    protected $_formSessionKey;

    /**
     * Allowed Key
     * @var string
     */
    protected $_allowedKey;

    /**
     * Model class name
     * @var string
     */
    protected $_modelClass;

    /**
     * Collection class name
     * @var string
     */
    protected $_collectionClass;

    /**
     * Active menu key
     * @var string
     */
    protected $_activeMenu;

    /**
     * Store config section key
     * @var string
     */
    protected $_configSection;

    /**
     * Request id key
     * @var string
     */
    protected $_idKey = 'id';

    /**
     * Status field name
     * @var string
     */
    protected $_statusField     = 'status';

    /**
     * Save request params key
     * @var string
     */
    protected $_paramsHolder;

    /**
     * Model Object
     * @var \Magento\Framework\Model\AbstractModel
     */
    protected $_model;

    /**
     * Colelction Object
     * @var \Magento\Framework\Model\Resource\Db\Collection|mixed
     */
    protected $_collection;

    /**
     * @var Filter
     */
    protected $filter;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var mixed|null
     */
    protected $helperData;

    /**
     * @var string
     */
    protected $helperDataClass;

    /**
     * @var string
     */
    protected $_mediaFolder = 'lof/community/';

    /**
     * @param Context $context
     * @param DataPersistorInterface $dataPersistor
     * @param Filter $filter
     */
    public function __construct(
        Context $context,
        DataPersistorInterface $dataPersistor,
        Filter $filter
    ) {
        $this->dataPersistor = $dataPersistor;
        $this->filter = $filter;
        parent::__construct($context);
    }

    /**
     * Action execute
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $_preparedActions = ['index', 'grid', 'new', 'edit', 'save', 'duplicate', 'delete', 'massDelete', 'config', 'massStatus', 'massEnable', 'massDisable'];
        $_action = $this->getRequest()->getActionName();
        $flag = null;
        if ($_action == "massDelete") {
            $_action = "delete";
        }
        if ($_action == "massEnable") {
            $_action = "massStatus";
            $flag = 1;
        }
        if ($_action == "massDisable") {
            $_action = "massStatus";
            $flag = 0;
        }
        if (in_array($_action, $_preparedActions)) {
            $method = '_'.$_action.'Action';

            $this->_beforeAction();
            if ($flag !== null) {
                $this->$method($flag);
            } else {
                $this->$method();
            }
            $this->_afterAction();
        }
    }

    /**
     * Index action
     * @return void
     */
    protected function _indexAction()
    {
        if ($this->getRequest()->getParam('ajax')) {
            $this->_forward('grid');
            return;
        }

        $this->_view->loadLayout();
        $this->_setActiveMenu($this->_activeMenu);
        $title = __('Manage %1', $this->_getModel(false)->getOwnTitle(true));
        $this->_view->getPage()->getConfig()->getTitle()->prepend($title);
        $this->_addBreadcrumb($title, $title);
        $this->_view->renderLayout();
    }

    /**
     * Grid action
     * @return void
     */
    protected function _gridAction()
    {
        $this->_view->loadLayout(false);
        $this->_view->renderLayout();
    }

    /**
     * New action
     * @return void
     */
    protected function _newAction()
    {
        $this->_forward('edit');
    }

    /**
     * Edit action
     * @return void
     */
    public function _editAction()
    {

        try {
            $model = $this->_getModel();
            $id = $this->getRequest()->getParam($this->_idKey);
            if (!$model->getId() && $id) {
                throw new \Exception("Item is not longer exist.", 1);
            }
            $this->_getRegistry()->register('current_model', $model);

            $this->_view->loadLayout();
            $this->_setActiveMenu($this->_activeMenu);

            $title = $model->getOwnTitle();
            if ($model->getId()) {
                $breadcrumbTitle = __('Edit %1', $title);
                $breadcrumbLabel = $breadcrumbTitle;
            } else {
                $breadcrumbTitle = __('New %1', $title);
                $breadcrumbLabel = __('Create %1', $title);
            }
            $this->_view->getPage()->getConfig()->getTitle()->prepend(__($title));
            $this->_view->getPage()->getConfig()->getTitle()->prepend(
                $model->getId() ? $this->_getModelName($model) : __('New %1', $title)
            );

            $this->_addBreadcrumb($breadcrumbLabel, $breadcrumbTitle);

            // restore data
            $values = $this->_getSession()->getData($this->_formSessionKey, true);
            if ($this->_paramsHolder) {
                $values = isset($values[$this->_paramsHolder]) ? $values[$this->_paramsHolder] : null;
            }

            if ($values) {
                $model->addData($values);
            }

            $this->_view->renderLayout();
        } catch (\Exception $e) {
            $this->messageManager->addException(
                $e,
                __(
                    'Something went wrong: %1',
                    $e->getMessage()
                )
            );
            $this->_redirect('*/*/');
        }
    }

    /**
     * Retrieve model name
     * @param  boolean $plural
     * @return string
     */
    protected function _getModelName(\Magento\Framework\Model\AbstractModel $model)
    {
        return $model->getName() ?: $model->getTitle();
    }

    /**
     * Save action
     * @return void
     */
    public function _saveAction()
    {
        $request = $this->getRequest();
        if (!$request->isPost()) {
            $this->getResponse()->setRedirect($this->getUrl('*/*'));
        }
        $model = $this->_getModel();

        try {
            $params = $this->_paramsHolder ? $request->getParam($this->_paramsHolder) : $request->getParams();
            $params = $this->filterParams($params);

            $idFieldName = $model->getResource()->getIdFieldName();
            if (isset($params[$idFieldName]) && empty($params[$idFieldName])) {
                unset($params[$idFieldName]);
            }
            $model->addData($params);

            $this->_beforeSave($model, $request);
            $model->save();
            $this->_afterSave($model, $request);

            $this->messageManager->addSuccess(__('%1 has been saved.', $model->getOwnTitle()));
            $this->_setFormData(false);
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addError(nl2br($e->getMessage()));
            $this->_setFormData($params);
        } catch (\Exception $e) {
            $this->messageManager->addException(
                $e,
                __(
                    'Something went wrong while saving this %1. %2',
                    strtolower($model->getOwnTitle()),
                    $e->getMessage()
                )
            );
            $this->_setFormData($params);
        }

        $hasError = (bool)$this->messageManager->getMessages()->getCountByType(
            \Magento\Framework\Message\MessageInterface::TYPE_ERROR
        );

        if ($request->getParam('isAjax')) {
            $block = $this->_objectManager->create(\Magento\Framework\View\Layout::class)->getMessagesBlock();
            $block->setMessages($this->messageManager->getMessages(true));

            $this->getResponse()->setBody(json_encode(
                [
                    'messages' => $block->getGroupedHtml(),
                    'error' => $hasError,
                    'model' => $model->toArray(),
                ]
            ));
        } else {
            if ($hasError || $request->getParam('back')) {
                $this->_redirect('*/*/edit', [$this->_idKey => $model->getId()]);
            } else {
                $this->_redirect('*/*');
            }
        }
    }

    /**
     * Duplicat action
     * @return void
     */
    protected function _duplicateAction()
    {
        try {
            $originModel = $this->_getModel();
            if (!$originModel->getId()) {
                throw new \Exception("Item is not longer exist.", 1);
            }

            $model = $originModel->duplicate();

            $this->messageManager->addSuccess(__('%1 has been duplicated.', $model->getOwnTitle()));
            $this->_redirect('*/*/edit', [$this->_idKey => $model->getId()]);
        } catch (\Exception $e) {
            $this->messageManager->addException(
                $e,
                __(
                    'Something went wrong while saving this %1. %2',
                    strtolower(isset($model) ? $model->getOwnTitle() : 'item'),
                    $e->getMessage()
                )
            );
            $this->_redirect('*/*/edit', [$this->_idKey => $originModel->getId()]);
        }
    }

    /**
     * Before model Save action
     * @return void
     */
    protected function _beforeSave($model, $request)
    {
    }

    /**
     * After model action
     * @return void
     */
    protected function _afterSave($model, $request)
    {
    }

    /**
     * Before action
     * @return void
     */
    protected function _beforeAction()
    {
    }

    /**
     * After action
     * @return void
     */
    protected function _afterAction()
    {
    }

    /**
     * Delete action
     * @return void
     */
    protected function _deleteAction()
    {
        $ids = $this->getRequest()->getParam($this->_idKey);
        if ($ids) {
            if (!is_array($ids)) {
                $ids = [$ids];
            }

            $error = false;
            try {
                foreach ($ids as $id) {
                    $this->_objectManager->create($this->_modelClass)->load($id)->delete();
                }
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $error = true;
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $error = true;
                $this->messageManager->addException(
                    $e,
                    __(
                        "We can't delete %1 right now. %2",
                        strtolower($this->_getModel(false)->getOwnTitle()),
                        $e->getMessage()
                    )
                );
            }

            if (!$error) {
                $this->messageManager->addSuccess(
                    __('%1 have been deleted.', $this->_getModel(false)->getOwnTitle(count($ids) > 1))
                );
            }
        } else {
            $this->_executeMassAction("delete");
        }

        $this->_redirect('*/*');
    }

    /**
     * Change status action
     * @var string|int|null $flag
     * @return void
     */
    protected function _massStatusAction($flag = null)
    {
        $ids = $this->getRequest()->getParam($this->_idKey);
        if ($ids) {
            if (!is_array($ids)) {
                $ids = [$ids];
            }

            $model = $this->_getModel(false);

            $error = false;

            try {
                $status = $this->getRequest()->getParam('status');
                if ($flag !== null) {
                    $status = (int)$flag;
                }
                $statusFieldName = $this->_statusField;

                if (is_null($status)) {
                    throw new \Exception(__('Parameter "Status" missing in request data.'));
                }

                if (is_null($statusFieldName)) {
                    throw new \Exception(__('Status Field Name is not specified.'));
                }

                foreach ($ids as $id) {
                    $this->_objectManager->create($this->_modelClass)
                        ->load($id)
                        ->setData($this->_statusField, $status)
                        ->save();
                }
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $error = true;
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $error = true;
                $this->messageManager->addException(
                    $e,
                    __(
                        "We can't change status of %1 right now. %2",
                        strtolower($model->getOwnTitle()),
                        $e->getMessage()
                    )
                );
            }

            if (!$error) {
                $this->messageManager->addSuccess(
                    __('%1 status have been changed.', $model->getOwnTitle(count($ids) > 1))
                );
            }
        } else {
            $this->_executeMassAction("status", $flag);
        }
        $this->_redirect('*/*');
    }

    /**
     * Go to config section action
     * @return void
     */
    protected function _configAction()
    {
        $this->_redirect('admin/system_config/edit', ['section' => $this->_configSection()]);
    }

    /**
     * Set form data
     * @return $this
     */
    protected function _setFormData($data = null)
    {
        if (null === $data) {
            $data = $this->getRequest()->getParams();
        }

        if (false === $data) {
            $this->dataPersistor->clear($this->_formSessionKey);
        } else {
            $this->dataPersistor->set($this->_formSessionKey, $data);
        }

        /* deprecated save in session */
        $this->_getSession()->setData($this->_formSessionKey, $data);

        return $this;
    }

    /**
     * Filter request params
     * @param  array $data
     * @return array
     */
    protected function filterParams($data)
    {
        return $data;
    }

    /**
     * Get core registry
     * @return void
     */
    protected function _getRegistry()
    {
        if (is_null($this->_coreRegistry)) {
            $this->_coreRegistry = $this->_objectManager->get(\Magento\Framework\Registry::class);
        }
        return $this->_coreRegistry;
    }

    /**
     * Check is allowed access
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed($this->_allowedKey);
    }

    /**
     * Retrieve model object
     * @return \Magento\Framework\Model\AbstractModel
     */
    protected function _getModel($load = true)
    {
        if (is_null($this->_model)) {
            $this->_model = $this->_objectManager->create($this->_modelClass);

            $id = (int)$this->getRequest()->getParam($this->_idKey);
            $idFieldName = $this->_model->getResource()->getIdFieldName();
            if (!$id && $this->_idKey !== $idFieldName) {
                $id = (int)$this->getRequest()->getParam($idFieldName);
            }

            if ($id && $load) {
                $this->_model->load($id);
            }
        }
        return $this->_model;
    }

    /**
     * Retrieve helper data object
     * @return mixed
     */
    public function getHelperData()
    {
        if (is_null($this->helperData)) {
            $this->helperData = $this->_objectManager->create($this->helperDataClass);
        }
        return $this->helperData;
    }

    /**
     * Retrieve model object
     * @return \Magento\Framework\Model\ResourceModel\Db\Collection|mixed
     */
    protected function _getCollection()
    {
        if (is_null($this->_collection)) {
            $this->_collection = $this->_objectManager->create($this->_collectionClass);
        }
        return $this->_collection;
    }

    /**
     * execute mass action
     *
     * @param string $type
     * @param mixed|null $flag
     * @return void
     */
    protected function _executeMassAction($type = "status", $flag = null)
    {
        $collection = $this->filter->getCollection($this->_getCollection());
        $count = $collection->getSize();
        if ($count > 0) {
            switch ($type) {
                case "status":
                    $status = $this->getRequest()->getParam('status');
                    if ($flag !== null) {
                        $status = (int)$flag;
                    }
                    $statusFieldName = $this->_statusField;

                    if (is_null($status)) {
                        throw new \Exception(__('Parameter "Status" missing in request data.'));
                    }

                    if (is_null($statusFieldName)) {
                        throw new \Exception(__('Status Field Name is not specified.'));
                    }

                    foreach ($collection as $item) {
                        $this->_objectManager->create($this->_modelClass)
                            ->load($item->getId())
                            ->setData($this->_statusField, $status)
                            ->save();
                    }
                    $this->messageManager->addSuccess(
                        __('%1 items have been changed status.', $count)
                    );
                break;
                case "delete":
                    foreach ($collection as $item) {
                        $item->delete();
                    }
                    $this->messageManager->addSuccess(
                        __('%1 items have been deleted.', $count)
                    );
                break;
                default:
                break;
            }
        }
    }

    /**
     * upload image
     *
     * @param string $fieldId
     * @return string|mixed
     */
    public function uploadImage($fieldId = 'image')
    {
        if (isset($_FILES[$fieldId]) && $_FILES[$fieldId]['name']!='') {
            $uploader = $this->_objectManager->create(
                'Magento\Framework\File\Uploader',
                array('fileId' => $fieldId)
            );
            $fieldId = $this->getRequest()->getParam($this->_idKey);
            /** @var \Magento\Framework\Filesystem\Directory\Read $mediaDirectory */
            $mediaDirectory = $this->_objectManager->get('Magento\Framework\Filesystem')
                                    ->getDirectoryRead(DirectoryList::MEDIA);

            try {
                $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
                $uploader->setAllowRenameFiles(true);
                $uploader->setFilesDispersion(false);
                $result = $uploader->save($mediaDirectory->getAbsolutePath($this->_mediaFolder)
                    );
                return $this->_mediaFolder.$result['name'];
            } catch (\Exception $e) {
                $this->_logger->critical($e);
                $this->messageManager->addError($e->getMessage());
                if ($fieldId) {
                    $this->_redirect('*/*/edit', [$this->_idKey => $fieldId]);
                } else {
                    $this->_redirect('*/*');
                }
            }
        }
        return "";
    }

}
