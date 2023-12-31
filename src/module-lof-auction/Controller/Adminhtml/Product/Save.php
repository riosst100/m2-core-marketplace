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

use Exception;
use Lof\Auction\Helper\ProcessWinner;
use Lof\Auction\Model\Product;
use Lof\Auction\Model\ProductFactory;
use Lof\Auction\Model\ResourceModel\Product\Source\AuctionStatus;
use Magento\Backend\App\Action;
use Magento\Backend\Helper\Js;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\File\Csv;
use Magento\Framework\Filesystem;
use Magento\Framework\Stdlib\DateTime\Filter\DateTime;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Magento\Framework\App\CacheInterface;

/**
 * Class Save
 *
 * Lof\Auction\Controller\Adminhtml\Product
 */
class Save extends Action
{
    /**
     * @var Product
     */
    protected $_auction;

    /**
     * @var ProcessWinner
     */
    protected $_processWinnerHelper;

    /**
     * @var Filesystem
     */
    private $_fileSystem;

    /**
     * @var Js
     */
    private $jsHelper;

    /**
     * @var UploaderFactory
     */
    private $_fileUploader;

    /**
     * @var Csv
     */

    private $_csvReader;

    /**
     * @var DateTime
     */
    private $_dateFilter;

    /**
     * @var TimezoneInterface
     */
    private $timezone;

    /**
     * Save constructor.
     *
     * @param Action\Context    $context
     * @param Filesystem        $filesystem
     * @param Js                $jsHelper
     * @param UploaderFactory   $fileUploader
     * @param Csv               $csvReader
     * @param ProductFactory    $auction
     * @param ProcessWinner     $processWinner
     * @param DateTime          $dateFilter
     * @param TimezoneInterface $timezone
     */
    public function __construct(
        Action\Context $context,
        Filesystem $filesystem,
        Js $jsHelper,
        UploaderFactory $fileUploader,
        Csv $csvReader,
        ProductFactory $auction,
        ProcessWinner $processWinner,
        DateTime $dateFilter,
        TimezoneInterface $timezone
    ) {
        $this->_fileSystem          = $filesystem;
        $this->jsHelper             = $jsHelper;
        $this->_fileUploader        = $fileUploader;
        $this->_csvReader           = $csvReader;
        $this->_auction             = $auction;
        $this->_processWinnerHelper = $processWinner;
        $this->_dateFilter          = $dateFilter;
        $this->timezone             = $timezone;
        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Lof_Auction::product_save');
    }

    /**
     * Save action
     *
     * @return ResultInterface
     * @throws Exception
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        $resultRedirect = $this->resultRedirectFactory->create();
        if (isset($data['product_id']) && ! is_numeric($data['product_id'])) {
            $data['product_id'] = str_replace('product/', '', $data['product_id']);
        }
        if ($data && $this->validate($data)) {
            if ($this->getRequest()->getParam('back')) {
                $resultRedirect->setPath(
                    '*/*/edit',
                    ['id' => $this->getRequest()->getParam('auction_entity_id')]
                );
            }
            if (isset($_FILES['import_file'])) {
                $this->handleImport();
            } else {
                $model = $this->_objectManager->create('Lof\Auction\Model\Product');

                if (isset($data['auction_entity_id']) && $data['auction_entity_id']) {
                    $data['entity_id'] = $data['auction_entity_id'];
                } else {
                    unset($data['entity_id']);
                }

                if (! isset($data['entity_id']) && (! isset($data['product_id']) || ! $data['product_id'])) {
                    $this->_getSession()->setFormData($data);
                    $this->messageManager->addErrorMessage(__('Please choose a product.'));

                    return $resultRedirect;
                }

                $data['stop_auction_time'] = $this->convertToTz(
                    $data['stop_auction_time'],
                    $this->timezone->getDefaultTimezone(),
                    $this->timezone->getConfigTimezone()
                );

                $data['start_auction_time'] = $this->convertToTz(
                    $data['start_auction_time'],
                    $this->timezone->getDefaultTimezone(),
                    $this->timezone->getConfigTimezone()
                );

                if (! isset($data['max_amount']) || ! $data['max_amount']) {
                    $data['max_amount'] = null;
                }

                if (! isset($data['reserve_price']) || ! $data['reserve_price']) {
                    $data['reserve_price'] = null;
                }
                $id = 0;
                if (isset($data['entity_id']) && $data['entity_id']) {
                    $id = $data['entity_id'];
                    $model->load($id);
                    $data['product_id'] = $model->getProductId();
                    $model->setData($data);
                    $model->setAuctionStatus(AuctionStatus::STATUS_PROCESS);
                } else {
                    $model->setData($data);
                    $model->setAuctionStatus(AuctionStatus::STATUS_PROCESS);
                }

                if ($this->findAuction($data['product_id'], $id)) {
                    unset($data['product_id']);
                    $this->_getSession()->setFormData($data);
                    $this->messageManager->addErrorMessage(
                        __("Cannot save auction because there is another auction running with this product already")
                    );
                    return $resultRedirect->setPath('*/*/');
                }

                try {
                    if (!$model->getId()) {
                        $dateTime = $this->_objectManager->create(\Magento\Framework\Stdlib\DateTime::class);
                        $model->setData("created_at", $dateTime->formatDate(true));
                    }

                    $model->save();
                    $this->clearProductDetailCache($data['product_id']);

                    // Find Winner when auction end time
                    if ($this->_processWinnerHelper->checkIsStopDateEnd($model->getStopAuctionTime())) {
                        $this->_processWinnerHelper->processFindWinner($model->getProductId(), $model);
                    }
                    $this->_getSession()->setFormData(false);
                    $this->messageManager->addSuccessMessage(__('You saved this product.'));
                    return $resultRedirect->setPath('*/*/edit', ['id' => $model->getEntityId()]);

                } catch (\Magento\Framework\Exception\LocalizedException $e) {
                    $this->messageManager->addErrorMessage($e->getMessage());
                } catch (\RuntimeException $e) {
                    $this->messageManager->addErrorMessage($e->getMessage());
                } catch (\Exception $e) {
                    $this->messageManager->addExceptionMessage(
                        $e,
                        __('Something went wrong while saving the auction.')
                    );
                }
                if (isset($data['product_id'])) {
                    unset($data['product_id']);
                }
                $this->_getSession()->setFormData($data);

                return $resultRedirect->setPath('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);
            }
        }
        if ($this->getRequest()->getParam('back')) {
            if (isset($data['product_id']) && !isset($data['auction_entity_id'])) {
                unset($data['product_id']);
            }
            $this->_getSession()->setFormData($data);
            return $resultRedirect->setPath(
                '*/*/edit',
                ['id' => $this->getRequest()->getParam('auction_entity_id')]
            );
        }

        return $resultRedirect->setPath('*/*/');
    }

    /**
     * Clear product detail page cache
     * 
     * @param int $productId
     * @return null
     */
    protected function clearProductDetailCache($productId)
    {
        if ($productId == null) {
            return;
        }
        $pageCache = $this->_objectManager->create(\Magento\PageCache\Model\Cache\Type::class);
        $tags = ['cat_p_'.$productId];
        $cacheManager = $this->_objectManager->create(CacheInterface::class);
        $pageCache->clean(\Zend_Cache::CLEANING_MODE_MATCHING_TAG, $tags);
        $cacheManager->clean([\Lof\Auction\Model\Product::CACHE_TAG]);
    }

    /**
     * @param $data
     * @return bool
     */
    public function validate($data)
    {
        if (!isset($data['starting_price']) || !isset($data['start_auction_time'])
            || !isset($data['stop_auction_time']) || !isset($data['days']) || !isset($data['days'])
            || !isset($data['min_qty']) || !isset($data['max_qty'])
        ) {
            $this->messageManager->addErrorMessage(__('Please filter data.'));
            return false;
        }
        if ((int)$data['min_qty'] <= 0 || (int)$data['max_qty'] <= 0) {
            $this->messageManager->addErrorMessage(__('Minimum quantity and maximum quantity should bee equal or greater than 1.'));
            return false;
        }
        if (isset($data['max_amount']) && !!$data['max_amount'] && $data['max_amount'] < $data['starting_price']) {
            $this->messageManager->addErrorMessage(__('Starting price must be less than max price.'));
            return false;
        }
        if (isset($data['max_amount']) && !!$data['max_amount'] && isset($data['reserve_price'])
            && $data['max_amount'] <= $data['reserve_price']) {
            $this->messageManager->addErrorMessage(__('Max price must be greater than reserve price.'));
            return false;
        }
        if (strtotime($data['start_auction_time']) >= strtotime($data['stop_auction_time'])) {
            $this->messageManager->addErrorMessage(__('Start auction time must be less than stop auction time.'));
            return false;
        }
        if ($data['min_qty'] > $data['max_qty']) {
            $this->messageManager->addErrorMessage(__('Minimum quantity must be less than maximum quantity.'));
            return false;
        }
        return true;
    }

    /**
     * Handle save auction when import CSV file.
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     * @throws \Exception
     */
    public function handleImport()
    {
        if (! $this->_formKeyValidator->validate($this->getRequest())) {
            return $this->resultRedirectFactory->create()->setPath('*/*/index');
        }

        $uploader = $this->_fileUploader->create(
            ['fileId' => 'import_file']
        );

        $result = $uploader->validateFile();

        $file          = $result['tmp_name'];
        $fileNameArray = explode('.', $result['name']);

        $ext = end($fileNameArray);
        if ($file != '' && $ext == 'csv') {
            $csvFileData = $this->_csvReader->getData($file);

            $count   = 0;
            $csvData = [];
            foreach ($csvFileData as $key => $rowData) {
                if (count($rowData) < 18 && $count == 0) {
                    $this->messageManager->addError(__('Csv file is not a valid file!'));

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
                $csvData['product_id']         = $rowData[0];
                $csvData['customer_id']        = $rowData[1];
                $csvData['min_amount']         = $rowData[2];
                $csvData['starting_price']     = $rowData[3];
                $csvData['reserve_price']      = $rowData[4];
                $csvData['auction_status']     = $rowData[5];
                $csvData['days']               = $rowData[6];
                $csvData['min_qty']            = $rowData[7];
                $csvData['max_qty']            = $rowData[8];
                $csvData['start_auction_time'] = $rowData[9];
                $csvData['stop_auction_time']  = $rowData[10];
                $csvData['increment_opt']      = $rowData[11];
                $csvData['increment_price']    = $rowData[12];
                $csvData['auto_auction_opt']   = $rowData[13];
                $csvData['status']             = $rowData[14];
                $csvData['created_at']         = $rowData[15];
                $csvData['buy_it_now']         = $rowData[16];
                $csvData['featured_auction']   = $rowData[17];


                $auction       = $this->_auction->create();
                $count_auction = $auction->getCollection()->addFieldToFilter('product_id', $rowData[0]);

                if (count($count_auction) == 0) {
                    $auction->setData($csvData)->save();
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

    /**
     * converToTz convert Datetime from one zone to another
     *
     * @param string $dateTime which we want to convert
     * @param string $toTz     timezone in which we want to convert
     * @param string $fromTz   timezone from which we want to convert
     * @return string
     * @throws Exception
     */
    protected function convertToTz($dateTime = "", $toTz = '', $fromTz = '')
    {
        // timezone by php friendly values
        $date = new \DateTime($dateTime, new \DateTimeZone($fromTz));
        $date->setTimezone(new \DateTimeZone($toTz));
        $dateTime = $date->format('m/d/Y H:i:s');

        return $dateTime;
    }


    /**
     * @param $productId
     * @param int $id
     * @return bool
     */
    public function findAuction($productId, $id = 0)
    {
        $auctionCollection = $this->_auction->create()
            ->getCollection()
            ->addFieldToFilter(
                'auction_status',
                [
                    "in" => [
                        AuctionStatus::STATUS_WINNER_NOT_DEFINED,
                        AuctionStatus::STATUS_PROCESS,
                        AuctionStatus::STATUS_TIME_END,
                    ],
                ]
            )
            ->addFieldToFilter('product_id', $productId)
            ->addFieldToFilter('entity_id', ['neq' => $id]);
        return $auctionCollection->getData() ? true : false;
    }
}
