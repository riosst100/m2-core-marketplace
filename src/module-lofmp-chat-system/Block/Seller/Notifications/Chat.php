<?php
/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * http://landofcoder.com/license
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Landofcoder
 * @package    Lofmp_ChatSystem
 *
 * @copyright  Copyright (c) 2016 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */

namespace Lofmp\ChatSystem\Block\Seller\Notifications;

class Chat extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Lofmp\ChatSystem\Model\ChatMessage
     */
    protected $message;

    /**
     * @var \Lofmp\ChatSystem\Model\Chat
     */
    protected $chat;

    /**
     * @var \Lof\MarketPlace\Helper\Data
     */
    protected $helper;

    /**
     * Chat constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Lofmp\ChatSystem\Model\ChatMessage $message
     * @param \Lof\MarketPlace\Helper\Data $helper
     * @param \Lofmp\ChatSystem\Model\Chat $chat
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Lofmp\ChatSystem\Model\ChatMessage $message,
        \Lof\MarketPlace\Helper\Data $helper,
        \Lofmp\ChatSystem\Model\Chat $chat
    ) {
        $this->chat = $chat;
        $this->helper = $helper;
        $this->message = $message;
        parent::__construct($context);
    }

    public function countUnread()
    {
        $chat = $this->chat->getCollection()->addFieldToFilter('seller_id', $this->helper->getSellerId());
        $chatId = [];
        foreach ($chat as $key => $_chat) {
            array_push($chatId, $_chat->getData('chat_id'));
        }
        $message = $this->message->getCollection()
            ->addFieldToFilter('chat_id', array('in' => $chatId))->addFieldToFilter('is_read', 1);

        return count($message);
    }

}
