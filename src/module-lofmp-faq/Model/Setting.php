<?php
/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * http://landofcoder.com/license
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Landofcoder
 * @package    Lofmp_Faq
 * @copyright  Copyright (c) 2017 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */

namespace Lofmp\Faq\Model;

use Magento\Cms\Api\Data\BlockInterface;
use Magento\Framework\DataObject\IdentityInterface;

class Setting extends \Magento\Framework\Model\AbstractModel
{
    /** @inheritdoc */
    protected function _construct()
    {
        $this->_init(\Lofmp\Faq\Model\ResourceModel\Setting::class);
    }

    /** @inheritdoc */
    public function isEnableFaq()
    {
        return $this->getEnable();
    }

    /** @inheritdoc */
    public function isShowAuthor()
    {
        return $this->getShowAuthor();
    }

    /** @inheritdoc */
    public function isShowDate()
    {
        return $this->getShowDate();
    }

    /** @inheritdoc */
    public function isEnableRecentTab()
    {
        return $this->getRecentTab();
    }

    /** @inheritdoc */
    public function isEnablePopupForm()
    {
        return $this->getPopupForm();
    }

    /** @inheritdoc */
    public function isEnableRecaptcha()
    {
        return $this->getEnableRecaptcha();
    }
}
