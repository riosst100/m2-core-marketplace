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
 * @package    Lof_MarketPermissions
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lof\MarketPermissions\Observer;

use Lof\MarketPermissions\Model\SellerContext;
use Magento\Backend\App\AbstractAction;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\ActionFlag;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\UrlInterface;

class PredispatchMarketPermissionsObserver implements ObserverInterface
{

    /**
     * Status mapper
     *
     * @var array
     */
    public static $marketPermissionsMapper = [
        'Lof_MarketPlace::dashboard_view' => 'catalog_dashboard_index',
        'Lof_MarketPlace::profile_view' => 'catalog_seller_index',
        'Lof_MarketPlace::profile_edit' => 'catalog_saveprofile_index',
        'Lof_MarketPlace::orders_view' => 'catalog_sales_order',
        'Lof_MarketPlace::orders_edit' => 'catalog_sales_orderview',
        'Lof_MarketPlace::orders_send_email' => 'catalog_order_email',
        'Lof_MarketPlace::orders_create_invoice' => 'catalog_order_invoice',
        'Lof_MarketPlace::orders_create_ship' => 'catalog_order_ship',
        'Lof_MarketPlace::invoice_view' => 'catalog_sales_index',
        'Lof_MarketPlace::invoice_edit' => 'catalog_sales_invoiceview',
        'Lof_MarketPlace::shipments_view' => 'catalog_sales_shipment',
        'Lof_MarketPlace::shipments_edit' => 'catalog_sales_shipmentview',
        'Lof_MarketPlace::amounttransaction_view' => 'catalog_sales_amounttransaction',
        'Lof_MarketPlace::manager_product_view' => 'catalog_product_index',
        'Lof_MarketPlace::manager_product_edit' => 'catalog_product_edit',
        'Lof_MarketPlace::manager_product_add' => 'catalog_product_new',
        'Lof_MarketPlace::manager_product_save' => 'catalog_product_save',
        'Lof_MarketPlace::manager_product_delete' => 'catalog_product_massDelete',
        'Lof_MarketPlace::manager_product_status' => 'catalog_product_massStatus',
        'Lof_MarketPlace::withdrawals_view' => 'catalog_withdrawals_index',
        'Lof_MarketPlace::withdrawals_request' => 'catalog_withdrawals_payment',
        'Lof_MarketPlace::withdrawals_history' => 'catalog_withdrawals_viewtransaction',
        'Lof_MarketPlace::message_admin_view' => 'catalog_message_admin',
        'Lof_MarketPlace::message_admin_send' => 'catalog_messageadmin_save',
        'Lof_MarketPlace::message_customer_view' => 'catalog_message_index',
        'Lof_MarketPlace::message_customer_send' => 'catalog_message_save',
        'Lof_MarketPlace::send_admin' => 'catalog_message_save',
        'Lof_MarketPlace::review_view' => 'catalog_review_index',
        'Lof_MarketPlace::review_edit' => 'catalog_review_view',
        'Lof_MarketPlace::rating_view' => 'catalog_rating_index',
        'Lof_MarketPlace::rating_edit' => 'catalog_rating_view',
        'Lof_MarketPlace::vacation_save' => 'catalog_vacation_save',
        'Lof_MarketPlace::uploadimage_image' => 'catalog_product_saveimage',
        'Lof_MarketPlace::uploadimage_imagezip' => 'catalog_product_upload',
        'Lof_MarketPlace::import_validate' => 'catalog_product_validateimport',
        'Lof_MarketPlace::export_file' => 'catalog_product_processexport',
        'Lof_MarketPlace::report_view' => 'catalog_report_index'
    ];

    /**
     * @var AbstractAction|null
     */
    private $action;

    /**
     * @var ActionFlag
     */
    private $actionFlag;

    /**
     * @var UrlInterface
     */
    private $url;

    /**
     * @var SellerContext
     */
    private $sellerContext;

    /**
     * PredispatchMarketPermissionsObserver constructor.
     *
     * @param ActionFlag $actionFlag
     * @param UrlInterface $url
     * @param SellerContext $sellerContext
     */
    public function __construct(
        ActionFlag $actionFlag,
        UrlInterface $url,
        SellerContext $sellerContext
    ) {
        $this->url = $url;
        $this->actionFlag = $actionFlag;
        $this->sellerContext = $sellerContext;
    }

    /**
     * Redirect user to given URL.
     *
     * @param string $url
     * @return void
     */
    private function redirect(string $url): void
    {
        $this->actionFlag->set('', Action::FLAG_NO_DISPATCH, true);
        $this->action->getResponse()->setRedirect($this->url->getUrl($url));
    }

    /**
     * @param Observer $observer
     * @return PredispatchMarketPermissionsObserver|void
     */
    public function execute(Observer $observer)
    {

        if (!$this->sellerContext->isModuleActive()) {
            return;
        }

        if (!$this->sellerContext->isCurrentUserSellerUser()) {
            return;
        }

        $controllerAction = $observer->getData('controller_action');
        $this->action = $controllerAction;
        $request = $observer->getEvent()->getData('request');

        if ($request->isAjax()) {
            return;
        }

        $fullActionName = $request->getFullActionName();

        $resource = $this->getResourceFromActionName($fullActionName);
        if ($resource) {
            $isAllowed = $this->sellerContext->isResourceAllowed($resource);

            if (!$isAllowed) {
                return $this->redirect('permissions/accessdenied');
            }
        }

        return $this;
    }

    /**
     * @param $fullActionName
     * @return int|string
     */
    private function getResourceFromActionName($fullActionName)
    {
        $marketPermissionsMapper = array_flip(self::$marketPermissionsMapper);
        if (!isset($marketPermissionsMapper[$fullActionName])) {
            return false;
        }

        return $marketPermissionsMapper[$fullActionName];
    }
}
