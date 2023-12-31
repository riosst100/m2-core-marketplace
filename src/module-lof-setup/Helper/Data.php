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
 * @category  Landofcoder
 * @package   Lof_Setup
 * @copyright Copyright (c) 2016 Landofcoder (http://www.landofcoder.com/)
 * @license   http://www.landofcoder.com/LICENSE-1.0.html
 */
namespace Lof\Setup\Helper;
use Magento\Framework\App\Filesystem\DirectoryList;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Cms\Model\Template\FilterProvider
     */
    protected $_filterProvider;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Theme\Model\Theme
     */
    protected $_collectionThemeFactory;

    /**
     * File system
     *
     * @var \Magento\Framework\Filesystem
     */
    protected $_filesystem;

    /**
     * Media directory read
     *
     * @var \Magento\Framework\Filesystem\Directory\Read
     */
    protected $lofthemeDirectory;

    /**
     * @param \Magento\Framework\App\Helper\Context                      $context                
     * @param \Magento\Store\Model\StoreManagerInterface                 $storeManager           
     * @param \Magento\Framework\Registry                                $registry               
     * @param \Magento\Cms\Model\Template\FilterProvider                 $filterProvider         
     * @param \Magento\Theme\Model\ResourceModel\Theme\CollectionFactory $collectionThemeFactory 
     * @param \Magento\Framework\Filesystem                              $filesystem           
     * @param \Magento\Theme\Model\Theme                                 $themeModel             
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Registry $registry,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        \Magento\Theme\Model\ResourceModel\Theme\CollectionFactory $collectionThemeFactory,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Theme\Model\Theme $themeModel
    ) {
        parent::__construct($context);
        $this->_storeManager = $storeManager;
        $this->_coreRegistry = $registry;
        $this->_filterProvider = $filterProvider;
        $this->_collectionThemeFactory = $collectionThemeFactory;
        $this->_filesystem = $filesystem;
        $this->_themeModel = $themeModel;
    }
    /**
     * Check if current url is url for home page
     *
     * @return bool
     */
    public function isHomePage()
    {
        $currentUrl = $this->getUrl('', ['_current' => true]);
        $urlRewrite = $this->getUrl('*/*/*', ['_current' => true, '_use_rewrite' => true]);
        return $currentUrl == $urlRewrite;
    }

    public function getMediaUrl()
    {
        $url = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        return $url;
    }

    public function getCoreRegistry()
    {
        return $this->_coreRegistry;
    }

    /**
     * Check product is new
     *
     * @param  Mage_Catalog_Model_Product $_product
     * @return bool
     */
    public function checkProductIsNew($_product = null) 
    {
        $from_date = $_product->getNewsFromDate();
        $to_date = $_product->getNewsToDate();
        $is_new = false;
        $is_new = $this->isNewProduct($from_date, $to_date);
        $today = strtotime("now");

        if ($from_date && $to_date) {
            $from_date = strtotime($from_date);
            $to_date = strtotime($to_date);
            if ($from_date <= $today && $to_date >= $today) {
                $is_new = true;
            }
        }
        elseif ($from_date && !$to_date) {
            $from_date = strtotime($from_date);
            if ($from_date <= $today) {
                $is_new = true;
            }
        }elseif (!$from_date && $to_date) {
            $to_date = strtotime($to_date);
            if ($to_date >= $today) {
                $is_new = true;
            }
        }
        return $is_new;
    }

    public function isNewProduct( $created_date, $num_days_new = 3) 
    {
        $check = false;

        $startTimeStamp = strtotime($created_date);
        $endTimeStamp = strtotime("now");

        $timeDiff = abs($endTimeStamp - $startTimeStamp);
        $numberDays = $timeDiff/86400;// 86400 seconds in one day

        // and you might want to convert to integer
        $numberDays = intval($numberDays);
        if($numberDays <= $num_days_new) {
            $check = true;
        }
        return $check;
    }

    public function subString($text, $length = 100, $replacer = '...', $is_striped = true) 
    {
        $text = ($is_striped == true) ? strip_tags($text) : $text;
        if (strlen($text) <= $length) {
            return $text;
        }
        $text = substr($text, 0, $length);
        $pos_space = strrpos($text, ' ');
        return substr($text, 0, $pos_space) . $replacer;
    }

    public function filter($str)
    {
        $html = $this->_filterProvider->getPageFilter()->filter($str);
        return $html;
    }

    public function getAllStores() 
    {
        $allStores = $this->_storeManager->getStores();
        $stores = array();
        foreach ($allStores as $_eachStoreId => $val)
        {
            $stores[]  = $this->_storeManager->getStore($_eachStoreId)->getId();
        }
        return $stores;
    }

    // Store code
    /**
     * get path folder lof theme
     *
     * @param  [int] $storeId
     * @return array
     */
    public function getLofTheme($storeId = null)
    {

        $store = $this->_storeManager->getStore($storeId);
        
        $themeId =  $this->scopeConfig->getValue(
            \Magento\Framework\View\DesignInterface::XML_PATH_THEME_ID,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
        $theme = $this->_themeModel->load($themeId);

        $themePaths = [];
        $file = $this->_filesystem->getDirectoryRead(DirectoryList::APP)->getAbsolutePath('design/frontend/');
        $lofPackagePaths = glob($file . '*/*/config.xml');
        foreach ($lofPackagePaths as $k => $v) {
            $tmp = str_replace($file, "", $v);
            $tmp = str_replace("/config.xml", "", $tmp);
            if($theme->getCode()) {
                $t = explode('/', $theme->getCode());
                if($t[0] === $tmp ) {
                    $themePaths[] = $v;
                }
            }else{
                $themePaths[] = $v;
            }
        }
        return $themePaths;
    }
}