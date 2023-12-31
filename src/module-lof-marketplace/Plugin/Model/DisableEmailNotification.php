<?php
/**
 * Lof
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Lof.com license that is
 * available through the world-wide-web at this URL:
 * https://landofcoder.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Lof
 * @package     Lof_MarketPlace
 * @copyright   Copyright (c) 2021 Lof (https://landofcoder.com/)
 * @license     https://landofcoder.com/LICENSE.txt
 */

namespace Lof\MarketPlace\Plugin\Model;

use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\EmailNotification;

class DisableEmailNotification
{
    /**
     * @var \Lof\MarketPlace\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $_request;

    /**
     * @param \Lof\MarketPlace\Helper\Data $helper
     * @param \Magento\Framework\App\RequestInterface $request
     */
    public function __construct(
        \Lof\MarketPlace\Helper\Data $helper,
        \Magento\Framework\App\RequestInterface $request
    ) {
        $this->helper = $helper;
        $this->_request = $request;
    }

    /**
     * @param EmailNotification $subject
     * @param callable $proceed
     * @param CustomerInterface $customer
     * @param $type
     * @param string $backUrl
     * @param null $storeId
     * @param null $sendemailStoreId
     * @return EmailNotification
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function aroundNewAccount(
        \Magento\Customer\Model\EmailNotification $subject,
        callable $proceed,
        CustomerInterface $customer,
        $type = \Magento\Customer\Model\EmailNotification::NEW_ACCOUNT_EMAIL_REGISTERED,
        $backUrl = '',
        $storeId = null,
        $sendemailStoreId = null
    ) {
        if (!$this->helper->getConfig('general_settings/enable') || $this->helper->getConfigCustomer('create_account/confirm')) {
            return $proceed($customer, $type, $backUrl, $storeId, $sendemailStoreId);
        }

        if ($this->_request->getParam('is_seller')) {
            return $subject;
        }
        return $proceed($customer, $type, $backUrl, $storeId, $sendemailStoreId);
    }
}
