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
 * @package    Lof_Auction
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */
namespace Lof\Auction\Controller\Adminhtml\Product;

use Lof\Auction\Controller\Adminhtml\Product;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Filesystem;
use Magento\Framework\Registry;
use Magento\Framework\View\LayoutInterface;

/**
 * Class Exportcsv
 * @package Lof\Auction\Controller\Adminhtml\Product
 */
class Exportcsv extends Product
{
    /**
     * @var LayoutInterface
     */
    protected $_layout;
    /**
     * @var Filesystem\Directory\WriteInterface
     */
    private $directory;
    /**
     * @var FileFactory
     */
    private $fileFactory;

    /**
     * @param Context $context
     * @param Registry $coreRegistry
     * @param LayoutInterface $layout
     * @param Filesystem $filesystem
     * @param FileFactory $fileFactory
     * @throws FileSystemException
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        LayoutInterface $layout,
        Filesystem $filesystem,
        FileFactory $fileFactory
    ) {
        parent::__construct($context, $coreRegistry);
        $this->_layout = $layout;
        $this->directory = $filesystem->getDirectoryWrite(DirectoryList::VAR_DIR);
        $this->fileFactory = $fileFactory;
    }


    /**
     * @return Redirect|ResponseInterface|ResultInterface
     */
    public function execute()
    {
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        try {
            // init model and delete
            $collection = $this->_objectManager->create('Lof\Auction\Model\Product')->getCollection();
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
            $headers = ['product_id','customer_id','min_amount','starting_price',' reserve_price','auction_status','days','min_qty','max_qty','start_auction_time','stop_auction_time','increment_opt','increment_price','auto_auction_opt','status','created_at','buy_it_now','featured_auction'];
            $stream->writeCsv($headers);
            foreach ($params as $row) {
                $rowData = $fields;
                foreach ($row as $v) {
                    $rowData['product_id'] = $row['product_id'];
                    $rowData['customer_id'] = $row['customer_id'];
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
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Lof_Auction::export_csv');
    }
}
