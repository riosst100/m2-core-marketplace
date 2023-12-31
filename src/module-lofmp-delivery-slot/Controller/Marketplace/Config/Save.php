<?php
/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * https://landofcoder.com/license
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Landofcoder
 * @package    Lofmp_DeliverySlot
 * @copyright  Copyright (c) 2021 Landofcoder (https://landofcoder.com/)
 * @license    https://landofcoder.com/LICENSE-1.0.html
 */
namespace Lofmp\DeliverySlot\Controller\Marketplace\Config;

use Lof\MarketPlace\Model\SellerFactory;
use Magento\Customer\Model\Session;
use Magento\Customer\Model\Url as CustomerUrl;
use Magento\Framework\Url;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Framework\App\Action\Context;
use Lofmp\DeliverySlot\Controller\Marketplace\DeliverySlot;
use Lofmp\DeliverySlot\Helper\Data;
use Magento\Store\Model\ScopeInterface;

use Magento\Framework\View\Result\PageFactory;

class Save extends DeliverySlot
{
    const SELLER_RESOURCE = 'Lofmp_DeliverySlot::lofmp_deliveryslot_config';

    protected $resultPageFactory;
    protected $scopeConfig;
    protected $messageManager;

    /**
     * @var Data
     */
    protected $helperData;
    
    public function __construct(
        Context $context,
        Session $customerSession,
        CustomerUrl $customerUrl,
        Filter $filter,
        SellerFactory $sellerFactory,
        Url $url,
        PageFactory $resultPageFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        Data $helperData,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        parent::__construct($context, $customerSession, $customerUrl, $filter, $url, $sellerFactory, $helperData);
        $this->resultPageFactory = $resultPageFactory;
        $this->scopeConfig = $scopeConfig;
        $this->messageManager = $messageManager;
        $this->helperData = $helperData;
    }

    public function execute()
    {
        $isActived = $this->isActiveSeler();
        if ($isActived) {
            $seller = $this->helperData->getSeller();
            $newConfigData = $this->getRequest()->getParams();
            try {
                /**
                 * Check store config exits
                 */
                if ($newConfigData) {
                    $storeId = $this->helperData->getStore()->getId();
                    foreach ($newConfigData as $key => $value) {
                        $path = str_replace("__","/", $key);
                        $path_array = explode("/",$path);
                        $group = $path_array[0];
                        $path_key = isset($path_array[1])?$path_array[1]:"";
                        if ($group == Data::XML_PATH_GROUP && $path_key) {
                            $existConfig = $this->helperData->getSellerConfig($path, $storeId, false);
                            $sellerConfigModel = $this->helperData->getSellerConfigModel();
                            if ($existConfig && isset($existConfig['setting_id']) && $existConfig['setting_id']) {
                                $sellerConfigModel->load((int)$existConfig['setting_id']);
                            }
                            $configData = [
                                "group" => $group,
                                "key" => $path_key,
                                "path" => $path,
                                "scope" => ScopeInterface::SCOPE_STORE,
                                "scope_id" => $storeId,
                                "seller_id" => $seller->getId(),
                                "value" => $value
                            ];
                            $sellerConfigModel->setData($configData);
                            $sellerConfigModel->save();
                        }
                    }
                }
                $this->messageManager->addSuccessMessage('Save configuration sucessfully!');
            } catch (\Exception $e) {
                $this->messageManager->addError('Unable save the configuration.');
            }
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setPath('deliveryslot/config/edit');
        }
        
    }
}
