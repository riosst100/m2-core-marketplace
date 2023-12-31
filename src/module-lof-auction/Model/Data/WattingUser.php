<?php
/**
 * Copyright © landofcoder.com All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Lof\Auction\Model\Data;

use Lof\Auction\Api\Data\WattingUserInterface;

class WattingUser extends \Magento\Framework\Api\AbstractExtensibleObject implements WattingUserInterface
{

    /**
     * {@inheritdoc}
     */
    public function getAucListUrl()
    {
        return $this->_get(self::AUC_LIST_URL);
    }

    /**
     * {@inheritdoc}
     */
    public function setAucListUrl($auc_list_url)
    {
        return $this->setData(self::AUC_LIST_URL, $auc_list_url);
    }

    /**
     * {@inheritdoc}
     */
    public function getAucUrlLable()
    {
        return $this->_get(self::AUC_URL_LABLE);
    }

    /**
     * {@inheritdoc}
     */
    public function setAucUrlLable($auc_url_lable)
    {
        return $this->setData(self::AUC_URL_LABLE, $auc_url_lable);
    }

    /**
     * {@inheritdoc}
     */
    public function getMsgLable()
    {
        return $this->_get(self::MSG_LABLE);
    }

    /**
     * {@inheritdoc}
     */
    public function setMsgLable($msg_lable)
    {
        return $this->setData(self::MSG_LABLE, $msg_lable);
    }

    /**
     * {@inheritdoc}
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * {@inheritdoc}
     */
    public function setExtensionAttributes(
        \Lof\Auction\Api\Data\WattingUserExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}

