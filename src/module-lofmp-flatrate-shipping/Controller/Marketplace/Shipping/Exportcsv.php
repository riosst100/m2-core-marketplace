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

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Lofmp\FlatRateShipping\Helper\Data;
use Magento\Customer\Model\Url;
use Magento\Customer\Model\Session;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Url as FrontendUrl;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Exportcsv extends \Lofmp\FlatRateShipping\Controller\Marketplace\Shipping
{
    const FLAG_IS_URLS_CHECKED = 'check_url_settings';

    /**
     * @var \Magento\Framework\View\LayoutInterface
     */
    protected $_layout;

    /**
     * @var \Lofmp\FlatRateShipping\Model\ShippingFactory
     */
    protected $shippingFactory;

    /**
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    protected $fileFactory;

    /**
     * @var Filesystem\Directory\WriteInterface
     */
    protected $directory;

    /**
     * @var \Lofmp\FlatRateShipping\Model\ShippingmethodFactory
     */
    protected $shippingmethodFactory;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var Url
     */
    protected $_customerUrl;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var FrontendUrl
     */
    protected $_frontendUrl;

    /**
     * @var Session
     */
    protected $_session;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\View\LayoutInterface $layout
     * @param Filesystem $filesystem
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     * @param \Lofmp\FlatRateShipping\Model\ShippingFactory $shippingFactory
     * @param \Lofmp\FlatRateShipping\Model\ShippingmethodFactory $shippingmethodFactory
     * @param Data $helper
     * @param Session $customerSession
     * @param Url $customerUrl
     * @param FrontendUrl $frontendUrl
     * @throws \Magento\Framework\Exception\FileSystemException
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\LayoutInterface $layout,
        Filesystem $filesystem,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Lofmp\FlatRateShipping\Model\ShippingFactory $shippingFactory,
        \Lofmp\FlatRateShipping\Model\ShippingmethodFactory $shippingmethodFactory,
        Data $helper,
        Session $customerSession,
        Url $customerUrl,
        FrontendUrl $frontendUrl
    ) {
        parent::__construct($context, $coreRegistry);
        $this->_layout = $layout;
        $this->directory = $filesystem->getDirectoryWrite(DirectoryList::VAR_DIR);
        $this->fileFactory = $fileFactory;
        $this->shippingFactory = $shippingFactory;
        $this->shippingmethodFactory = $shippingmethodFactory;
        $this->_customerUrl = $customerUrl;
        $this->_session = $customerSession;
        $this->helper = $helper;
        $this->_frontendUrl = $frontendUrl;
    }

    /**
     * @param RequestInterface $request
     * @return \Magento\Framework\App\ResponseInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function dispatch(RequestInterface $request)
    {
        $model = $this->_customerUrl;
        $url = $model->getLoginUrl();
        if (!$this->_session->authenticate($url)) {
            $this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);
        }

        return parent::dispatch($request);
    }

    /**
     * Redirect to URL
     * @param string $url
     * @return \Magento\Framework\App\ResponseInterface
     */
    protected function _redirectUrl($url)
    {
        $this->getResponse()->setRedirect($url);
        $this->_session->setIsUrlNotice($this->_actionFlag->get('', self::FLAG_IS_URLS_CHECKED));
        return $this->getResponse();
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Redirect|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $seller = $this->helper->getSeller();
        $status = $seller ? $seller->getStatus() : 0;
        if ($this->_session->isLoggedIn() && $status == 1) {
            try {
                // init model and delete
                $partnerId = $seller ? $seller->getId() : 0;
                if (!$partnerId) {
                    $this->_redirectUrl($this->getFrontendUrl('lofmarketplace/seller/becomeseller'));
                    return;
                }
                $collection = $this->shippingFactory->create()->getCollection()
                    ->addFieldToFilter('partner_id', $partnerId);
                $params = [];
                foreach ($collection as $model) {
                    $params[] = $model->getData();
                }
                $name = 'shippinginfo';
                $file = 'export/flatrateshipping/' . $name . '.csv';

                $this->directory->create('export');
                $stream = $this->directory->openFile($file, 'w+');
                $stream->lock();
                $fields = [];
                $headers = [
                    'title',
                    'type',
                    'free_shipping',
                    'sort_order',
                    'price',
                    'status',
                ];
                $stream->writeCsv($headers);
                foreach ($params as $row) {
                    $rowData = $fields;
                    $rowData['title'] = $row['title'];
                    $rowData['type'] = $row['type'];
                    $rowData['free_shipping'] = $row['free_shipping'];
                    $rowData['sort_order'] = $row['sort_order'];
                    $rowData['price'] = $row['price'];
                    $rowData['status'] = $row['status'];
                    $stream->writeCsv($rowData);
                }
                $stream->unlock();
                $stream->close();
                $file = [
                    'type' => 'filename',
                    'value' => $file,
                    'rm' => true  // can delete file after use
                ];
                // display success message
                $this->messageManager->addSuccessMessage(__('You export table rate shipping to csv success.'));
                return $this->fileFactory->create($name . '.csv', $file, 'var');

            } catch (\Exception $e) {
                // display error message
                $this->messageManager->addErrorMessage($e->getMessage());
                // go back to edit form
                return $resultRedirect->setPath('*/*/view');
            }
            // display error message
            $this->messageManager->addErrorMessage(__('We can\'t find a table rate shipping to exportcsv.'));
            // go to grid
            return $resultRedirect->setPath('*/*/');
        } elseif ($this->_session->isLoggedIn() && $status == 0) {
            $this->_redirectUrl($this->getFrontendUrl('lofmarketplace/seller/becomeseller'));
        } else {
            $this->messageManager->addNoticeMessage(__('You must have a seller account to access'));
            $this->_redirectUrl($this->getFrontendUrl('lofmarketplace/seller/login'));
        }
    }

    /**
     * @param $shippingMethodId
     * @return mixed
     */
    public function getShippingNameById($shippingMethodId)
    {
        $shippingMethodModel = $this->shippingmethodFactory->create()->load($shippingMethodId);
        return $shippingMethodModel && $shippingMethodModel->getId()
            ? $shippingMethodModel->getMethodName()
            : $shippingMethodId;
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
