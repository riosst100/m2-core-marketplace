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


namespace Lofmp\Rma\Model\ResourceModel;

class Rma extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    public function __construct(
        \Lofmp\Rma\Helper\Help                                $Helper,
        \Magento\Catalog\Model\Product\Url $productUrl,
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        $resourcePrefix = null
    ) {
        $this->productUrl     = $productUrl;
        $this->context        = $context;
        $this->resourcePrefix = $resourcePrefix;
        $this->helper           = $Helper;

        parent::__construct($context, $resourcePrefix);
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init('lofmp_rma_rma', 'rma_id');
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $object
     *
     * @return \Magento\Framework\Model\AbstractModel|\Lofmp\Rma\Model\Rma
     */
    protected function loadStoreIds(\Magento\Framework\Model\AbstractModel $object)
    {
        /* @var  \Lofmp\Rma\Model\Rma $object */
        $select = $this->getConnection()->select()
            ->from($this->getTable('lofmp_rma_rma_store'))
            ->where('rs_rma_id = ?', $object->getId());
        if ($data = $this->getConnection()->fetchAll($select)) {
            $array = [];
            foreach ($data as $row) {
                $array[] = $row['rs_store_id'];
            }
            $object->setData('store_ids', $array);
        }

        return $object;
    }

    /**
     * @param string $object
     * @return void
     */
    protected function saveStoreIds($object)
    {
        /* @var  \Lofmp\Rma\Model\Rma $object */
        $condition = $this->getConnection()->quoteInto('rs_rma_id = ?', $object->getId());
        $this->getConnection()->delete($this->getTable('lofmp_rma_rma_store'), $condition);
        foreach ((array)$object->getStoreId() as $id) {
            $objArray = [
                'rs_rma_id' => $object->getId(),
                'rs_store_id' => $id,
            ];
            $this->getConnection()->insert(
                $this->getTable('lofmp_rma_rma_store'),
                $objArray
            );
        }
    }


    /**
     * {@inheritdoc}
     */
    protected function _afterLoad(\Magento\Framework\Model\AbstractModel $object)
    {
        /** @var  \Lofmp\Rma\Model\Rma $object */
        if (!$object->getIsMassDelete()) {
            $this->loadStoreIds($object);
        }

        return parent::_afterLoad($object);
    }

    /**
     * {@inheritdoc}
     */
    protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
    {
        /** @var  \Lofmp\Rma\Model\Rma $object */
        if (!$object->getId()) {
            $object->setCreatedAt((new \DateTime())->format(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT));
            $object->setCode($this->normalize($object->getCode()));
            if (!$object->getStatusId()) {
                $object->setStatusId($this->helper->getConfig($store = null, 'rma/general/default_status'));
            }
            if (!$object->getUserId()) {
                $object->setUserId($this->helper->getConfig($store = null, 'rma/general/default_user'));
            }
        }

    
            $object->setUpdatedAt((new \DateTime())->format(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT));
        

        return parent::_beforeSave($object);
    }

    /**
     * {@inheritdoc}
     */
    protected function _afterSave(\Magento\Framework\Model\AbstractModel $object)
    {
        /** @var  \Lofmp\Rma\Model\Rma $object */
        if (!$object->getIsMassStatus()) {
            $this->saveStoreIds($object);
        }

        return parent::_afterSave($object);
    }

    /**
     * @param string $string
     *
     * @return string
     */
    public function normalize($string)
    {
        $string = $this->productUrl->formatUrlKey($string);
        $string = str_replace('-', '_', $string);

        return 'f_'.$string;
    }
}
