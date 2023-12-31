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

namespace Lofmp\FlatRateShipping\Controller\Marketplace\Shipping;

use Magento\Framework\App\Action\Action;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Data\Form\FormKey\Validator as FormKeyValidator;
use Magento\Framework\App\RequestInterface;
use Lofmp\FlatRateShipping\Model\ShippingmethodFactory;
use Magento\Customer\Model\Url;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Lofmp\FlatRateShipping\Model\ShippingFactory;
use Lofmp\FlatRateShipping\Helper\Data;
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
                        $params['title'] = isset($params['title']) && $params['title']
                            ? ($params['title'])
                            : '';
                        $params['type'] = isset($params['type']) && $params['type']
                            ? ($params['type'])
                            : '';
                        $params['sort_order'] = isset($params['sort_order']) && $params['sort_order']
                            ? ($params['sort_order'])
                            : '1';
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
                                'title' => $params['title'],
                                'type' => $params['type'],
                                'sort_order' => $params['sort_order'],
                                'free_shipping' => $params['free_shipping'] ? (float)$params['free_shipping'] : 0.0000,
                                'price' => (float)$params['price'],
                                'status' => $params['status'],
                                'partner_id' => $partnerId,
                                'website_id' => $currentWebsiteId,
                            ];
                        } else {
                            $temp = [
                                'title' => $params['title'],
                                'type' => $params['type'],
                                'sort_order' => $params['sort_order'],
                                'free_shipping' => $params['free_shipping'] ? (float)$params['free_shipping'] : 0.0000,
                                'price' => (float)$params['price'],
                                'status' => $params['status'],
                                'partner_id' => $partnerId,
                                'website_id' => $currentWebsiteId,
                            ];
                        }

                        $shippingModel->setData($temp);
                        $shippingModel->save();

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
                        return $this->resultRedirectFactory->create()->setPath('lofmpflatrateshipping/shipping/view');
                    }
                } catch (\Exception $e) {
                    $this->messageManager->addErrorMessage($e->getMessage());
                    return $this->resultRedirectFactory->create()->setPath('lofmpflatrateshipping/shipping/view');
                }
            } else {
                return $this->resultRedirectFactory->create()->setPath('lofmpflatrateshipping/shipping/view');
            }
        } elseif ($this->_customerSession->isLoggedIn() && $status == 0) {
            $this->_redirectUrl($this->getFrontendUrl('lofmarketplace/seller/becomeseller'));
        } else {
            $this->messageManager->addNoticeMessage(__('You must have a seller account to access'));
            $this->_redirectUrl($this->getFrontendUrl('lofmarketplace/seller/login'));
        }
        return $this->resultRedirectFactory->create()->setPath('lofmpflatrateshipping/shipping/view');
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
        $currentWebsiteId = $this->_storeManager->getStore()->getWebsiteId();
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
                $temp['title'] = $this->getRowValue($rowData, $headingArray, 'title');
                $temp['type'] = $this->getRowValue($rowData, $headingArray, 'type');
                $temp['price'] = (float)$this->getRowValue($rowData, $headingArray, 'price');
                $temp['partner_id'] = $partnerId;
                $temp['free_shipping'] = $this->getRowValue($rowData, $headingArray, 'free_shipping');
                $temp['sort_order'] = $this->getRowValue($rowData, $headingArray, 'sort_order');
                $temp['status'] = $this->getRowValue($rowData, $headingArray, 'status');
                $temp['website_id'] = $currentWebsiteId;

                if ($temp['title'] == '' ||
                    $temp['type'] == '' ||
                    $temp['price'] == '' ||
                    $temp['free_shipping'] == ''
                ) {
                    continue;
                }
                $this->addDataToCollection($temp, $temp['partner_id']);
            }
            if (($count - 1) > 1) {
                $this->messageManager->addNoticeMessage(__('Some rows are not valid!'));
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
     * @param $columnName
     * @return string
     */
    public function getRowValue($rowData, $headingArray, $columnName)
    {
        $rowIndex = isset($headingArray[$columnName]) ? $headingArray[$columnName] : -1;
        return isset($rowData[$rowIndex]) ? $rowData[$rowIndex] : "";
    }

    /**
     * @param $temp
     * @param $partnerId
     * @throws \Exception
     */
    public function addDataToCollection($temp, $partnerId)
    {
        $collection = $this->_mpshippingModel->create()
            ->getCollection()
            ->addFieldToFilter('type', $temp['type'])
            ->addFieldToFilter('price', $temp['price'])
            ->addFieldToFilter('free_shipping', $temp['free_shipping'])
            ->addFieldToFilter('website_id', $temp['website_id'])
            ->addFieldToFilter('partner_id', $partnerId);

        if ($collection->getSize() > 0) {
            foreach ($collection as $row) {
                $dataArray = [
                    'price' => $temp['price'],
                    'free_shipping' => $temp['free_shipping'] ? (float)$temp['free_shipping'] : 0.0000
                ];
                $this->_mpshippingModel->create()->load($row->getLofmpshippingId())->addData($dataArray)->save();
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
            ->addFieldToFilter('title', $shippingMethodName);
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
