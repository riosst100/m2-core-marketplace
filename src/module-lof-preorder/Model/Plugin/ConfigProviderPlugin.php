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
 * @package    Lof_PreOrder
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lof\PreOrder\Model\Plugin;

use Lof\RewardPoints\Model\Config;

class ConfigProviderPlugin
{
    protected $_preorderHelper;
    protected $_cart;
    /**
     * @param \Lof\PreOrder\Helper\Data $preorderHelper
     * @param \Magento\Checkout\Model\CartFactory $cart
     */
    public function __construct(
        \Lof\PreOrder\Helper\Data $preorderHelper,
        \Magento\Checkout\Model\CartFactory $cart
    ) {
        $this->_preorderHelper = $preorderHelper;
        $this->_cart = $cart;
    }

    /**
     * @param \Magento\Checkout\Model\DefaultConfigProvider $subject
     * @param array                                         $result
     *
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetConfig(\Magento\Checkout\Model\DefaultConfigProvider $subject, array $result)
    {
        $helper = $this->_preorderHelper;
        $quote = $this->_cart->create()->getQuote();
        $quoteMessages = isset($result['quoteMessages'])?$result['quoteMessages']:[];
        $quotePreorderNotes = [];
        $update_message = false;
        $update_preorder_msg = false;
        foreach ($quote->getAllItems() as &$quoteItem) {
            $cart_warning_msg = $helper->getMsgWarningQtyInCart($quoteItem->getProductId(), $quoteItem->getname(), $quoteItem->getQty());
            if ($cart_warning_msg) {
                if (!isset($quoteMessages[$quoteItem->getId()])) {
                    $quoteMessages[$quoteItem->getId()] = "";
                }
                $quoteMessages[$quoteItem->getId()] .= "\n".$cart_warning_msg;
                $quoteItem->setMessage($quoteMessages[$quoteItem->getId()]);
                $update_message = true;
            }
            if (!isset($quotePreorderNotes[$quoteItem->getId()])) {
                $quotePreorderNotes[$quoteItem->getId()] = '';
            }
            if ($helper->isPreorder($quoteItem->getProductId())) {
                $msg = $helper->getPreOrderInfoBlock($quoteItem->getProductId(), $quoteItem->getProduct());
                if ($msg) {
                    $quoteItem->setData("preorder_msg", $msg);
                    $quotePreorderNotes[$quoteItem->getId()] = $msg;
                    $update_preorder_msg = true;
                }
            }
        }
        if ($update_message || $update_preorder_msg) {
            if ($update_message) {
                $result['quoteMessages'] = $quoteMessages;
            }
            $result['quotePreorderMessages'] = $quotePreorderNotes;
            $quote->save();
        }
        return $result;
    }
}
