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
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Sales\Model\Order\Pdf\Shipment;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Sales\Model\ResourceModel\Order\Shipment\CollectionFactory;
use Magento\Customer\Model\Session;
use Magento\Customer\Model\Url as CustomerUrl;
use Magento\Framework\Url;
use Lof\MarketPlace\Model\SellerFactory;

abstract class Pdfshipments extends \Lof\MarketPlace\Controller\Marketplace\AbstractMarketplaceAction
{
    /**
     * @var FileFactory
     */
    protected $fileFactory;

    /**
     * @var DateTime
     */
    protected $dateTime;

    /**
     * @var Shipment
     */
    protected $pdfShipment;

    /**
     * @param Context $context
     * @param Filter $filter
     * @param DateTime $dateTime
     * @param FileFactory $fileFactory
     * @param Shipment $shipment
     * @param CollectionFactory $collectionFactory
     * @param Session $customerSession
     * @param CustomerUrl $customerUrl
     * @param SellerFactory $sellerFactory
     * @param Url $url
     */
    public function __construct(
        Context $context,
        Filter $filter,
        DateTime $dateTime,
        FileFactory $fileFactory,
        Shipment $shipment,
        CollectionFactory $collectionFactory,
        Session $customerSession,
        CustomerUrl $customerUrl,
        SellerFactory $sellerFactory,
        Url $url
    ) {
        $this->fileFactory = $fileFactory;
        $this->dateTime = $dateTime;
        $this->pdfShipment = $shipment;
        $this->collectionFactory = $collectionFactory;
        parent::__construct($context, $customerSession, $customerUrl, $filter, $url, $sellerFactory);
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $isActived = $this->isActiveSeler(true);
        if ($isActived) {
            return parent::executeMassAction();
        }
        return false;
    }

    /**
     * @param AbstractCollection $collection
     * @return ResponseInterface|$this
     * @throws \Exception
     */
    public function massAction(AbstractCollection $collection)
    {
        $pdf = $this->pdfShipment->getPdf($collection);
        $fileContent = ['type' => 'string', 'value' => $pdf->render(), 'rm' => true];

        return $this->fileFactory->create(
            sprintf('packingslip%s.pdf', $this->dateTime->date('Y-m-d_H-i-s')),
            $fileContent,
            DirectoryList::VAR_DIR,
            'application/pdf'
        );
    }
}
