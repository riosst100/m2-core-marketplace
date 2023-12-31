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
 * @package    Lof_Auction
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */
namespace Lof\Auction\Api\Data;

/**
 * Interface WattingUserInterface
 * @package Lof\Auction\Api\Data
 */
interface WattingUserInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    const AUC_LIST_URL = 'auc_list_url';
    const AUC_URL_LABLE = 'auc_url_lable';
    const MSG_LABLE = 'msg_lable';

    /**
     * Get auc_list_url
     *
     * @return string|null
     */
    public function getAucListUrl();

    /**
     * Set auc_list_url
     *
     * @param string|null
     * @return $this
     */
    public function setAucListUrl($auc_list_url);

    /**
     * Get auc_url_lable
     *
     * @return string|null
     */
    public function getAucUrlLable();

    /**
     * Set auc_url_lable
     *
     * @param string|null
     * @return $this
     */
    public function setAucUrlLable($auc_url_lable);

    /**
     * Get msg_lable
     *
     * @return string|null
     */
    public function getMsgLable();

    /**
     * Set msg_lable
     *
     * @param string|null
     * @return $this
     */
    public function setMsgLable($msg_lable);

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Lof\Auction\Api\Data\WattingUserExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     * @param \Lof\Auction\Api\Data\WattingUserExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Lof\Auction\Api\Data\WattingUserExtensionInterface $extensionAttributes
    );

}
