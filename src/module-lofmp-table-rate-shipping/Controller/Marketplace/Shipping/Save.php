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

namespace Lofmp\TableRateShipping\Controller\Marketplace\Shipping;

use Magento\Framework\App\Action\Action;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Data\Form\FormKey\Validator as FormKeyValidator;
use Magento\Framework\App\RequestInterface;
use Lofmp\TableRateShipping\Model\ShippingmethodFactory;
use Magento\Customer\Model\Url;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Lofmp\TableRateShipping\Model\ShippingFactory;
use Lofmp\TableRateShipping\Helper\Data;
use Magento\Framework\Url as FrontendUrl;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Save extends Action
{
    const FLAG_IS_URLS_CHECKED = 'check_url_settings';

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Framework\Data\Form\FormKey\Validator
     */
    protected $_formKeyValidator;

    /**
     * @var ShippingmethodFactory
     */
    protected $_mpshippingMethod;

    /**
     * @var Url
     */
    protected $_customerUrl;

    /**
     * @var ShippingFactory
     */
    protected $_mpshippingModel;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var FrontendUrl
     */
    protected $_frontendUrl;

    /**
     * @var UploaderFactory
     */
    protected $_fileUploader;

    /**
     * @var \Magento\Framework\File\Csv
     */
    protected $_csvReader;

    /**
     * @param Context $context
     * @param Session $customerSession
     * @param FormKeyValidator $formKeyValidator
     * @param ShippingmethodFactory $shippingmethodFactory
     * @param Url $customerUrl
     * @param Data $helper
     * @param ShippingFactory $mpshippingModel
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param FrontendUrl $frontendUrl
     * @param \Magento\Framework\File\Csv $csvReader
     * @param UploaderFactory $fileUploader
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        FormKeyValidator $formKeyValidator,
        ShippingmethodFactory $shippingmethodFactory,
        Url $customerUrl,
        Data $helper,
        ShippingFactory $mpshippingModel,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        FrontendUrl $frontendUrl,
        \Magento\Framework\File\Csv $csvReader,
        UploaderFactory $fileUploader
    ) {
        parent::__construct($context);
        $this->_customerSession = $customerSession;
        $this->_formKeyValidator = $formKeyValidator;
        $this->_mpshippingMethod = $shippingmethodFactory;
        $this->_customerUrl = $customerUrl;
        $this->_mpshippingModel = $mpshippingModel;
        $this->helper = $helper;
        $this->_storeManager = $storeManager;
        $this->_frontendUrl = $frontendUrl;
        $this->_csvReader = $csvReader;
        $this->_fileUploader = $fileUploader;
    }

    /**
     * @return Session
     */
    protected function _getSession()
    {
        return $this->_customerSession;
    }

    /**
     * @param RequestInterface $request
     * @return \Magento\Framework\App\ResponseInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function dispatch(RequestInterface $request)
    {
        $loginUrl = $this->_customerUrl->getLoginUrl();
        if (!$this->_customerSession->authenticate($loginUrl)) {
            $this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);
        }
        return parent::dispatch($request);
    }

    /**
     * @param $url
     * @return \Magento\Framework\App\ResponseInterface
     */
    protected function _redirectUrl($url)
    {
        $this->getResponse()->setRedirect($url);
        $this->_customerSession->setIsUrlNotice($this->_actionFlag->get('', self::FLAG_IS_URLS_CHECKED));
        return $this->getResponse();
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect|\Magento\Framework\Controller\ResultInterface|void
     * @phpcs:disable Generic.Metrics.NestingLevel.TooHigh
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function execute()
    {
        $seller = $this->helper->getSeller();
        $status = $seller ? $seller->getStatus() : 0;
        $partnerId = $seller ? $seller->getId() : 0;
        // phpcs:disable Generic.Metrics.NestingLevel.TooHigh
        if ($this->_customerSession->isLoggedIn() && $status == 1) {
            if (!$partnerId) {
                $this->_redirectUrl($this->getFrontendUrl('lofmarketplace/seller/becomeseller'));
                return;
            }
            if ($this->getRequest()->isPost() && $partnerId) {
                try {
                    $files = $this->getRequest()->getFiles();
                    if (count($files->toArray())) {
                        $uploader = $this->_fileUploader->create(
                            ['fileId' => 'import_file']
                        );
                        $fileUpload = $uploader ? $uploader->validateFile() : false;
                        $this->runImportCsvFile($seller, $fileUpload);
                    } else {
                        $params = $this->getRequest()->getParams();
                        $params['dest_region_id'] = isset($params['dest_region_id']) && $params['dest_region_id']
                            ? @strtoupper($params['dest_region_id'])
                            : '';
                        $params['dest_zip'] = isset($params['dest_zip']) && $params['dest_zip']
                            ? @strtoupper($params['dest_zip'])
                            : '';
                        $params['dest_zip_to'] = isset($params['dest_zip_to']) && $params['dest_zip_to']
                            ? @strtoupper($params['dest_zip_to'])
                            : '';
                        $shippingMethodId = $this->calculateShippingMethodId($params['shipping_method'], $partnerId);
                        $currentWebsiteId = $this->_storeManager->getStore()->getWebsiteId();
                        $shippingModel = $this->_mpshippingModel->create();
                        if (isset($params['lofmpshipping_id']) && $params['lofmpshipping_id']) {
                            $shippingModel->load($params['lofmpshipping_id']);
                            if (!$shippingModel->getId()
                                || $params['lofmpshipping_id'] != $shippingModel->getId()
                                || ($shippingModel->getId() && $partnerId != $shippingModel->getPartnerId())
                            ) {
                                throw new \Magento\Framework\Exception\LocalizedException(
                                    __('The wrong shipping is specified.')
                                );
                            }
                            $temp = [
                                'lofmpshipping_id' => $params['lofmpshipping_id'],
                                'dest_country_id' => $params['dest_country_id'],
                                'dest_region_id' => $params['dest_region_id'],
                                'dest_zip' => $params['dest_zip'],
                                'dest_zip_to' => $params['dest_zip_to'],
                                'price' => (float)$params['price'],
                                'cart_total' => $params['cart_total'] ? (float)$params['cart_total'] : '',
                                'free_shipping' => $params['free_shipping'] ? (float)$params['free_shipping'] : '',
                                'weight_from' => (float)$params['weight_from'],
                                'weight_to' => (float)$params['weight_to'],
                                'shipping_method_id' => $shippingMethodId,
                                'partner_id' => $partnerId,
                                'website_id' => $currentWebsiteId,
//                                'cost' => (float)$params['cost'],
                            ];
                        } else {
                            $temp = [
                                'dest_country_id' => $params['dest_country_id'],
                                'dest_region_id' => $params['dest_region_id'],
                                'dest_zip' => $params['dest_zip'],
                                'dest_zip_to' => $params['dest_zip_to'],
                                'price' => (float)$params['price'],
                                'cart_total' => $params['cart_total'] ? (float)$params['cart_total'] : '',
                                'free_shipping' => $params['free_shipping'] ? (float)$params['free_shipping'] : '',
                                'weight_from' => (float)$params['weight_from'],
                                'weight_to' => (float)$params['weight_to'],
                                'shipping_method_id' => $shippingMethodId,
                                'partner_id' => $partnerId,
                                'website_id' => $currentWebsiteId,
//                                'cost' => (float)$params['cost'],
                            ];
                        }

                        $shippingCollection = $this->_mpshippingModel->create()
                            ->getCollection()
                            ->addFieldToFilter('partner_id', $partnerId)
                            ->addFieldToFilter('dest_country_id', $params['dest_country_id'])
                            ->addFieldToFilter('dest_region_id', $params['dest_region_id'])
                            ->addFieldToFilter('dest_zip', $params['dest_zip'])
                            ->addFieldToFilter('dest_zip_to', $params['dest_zip_to'])
                            ->addFieldToFilter('weight_from', (float)$params['weight_from'])
                            ->addFieldToFilter('weight_to', (float)$params['weight_to'])
                            ->addFieldToFilter('shipping_method_id', $shippingMethodId)
                            ->addFieldToFilter('cart_total', (float)$params['cart_total'])
                            ->addFieldToFilter('website_id', $currentWebsiteId);

                        if ($shippingCollection->getsize() > 0) {
                            foreach ($shippingCollection as $row) {
                                $dataArray = [
                                    'price' => (float)$temp['price'],
//                                    'cost' => (float)$temp['cost'],
                                    'free_shipping' => $temp['free_shipping'] ? (float)$temp['free_shipping'] : ''
                                ];

                                $_shippingModel = $this->_mpshippingModel->create()->load($row->getLofmpshippingId());
                                $_shippingModel->addData($dataArray);
                                $_shippingModel->save();
                            }
                        } else {
                            $shippingModel->setData($temp);
                            $shippingModel->save();
                        }
                        $this->messageManager->addSuccessMessage(
                            __('Your shipping detail has been successfully saved!')
                        );
                        // check if 'Save and Continue'
                        if ($this->getRequest()->getParam('back')) {
                            return $this->resultRedirectFactory->create()->setPath(
                                '*/*/edit',
                                ['lofmpshipping_id' => $params['lofmpshipping_id']]
                            );
                        }
                        return $this->resultRedirectFactory->create()->setPath('lofmptablerateshipping/shipping/view');
                    }
                } catch (\Exception $e) {
                    $this->messageManager->addErrorMessage($e->getMessage());
                    return $this->resultRedirectFactory->create()->setPath('lofmptablerateshipping/shipping/view');
                }
            } else {
                return $this->resultRedirectFactory->create()->setPath('lofmptablerateshipping/shipping/view');
            }
        } elseif ($this->_customerSession->isLoggedIn() && $status == 0) {
            $this->_redirectUrl($this->getFrontendUrl('lofmarketplace/seller/becomeseller'));
        } else {
            $this->messageManager->addNotice(__('You must have a seller account to access'));
            $this->_redirectUrl($this->getFrontendUrl('lofmarketplace/seller/login'));
        }
        return $this->resultRedirectFactory->create()->setPath('lofmptablerateshipping/shipping/view');
    }

    /**
     * @param $seller
     * @param $result
     * @return \Magento\Framework\Controller\Result\Redirect|void
     * @throws \Exception
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function runImportCsvFile($seller, $result)
    {
        $file = $result['tmp_name'];
        $fileNameArray = explode('.', $result['name']);

        $ext = end($fileNameArray);
        if ($file != '' && $ext == 'csv') {
            $csvFileData = $this->_csvReader->getData($file);
            $partnerId = $seller->getId();
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
                        __('Your shipping detail has been successfully saved')
                    );
            }
            return $this->resultRedirectFactory->create()->setPath('*/*/index');
        } else {
            $this->messageManager->addErrorMessage(__('Please upload Csv file'));
        }
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
        $collection = $this->_mpshippingModel->create()
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
                $this->_mpshippingModel->create()
                    ->load($row->getLofmpshippingId())
                    ->addData($dataArray)
                    ->save();
            }
        } else {
            $this->_mpshippingModel->create()->setData($temp)->save();
        }
    }

    /**
     * @param $shippingMethodName
     * @param $partnerId
     * @return int
     */
    public function getShippingNameById($shippingMethodName, $partnerId)
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
     * @param $shippingMethodName
     * @param $partnerId
     * @return int|null
     * @throws \Exception
     */
    public function calculateShippingMethodId($shippingMethodName, $partnerId)
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
     * @param string $route
     * @param array $params
     * @return string|null
     */
    public function getFrontendUrl($route = '', $params = [])
    {
        return $this->_frontendUrl->getUrl($route, $params);
    }
}
