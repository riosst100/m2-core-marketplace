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

namespace Lof\MarketPlace\Model\ResourceModel;

class MessageAdmin extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $authSession;

    /**
     * MessageAdmin constructor.
     *
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Backend\Model\Auth\Session $authSession
     * @param null $connectionName
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Backend\Model\Auth\Session $authSession,
        $connectionName = null
    ) {
        parent::__construct($context, $connectionName);
        $this->_storeManager = $storeManager;
        $this->authSession = $authSession;
    }

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('lof_marketplace_message_admin', 'message_id');
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return MessageAdmin|void
     */
    protected function _afterSave(\Magento\Framework\Model\AbstractModel $object)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        if ($object->getMessage()) {
            $user = $this->authSession->getUser();
            $dataMessage = [
                'message_id' => (int)$object->getMessageId(),
                'content' => $object->getMessage(),
                'seller_send' => 0,
                'sender_id' => $user->getId(),
                'sender_email' => $user->getEmail(),
                'sender_name' => $user->getFirstname() . ' ' . $user->getLastname(),
                'receiver_id' => $object->getSellerId(),
                'receiver_name' => $object->getSellerName(),
                'receiver_email' => $object->getSellerEmail(),
                'message_admin' => 1
            ];

            $messageModel = $objectManager->get(\Lof\MarketPlace\Model\MessageDetail::class);

            $messageModel->setData($dataMessage)->save();
        }
    }
}
