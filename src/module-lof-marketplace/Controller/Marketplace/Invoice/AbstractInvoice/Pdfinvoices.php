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

namespace Lof\MarketPlace\Controller\Marketplace\Invoice\AbstractInvoice;

use Magento\Framework\App\ResponseInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\App\Action\Context;
use Magento\Sales\Model\ResourceModel\Order\Invoice\CollectionFactory as SaleInvoiceCollectionFactory;
use Magento\Customer\Model\Session;
use Magento\Customer\Model\Url as CustomerUrl;
use Magento\Framework\Url;
use Magento\Store\Model\App\Emulation;
use Lof\MarketPlace\Model\Order\Pdf\Invoice;
use Lof\MarketPlace\Model\SellerFactory;
use Lof\MarketPlace\Model\ResourceModel\Invoice\CollectionFactory;
use Lof\MarketPlace\Model\OrderitemsFactory;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
abstract class Pdfinvoices extends \Lof\MarketPlace\Controller\Marketplace\AbstractMarketplaceAction
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const SELLER_RESOURCE = 'Lof_MarketPlace::invoice_pdf';

    /**
     * @var FileFactory
     */
    protected $fileFactory;

    /**
     * @var DateTime
     */
    protected $dateTime;

    /**
     * @var Invoice
     */
    protected $pdfInvoice;

    /**
     * @var SaleInvoiceCollectionFactory
     */
    protected $saleInvoiceCollectionFactory;

    /**
     * @var OrderitemsFactory
     */
    protected $orderItemsFactory;

    /**
     * @var Emulation
     */
    protected $appEmulation;

    /**
     * @param Context $context
     * @param Filter $filter
     * @param DateTime $dateTime
     * @param FileFactory $fileFactory
     * @param Invoice $pdfInvoice
     * @param CollectionFactory $collectionFactory
     * @param Session $customerSession
     * @param CustomerUrl $customerUrl
     * @param SellerFactory $sellerFactory
     * @param Url $url
     * @param SaleInvoiceCollectionFactory $saleInvoiceCollectionFactory
     * @param OrderitemsFactory $orderItemsFactory
     * @param Emulation $appEmulation
     */
    public function __construct(
        Context $context,
        Filter $filter,
        DateTime $dateTime,
        FileFactory $fileFactory,
        Invoice $pdfInvoice,
        CollectionFactory $collectionFactory,
        Session $customerSession,
        CustomerUrl $customerUrl,
        SellerFactory $sellerFactory,
        Url $url,
        SaleInvoiceCollectionFactory $saleInvoiceCollectionFactory,
        OrderitemsFactory $orderItemsFactory,
        Emulation $appEmulation
    ) {
        $this->fileFactory = $fileFactory;
        $this->dateTime = $dateTime;
        $this->pdfInvoice = $pdfInvoice;
        $this->collectionFactory = $collectionFactory;
        $this->saleInvoiceCollectionFactory = $saleInvoiceCollectionFactory;
        $this->orderItemsFactory = $orderItemsFactory;
        $this->appEmulation = $appEmulation;
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
     * Save collection items to pdf invoices
     *
     * @param AbstractCollection $collection
     * @return ResponseInterface|$this
     * @throws \Exception
     */
    public function massAction(AbstractCollection $collection)
    {
        $saleCollection = $this->saleInvoiceCollectionFactory->create();
        if ($collection->getSize()) {
            $ids = [];
            foreach ($collection as $_item) {
                $ids[] = $_item->getInvoiceId();
            }
            $saleCollection->addFieldToFilter("entity_id", ["in" => $ids]);
        }
        $seller = $this->getCurrentSeller();
        $sellerId = $seller->getId();
        $pdf = $this->pdfInvoice->setCurrentSellerId($sellerId)
                                ->setOrderItems($this->orderItemsFactory)
                                ->setAppEmulation($this->appEmulation)
                                ->getPdf($saleCollection);

        $fileContent = ['type' => 'string', 'value' => $pdf->render(), 'rm' => true];

        return $this->fileFactory->create(
            sprintf('invoice%s.pdf', $this->dateTime->date('Y-m-d_H-i-s')),
            $fileContent,
            DirectoryList::VAR_DIR,
            'application/pdf'
        );
    }
}
