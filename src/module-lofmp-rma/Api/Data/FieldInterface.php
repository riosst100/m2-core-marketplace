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

/**
 * @method Api\Data\FieldSearchResultsInterface getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria)
 */
interface FieldInterface extends DataInterface
{
    const KEY_NAME                        = 'name';
    const KEY_CODE                        = 'code';
    const KEY_TYPE                        = 'type';
    const KEY_VALUES                      = 'values';
    const KEY_DESCRIPTION                 = 'description';
    const KEY_IS_ACTIVE                   = 'is_active';
    const KEY_SORT_ORDER                  = 'sort_order';
    const KEY_IS_REQUIRED_STAFF           = 'is_required_staff';
    const KEY_IS_REQUIRED_CUSTOMER        = 'is_required_customer';
    const KEY_IS_VISIBLE_CUSTOMER         = 'is_visible_customer';
    const KEY_IS_EDITABLE_CUSTOMER        = 'is_editable_customer';
    const KEY_VISIBLE_CUSTOMER_STATUS     = 'visible_customer_status';
    const KEY_IS_SHOW_IN_CONFIRM_SHIPPING = 'is_show_in_confirm_shipping';

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
    public function getCode();

    /**
     * @param string $code
     * @return $this
     */
    public function setCode($code);

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
     * @return string|array
     */
    public function getValues();

    /**
     * @param string $values
     * @return $this
     */
    public function setValues($values);

    /**
     * @return string
     */
    public function getDescription();

    /**
     * @param string $description
     * @return $this
     */
    public function setDescription($description);

    /**
     * @return bool|int
     */
    public function getIsActive();

    /**
     * @param bool $isActive
     * @return $this
     */
    public function setIsActive($isActive);

    /**
     * @return int
     */
    public function getSortOrder();

    /**
     * @param int $sortOrder
     * @return $this
     */
    public function setSortOrder($sortOrder);

    /**
     * @return bool|int
     */
    public function getIsRequiredStaff();

    /**
     * @param bool $isRequiredStaff
     * @return $this
     */
    public function setIsRequiredStaff($isRequiredStaff);

    /**
     * @return bool|int
     */
    public function IsCustomerRequired();

    /**
     * @param bool $isRequiredCustomer
     * @return $this
     */
    public function setIsRequiredCustomer($isRequiredCustomer);

    /**
     * @return bool|int
     */
    public function getIsVisibleCustomer();

    /**
     * @param bool $isVisibleCustomer
     * @return $this
     */
    public function setIsVisibleCustomer($isVisibleCustomer);

    /**
     * @return bool|int
     */
    public function getIsEditableCustomer();

    /**
     * @param bool $isEditableCustomer
     * @return $this
     */
    public function setIsEditableCustomer($isEditableCustomer);

    /**
     * @return string
     */
    public function getVisibleCustomerStatus();

    /**
     * @param string $visibleCustomerStatus
     * @return $this
     */
    public function setVisibleCustomerStatus($visibleCustomerStatus);

    /**
     * @return bool|int
     */
    public function getIsShowInConfirmShipping();

    /**
     * @param bool $isShowInConfirmShipping
     * @return $this
     */
    public function setIsShowInConfirmShipping($isShowInConfirmShipping);
}
