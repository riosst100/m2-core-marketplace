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
 * @package    Lofmp_SellerIdentificationApproval
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lofmp\SellerIdentificationApproval\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractHelper
{

    const XML_PATH_TAG = 'seller_identification_approval';

    /**
     * Retrieve the module config
     *
     * @param string $config
     * @return mixed
     */
    public function getConfig($config)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_TAG . '/' . $config,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @param $field
     * @param null $storeId
     * @return mixed
     */
    public function getConfigValue($field, $storeId = null)
    {
        return $this->scopeConfig->getValue(
            $field,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param $type
     * @return bool|string|string[]
     */
    public function getAllowedExtensions($type)
    {
        $extensions = $this->getConfig($type . "/file_formats");
        $extensions = $extensions ? str_replace(' ', '', $extensions) : $extensions;

        return $extensions;
    }

    /**
     *
     * @param $type
     * @return int|null
     */
    public function getMaxFileSize($type)
    {
        return $this->getConfig($type . "/maximum_file_size");
    }

    /**
     * is enabled upload file type
     *
     * @param string|null $type
     * @return mixed|int|bool
     */
    public function isEnable($type)
    {
        $type = $type ? $type : "general";
        return $this->getConfig($type . "/enabled");
    }

    /**
     * check allow seller update files
     *
     * @param string|null $type
     * @param int $countFiles
     * @return mixed|bool|int
     */
    public function allowUpdate($type, $countFiles = 1)
    {
        $type = $type ? $type : "general";
        $allow_update = (int)$this->getConfig($type . "/allow_update");
        if ($countFiles <= 1) {
            $allow_update = true;
        }
        return $allow_update;
    }

    /**
     * @return mixed
     */
    public function isRequire()
    {
        return $this->getConfig("general/require");
    }

    /**
     * Returns Identification Types
     * @return array
     */
    public function getIdentificationTypes()
    {
        return [
            '' => __('Please select...'),
            'driving_license' => __('Driving License'),
            'passport' => __('Passport'),
            'identity_card' => __('Identity Card'),
            'company_certification' => __('Company Certification'),
            'bank_checkbook' => __('Bank Checkbook'),
            'signature' => __('Signature'),
        ];
    }
}
