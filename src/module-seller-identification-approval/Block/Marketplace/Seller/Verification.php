<?php

namespace CoreMarketplace\SellerIdentificationApproval\Block\Marketplace\Seller;

use Lof\MarketPlace\Model\Seller;
use Lofmp\SellerIdentificationApproval\Helper\Data;
use Lofmp\SellerIdentificationApproval\Model\ResourceModel\Attachment\Collection;
use Lofmp\SellerIdentificationApproval\Model\ResourceModel\Attachment\CollectionFactory;
use Magento\Customer\Model\Session;
use Magento\Framework\DataObject;
use Magento\Framework\File\Size;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

/**
 * @SuppressWarnings(PHPMD.CamelCasePropertyName)
 */

class Verification extends Template
{

    /**
     * @var Size
     */
    private $fileSize;

    /**
     * @var UrlInterface
     */
    protected $_urlInterface;

    /**
     * @var Data
     */
    protected $_helperConfig;

    /**
     * @var Seller
     */
    private $seller;

    /**
     * @var Session
     */
    private $session;

    /**
     * @var CollectionFactory
     */
    private $attachmentCollection;

    /**
     * Popup constructor
     *
     * @param Context $context
     * @param Size $fileSize
     * @param Data $_helperConfig
     * @param Seller $seller
     * @param Session $customerSession
     * @param CollectionFactory $attachmentCollection
     * @param UrlInterface $_urlInterface
     * @param array $data
     * @SuppressWarnings(PHPMD.CamelCaseParameterName)
     * @SuppressWarnings(PHPMD.CamelCaseVariableName)
     */
    public function __construct(
        Template\Context $context,
        Size $fileSize,
        Data $_helperConfig,
        Seller $seller,
        Session $customerSession,
        CollectionFactory $attachmentCollection,
        UrlInterface $_urlInterface,
        array $data = []
    ) {
        $this->fileSize = $fileSize;
        $this->_helperConfig = $_helperConfig;
        $this->seller = $seller;
        $this->session = $customerSession;
        $this->attachmentCollection = $attachmentCollection;
        $this->_urlInterface = $_urlInterface;
        parent::__construct($context, $data);
    }

    /**
     * Get max file size
     *
     * @param $type
     * @return float
     */
    public function getMaxFileSize($type)
    {
        return $this->fileSize->convertSizeToInteger($this->getMaxFileSizeMb($type) . 'M');
    }

    /**
     * Get allowed file extensions
     *
     * @param $type
     * @return string
     */
    public function getAllowedExtensions($type)
    {
        return $this->_helperConfig->getAllowedExtensions($type);
    }

    /**
     * Get max file size in Mb
     *
     * @param $type
     * @return float
     */
    public function getMaxFileSizeMb($type)
    {
        $configSize = $this->_helperConfig->getMaxFileSize($type);
        $phpLimit = $this->fileSize->getMaxFileSizeInMb();
        if ($configSize) {
            return min($configSize, $phpLimit);
        }

        return $phpLimit;
    }

    /**
     * @param string $type
     * @return mixed|bool|int
     */
    public function isEnable($type = null)
    {
        return $this->_helperConfig->isEnable($type);
    }

    /**
     * @param string $type
     * @param int $countFiles
     * @return mixed|bool|int
     */
    public function allowUpdate($type = null, $countFiles = 0)
    {
        return $this->_helperConfig->allowUpdate($type, $countFiles);
    }


    /**
     * @return DataObject
     */
    public function getSeller()
    {
        if ($sellerId = $this->getRequest()->getParam('seller_id')) {
            return $this->seller->load($sellerId);
        } else {
            return $this->seller->getCollection()->addFieldToFilter(
                'customer_id',
                $this->session->getId()
            )->getFirstItem();
        }
    }

    /**
     * @param $type
     * @return bool|Collection
     */
    public function getFileUpload($type)
    {
        $seller = $this->getSeller();
        if ($sellerId = $seller->getData('seller_id')) {
            $collection = $this->attachmentCollection->create();
            $collection->addFieldToFilter('seller_id', $sellerId)
                ->addFieldToFilter('identify_type', $type);
            return $collection;
        } else {
            return false;
        }
    }

    /**
     * @param $attachmentId
     * @return string
     */
    public function getAttachmentUrl($attachmentId)
    {
        return $this->_urlInterface->getUrl('*/*/downloadIdentify', ['attachment_id' => $attachmentId]);
    }

    /**
     * Returns Identification Types
     * @return array
     */
    public function getIdentificationTypes()
    {
        return $this->_helperConfig->getIdentificationTypes();
    }

    /**
     * @return mixed
     */
    public function isRequire()
    {
        return $this->_helperConfig->isRequire();
    }
}
