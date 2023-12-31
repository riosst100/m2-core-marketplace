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
 * @package    Lofmp_TableRateShipping
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lofmp\TableRateShipping\Controller\Adminhtml\Shipping;

use Magento\Backend\App\Action;
use Lofmp\TableRateShipping\Model\ShippingmethodFactory;
use Lofmp\TableRateShipping\Model\ShippingFactory;
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
     * @var ShippingmethodFactory
     */
    protected $_mpshippingMethod;

    /**
     * @var ShippingFactory
     */
    protected $_mpshipping;

    /**
     * @var UploaderFactory
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
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        if ($this->getRequest()->isPost()) {
            try {
                $files = $this->getRequest()->getFiles();
                if (count($files->toArray())) {
                    $uploader = $this->_fileUploader->create(
                        ['fileId' => 'import_file']
                    );
                    $fileUpload = $uploader->validateFile();
                    $this->runImportCsvFile($fileUpload);
                } else {
                    $params = $data;
                    $params['dest_region_id'] = isset($params['dest_region_id']) && $params['dest_region_id']
                        ? @strtoupper($params['dest_region_id'])
                        : '';
                    $params['dest_zip'] = isset($params['dest_zip']) && $params['dest_zip']
                        ? @strtoupper($params['dest_zip'])
                        : '';
                    $params['dest_zip_to'] = isset($params['dest_zip_to']) && $params['dest_zip_to']
                        ? @strtoupper($params['dest_zip_to'])
                        : '';
                    $shippingModel = $this->_mpshipping->create();
                    $shippingMethodId = $this->calculateShippingMethodId($params['shipping_method']);

                    if (isset($params['lofmpshipping_id']) && $params['lofmpshipping_id']) {
                        $id = $params['lofmpshipping_id'];
                        $shippingModel->load($id);
                        $partnerid = isset($params["partner_id"]) && $params["partner_id"]
                            ? $params["partner_id"]
                            : 0;

                        if ($id != $shippingModel->getId()) {
                            throw new \Magento\Framework\Exception\LocalizedException(
                                __('The wrong shipping is specified.')
                            );
                        }
                        $shippingMethodId = $this->calculateShippingMethodId($params['shipping_method'], $partnerid);
                        $temp = [
                            'lofmpshipping_id' => $params['lofmpshipping_id'],
                            'dest_country_id' => $params['dest_country_id'],
                            'dest_region_id' => $params['dest_region_id'],
                            'dest_zip' => $params['dest_zip'],
                            'dest_zip_to' => $params['dest_zip_to'],
                            'price' => (float)$params['price'],
                            'cart_total' => isset($params['cart_total']) && $params['cart_total']
                                ? (float)$params['cart_total']
                                : '',
                            'free_shipping' => isset($params['free_shipping']) && $params['free_shipping']
                                ? (float)$params['free_shipping']
                                : '',
                            'weight_from' => (float)$params['weight_from'],
                            'weight_to' => (float)$params['weight_to'],
                            'shipping_method_id' => $shippingMethodId,
                            'partner_id' => $partnerid,
//                            'cost' => (float)$params['cost'],
                        ];
                    } else {
                        $partnerid = isset($params["partner_id"]) && $params["partner_id"] ? $params["partner_id"] : 0;
                        $temp = [
                            'dest_country_id' => $params['dest_country_id'],
                            'dest_region_id' => $params['dest_region_id'],
                            'dest_zip' => $params['dest_zip'],
                            'dest_zip_to' => $params['dest_zip_to'],
                            'price' => (float)$params['price'],
                            'cart_total' => isset($params['cart_total']) && $params['cart_total']
                                ? (float)$params['cart_total']
                                : '',
                            'free_shipping' => isset($params['free_shipping']) && $params['free_shipping']
                                ? (float)$params['free_shipping']
                                : '',
                            'weight_from' => (float)$params['weight_from'],
                            'weight_to' => (float)$params['weight_to'],
                            'shipping_method_id' => $shippingMethodId,
                            'partner_id' => $partnerid,
//                            'cost' => (float)$params['cost'],
                        ];
                    }

                    $shippingCollection = $this->_mpshipping->create()
                        ->getCollection()
                        ->addFieldToFilter('partner_id', $partnerid)
                        ->addFieldToFilter('dest_country_id', $params['dest_country_id'])
                        ->addFieldToFilter('dest_region_id', $params['dest_region_id'])
                        ->addFieldToFilter('dest_zip', $params['dest_zip'])
                        ->addFieldToFilter('dest_zip_to', $params['dest_zip_to'])
                        ->addFieldToFilter('weight_from', (float)$params['weight_from'])
                        ->addFieldToFilter('weight_to', (float)$params['weight_to'])
                        ->addFieldToFilter('cart_total', (float)$params['cart_total'])
                        ->addFieldToFilter('shipping_method_id', $shippingMethodId);

                    if ($shippingCollection->getsize() > 0) {
                        foreach ($shippingCollection as $row) {
                            $dataArray = [
                                'price' => (float)$temp['price'],
//                                'cost' => (float)$temp['cost'],
                                'free_shipping' => $temp['free_shipping'] ? (float)$temp['free_shipping'] : ''
                            ];
                            $_shippingModel = $this->_mpshipping->create()->load($row->getLofmpshippingId());
                            $_shippingModel->addData($dataArray);
                            $_shippingModel->save();
                        }
                    } else {
                        //$shippingModel = $this->_mpshipping->create();
                        $shippingModel->setData($temp);
                        $shippingModel->save();
                    }
                    $this->messageManager->addSuccessMessage(__('Your shipping detail has been successfully Saved'));
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
     * @param array $result
     * @return \Magento\Framework\Controller\Result\Redirect|void
     * @throws \Exception
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function runImportCsvFile($result = [])
    {
        if (!$result || !$this->_formKeyValidator->validate($this->getRequest())) {
            return $this->resultRedirectFactory->create()->setPath('*/*/index');
        }

        $file = $result['tmp_name'];
        $fileNameArray = explode('.', $result['name']);

        $ext = end($fileNameArray);
        if ($file != '' && $ext == 'csv') {
            $csvFileData = $this->_csvReader->getData($file);
            $partnerId = 0;
            $count = 0;
            $headingArray = [];
            foreach ($csvFileData as $rowData) {
                if ($count == 0) {
                    foreach ($rowData as $i => $label) {
                        if (!isset($headingArray[$label])) {
                            $headingArray[$label] = $i;
                        }
                    }
                    ++$count;
                    continue;
                }
                $temp = [];
                $partnerId = $this->getRowValue($rowData, $headingArray, "partner_id");
                $shipping_method = $this->getRowValue($rowData, $headingArray, "shipping_method");
                $shippingMethodId = $this->calculateShippingMethodId($shipping_method, $partnerId);
                $temp['dest_country_id'] = $this->getRowValue($rowData, $headingArray, "country_code");
                $temp['dest_region_id'] = $this->getRowValue($rowData, $headingArray, "region_id");
                $temp['dest_zip'] = $this->getRowValue($rowData, $headingArray, "zip");
                $temp['dest_zip_to'] = $this->getRowValue($rowData, $headingArray, "zip_to");
                $temp['price'] = (float)$this->getRowValue($rowData, $headingArray, "price");
                $temp['weight_from'] = (float)$this->getRowValue($rowData, $headingArray, "weight_from");
                $temp['weight_to'] = (float)$this->getRowValue($rowData, $headingArray, "weight_to");
                $temp['shipping_method_id'] = $shippingMethodId;
                $temp['partner_id'] = $partnerId;
                $temp['free_shipping'] = $this->getRowValue($rowData, $headingArray, "free_shipping");
                $temp['cart_total'] = (float)$this->getRowValue($rowData, $headingArray, "cart_total");
//                $temp['cost'] = (float)$this->getRowValue($rowData, $headingArray, "cost");

                if ($temp['dest_country_id'] == '' ||
                    $temp['dest_region_id'] == '' ||
                    $temp['dest_zip'] == '' ||
                    $temp['dest_zip_to'] == ''
                ) {
                    continue;
                }
                $temp['price'] = $temp['price'] ? $temp['price'] : 0.0000;
                $temp['weight_from'] = $temp['weight_from'] ? $temp['weight_from'] : 0.0000;
                $temp['weight_to'] = $temp['weight_to'] ? $temp['weight_to'] : 0.0000;
                $temp['dest_region_id'] = @strtoupper($temp['dest_region_id']);
                $temp['dest_zip'] = @strtoupper($temp['dest_zip']);
                $temp['dest_zip_to'] = @strtoupper($temp['dest_zip_to']);
                $this->addDataToCollection($temp, $shippingMethodId, $temp['partner_id']);
            }
            if (($count - 1) > 1) {
                $this->messageManager->addNotice(__('Some rows are not valid!'));
            }
            if (($count - 1) <= 1) {
                $this->messageManager
                    ->addSuccessMessage(
                        __('Your shipping detail has been successfully Saved')
                    );
            }
            return $this->resultRedirectFactory->create()->setPath('*/*/index');
        } else {
            $this->messageManager->addErrorMessage(__('Please upload Csv file'));
        }
    }

    /**
     * @param $shippingMethodName
     * @param int $partnerId
     * @return int
     */
    public function getShippingNameById($shippingMethodName, $partnerId = 0)
    {
        $entityId = 0;
        $shippingMethodModel = $this->_mpshippingMethod->create()
            ->getCollection()
            ->addFieldToFilter('partner_id', $partnerId)
            ->addFieldToFilter('method_name', $shippingMethodName);
        foreach ($shippingMethodModel as $shippingMethod) {
            $entityId = $shippingMethod->getEntityId();
        }
        return $entityId;
    }

    /**
     * @param $rowData
     * @param $headingArray
     * @param $column_name
     * @return string
     */
    public function getRowValue($rowData, $headingArray, $column_name)
    {
        $rowIndex = isset($headingArray[$column_name]) ? $headingArray[$column_name] : -1;
        return isset($rowData[$rowIndex]) ? $rowData[$rowIndex] : "";
    }

    /**
     * @param $temp
     * @param $shippingMethodId
     * @param $partnerid
     * @throws \Exception
     */
    public function addDataToCollection($temp, $shippingMethodId, $partnerid)
    {
        $collection = $this->_mpshipping->create()
            ->getCollection()
            ->addFieldToFilter('weight_to', $temp["weight_to"])
            ->addFieldToFilter('dest_zip_to', $temp["dest_zip_to"])
            ->addFieldToFilter('dest_country_id', $temp["dest_country_id"])
            ->addFieldToFilter('partner_id', $partnerid)
            ->addFieldToFilter('dest_region_id', $temp["dest_region_id"])
            ->addFieldToFilter('dest_zip', $temp["dest_zip"])
            ->addFieldToFilter('weight_from', $temp["weight_from"])
            ->addFieldToFilter('cart_total', $temp["cart_total"])
            ->addFieldToFilter('shipping_method_id', $shippingMethodId);

        if ($collection->getSize() > 0) {
            foreach ($collection as $row) {
                $dataArray = [
                    'price' => $temp['price'],
//                    'cost' => $temp['cost'],
                    'free_shipping' => $temp['free_shipping'] ? (float)$temp['free_shipping'] : ''
                ];
                $this->_mpshipping->create()
                    ->load($row->getLofmpshippingId())
                    ->addData($dataArray)
                    ->save();
            }
        } else {
            $this->_mpshipping->create()->setData($temp)->save();
        }
    }

    /**
     * @param $shippingMethodName
     * @param int $partnerId
     * @return int|null
     * @throws \Exception
     */
    public function calculateShippingMethodId($shippingMethodName, $partnerId = 0)
    {
        $shippingMethodId = $this->getShippingNameById($shippingMethodName, $partnerId);
        if ($shippingMethodId == 0) {
            $mpshippingMethod = $this->_mpshippingMethod->create();
            $mpshippingMethod->setPartnerId($partnerId);
            $mpshippingMethod->setMethodName($shippingMethodName);
            $savedMethod = $mpshippingMethod->save();
            $shippingMethodId = $savedMethod->getEntityId();
        }
        return $shippingMethodId;
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Lofmp_TableRateShipping::shipping');
    }
}
