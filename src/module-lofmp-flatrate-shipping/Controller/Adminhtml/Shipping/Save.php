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
 * @package    Lofmp_FlatRateShipping
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lofmp\FlatRateShipping\Controller\Adminhtml\Shipping;

use Magento\Backend\App\Action;
use Lofmp\FlatRateShipping\Model\ShippingmethodFactory;
use Lofmp\FlatRateShipping\Model\ShippingFactory;
use Magento\MediaStorage\Model\File\UploaderFactory;

class Save extends \Magento\Backend\App\Action
{
    /**
     * Core registry.
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $_resultPageFactory;

    /**
     * @var Lofmp\FlatRateShipping\Model\ShippingmethodFactory
     */
    protected $_mpshippingMethod;

    /**
     * @var Lofmp\FlatRateShipping\Model\Shipping
     */
    protected $_mpshipping;

    /**
     * @var Magento\MediaStorage\Model\File\UploaderFactory
     */
    protected $_fileUploader;

    /**
     * @var \Magento\Framework\File\Csv
     */
    protected $_csvReader;

    /**
     * @param Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param ShippingmethodFactory $shippingmethodFactory
     * @param ShippingFactory $mpshipping
     * @param UploaderFactory $fileUploader
     * @param \Magento\Framework\File\Csv $csvReader
     */
    public function __construct(
        Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        ShippingmethodFactory $shippingmethodFactory,
        ShippingFactory $mpshipping,
        UploaderFactory $fileUploader,
        \Magento\Framework\File\Csv $csvReader
    ) {
        parent::__construct($context);
        $this->_resultPageFactory = $resultPageFactory;
        $this->_mpshippingMethod = $shippingmethodFactory;
        $this->_mpshipping = $mpshipping;
        $this->_fileUploader = $fileUploader;
        $this->_csvReader = $csvReader;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect|\Magento\Framework\Controller\ResultInterface
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @phpcs:disable Generic.Metrics.NestingLevel.TooHigh
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();

        if ($this->getRequest()->isPost()) {
            try {
                $shippingModel = $this->_mpshipping->create();
                // phpcs:disable Magento2.Security.Superglobal.SuperglobalUsageError
                if (isset($_FILES['import_file'])) {
                    if (!$this->_formKeyValidator->validate($this->getRequest())) {
                        return $this->resultRedirectFactory->create()->setPath('*/*/index');
                    }

                    $uploader = $this->_fileUploader->create(['fileId' => 'import_file']);
                    $result = $uploader->validateFile();
                    $file = $result['tmp_name'];
                    $fileNameArray = explode('.', $result['name']);
                    $ext = end($fileNameArray);
                    if ($file != '' && $ext == 'csv') {
                        $csvFileData = $this->_csvReader->getData($file);
                        $count = 0;
                        foreach ($csvFileData as $rowData) {
                            if (count($rowData) < 7 && $count == 0) {
                                $this->messageManager->addErrorMessage(__('Csv file is not a valid file!'));
                                return $this->resultRedirectFactory->create()->setPath('*/*/index');
                            }
                            if ($rowData[0] == '' ||
                                $rowData[1] == '' ||
                                $rowData[2] == '' ||
                                $rowData[3] == '' ||
                                $count == 0
                            ) {
                                ++$count;
                                continue;
                            }
                            $temp = [];
                            $temp['title'] = $rowData[0];
                            $temp['type'] = $rowData[1];
                            $temp['free_shipping'] = $rowData[2];
                            $temp['sort_order'] = $rowData[3];
                            $temp['price'] = $rowData[4];
                            $temp['status'] = $rowData[5];
                            $temp['partner_id'] = $rowData[6];
                            $partnerid = $rowData[6];
                            $this->addDataToCollection($temp, $rowData, $partnerid);
                        }
                        if (($count - 1) > 1) {
                            $this->messageManager->addNoticeMessage(__('Some rows are not valid!'));
                        }
                        if (($count - 1) <= 1) {
                            $this->messageManager->addSuccessMessage(
                                __('Your shipping detail has been successfully Saved')
                            );
                        }

                        return $this->resultRedirectFactory->create()->setPath('*/*/index');
                    } else {
                        $this->messageManager->addErrorMessage(__('Please upload CSV file.'));
                    }
                } else {
                    $params = $data;
                    $id = $this->getRequest()->getParam('lofmpshipping_id');
                    if ($id) {
                        $shippingModel->load($id);

                        if ($id != $shippingModel->getId()) {
                            throw new \Magento\Framework\Exception\LocalizedException(
                                __('The wrong shipping is specified.')
                            );
                        }
                    }

                    $shippingModel->setData($params);
                    $shippingModel->save();

                    $this->messageManager->addSuccessMessage(__('Your shipping detail has been successfully saved'));
                    // check if 'Save and Continue'
                    if ($this->getRequest()->getParam('back')) {
                        return $this->resultRedirectFactory->create()->setPath(
                            '*/*/edit',
                            ['lofmpshipping_id' => $params['lofmpshipping_id']]
                        );
                    }
                }
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }
        }
        return $this->resultRedirectFactory->create()->setPath('*/*/index');
    }

    /**
     * @param $shippingMethodName
     * @return int
     */
    public function getShippingNameById($shippingMethodName)
    {
        $entityId = 0;
        $shippingMethodModel = $this->_mpshippingMethod->create()
            ->getCollection()
            ->addFieldToFilter('method_name', $shippingMethodName);
        foreach ($shippingMethodModel as $shippingMethod) {
            $entityId = $shippingMethod->getEntityId();
        }
        return $entityId;
    }

    /**
     * @param $temp
     * @param $rowData
     * @param $partnerid
     * @throws \Exception
     */
    public function addDataToCollection($temp, $rowData, $partnerid)
    {
        $collection = $this->_mpshipping->create()
            ->getCollection()
            ->addFieldToFilter('price', $rowData[4])
            ->addFieldToFilter('sort_order', $rowData[3])
            ->addFieldToFilter('title', $rowData[0])
            ->addFieldToFilter('partner_id', $partnerid)
            ->addFieldToFilter('type', $rowData[1])
            ->addFieldToFilter('free_shipping', $rowData[2])
            ->addFieldToFilter('status', $rowData[5]);

        if ($collection->getSize() > 0) {
            foreach ($collection as $data) {
                $rowId = $data->getLofmpshippingId();
                $dataArray = ['price' => $rowData[4]];
                $model = $this->_mpshipping->create();
                $shippingModel = $model->load($rowId)->addData($dataArray);
                $shippingModel->setLofmpshippingId($rowId)->save();
            }
        } else {
            $shippingModel = $this->_mpshipping->create();
            $shippingModel->setData($temp)->save();
        }
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Lofmp_FlatRateShipping::shipping');
    }
}
