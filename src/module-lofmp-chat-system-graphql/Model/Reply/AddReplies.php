<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Lofmp\ChatSystemGraphQl\Model\Reply;

use Magento\Framework\GraphQl\Exception\GraphQlNoSuchEntityException;

class AddReplies
{

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $session;

    /**
     * @var \Lof\MarketPlace\Model\SellerFactory
     */
    protected $sellerFactory;

    /**
     * @var \Lof\MarketPlace\Model\MessageAdminFactory
     */
    protected $messageAdminFactory;

    /**
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Lof\MarketPlace\Model\SellerFactory $sellerFactory
     * @param \Lof\MarketPlace\Model\MessageAdminFactory $messageAdminFactory
     */
    public function __construct(
        \Lof\MarketPlace\Model\SellerFactory $sellerFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Lof\MarketPlace\Model\MessageAdminFactory $messageAdminFactory
    ) {
        $this->session = $customerSession;
        $this->sellerFactory = $sellerFactory;
        $this->messageAdminFactory = $messageAdminFactory;
    }

    /**
     * Add review to product
     *
     * @param array $data
     * @param int $storeId
     *
     * @return mixed
     *
     * @throws GraphQlNoSuchEntityException
     */
    public function execute(array $data)
    {
        $userId = $this->authSession->getUser()->getData();
        if ($data) {
            $model = $this->_objectManager->create(\Lof\MarketPlace\Model\MessageAdmin::class);

            if ($data['message_id']) {
                $model->load($data['message_id']);
                $_data = $model->getData();
                $_data['message'] = $data['content'];
                $model->setData($_data);
                $model->setAdminId($userId['user_id']);
                $model->setAdminName($userId['firstname'] . $userId['lastname']);
                $model->setAdminEmail($userId['email']);
                $model->save();
            }
            $res = [
                "code" => 405,
                "message" => "You saved this message"
            ];
            return $res;
        }
    }

    public function sellerReplies(array $data)
    {
        $customerSession = $this->session;
        $customerId = $customerSession->getId();
        $status = $this->sellerFactory->create()->load($customerId, 'customer_id');

            if ($data['message_id'] == $status['message_id']) {
                $message = $this->messageAdminFactory->create();
                $message->setContent($data['content']);
                $message->save();
                $res = [
                    "code" => 405,
                    "message" => "You saved this message"
                ];
            }
        return $res;
    }
}
