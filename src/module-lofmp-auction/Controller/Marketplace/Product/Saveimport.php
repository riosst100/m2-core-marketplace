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
 * @package    Lofmp_Auction
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */


namespace Lofmp\Auction\Controller\Marketplace\Product;

use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Lof\Auction\Model\ProductFactory;

class Saveimport extends \Magento\Framework\App\Action\Action
{
    /**
     *
     * @var Magento\Framework\App\Action\Session
     */
    protected $session;

    /**
     *
     * @var Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;
    /**
     *
     * @var \Lof\MarketPlace\Model\SalesFactory
     */
    protected $sellerFactory;

    const FLAG_IS_URLS_CHECKED = 'check_url_settings';

    protected $_frontendUrl;

    /**
     * @var \Magento\Framework\App\ActionFlag
     */
    protected $_actionFlag;
    /**
     * @var \Lof\Auction\Helper\Data
     */
    protected $_assignHelper;
    /**
     * @var \Lof\MarketPlace\Helper\Data
     */
    protected $marketHelper;

    /**
     *
     * @param Context $context
     * @param Magento\Framework\App\Action\Session $customerSession
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Lof\MarketPlace\Model\SellerFactory $sellerFactory,
        \Lof\MarketPlace\Helper\Data $marketHelper,
        \Magento\Framework\Url $frontendUrl,
        \Lof\Auction\Helper\Data $helper,
        UploaderFactory $fileUploader,
        \Magento\Framework\File\Csv $csvReader,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        ProductFactory $auction
    ) {
        parent::__construct($context);
        $this->_assignHelper = $helper;
        $this->marketHelper = $marketHelper;
        $this->_frontendUrl = $frontendUrl;
        $this->_actionFlag = $context->getActionFlag();
        $this->sellerFactory = $sellerFactory;
        $this->session = $customerSession;
        $this->resultPageFactory = $resultPageFactory;
        $this->_fileUploader = $fileUploader;
        $this->_csvReader = $csvReader;
        $this->_auction = $auction;
    }

    public function getFrontendUrl($route = '', $params = [])
    {
        return $this->_frontendUrl->getUrl($route, $params);
    }

    /**
     * Redirect to URL
     * @param string $url
     * @return \Magento\Framework\App\ResponseInterface
     */
    protected function _redirectUrl($url)
    {
        $this->getResponse()->setRedirect($url);
        $this->session->setIsUrlNotice($this->_actionFlag->get('', self::FLAG_IS_URLS_CHECKED));
        return $this->getResponse();
    }

    /**
     * Customer login form page
     *
     * @return \Magento\Framework\Controller\Result\Redirect|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {

        $customerSession = $this->session;
        $customerId = $customerSession->getId();
        $seller = $this->sellerFactory->create()->load($customerId, 'customer_id');
        $status = $seller->getStatus();

        if ($customerSession->isLoggedIn() && $status == 1) {
            if (isset($_FILES['import_file'])) {


                $uploader = $this->_fileUploader->create(
                    ['fileId' => 'import_file']
                );

                $result = $uploader->validateFile();

                $file = $result['tmp_name'];
                $fileNameArray = explode('.', $result['name']);

                $ext = end($fileNameArray);
                if ($file != '' && $ext == 'csv') {
                    $csvFileData = $this->_csvReader->getData($file);

                    $count = 0;
                    $csvData = [];
                    foreach ($csvFileData as $key => $rowData) {

                        if ($rowData[2] == $seller->getId() && $this->marketHelper->getSellerIdByProduct($rowData[0]) == $seller->getId()) {

                            if (count($rowData) < 19 && $count == 0) {
                                $this->messageManager->addError(__('Csv file is not a valid file!'));
                                return $this->resultRedirectFactory->create()->setPath('*/*/index');
                            }

                            if ($rowData[0] == '' ||
                                $rowData[1] == '' ||
                                $rowData[3] == '' ||
                                $rowData[4] == ''
                            ) {

                                ++$count;
                                continue;
                            }

                            $csvData['product_id'] = $rowData[0];
                            $csvData['customer_id'] = $rowData[1];
                            $csvData['seller_id'] = $seller->getId();
                            $csvData['min_amount'] = $rowData[3];
                            $csvData['starting_price'] = $rowData[4];
                            $csvData['reserve_price'] = $rowData[5];
                            $csvData['auction_status'] = $rowData[6];
                            $csvData['days'] = $rowData[7];
                            $csvData['min_qty'] = $rowData[8];
                            $csvData['max_qty'] = $rowData[9];
                            $csvData['start_auction_time'] = $rowData[10];
                            $csvData['stop_auction_time'] = $rowData[11];
                            $csvData['increment_opt'] = $rowData[12];
                            $csvData['increment_price'] = $rowData[13];
                            $csvData['auto_auction_opt'] = $rowData[14];
                            $csvData['status'] = $rowData[15];
                            $csvData['created_at'] = $rowData[16];
                            $csvData['buy_it_now'] = $rowData[17];
                            $csvData['featured_auction'] = $rowData[18];

                            $auction = $this->_auction->create();
                            $count_auction = $auction->getCollection()->addFieldToFilter('product_id', $rowData[0]);

                            if (count($count_auction) == 0) {
                                $auction->setData($csvData)->save();
                            }
                        }
                    }

                    if (($count - 1) > 1) {
                        $this->messageManager->addNotice(__('Some rows are not valid!'));
                    }
                    if (($count - 1) <= 1) {
                        $this->messageManager
                            ->addSuccess(
                                __('Your auction detail has been successfully Saved')
                            );
                    }
                    return $this->resultRedirectFactory->create()->setPath('*/*/index');
                }
            }

        } elseif ($customerSession->isLoggedIn() && $status == 0) {
            $this->_redirectUrl($this->getFrontendUrl('lofmarketplace/seller/becomeseller'));
        } else {
            $this->messageManager->addNotice(__('You must have a seller account to access'));
            $this->_redirectUrl($this->getFrontendUrl('lofmarketplace/seller/login'));
        }
    }
}
