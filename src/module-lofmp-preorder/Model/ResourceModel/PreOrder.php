<?php

namespace Lofmp\PreOrder\Model\ResourceModel;

use Lof\MarketPlace\Helper\SellerOrderHelper;
use Magento\Framework\DataObject;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Store\Model\StoreManagerInterface;

/**
 * @SuppressWarnings(PHPMD.CamelCasePropertyName)
 */
class PreOrder extends AbstractDb
{
    /**
     * Store manager
     *
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var SellerOrderHelper
     */
    private $helper;

    /**
     * Construct
     *
     * @param SellerOrderHelper $helper
     * @param Context $context
     * @param StoreManagerInterface $storeManager
     * @param string $connectionName
     */
    public function __construct(
        SellerOrderHelper $helper,
        Context $context,
        StoreManagerInterface $storeManager,
        $connectionName = null
    ) {
        parent::__construct($context, $connectionName);
        $this->_storeManager = $storeManager;
        $this->helper = $helper;
    }

    /**
     * Initialize resource model
     *
     * @return void
     * @SuppressWarnings(PHPMD.CamelCaseMethodName)
     */
    protected function _construct()
    {
        $this->_init('lof_preorder_items', 'id');
    }

    /**
     * Perform actions after object save
     *
     * @param AbstractModel|DataObject $object
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @SuppressWarnings(PHPMD.CamelCaseMethodName)
     */
    public function _afterSave(AbstractModel $object)
    {
        $productId = $object->getData('product_id');
        $dataSellerId = $object->getSellerId();
        if (!$dataSellerId && $this->isSellerProduct($productId)) {

            $sellerId =  $this->helper->getSellerByProductId($productId)->getId();
            if ($sellerId) {
                $object->setData('seller_id', $sellerId);
            } else {
                $object->setData('seller_id', 0);
            }
            $object->save();
        }
        return parent::_afterSave($object);
    }

    /**
     * @param $productId
     * @return bool
     */
    public function isSellerProduct($productId)
    {
        $sellerId =  $this->helper->getSellerByProductId($productId)->getId();
        if ($sellerId) {
            return true;
        }
        return false;
    }
}
