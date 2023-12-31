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
 * @package    Lof_MarketPlace
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lof\MarketPlace\Controller\Marketplace\Product;

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\ImportExport\Model\Export as ExportModel;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\LocalizedException;
use Magento\Catalog\Model\Product;

class Processexport extends Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Magento_ImportExport::export';

    /**
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    protected $fileFactory;

    /**
     * @var \Lof\MarketPlace\Helper\Data
     */
    protected $helper;

    /**
     * @var Product
     */
    protected $collectionFactory;

    /**
     * Processexport constructor.
     *
     * @param Context $context
     * @param Product $collectionFactory
     * @param \Lof\MarketPlace\Helper\Data $helper
     * @param FileFactory $fileFactory
     */
    public function __construct(
        Context $context,
        Product $collectionFactory,
        \Lof\MarketPlace\Helper\Data $helper,
        FileFactory $fileFactory
    ) {
        $this->helper = $helper;
        $this->fileFactory = $fileFactory;
        $this->collectionFactory = $collectionFactory;
        parent::__construct($context);
    }

    /**
     * @inheritDoc
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function execute()
    {
        if (count($this->collectionFactory->getCollection()
                ->addFieldToFilter('seller_id', $this->helper->getSellerId())->getData()) == 0) {
            $this->messageManager->addErrorMessage(__('There is no data for the export.'));

            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setPath('*/*/export');
        }

        $data = [
            "activity" => "",
            "approval" => "",
            "category_gear" => "",
            "category_ids" => "",
            "climate" => "",
            "collar" => "",
            "color" => "",
            "cost" => [
                0 => "",
                1 => ""
            ],
            "country_of_manufacture" => "",
            "created_at" => [
                0 => "",
                1 => ""
            ],
            "custom_design" => "",
            "custom_design_from" => [
                0 => "",
                1 => ""
            ],
            "custom_design_to" => [
                0 => "",
                1 => ""
            ],
            "custom_layout" => "",
            "custom_layout_update" => "",
            "description" => "",
            "eco_collection" => "",
            "erin_recommends" => "",
            "features_bags" => "",
            "format" => "",
            "gallery" => "",
            "gender" => "",
            "gift_message_available" => "",
            "has_options" => "",
            "links_exist" => [
                0 => "",
                1 => ""
            ],
            "links_purchased_separately" => [
                0 => "",
                1 => ""
            ],
            "links_title" => "",
            "material" => "",
            "media_gallery" => "",
            "meta_description" => "",
            "meta_keyword" => "",
            "meta_title" => "",
            "minimal_price" => [
                0 => "",
                1 => ""
            ],
            "msrp" => [
                0 => "",
                1 => ""
            ],
            "msrp_display_actual_price_type" => "",
            "name" => "",
            "new" => "",
            "news_from_date" => [
                0 => "",
                1 => ""
            ],
            "news_to_date" => [
                0 => "",
                1 => ""
            ],
            "old_id" => [
                0 => "",
                1 => ""
            ],
            "options_container" => "",
            "page_layout" => "",
            "pattern" => "",
            "performance_fabric" => "",
            "price" => [
                0 => "",
                1 => ""
            ],
            "price_type" => [
                0 => "",
                1 => ""
            ],
            "price_view" => "",
            "quantity_and_stock_status" => "",
            "required_options" => "",
            "sale" => "",
            "samples_title" => "",
            "seller_id" => $this->helper->getSellerId(),
            "shipment_type" => "",
            "short_description" => "",
            "size" => "",
            "sku" => "",
            "sku_type" => [
                0 => "",
                1 => ""
            ],
            "sleeve" => "",

            "special_from_date" => [
                0 => "",
                1 => ""
            ],
            "special_price" => [
                0 => "",
                1 => ""
            ],
            "special_to_date" => [
                0 => "",
                1 => ""
            ],
            "status" => "",
            "strap_bags" => "",
            "style_bags" => "",
            "style_bottom" => "",
            "style_general" => "",

            "tax_class_id" => "",
            "thumbnail" => "",
            "thumbnail_label" => "",
            "tier_price" => [
                0 => "",
                1 => ""
            ],
            "updated_at" => [
                0 => "",
                1 => ""
            ],
            "url_key" => "",
            "url_path" => "",
            "visibility" => "",
            "weight" => [
                0 => "",
                1 => ""
            ],
            "weight_type" => [
                0 => "",
                1 => ""
            ],
        ];

        if ($data) {
            try {
                /** @var $model \Magento\ImportExport\Model\Export */
                $model = $this->_objectManager->create(\Magento\ImportExport\Model\Export::class);
                $model->setData($this->getRequest()->getParams());

                return $this->fileFactory->create(
                    $model->getFileName(),
                    $model->export(),
                    DirectoryList::VAR_DIR,
                    $model->getContentType()
                );

            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
                $resultRedirect->setPath('*/*/export');
            } catch (\Exception $e) {
                $this->_objectManager->get(\Psr\Log\LoggerInterface::class)->critical($e);
                $this->messageManager->addErrorMessage(__('Please correct the data sent value.'));
                /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
                $resultRedirect->setPath('*/*/export');
            }
            /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setPath('*/*/export');
        }
        return null;
    }
}
