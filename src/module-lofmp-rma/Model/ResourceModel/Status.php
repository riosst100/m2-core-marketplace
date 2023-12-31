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

class Status extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init('lofmp_rma_status', 'status_id');
    }

    /**
     * {@inheritdoc}
     */
    protected function _afterLoad(\Magento\Framework\Model\AbstractModel $object)
    {
        /** @var  \Lofmp\Rma\Model\Status $object */
        if (!$object->getIsMassDelete()) {
        }

        return parent::_afterLoad($object);
    }

    /**
     * {@inheritdoc}
     */
    protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
    {
        /** @var  \Lofmp\Rma\Model\Status $object */
        if (!$object->getId()) {
            $object->setCreatedAt((new \DateTime())->format(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT));

        }
        $object->setUpdatedAt((new \DateTime())->format(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT));

        $adminMessage    = [];
        $historyMessage  = [];
        $customerMessage = [];
        $saveToStore     = (int)$object->getStore();
        if ($object->getId()) {
            $adminMessage    = $object->decodeMessage($object->getOrigData('admin_message'));
            $historyMessage  = $object->decodeMessage($object->getOrigData('history_message'));
            $customerMessage = $object->decodeMessage($object->getOrigData('customer_message'));
        } else {
            if ($saveToStore) { //set default messages
                $adminMessage[0]    = $object->getData('admin_message');
                $historyMessage[0]  = $object->getData('history_message');
                $customerMessage[0] = $object->getData('customer_message');
            }
        }
        $adminMessage[$saveToStore]    = $object->getData('admin_message');
        $historyMessage[$saveToStore]  = $object->getData('history_message');
        $customerMessage[$saveToStore] = $object->getData('customer_message');

        $object->setAdminMessage($adminMessage);
        $object->setHistoryMessage($historyMessage);
        $object->setCustomerMessage($customerMessage);

        return parent::_beforeSave($object);
    }

    /**
     * {@inheritdoc}
     */
    protected function _afterSave(\Magento\Framework\Model\AbstractModel $object)
    {
       
        return parent::_afterSave($object);
    }

    /************************/
}
