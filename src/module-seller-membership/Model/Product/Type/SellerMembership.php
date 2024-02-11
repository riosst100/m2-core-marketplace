<?php

namespace CoreMarketplace\SellerMembership\Model\Product\Type;

use Lofmp\SellerMembership\Model\Source\DurationUnit;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\File\Http;
use Magento\Framework\File\UploaderFactory;

class SellerMembership extends \Lofmp\SellerMembership\Model\Product\Type\SellerMembership
{
    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $_filesystem;

    /**
     * @var \Magento\MediaStorage\Helper\File\Storage\Database
     */
    protected $_fileStorageDb;

    /**
     * @var UploaderFactory
     */
    private $uploaderFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * Core event manager proxy
     *
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $_eventManager;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger;

    /**
     * @var \Magento\Catalog\Model\Product\Type
     */
    protected $_catalogProductType;

    /**
     * @var \Magento\Eav\Model\Config
     */
    protected $_eavConfig;

    /**
     * @var \Magento\Catalog\Model\Product\Option
     */
    protected $_catalogProductOption;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * Serializer interface instance.
     *
     * @var \Magento\Framework\Serialize\Serializer\Json
     * @since 102.0.0
     */
    protected $serializer;

    /**
     * @var \Lofmp\SellerMembership\Helper\Data
     */
    protected $membershipHelper;

    /**
     * @param \Magento\Catalog\Model\Product\Option $catalogProductOption
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\Catalog\Model\Product\Type $catalogProductType
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\MediaStorage\Helper\File\Storage\Database $fileStorageDb
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Psr\Log\LoggerInterface $logger
     * @param ProductRepositoryInterface $productRepository
     * @param \Magento\Framework\Serialize\Serializer\Json|null $serializer
     * @param UploaderFactory $uploaderFactory
     * @param \Lofmp\SellerMembership\Helper\Data $membershipHelper
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Catalog\Model\Product\Option $catalogProductOption,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Catalog\Model\Product\Type $catalogProductType,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\MediaStorage\Helper\File\Storage\Database $fileStorageDb,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Registry $coreRegistry,
        \Psr\Log\LoggerInterface $logger,
        ProductRepositoryInterface $productRepository,
        \Lofmp\SellerMembership\Helper\Data $membershipHelper,
        \Magento\Framework\Serialize\Serializer\Json $serializer = null,
        UploaderFactory $uploaderFactory = null
    ) {
        $this->_catalogProductOption = $catalogProductOption;
        $this->_eavConfig = $eavConfig;
        $this->_catalogProductType = $catalogProductType;
        $this->_coreRegistry = $coreRegistry;
        $this->_eventManager = $eventManager;
        $this->_fileStorageDb = $fileStorageDb;
        $this->_filesystem = $filesystem;
        $this->_logger = $logger;
        $this->productRepository = $productRepository;
        $this->membershipHelper = $membershipHelper;
        $this->serializer = $serializer ?: ObjectManager::getInstance()
            ->get(\Magento\Framework\Serialize\Serializer\Json::class);
        $this->uploaderFactory = $uploaderFactory ?: ObjectManager::getInstance()->get(UploaderFactory::class);

        parent::__construct(
            $catalogProductOption,
            $eavConfig,
            $catalogProductType,
            $eventManager,
            $fileStorageDb,
            $filesystem,
            $coreRegistry,
            $logger,
            $productRepository,
            $serializer,
            $uploaderFactory
        );
    }

     /**
     * Product type code.
     */
    const TYPE_CODE = 'seller_membership';

    /**
     * Prepare product and its configuration to be added to some products list.
     * Perform standard preparation process and then prepare options belonging to specific product type.
     *
     * @param \Magento\Framework\DataObject  $buyRequest
     * @param \Magento\Catalog\Model\Product $product
     * @param string                         $processMode
     *
     * @return array|string
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected function _prepareProduct(\Magento\Framework\DataObject $buyRequest, $product, $processMode)
    {

        $options = $buyRequest->getData('seller_membership');

        $duration = isset($options['duration']) ? $options['duration'] : 0;

        if (!$duration) {
            return __('You need to choose options for your item.')->render();
        }
        list($duration, $durationUnit) = explode('|', $duration);

        $durationOptions = $product->getData('seller_duration');
        if (!is_array($durationOptions)) {
            $durationOptions = @json_decode($durationOptions, true);
        }

        $packagePrice = 0;
        $durationOptions = $this->membershipHelper->filterMembershipDurationByWebsite($durationOptions);
        foreach ($durationOptions as $option) {
            if ($duration == $option['membership_duration'] && $durationUnit == $option['membership_unit']) {
                $packagePrice = $option['membership_price'];
            }
        }

        $options['membership_duration'] = $duration;
        $options['membership_unit'] = $durationUnit;
        $options['seller_group'] = $product->getData('seller_group');
        $options['membership_price'] = $packagePrice;

        $product->addCustomOption('seller_duration', @serialize($options));

        return parent::_prepareProduct($buyRequest, $product, $processMode);
    }

    /**
     * Get duration label.
     *
     * @param int $duration
     * @param int $unit
     */
    public function getDurationLabel($duration, $unit)
    {
        $label = '';
        switch ($unit) {
            case DurationUnit::DURATION_DAY:
                $label = $duration == 1 ? __('%1 day', $duration) : __('%1 days', $duration);
                break;
            case DurationUnit::DURATION_WEEK:
                $label = $duration == 1 ? __('%1 week', $duration) : __('%1 weeks', $duration);
                break;
            case DurationUnit::DURATION_MONTH:
                $label = $duration == 1 ? __('%1 month', $duration) : __('%1 months', $duration);
                break;
            case DurationUnit::DURATION_YEAR:
                $label = $duration == 1 ? __('%1 year', $duration) : __('%1 years', $duration);
                break;
        }

        return $label;
    }

    /**
     * Prepare additional options/information for order item which will be
     * created from this product.
     *
     * @param \Magento\Catalog\Model\Product $product
     *
     * @return array
     */
    public function getOrderOptions($product)
    {
        $options = parent::getOrderOptions($product);
        if ($attributesOption = $product->getCustomOption('seller_membership')) {
            $data = @unserialize($attributesOption->getValue());
            $options['seller_membership'] = $data;
            $options['attributes_info'] = [
                ['label' => __('Duration').'', 'value' => $this->getDurationLabel($data['duration'], $data['membership_unit']).''],
            ];
        }

        return $options;
    }

    /**
     * Return true if product has options.
     *
     * @param \Magento\Catalog\Model\Product $product
     *
     * @return bool
     */
    public function hasOptions($product)
    {
        $duration = $product->getData('seller_duration');

        return (is_array($duration) && (sizeof($duration) >= 1)) || $product->getHasOptions();
    }
}
