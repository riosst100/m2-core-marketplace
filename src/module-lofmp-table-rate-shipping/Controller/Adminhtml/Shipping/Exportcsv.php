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

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;

class Exportcsv extends \Lofmp\TableRateShipping\Controller\Adminhtml\Shipping
{
    /**
     * @var \Magento\Framework\View\LayoutInterface
     */
    protected $_layout;

    /**
     * @var \Lofmp\TableRateShipping\Model\ShippingFactory
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
     * @var \Lofmp\TableRateShipping\Model\ShippingmethodFactory
     */
    protected $shippingmethodFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\View\LayoutInterface $layout
     * @param Filesystem $filesystem
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     * @param \Lofmp\TableRateShipping\Model\ShippingFactory $shippingFactory
     * @param \Lofmp\TableRateShipping\Model\ShippingmethodFactory $shippingmethodFactory
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\LayoutInterface $layout,
        Filesystem $filesystem,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Lofmp\TableRateShipping\Model\ShippingFactory $shippingFactory,
        \Lofmp\TableRateShipping\Model\ShippingmethodFactory $shippingmethodFactory
    ) {
        parent::__construct($context, $coreRegistry);
        $this->_layout = $layout;
        $this->directory = $filesystem->getDirectoryWrite(DirectoryList::VAR_DIR);
        $this->fileFactory = $fileFactory;
        $this->shippingFactory = $shippingFactory;
        $this->shippingmethodFactory = $shippingmethodFactory;
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Redirect|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        try {
            // init model and delete
            $collection = $this->shippingFactory->create()->getCollection();
            $params = [];
            foreach ($collection as $model) {
                $params[] = $model->getData();
            }
            $name = 'adminShippingInfo';
            $file = 'export/tablerateshipping/' . $name . '.csv';

            $this->directory->create('export');
            $stream = $this->directory->openFile($file, 'w+');
            $stream->lock();
            $headers = $fields = [];
            $headers = [
                'country_code',
                'region_id',
                'zip',
                'zip_to',
                'price',
                'weight_from',
                'weight_to',
                'shipping_method',
                'partner_id',
                'free_shipping'
            ];
            $stream->writeCsv($headers);
            foreach ($params as $row) {
                $shipping_method_name = $this->getShippingNameById($row["shipping_method_id"]);
                $rowData = $fields;
                $rowData['country_code'] = $row['dest_country_id'];
                $rowData['region_id'] = $row['dest_region_id'];
                $rowData['zip'] = strip_tags($row['dest_zip']);
                $rowData['zip_to'] = strip_tags($row['dest_zip_to']);
                $rowData['price'] = $row['price'];
                $rowData['weight_from'] = $row['weight_from'];
                $rowData['weight_to'] = $row['weight_to'];
                $rowData['shipping_method'] = strip_tags($shipping_method_name);
                $rowData['partner_id'] = $row['partner_id'];
                $rowData['free_shipping'] = $row['free_shipping'];
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
            return $resultRedirect->setPath('*/*/index');
        }
        // display error message
        $this->messageManager->addErrorMessage(__('We can\'t find a table rate shipping to exportcsv.'));
        // go to grid
        return $resultRedirect->setPath('*/*/');
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
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Lofmp_TableRateShipping::shipping');
    }
}
