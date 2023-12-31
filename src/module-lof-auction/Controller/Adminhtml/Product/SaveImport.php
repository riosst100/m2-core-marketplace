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

use Lof\Auction\Model\Product;
use Magento\Backend\App\Action;
use Lof\Auction\Model\ProductFactory;
use Magento\Framework\File\Csv;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Magento\MediaStorage\Model\File\UploaderFactory;

/**
 * Class Save
 * @package Lof\Auction\Controller\Adminhtml\Product
 */
class SaveImport extends Action
{
    /**
     * Core registry.
     *
     * @var Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var PageFactory
     */
    protected $_resultPageFactory;
    /**
     * @var Product
     */
    protected $_auction;
    /**
     * @var UploaderFactory
     */
    protected $_fileUploader;
    /**
     * @var Csv
     */
    protected $_csvReader;

    /**
     * @param Action\Context $context
     * @param PageFactory $resultPageFactory
     * @param ProductFactory $auction
     * @param UploaderFactory $fileUploader
     * @param Csv $csvReader
     */
    public function __construct(
        Action\Context $context,
        PageFactory $resultPageFactory,
        ProductFactory $auction,
        UploaderFactory $fileUploader,
        Csv $csvReader
    ) {
        parent::__construct($context);
        $this->_resultPageFactory = $resultPageFactory;
        $this->_auction = $auction;
        $this->_fileUploader = $fileUploader;
        $this->_csvReader = $csvReader;
    }

    /**
     * Check for is allowed.
     *
     * @return bool
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();

        if ($this->getRequest()->isPost()) {
            $files = $this->getRequest()->getFiles();
            if (count($files->toArray())) {
                $uploader = $this->_fileUploader->create(
                    ['fileId' => 'import_file']
                );
                $result = $uploader->validateFile();

                if ( !$result ||  !$this->_formKeyValidator->validate($this->getRequest())) {
                    return $this->resultRedirectFactory->create()->setPath('*/*/index');
                }

                $file = $result['tmp_name'];
                $fileNameArray = explode('.', $result['name']);

                $ext = end($fileNameArray);
                if ($file != '' && $ext == 'csv') {
                    try {
                        $csvFileData = $this->_csvReader->getData($file);
                        $count = 0;
                        $savedCount = 0;
                        $headingArray = [];
                        foreach ($csvFileData as $key => $rowData) {
                            if ($count == 0) {
                                foreach($rowData as $i=>$label){
                                    if(!isset($headingArray[$label])){
                                        $headingArray[$label] = $i;
                                    }
                                }
                                ++$count;
                                continue;
                            }
                            $rowItemData = [];
                            $rowItemData["product_id"] = $this->getRowValue($rowData, $headingArray, "product_id");
                            $rowItemData["product_id"] = (int)$rowItemData["product_id"];
                            $rowItemData["customer_id"] = $this->getRowValue($rowData, $headingArray, "customer_id");
                            $rowItemData["customer_id"] = (int)$rowItemData["customer_id"];
                            $rowItemData["seller_id"] = $this->getRowValue($rowData, $headingArray, "seller_id");
                            $rowItemData["seller_id"] = (int)$rowItemData["seller_id"];
                            $rowItemData["min_amount"] = (float)$this->getRowValue($rowData, $headingArray, "min_amount");
                            $rowItemData["starting_price"] = (float)$this->getRowValue($rowData, $headingArray, "starting_price");
                            $rowItemData["reserve_price"] = (float)$this->getRowValue($rowData, $headingArray, "reserve_price");
                            $rowItemData["auction_status"] = (int)$this->getRowValue($rowData, $headingArray, "auction_status");
                            $rowItemData["days"] = (int)$this->getRowValue($rowData, $headingArray, "days");
                            $rowItemData["min_qty"] = (int)$this->getRowValue($rowData, $headingArray, "min_qty");
                            $rowItemData["max_qty"] = (int)$this->getRowValue($rowData, $headingArray, "max_qty");
                            $rowItemData["start_auction_time"] = $this->getRowValue($rowData, $headingArray, "start_auction_time");
                            $rowItemData["stop_auction_time"] = $this->getRowValue($rowData, $headingArray, "stop_auction_time");
                            $rowItemData["increment_opt"] = (int)$this->getRowValue($rowData, $headingArray, "increment_opt");
                            $rowItemData["increment_price"] = $this->getRowValue($rowData, $headingArray, "increment_price");
                            $rowItemData["auto_auction_opt"] = (int)$this->getRowValue($rowData, $headingArray, "auto_auction_opt");
                            $rowItemData["status"] = (int)$this->getRowValue($rowData, $headingArray, "status");
                            $rowItemData["created_at"] = $this->getRowValue($rowData, $headingArray, "created_at");
                            $rowItemData["buy_it_now"] = (int)$this->getRowValue($rowData, $headingArray, "buy_it_now");
                            $rowItemData["featured_auction"] = (int)$this->getRowValue($rowData, $headingArray, "featured_auction");

                            $rowItemData["days"] = !empty( $rowItemData["days"] ) ?  $rowItemData["days"] : 1;
                            $rowItemData["min_qty"] = !empty( $rowItemData["min_qty"] ) ?  $rowItemData["min_qty"] : 1;
                            $rowItemData["max_qty"] = !empty( $rowItemData["max_qty"] ) ?  $rowItemData["max_qty"] : 1;

                            if (empty($rowItemData["product_id"]) ||
                                empty($rowItemData["starting_price"]) ||
                                empty($rowItemData["start_auction_time"]) ||
                                empty($rowItemData["stop_auction_time"])
                            ) {
                                ++$count;
                                continue;
                            }
                            $auction = $this->_auction->create();
                            $collection = $auction->getCollection();
                            $collection->addFieldToFilter('product_id', (int)$rowItemData["product_id"]);
                            if ($collection->getSize() == 0) {
                                $auction->setData($rowItemData)->save();
                                $savedCount++;
                            }
                        }
                        if ($savedCount) {
                            $this->messageManager
                                        ->addSuccess(
                                            __('Your auction products has been successfully imported.')
                                        );
                        } else {
                            $this->messageManager->addErrorMessage(__('Can not import auction products at now.'));
                        }
                        return $this->resultRedirectFactory->create()->setPath('*/*/index');
                    } catch (\Exception $e) {
                        $this->messageManager->addError($e->getMessage());
                    }
                }
            }
        }
        return $resultRedirect->setPath('*/*/index');
    }

    /**
     * Get import csv row value by heading
     *
     * @param mixed $rowData
     * @param mixed|array $headingArray
     * @param string $column_name
     * @return mixed
     */
    public function getRowValue($rowData, $headingArray, $column_name)
    {
        $rowIndex = isset($headingArray[$column_name]) ? $headingArray[$column_name] : -1;
        return isset($rowData[$rowIndex]) ? $rowData[$rowIndex] : "";
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Lof_Auction::save_import');
    }
}
