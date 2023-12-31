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

namespace Lof\Quickrfq\Model\ResourceModel\Attachment;

/**
 * Class Collection
 *
 * @package Lof\Quickrfq\Model\ResourceModel\Attachment
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'entity_id';

    /**
     * @var string
     */
    protected $_eventPrefix = 'lof_quickrfq_attachment_collection';

    /**
     * @var string
     */
    protected $_eventObject = 'attachment_collection';

    protected function _construct()
    {
        $this->_init('Lof\Quickrfq\Model\Attachment', 'Lof\Quickrfq\Model\ResourceModel\Attachment');
    }
}
