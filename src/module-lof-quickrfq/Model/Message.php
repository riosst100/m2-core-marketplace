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
 * @package    Lof_Quickrfq
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lof\Quickrfq\Model;

use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;

/**
 * Class Message
 *
 * @package Lof\Quickrfq\Model
 */
class Message extends AbstractModel implements IdentityInterface
{
    /**
     * Cache tag.
     */
    const CACHE_TAG = 'lof_quickrfq_message';

    /**
     * @var string
     */
    protected $_cacheTag = 'lof_quickrfq_message';

    /**
     * @var string
     */
    protected $_eventPrefix = 'lof_quickrfq_message';

    /**
     * Construct init.
     */
    protected function _construct()
    {
        $this->_init('Lof\Quickrfq\Model\ResourceModel\Message');
    }

    /**
     * @return string[]
     */
    public function getIdentities()
    {
        return [ self::CACHE_TAG . '_' . $this->getId() ];
    }

    /**
     * @return array
     */
    public function getDefaultValues()
    {
        return [];
    }
}
