<?php
/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * http://landofcoder.com/license
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Landofcoder
 * @package    Lof_MarketPlace
 * @copyright  Copyright (c) 2016 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */

namespace Lofmp\Slider\Block\Seller;

class Slider extends \Magento\Framework\View\Element\Template {

	/**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
	protected $_coreRegistry = null;
    /**
     * @var \Lof\MarketPlace\Model\Seller
     */
    protected $_sellerFactory;
    /**
     * @var \Lofmp\Slider\Model\Slider
     */
    protected $slider;
    /**
     * @var \Lof\MarketPlace\Model\Data
     */
    protected $_helper;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_resource;

    protected $_fileSystem;

    protected $_imageFactory;

    protected $_storeManager;

    /**
     * @var mixed|array|false
     */
    protected $_currentSlider = false;

    /**
     * @var \Magento\Framework\Serialize\SerializerInterface
     */
    protected $serializer;

    /**
     * @param \Magento\Framework\View\Element\Template\Context
     * @param \Magento\Framework\Registry
     * @param \Lof\MarketPlace\Model\Seller
     * @param \Lofmp\Slider\Model\Slider
     * @param \Magento\Framework\App\ResourceConnection
     * @param \Magento\Framework\Filesystem
     * @param \Magento\Framework\Image\AdapterFactory
     * @param \Magento\Store\Model\StoreManagerInterface
     * @param \Magento\Framework\Serialize\SerializerInterface $serializer
     * @param array
    */
	public function __construct(
    	\Magento\Framework\View\Element\Template\Context $context,
    	\Magento\Framework\Registry $registry,
        \Lof\MarketPlace\Model\Seller $sellerFactory,
        \Lofmp\Slider\Model\Slider $slider,
        \Lof\MarketPlace\Helper\Data $helper,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Image\AdapterFactory $imageFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Serialize\SerializerInterface $serializer,
        array $data = []
    ) {
        $this->slider         = $slider;
		$this->_helper        = $helper;
		$this->_coreRegistry  = $registry;
		$this->_sellerFactory = $sellerFactory;
		$this->_resource      = $resource;
        $this->_fileSystem       = $filesystem;
        $this->_imageFactory = $imageFactory;
        $this->_storeManager = $storeManager;
        $this->serializer = $serializer;
        parent::__construct($context, $data);
    }

    /**
     * Get current seller slider
     *
     * @return mixed|array|false
     */
    public function getSlider()
    {
        if (!$this->_currentSlider) {
            $seller = $this->getCurrentSeller();
            if ($seller && $seller->getId()) {
                $slider = $this->slider->getCollection()->addFieldToFilter('is_active',1)->addFieldToFilter('seller_id',$seller->getId())->getFirstItem();
                $this->_currentSlider =  $slider->getData();
            }
        }
        return $this->_currentSlider;
    }

    /**
     * Get current seller
     * @return mixed
     */
    public function getCurrentSeller()
    {
        $seller = $this->_coreRegistry->registry('current_seller');
        if ($seller) {
            $this->setData('current_seller', $seller);
        }
        return $seller;
    }

    /**
     * resize image
     *
     * @param string $image
     * @param int|float|null $width
     * @param int|float|null $height
     * @return string|null
     */
    public function resize($image, $width = null, $height = null)
    {
        if ($image == '') return null;

        $parsed = parse_url($image);
        if (!empty($parsed['scheme'])) { //return external image link
            return $image;
        }
        try {
            // print_r($image);die;
            $absolutePath = $this->_fileSystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA)->getAbsolutePath($image);

            $imageResized = $this->_fileSystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA)->getAbsolutePath('resized/'.$width.'/').$image;

            if (!file_exists($imageResized) && file_exists($absolutePath)) {
                //create image factory...
                $imageResize = $this->_imageFactory->create();
                $imageResize->open($absolutePath);
                $imageResize->constrainOnly(TRUE);
                $imageResize->keepTransparency(TRUE);
                $imageResize->keepFrame(FALSE);
                $imageResize->keepAspectRatio(TRUE);
                $imageResize->resize($width,$height);
                //destination folder
                $destination = $imageResized ;
                //save image
                $imageResize->save($destination);
            }
            if (file_exists($imageResized)) {
                $resizedURL = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA).'resized/'.$width.'/'.$image;
            } else {
                $resizedURL = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA).'/'.$image;
            }
        } catch (\Exception $e) {
            $resizedURL = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA).'/'.$image;
        }
        return $resizedURL;
    }

    /**
     * get slider images
     *
     * @param string $images
     * @return mixed|array
     */
    public function getSliderImages($images)
    {
        if ($images) {
            $dataImagesArray = $this->serializer->unserialize($images);
            if ($dataImagesArray) {
                $tmp = [];
                foreach ($dataImagesArray as $item) {
                    $tmp[] = $this->convertToObject($item);
                }
                $dataImagesArray = $tmp;
            }
            return $dataImagesArray;
        }
        return $images;
    }

    /**
     * @param mixed $array
     * @return mixed
     */
    public function convertToObject($array)
    {
        if (is_array($array)) {
            // Create new stdClass object
            $object = new \stdClass();

            // Use loop to convert array into
            // stdClass object
            foreach ($array as $key => $value) {
                if (is_array($value)) {
                    $value = $this->convertToObject($value);
                }
                $object->$key = $value;
            }
            $array = $object;
        }
        return $array;
    }
}
