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

use Lof\Auction\Helper\Data;
use Lof\Auction\Model\Product;
use Lof\MarketPlace\Model\SellerFactory;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Filesystem;
use Magento\Framework\Registry;
use Magento\Framework\Url;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class Exportcsv
 * Lofmp\Auction\Controller\Marketplace\Product
 */
class Exportcsv extends Action
{

    /**
     * @var Session
     */
    protected $session;

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var SellerFactory
     */
    protected $sellerFactory;

    /**
     *
     */
    const FLAG_IS_URLS_CHECKED = 'check_url_settings';

    /**
     * @var Url
     */
    protected $_frontendUrl;

    /**
     * @var \Magento\Framework\App\ActionFlag
     */
    protected $_actionFlag;
    /**
     * @var Data
     */
    protected $_assignHelper;

    /**
     * @var Registry
     */
    protected $_coreRegistry;
    /**
     * @var Filesystem\Directory\WriteInterface
     */
    private $directory;
    /**
     * @var FileFactory
     */
    private $fileFactory;

    /**
     *
     * @param Context $context
     * @param Session $customerSession
     * @param SellerFactory $sellerFactory
     * @param Url $frontendUrl
     * @param Data $helper
     * @param Registry $coreRegistry
     * @param PageFactory $resultPageFactory
     * @param Filesystem $filesystem
     * @param FileFactory $fileFactory
     * @throws FileSystemException
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        SellerFactory $sellerFactory,
        Url $frontendUrl,
        Data $helper,
        Registry $coreRegistry,
        PageFactory $resultPageFactory,
        Filesystem $filesystem,
        FileFactory $fileFactory
    ) {
        parent::__construct($context);
        $this->_coreRegistry = $coreRegistry;
        $this->_assignHelper = $helper;
        $this->_frontendUrl = $frontendUrl;
        $this->_actionFlag = $context->getActionFlag();
        $this->sellerFactory = $sellerFactory;
        $this->session = $customerSession;
        $this->resultPageFactory = $resultPageFactory;
        $this->directory = $filesystem->getDirectoryWrite(DirectoryList::VAR_DIR);
        $this->fileFactory = $fileFactory;
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
    /**
     * Redirect to URL
     * @param string $url
     * @return ResponseInterface
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
            /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultRedirectFactory->create();
            try {
                // init model and delete
                $collection = $this->_objectManager->create(Product::class)
                    ->getCollection()->addFieldToFilter('seller_id', $seller->getId());
                $params = [];
                foreach ($collection as $key => $model) {
                    $params[] = $model->getData();
                }
                $name = 'adminProductInfo';
                $file = 'export/aution/'. $name . '.csv';

                $this->directory->create('export');
                $stream = $this->directory->openFile($file, 'w+');
                $stream->lock();
                $headers = $fields = [];
                $headers = ['product_id','customer_id','seller_id','min_amount','starting_price',' reserve_price','auction_status','days','min_qty','max_qty','start_auction_time','stop_auction_time','increment_opt','increment_price','auto_auction_opt','status','created_at','buy_it_now','featured_auction'];
                $stream->writeCsv($headers);
                foreach ($params as $row) {
                    $rowData = $fields;
                    foreach ($row as $v) {
                        $rowData['product_id'] = $row['product_id'];
                        $rowData['customer_id'] = $row['customer_id'];
                        $rowData['seller_id'] = $row['seller_id'];
                        $rowData['min_amount'] = ($row['min_amount']);
                        $rowData['starting_price'] = $row['starting_price'];
                        $rowData['reserve_price'] = $row['reserve_price'];
                        $rowData['auction_status'] = $row['auction_status'];
                        $rowData['days'] = $row['days'];
                        $rowData['min_qty'] = $row['min_qty'];
                        $rowData['max_qty'] = $row['max_qty'];
                        $rowData['start_auction_time'] = ($row['start_auction_time']);
                        $rowData['stop_auction_time'] = $row['stop_auction_time'];
                        $rowData['increment_opt'] = $row['increment_opt'];
                        $rowData['increment_price'] = $row['increment_price'];
                        $rowData['auto_auction_opt'] = $row['auto_auction_opt'];
                        $rowData['status'] = ($row['status']);
                        $rowData['created_at'] = $row['created_at'];
                        $rowData['buy_it_now'] = $row['buy_it_now'];
                        $rowData['featured_auction'] = $row['featured_auction'];
                    }
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
                $this->messageManager->addSuccess(__('You export sms to csv success.'));
                return $this->fileFactory->create($name . '.csv', $file, 'var');
            } catch (\Exception $e) {
                // display error message
                $this->messageManager->addError($e->getMessage());
                // go back to edit form
                return $resultRedirect->setPath('*/*/index');
            }
            // display error message
            $this->messageManager->addError(__('We can\'t find a smslog to exportcsv.'));
            // go to grid
            return $resultRedirect->setPath('*/*/');
        } elseif ($customerSession->isLoggedIn() && $status == 0) {
            $this->_redirectUrl($this->getFrontendUrl('lofmarketplace/seller/becomeseller'));
        } else {
            $this->messageManager->addNotice(__('You must have a seller account to access'));
            $this->_redirectUrl($this->getFrontendUrl('lofmarketplace/seller/login'));
        }
    }
}
