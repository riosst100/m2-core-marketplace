<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Lof\MarketPlace\Controller\Marketplace\Shipment\AbstractShipment;

use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Backend\Model\View\Result\ForwardFactory;
use Magento\Customer\Model\Session;
use Magento\Customer\Model\Url as CustomerUrl;
use Magento\Framework\Url;
use Lof\MarketPlace\Model\SellerFactory;

abstract class PrintAction extends \Lof\MarketPlace\Controller\Marketplace\AbstractMarketplaceAction
{
    /**
     * @var FileFactory
     */
    protected $_fileFactory;

    /**
     * @var ForwardFactory
     */
    protected $resultForwardFactory;

    /**
     * @param Context $context
     * @param FileFactory $fileFactory
     * @param ForwardFactory $resultForwardFactory
     * @param Session $customerSession
     * @param CustomerUrl $customerUrl
     * @param SellerFactory $sellerFactory
     * @param Url $url
     */
    public function __construct(
        Context $context,
        FileFactory $fileFactory,
        ForwardFactory $resultForwardFactory,
        Session $customerSession,
        CustomerUrl $customerUrl,
        SellerFactory $sellerFactory,
        Url $url
    ) {
        $this->_fileFactory = $fileFactory;
        $this->resultForwardFactory = $resultForwardFactory;
        parent::__construct($context, $customerSession, $customerUrl, $filter, $url, $sellerFactory);
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Page|ResponseInterface|\Magento\Framework\Controller\Result\Redirect|\Magento\Framework\Controller\ResultInterface
     * @throws \Exception
     */
    public function execute()
    {
        $isActived = $this->isActiveSeler(true);
        if ($isActived) {
            $resultRedirect = $this->resultRedirectFactory->create();
            $shipmentId = $this->getRequest()->getParam('shipment_id');
            if ($shipmentId) {
                $shipment = $this->_objectManager->create(\Magento\Sales\Model\Order\Shipment::class)->load($shipmentId);
                if ($shipment) {
                    $pdf = $this->_objectManager->create(
                        \Magento\Sales\Model\Order\Pdf\Shipment::class
                    )->getPdf(
                        [$shipment]
                    );
                    $date = $this->_objectManager->get(
                        \Magento\Framework\Stdlib\DateTime\DateTime::class
                    )->date('Y-m-d_H-i-s');
                    $fileContent = ['type' => 'string', 'value' => $pdf->render(), 'rm' => true];

                    return $this->_fileFactory->create(
                        'packingslip' . $date . '.pdf',
                        $fileContent,
                        DirectoryList::VAR_DIR,
                        'application/pdf'
                    );
                }
            } else {
                $this->messageManager->addSuccessMessage('Page not found!');
                $resultRedirect->setPath('marketplace/catalog/dashboard');
                return $resultRedirect;
            }
        }
        return false;
    }
}
