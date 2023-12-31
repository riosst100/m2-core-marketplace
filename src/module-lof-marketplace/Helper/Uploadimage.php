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

namespace Lof\MarketPlace\Helper;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Area;
use Magento\Catalog\Helper\Image;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Uploadimage extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * Swatch area inside media folder
     *
     */
    const  SWATCH_MEDIA_PATH = 'catalog/product';

    /**
     * @var \Magento\Catalog\Model\Product\Media\Config
     */
    protected $mediaConfig;

    /**
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface
     */
    protected $mediaDirectory;

    /**
     * @var \Magento\MediaStorage\Helper\File\Storage\Database|null
     */
    protected $fileStorageDb = null;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\Image\Factory
     */
    protected $imageFactory;

    /**
     * @var \Magento\Theme\Model\ResourceModel\Theme\Collection
     */
    protected $themeCollection;

    /**
     * @var \Magento\Framework\View\ConfigInterface
     */
    protected $viewConfig;

    /**
     * @var string[]
     */
    protected $swatchImageTypes = ['swatch_image', 'swatch_thumb'];

    /**
     * Uploadimage constructor.
     *
     * @param \Magento\Catalog\Model\Product\Media\Config $mediaConfig
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\MediaStorage\Helper\File\Storage\Database $fileStorageDb
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Image\Factory $imageFactory
     * @param \Magento\Theme\Model\ResourceModel\Theme\Collection $themeCollection
     * @param \Magento\Framework\View\ConfigInterface $configInterface
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function __construct(
        \Magento\Catalog\Model\Product\Media\Config $mediaConfig,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\MediaStorage\Helper\File\Storage\Database $fileStorageDb,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Image\Factory $imageFactory,
        \Magento\Theme\Model\ResourceModel\Theme\Collection $themeCollection,
        \Magento\Framework\View\ConfigInterface $configInterface
    ) {
        $this->mediaConfig = $mediaConfig;
        $this->fileStorageDb = $fileStorageDb;
        $this->mediaDirectory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $this->storeManager = $storeManager;
        $this->imageFactory = $imageFactory;
        $this->themeCollection = $themeCollection;
        $this->viewConfig = $configInterface;
    }

    /**
     * @param $swatchType
     * @param $file
     * @return string
     */
    public function getSwatchAttributeImage($swatchType, $file)
    {
        $generationPath = $swatchType . '/' . $this->getFolderNameSize($swatchType) . $file;
        $absoluteImagePath = $this->mediaDirectory
            ->getAbsolutePath($this->getSwatchMediaPath() . '/' . $generationPath);
        //phpcs:disable Magento2.Functions.DiscouragedFunction.Discouraged
        if (!file_exists($absoluteImagePath)) {
            $this->generateSwatchVariations($file);
        }
        return $this->getSwatchMediaUrl() . '/' . $generationPath;
    }

    /**
     * @param $file
     * @return string|string[]
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function moveImageFromTmp($file)
    {
        if (strrpos($file, '.tmp') == strlen($file) - 4) {
            $file = substr($file, 0, strlen($file) - 4);
        }

        $destinationFile = $this->getUniqueFileName($file);

        /** @var $storageHelper \Magento\MediaStorage\Helper\File\Storage\Database */
        $storageHelper = $this->fileStorageDb;

        if ($storageHelper->checkDbUsage()) {
            $storageHelper->renameFile(
                $this->mediaConfig->getTmpMediaShortUrl($file),
                $this->mediaConfig->getMediaShortUrl($destinationFile)
            );

            $this->mediaDirectory->delete($this->mediaConfig->getTmpMediaPath($file));
            $this->mediaDirectory->delete($this->getAttributeSwatchPath($destinationFile));
        } else {
            $this->mediaDirectory->renameFile(
                $this->mediaConfig->getTmpMediaPath($file),
                $this->getAttributeSwatchPath($destinationFile)
            );
        }

        return str_replace('\\', '/', $destinationFile);
    }

    /**
     * @param $file
     * @return string|string[]
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function moveImageFromZip($file)
    {
        if (strrpos($file, '.tmp') == strlen($file) - 4) {
            $file = substr($file, 0, strlen($file) - 4);
        }

        $destinationFile = $this->getUniqueFileName($file);

        /** @var $storageHelper \Magento\MediaStorage\Helper\File\Storage\Database */
        $storageHelper = $this->fileStorageDb;

        if ($storageHelper->checkDbUsage()) {
            $storageHelper->renameFile(
                $this->mediaConfig->getTmpMediaShortUrl($file),
                $this->mediaConfig->getMediaShortUrl($destinationFile)
            );

            $this->mediaDirectory->delete($this->mediaConfig->getTmpMediaPath($file));
            $this->mediaDirectory->delete($this->getAttributeSwatchPath($destinationFile));
        } else {
            $this->mediaDirectory->renameFile(
                $this->mediaConfig->getTmpMediaPath($file),
                $this->getAttributeSwatchPath($destinationFile)
            );
        }

        return str_replace('\\', '/', $destinationFile);
    }

    /**
     * @param $file
     * @return string
     */
    protected function getUniqueFileName($file)
    {
        if ($this->fileStorageDb->checkDbUsage()) {
            $destFile = $this->fileStorageDb->getUniqueFilename(
                $this->mediaConfig->getBaseMediaUrlAddition(),
                $file
            );
        } else {
            $filePath = \Magento\MediaStorage\Model\File\Uploader::getNewFileName(
                $this->mediaDirectory->getAbsolutePath($this->getAttributeSwatchPath($file))
            );
            // phpcs:ignore Magento2.Functions.DiscouragedFunction
            $destFile = dirname($file) . '/' . $filePath;
        }

        return $destFile;
    }

    /**
     * @param $imageUrl
     * @return $this
     */
    public function generateSwatchVariations($imageUrl)
    {
        $absoluteImagePath = $this->mediaDirectory->getAbsolutePath($this->getAttributeSwatchPath($imageUrl));
        foreach ($this->swatchImageTypes as $swatchType) {
            $imageConfig = $this->getImageConfig();
            $swatchNamePath = $this->generateNamePath($imageConfig, $imageUrl, $swatchType);
            $image = $this->imageFactory->create($absoluteImagePath);
            $this->setupImageProperties($image);
            $image->resize($imageConfig[$swatchType]['width'], $imageConfig[$swatchType]['height']);
            $this->setupImageProperties($image, true);
            $image->save($swatchNamePath['path_for_save'], $swatchNamePath['name']);
        }
        return $this;
    }

    /**
     * @param \Magento\Framework\Image $image
     * @param bool $isSwatch
     * @return $this
     */
    protected function setupImageProperties(\Magento\Framework\Image $image, $isSwatch = false)
    {
        $image->quality(100);
        $image->constrainOnly(true);
        $image->keepAspectRatio(true);
        if ($isSwatch) {
            $image->keepFrame(true);
            $image->keepTransparency(true);
            $image->backgroundColor('#FFF');
        }
        return $this;
    }

    /**
     * @param $imageConfig
     * @param $imageUrl
     * @param $swatchType
     * @return array
     */
    protected function generateNamePath($imageConfig, $imageUrl, $swatchType)
    {
        $fileName = $this->prepareFileName($imageUrl);
        $absolutePath = $this->mediaDirectory->getAbsolutePath($this->getSwatchCachePath($swatchType));
        return [
            'path_for_save' => $absolutePath . $this->getFolderNameSize($swatchType, $imageConfig) . $fileName['path'],
            'name' => $fileName['name']
        ];
    }

    /**
     * @param $swatchType
     * @param null $imageConfig
     * @return string
     */
    public function getFolderNameSize($swatchType, $imageConfig = null)
    {
        if ($imageConfig === null) {
            $imageConfig = $this->getImageConfig();
        }
        return $imageConfig[$swatchType]['width'] . 'x' . $imageConfig[$swatchType]['height'];
    }

    /**
     * @return array
     */
    public function getImageConfig()
    {
        $imageConfig = [];
        foreach ($this->themeCollection->loadRegisteredThemes() as $theme) {
            $config = $this->viewConfig->getViewConfig([
                'area' => Area::AREA_FRONTEND,
                'themeModel' => $theme,
            ]);
            // phpcs:disable Magento2.Performance.ForeachArrayMerge.ForeachArrayMerge
            $imageConfig = array_merge(
                $imageConfig,
                $config->getMediaEntities('Magento_Catalog', Image::MEDIA_TYPE_CONFIG_NODE)
            );
        }
        return $imageConfig;
    }

    /**
     * Image url /m/a/magento.png return ['name' => 'magento.png', 'path => '/m/a']
     *
     * @param string $imageUrl
     * @return array
     */
    protected function prepareFileName($imageUrl)
    {
        $fileArray = explode('/', $imageUrl);
        $fileName = array_pop($fileArray);
        $filePath = implode('/', $fileArray);
        return ['name' => $fileName, 'path' => $filePath];
    }

    /**
     * Url type http://url/pub/media/attribute/swatch/
     *
     * @return string
     */
    public function getSwatchMediaUrl()
    {
        return $this->storeManager
                ->getStore()
                ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . $this->getSwatchMediaPath();
    }

    /**
     * Return example: attribute/swatch/m/a/magento.jpg
     *
     * @param string $file
     * @return string
     */
    public function getAttributeSwatchPath($file)
    {
        return $this->getSwatchMediaPath() . '/' . $this->prepareFile($file);
    }

    /**
     * Media swatch path
     *
     * @return string
     */
    public function getSwatchMediaPath()
    {
        return self::SWATCH_MEDIA_PATH;
    }

    /**
     * Media path with swatch_image or swatch_thumb folder
     *
     * @param string $swatchType
     * @return string
     */
    public function getSwatchCachePath($swatchType)
    {
        return self::SWATCH_MEDIA_PATH . '/' . $swatchType . '/';
    }

    /**
     * Prepare file for saving
     *
     * @param string $file
     * @return string
     */
    protected function prepareFile($file)
    {
        return ltrim(str_replace('\\', '/', $file), '/');
    }
}
