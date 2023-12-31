<?php
/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * http://landofcoder.com/license
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Landofcoder
 * @package    Lof_SocialLogin
 *
 * @copyright  Copyright (c) 2016 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */
namespace Lof\SocialLogin\Helper\Google;

use Magento\Store\Model\StoreManagerInterface;
use Lof\SocialLogin\Helper\Data as HelperData;
  
class Data extends HelperData
{

 
    const XML_PATH_GOOGLE_CLIENT_ID = 'sociallogin/google/client_id';
    const XML_PATH_GOOGLE_CLIENT_SECRET = 'sociallogin/google/client_secret';
    const XML_PATH_GOOGLE_REDIRECT_URL = 'sociallogin/google/redirect_url';
    const XML_PATH_GOOGLE_SEND_PASSWORD = 'sociallogin/google/send_password';
    
    public function getClientId($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_GOOGLE_CLIENT_ID, $storeId);
    }

    public function sendPassword($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_GOOGLE_SEND_PASSWORD, $storeId);
    }

    public function getClientSecret($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_GOOGLE_CLIENT_SECRET, $storeId);
    }

    public function getRedirectUrl($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_GOOGLE_REDIRECT_URL, $storeId);
    }

    public function getAuthUrl()
    {
        $url = $this->_getUrl('lofsociallogin/google/callback', ['_secure' => $this->isSecure()]);
        return str_replace("/index.php", "", $url);
    }

    public function getUrl($path)
    {
        return $this->_getUrl($path, ['_secure' => $this->isSecure()]);
    }
}
