<?php
/**
 * LandOfCoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * https://landofcoder.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   LandOfCoder
 * @package    Lofmp_Rma
 * @copyright  Copyright (c) 2021 Landofcoder (https://landofcoder.com/)
 * @license    https://landofcoder.com/LICENSE-1.0.html
 */

namespace Lofmp\Rma\Api\Data;

use Lofmp\Rma\Api;
use Magento\Framework\Api\SearchCriteriaInterface;

/**
 * @method Api\Data\AttachmentSearchResultsInterface getList(SearchCriteriaInterface $searchCriteria)
 */
interface AttachmentInterface extends DataInterface
{
    const KEY_ITEM_TYPE  = 'item_type';
    const KEY_ITEM_ID    = 'item_id';
    const KEY_UID        = 'uid';
    const KEY_NAME       = 'name';
    const KEY_TYPE       = 'type';
    const KEY_SIZE       = 'size';
    const KEY_BODY       = 'body';
    const KEY_CREATED_AT = 'created_at';

    /**
     * @return string
     */
    public function getItemType();

    /**
     * @param string $itemType
     * @return $this
     */
    public function setItemType($itemType);

    /**
     * @return int
     */
    public function getItemId();

    /**
     * @param int $itemId
     * @return $this
     */
    public function setItemId($itemId);

    /**
     * @return string
     */
    public function getUid();

    /**
     * @param string $uid
     * @return $this
     */
    public function setUid($uid);

    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $name
     * @return $this
     */
    public function setName($name);


    /**
     * @return string
     */
    public function getType();

    /**
     * @param string $type
     * @return $this
     */
    public function setType($type);

    /**
     * @return int
     */
    public function getSize();

    /**
     * @param int $size
     * @return $this
     */
    public function setSize($size);

    /**
     * @return string
     */
    public function getBody();

    /**
     * @param string $body
     * @return $this
     */
    public function setBody($body);

    /**
     * @return string
     */
    public function getCreatedAt();

    /**
     * @param string $createdAt
     * @return $this
     */
    public function setCreatedAt($createdAt);
}
