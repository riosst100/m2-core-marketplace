<?php
/**
 * Landofcoder
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
 * @category   Landofcoder
 * @package    Lof_SmtpEmail
 * @copyright  Copyright (c) 2016 Landofcoder (https://landofcoder.com/)
 * @license    https://landofcoder.com/LICENSE-1.0.html
 */

namespace Lof\SmtpEmail\Model;

class Store
{
    /** @var int|null  */
    protected $store_id = null;

    /**
     * @var string|mixed|null
     */
    protected $from = null;

    /**
     * @return int|null
     */
    public function getStoreId()
    {
        return $this->store_id;
    }

    /**
     * @param $store_id
     * @return $this
     */
    public function setStoreId($store_id)
    {
        $this->store_id = $store_id;
        return $this;
    }

    /**
     * @return string|array
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * @param string|array $from
     * @return $this
     */
    public function setFrom($from)
    {
        $this->from = $from;
        return $this;
    }
}
