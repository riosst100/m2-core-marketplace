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
 * @package    Lofmp_SellerBadge
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lofmp\SellerBadge\Model\SellerBadge;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;

class Image
{
    /**
     * @var FileInfo
     */
    private $fileInfo;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * Image constructor.
     * @param FileInfo $fileInfo
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        FileInfo $fileInfo,
        StoreManagerInterface $storeManager
    ) {
        $this->fileInfo = $fileInfo;
        $this->storeManager = $storeManager;
    }

    /**
     * @param $model
     * @param $param
     * @return string
     * @throws LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getUrl($model, $param)
    {
        $url = '';
        $image = $model->getData($param);
        if ($image) {
            if (is_string($image)) {
                $store = $this->storeManager->getStore();
                $mediaBaseUrl = $store->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);
                if ($this->fileInfo->isBeginsWithMediaDirectoryPath($image)) {
                    $relativePath = $this->fileInfo->getRelativePathToMediaDirectory($image);
                    $url = rtrim($mediaBaseUrl, '/') . '/' . ltrim($relativePath, '/');
                } elseif (substr($image, 0, 1) !== '/') {
                    $url = rtrim($mediaBaseUrl, '/') . '/' . ltrim(FileInfo::ENTITY_MEDIA_PATH, '/') . '/' . $image;
                } else {
                    $url = $image;
                }
            } else {
                throw new LocalizedException(
                    __('Something went wrong while getting the image url.')
                );
            }
        }
        return $url;
    }
}
