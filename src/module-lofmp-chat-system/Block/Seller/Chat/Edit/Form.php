<?php
/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * http://www.landofcoder.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Landofcoder
 * @package    Lofmp_ChatSystem
 * @copyright  Copyright (c) 2017 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */

namespace Lofmp\ChatSystem\Block\Seller\Chat\Edit;

class Form extends \Magento\Framework\View\Element\Template
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var string
     */
    protected $_template = 'Lofmp_ChatSystem::chat/chat.phtml';

    /**
     * @var string
     */
    protected $_columnDate = 'main_table.created_at';

    /**
     * @var \Magento\Framework\Data\Form\FormKey
     */
    protected $formKey;

    /**
     * @var \Lof\MarketPlace\Helper\Data
     */
    protected $helper;

    /**
     * @var \Lofmp\ChatSystem\Model\ChatMessage
     */
    protected $messsage;

    /**
     * Form constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Lofmp\ChatSystem\Model\ChatMessage $messsage
     * @param \Lof\MarketPlace\Helper\Data $helper
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Lofmp\ChatSystem\Model\ChatMessage $messsage,
        \Lof\MarketPlace\Helper\Data $helper
    ) {
        parent::__construct($context);
        $this->helper = $helper;
        $this->_coreRegistry = $registry;
        $this->formKey = $context->getFormKey();
        $this->messsage = $messsage;
    }

    public function getCurrentChat()
    {
        return $this->_coreRegistry->registry('lofmpchatsystem_chat');
    }

    public function getFormKey()
    {
        return $this->formKey->getFormKey();
    }

    public function getSeller()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $seller = $objectManager->create(\Lof\MarketPlace\Model\Seller::class)
            ->load($this->helper->getSellerId(), 'seller_id');
        return $seller;
    }

    public function isRead()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $chat = $objectManager->create(\Lofmp\ChatSystem\Model\Chat::class)
            ->load($this->getCurrentChat()->getData('chat_id'));
        $messsage = $this->messsage->getCollection()
            ->addFieldToFilter('chat_id', $this->getCurrentChat()->getData('chat_id'))
            ->addFieldToFilter('is_read', 1);
        foreach ($messsage as $key => $_messsage) {
            $_messsage->setData('is_read', 0)->save();
        }

        $chat->setData('is_read', 0)->save();

        return;
    }
}
