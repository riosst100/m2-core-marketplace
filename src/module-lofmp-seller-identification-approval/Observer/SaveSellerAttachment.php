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
 * @package    Lofmp_SellerIdentificationApproval
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lofmp\SellerIdentificationApproval\Observer;

use Lof\MarketPlace\Helper\Seller as SellerHelper;
use Lof\MarketPlace\Model\Seller;
use Lof\MarketPlace\Model\Sender;
use Lofmp\SellerIdentificationApproval\Model\AttachmentFactory;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Event\Observer;
use Lofmp\SellerIdentificationApproval\Helper\Data;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Filesystem\File\ReadFactory;

/**
 * @SuppressWarnings(PHPMD.CamelCasePropertyName)
 */
class SaveSellerAttachment implements ObserverInterface
{
    /**
     * @var SellerHelper
     */
    private $_sellerHelper;

    /**
     * @var Data
     */
    private $_helper;

    /**
     * @var Sender
     */
    private $_sender;

    /**
     * @var \Lof\MarketPlace\Helper\Data
     */
    private $_marketplaceHelper;

    /**
     * @var ReadFactory
     */
    private $_readFactory;

    /**
     * @var AttachmentFactory
     */
    private $_attachmentFactory;

    /**
     * SaveSellerAttachment constructor.
     * @param SellerHelper $sellerHelper
     * @param Data $helper
     * @param \Lof\MarketPlace\Helper\Data $marketplaceHelper
     * @param Sender $sender
     * @param ReadFactory $readFactory
     * @param AttachmentFactory $attachmentFactory
     */
    public function __construct(
        SellerHelper $sellerHelper,
        Data $helper,
        \Lof\MarketPlace\Helper\Data $marketplaceHelper,
        Sender $sender,
        ReadFactory $readFactory,
        AttachmentFactory $attachmentFactory
    ) {
        $this->_sellerHelper = $sellerHelper->getHelperData();
        $this->_helper = $helper;
        $this->_sender = $sender;
        $this->_marketplaceHelper = $marketplaceHelper;
        $this->_readFactory = $readFactory;
        $this->_attachmentFactory = $attachmentFactory;
    }

    /**
     * @param Observer $observer
     * @return $this|void
     * @throws NoSuchEntityException
     */
    public function execute(Observer $observer)
    {
        if (!$this->_helper->isEnable(null)) {
            return;
        }

        if ($this->isAction($observer)) {
            $sellerApproval = $this->_sellerHelper->getConfig('general_settings/seller_approval');
            $seller = $observer->getData('seller');
            if ($sellerApproval && $this->_helper->isRequire()) {
                $this->disapprovedSeller($seller->getData('seller_id'));
            }
        }
    }

    /**
     * @param $observer
     * @return bool
     */
    public function isAction($observer)
    {
        $seller = $observer->getData('seller');
        $request = $observer->getData('account_controller')->getRequest();

        if ($this->isIdentificationChanged($request, $seller)) {
            return true;
        }

        if ($this->isFilesChanged($request)) {
            return true;
        }

        if ($this->isDeleted($request)) {
            return true;
        }

        return false;
    }

    /**
     * @param $sellerId
     * @throws NoSuchEntityException
     */
    public function disapprovedSeller($sellerId)
    {
        $objectManager = ObjectManager::getInstance();
        $model = $objectManager->create(Seller::class);
        $model->load($sellerId);
        $data = $model->getData();
        $data['url'] = $model->getUrl();
        $model->setStatus(2)->save();

        if ($this->_marketplaceHelper->getConfig('email_settings/enable_send_email')) {
            $this->_sender->pendingSellerProfile($data);
        }
    }

    /**
     * @param $request
     * @return bool
     */
    public function isFilesChanged($request)
    {
        $filesArray = (array)$request->getFiles();
        $identification = $request->getParam('identification');
        if ($identification == '') {
            return false;
        }
        $fileTypes = $filesArray[$identification . '-files'];
        foreach ($fileTypes as $file) {
            if (!empty($file['tmp_name'])) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param $request
     * @param $seller
     * @return bool
     */
    public function isIdentificationChanged($request, $seller)
    {
        $identification = $request->getParam('identification');
        if ($identification != $seller->getOrigData('identification_request')) {
            return true;
        }
        return false;
    }

    /**
     * @param $request
     * @return bool
     */
    public function isDeleted($request)
    {
        $identification = $request->getParam('identification');
        $deleteIds = $request->getParam('delete_ids');
        $deleteIds = explode(',', $deleteIds);
        $collection = $this->_attachmentFactory->create()
            ->getCollection()
            ->addFieldToFilter(
                'entity_id',
                ['in' => $deleteIds]
            );
        foreach ($collection as $item) {
            if ($item->getData('identify_type') == $identification) {
                return true;
            }
        }
        return false;
    }
}
