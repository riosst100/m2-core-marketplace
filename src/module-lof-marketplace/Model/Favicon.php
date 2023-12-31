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
 * @package    Lof_MarketPlace
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lof\MarketPlace\Model;

class Favicon extends \Magento\Theme\Model\Favicon\Favicon
{
    /**
     * @return string
     */
    public function getDefaultFavicon()
    {
        return 'favicon.ico';
    }

    /**
     * @return string
     */
    protected function prepareFaviconFile()
    {
        $folderName = \Magento\Config\Model\Config\Backend\Image\Favicon::UPLOAD_DIR;
        $scopeConfig = $this->scopeConfig->getValue(
            'lofmarketplace/design/head_favicon',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        $path = $folderName . '/' . $scopeConfig;
        $faviconUrl = $this->storeManager->getStore()
                ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . $path;

        if ($scopeConfig !== null && $this->checkIsFile($path)) {
            return $faviconUrl;
        }

        return false;
    }
}
