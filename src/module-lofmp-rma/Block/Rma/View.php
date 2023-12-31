<?php
/**
 * LandOfCoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * https://landofcoder.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   LandOfCoder
 * @package    Lofmp_Rma
 * @copyright  Copyright (c) 2021 Landofcoder (https://landofcoder.com/)
 * @license    https://landofcoder.com/LICENSE-1.0.html
 */


namespace Lofmp\Rma\Block\Rma;

class View extends \Magento\Framework\View\Element\Template
{
    public function __construct(
        \Magento\Framework\Registry                       $registry,
        \Magento\Framework\View\Element\Template\Context  $context,
        \Magento\Sales\Model\Order\Address\Renderer       $addressRenderer,
        \Magento\Catalog\Helper\Image                     $imageHelper,
        \Magento\Framework\Api\SortOrderBuilder $sortOrderBuilder,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Lofmp\Rma\Api\Repository\FieldRepositoryInterface $fieldRepository,
        \Lofmp\Rma\Helper\Data                            $dataHelper,
        \Lofmp\Rma\Helper\Help                              $Helper,
        \Lofmp\Rma\Model\Status                           $statusFactory,
        array $data = []
    ) {
         $this->datahelper               = $dataHelper;
        $this->registry                = $registry;
        $this->addressRenderer         = $addressRenderer;
        $this->imageHelper             = $imageHelper;
         $this->fieldRepository       = $fieldRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->sortOrderBuilder      = $sortOrderBuilder;
        $this->helper                = $Helper;
        $this->context  = $context;
        $this->request =  $context->getRequest();
        $this->status         = $statusFactory;
        parent::__construct($context, $data);
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if ($rma = $this->getRma()) {
            $this->pageConfig->getTitle()->set(__('RMA #%1', $rma->getIncrementId()));
            $pageMainTitle = $this->getLayout()->getBlock('page.main.title');
            if ($pageMainTitle) {
                $pageMainTitle->setPageTitle(
                    __(
                        'RMA #%1 - %2',
                        $rma->getIncrementId(),
                        $this->getStatusname($rma->getStatusId())
                    )
                );
            }
        }
    }
    /**
     * @return \Lofmp\Rma\Model\Rma
     */
    public function getRma()
    {
        if ($this->registry->registry('current_rma') && $this->registry->registry('current_rma')->getId()) {
            return $this->registry->registry('current_rma');
        }
    }
    
    public function getOrder()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $order = $objectManager->get('Magento\Sales\Model\Order')->load($this->getOrderId());
        return $order;
    }
    public function getOrderId()
    {
        return $this->getRma()->getOrderId();
    }
    public function getFormattedAddress()
    {
        if ($this->getOrder()->getShippingAddress()) {
            return $this->addressRenderer->format($this->getOrder()->getShippingAddress(), 'html');
        } else {
            return;
        }
    }

    public function getBillingAddress()
    {
        return $this->addressRenderer->format($this->getOrder()->getBillingAddress(), 'html');
    }

    public function getOrderDate()
    {
        return $this->formatDate(
            $this->getOrderAdminDate($this->getOrder()->getCreatedAt()),
            \IntlDateFormatter::MEDIUM,
            true
        );
    }
    /**
     * Get order store name
     *
     * @return null|string
     */
    public function getOrderStoreName()
    {
        if ($this->getOrder()) {
            $storeId = $this->getOrder()->getStoreId();
            if ($storeId === null) {
                $deleted = __(' [deleted]');
                return nl2br($this->getOrder()->getStoreName()) . $deleted;
            }
            $store = $this->_storeManager->getStore($storeId);
            $name = [$store->getWebsite()->getName(), $store->getGroup()->getName(), $store->getName()];
            return implode('<br/>', $name);
        }

        return null;
    }
    
    
    public function getQtyAvailable($item)
    {
        return $this->datahelper->getItemQuantityAvaiable($item);
    }
    public function getQtyRequest($item)
    {
        return $this->datahelper->getQtyReturnedRma($item, $this->getRma()->getId());
    }


    public function getRmaItemData($item)
    {
        return $this->datahelper->getRmaItemData($item, $this->getRma()->getId());
    }

    public function getAttachmentUrl($Uid)
    {
        return $this->_urlBuilder->getUrl('rma/attachment/download', ['uid' => $Uid]);
    }

    public function getPrintUrl()
    {
        return $this->_urlBuilder->getUrl(
            'rma/rma/print',
            ['id' => $this->getRma()->getId(), '_nosid' => true]
        );
    }

    /**
     * @param \Lofmp\Rma\Api\Data\RmaInterface $rma
     * @return bool|string
     */
    public function getPrintLabelUrl()
    {
        return $this->_urlBuilder->getUrl(
            'rma/rma/printlabel',
            ['id' => $this->getRma()->getId(), '_nosid' => true]
        );
    }

    public function getReturnlabel()
    {
            return $this->datahelper->getAttachments('return_label', $this->getRma()->getId());
    }

    public function getStatusname($id)
    {
         $status =  $this->status->load($id);
         return $status->getName();
    }

    public function getRmaDate()
    {
        return $this->formatDate(
            $this->getOrderAdminDate($this->getRma()->getCreatedAt()),
            \IntlDateFormatter::MEDIUM,
            true
        );
    }

    public function initImage($item)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $product = $objectManager->get('Magento\Catalog\Model\Product')->loadByAttribute('sku', $item->getData('sku'));
        return $this->imageHelper->init($product, 'product_page_image_small');
    }
    public function PostUrl($id)
    {
        return $this->_urlBuilder->getUrl('rma/rma/savemessage', ['id' => $id]);
    }


    public function isShowShipping()
    {
        $rma = $this->getRma();
        $status =  $this->status->load($rma->getStatusId());
        return $status->getIsShowShipping();
    }
    /**
     * @return bool
     */
    public function isShowShippingConfirm()
    {
        $dontShowShippingConfirmationButton = [
            \Lofmp\Rma\Api\Data\StatusInterface::PACKAGE_SENT,
            \Lofmp\Rma\Api\Data\StatusInterface::REJECTED,
            \Lofmp\Rma\Api\Data\StatusInterface::CLOSED,
        ];
        $rma = $this->getRma();
        $status =  $this->status->load($rma->getStatusId());

        if (in_array($status->getCode(), $dontShowShippingConfirmationButton)) {
            return false;
        }

        return $this->helper->getConfig($rma->getStoreId(), 'rma/general/is_require_shipping_confirmation');
    }
    /**
     * @return \Lofmp\Rma\Model\Field[]
     */
    public function getShippingConfirmFields()
    {
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter('is_active', true)
            ->addFilter('is_show_in_confirm_shipping', true)
            ->addSortOrder($this->sortOrderBuilder
            ->setField('sort_order')
            ->setDirection(\Magento\Framework\Data\Collection::SORT_ORDER_ASC)
            ->create())
        ;

        return $this->fieldRepository->getList($searchCriteria->create())->getItems();
    }

    /**
     * @return string
     */
    public function getShippingConfirm()
    {
        $str = $this->helper->getConfig($this->context->getStoreManager()->getStore(), 'rma/general/shipping_confirmation_text');
        ;
        $str = str_replace('"', '\'', $str);

        return $str;
    }

     /**
      * @param \Lofmp\Rma\Api\Data\RmaInterface $rma
      * @return string
      */
    public function getConfirmationUrl()
    {
        return $this->_urlBuilder->getUrl(
            'rma/rma/savemessage',
            ['id' => $this->getRma()->getId(), 'shipping_confirmation' => true]
        );
    }
}
