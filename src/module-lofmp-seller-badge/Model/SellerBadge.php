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

namespace Lofmp\SellerBadge\Model;

use Lofmp\SellerBadge\Api\Data\SellerBadgeInterface;
use Lofmp\SellerBadge\Model\ResourceModel\SellerBadge as SellerBadgeResourceModel;
use Lofmp\SellerRule\Model\Rule;
use Magento\Framework\Api\AttributeValueFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\ExtensionAttributesFactory;
use Lof\MarketPlace\Model\ResourceModel\Seller\Collection as SellerCollection;

class SellerBadge extends Rule implements SellerBadgeInterface
{
    /**#@+
     * Available events
     */
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;

    /**
     * @var string
     */
    protected $_eventPrefix = 'lofmp_sellerbadge_badge';

    /**
     * Store matched seller Ids
     *
     * @var array
     */
    protected $_sellerIds;

    /**
     * Limitation for seller collection
     *
     * @var int|array|null
     */
    protected $_sellersFilter = null;

    /**
     * @var \Magento\Framework\Stdlib\DateTime
     */
    private $dateTime;

    /**
     * @var \Magento\Framework\Api\DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * @var \Lofmp\SellerBadge\Api\Data\SellerBadgeInterfaceFactory
     */
    protected $sellerBadgeDataFactory;

    /**
     * @var \Magento\Framework\Model\ResourceModel\Iterator
     */
    protected $_resourceIterator;

    /**
     * @var \Lof\MarketPlace\Model\ResourceModel\Seller\CollectionFactory
     */
    private $_sellerCollectionFactory;

    /**
     * @var \Lof\MarketPlace\Model\SellerFactory
     */
    private $_sellerFactory;

    /**
     * @var \Lofmp\SellerBadge\Model\Indexer\SellerBadgeManagerIndexer
     */
    private $_sellerBadgeManagerIndexer;

    /**
     * SellerBadge constructor.
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param \Lofmp\SellerBadge\Api\Data\SellerBadgeInterfaceFactory $sellerBadgeDataFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param SellerBadgeResourceModel $resource
     * @param SellerBadgeResourceModel\Collection $resourceCollection
     * @param \Magento\Framework\Stdlib\DateTime $dateTime
     * @param \Lofmp\SellerRule\Model\Rule\Condition\CombineFactory $combineFactory
     * @param \Lofmp\SellerRule\Model\Rule\Action\CollectionFactory $actionCollectionFactory
     * @param \Magento\Framework\Model\ResourceModel\Iterator $resourceIterator
     * @param \Lof\MarketPlace\Model\ResourceModel\Seller\CollectionFactory $sellerCollectionFactory
     * @param \Lofmp\SellerBadge\Model\Indexer\SellerBadgeManagerIndexer $sellerBadgeManagerIndexer
     * @param \Lof\MarketPlace\Model\SellerFactory $sellerFactory
     * @param array $data
     * @param ExtensionAttributesFactory|null $extensionFactory
     * @param AttributeValueFactory|null $customAttributeFactory
     * @param \Magento\Framework\Serialize\Serializer\Json|null $serializer
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Lofmp\SellerBadge\Api\Data\SellerBadgeInterfaceFactory $sellerBadgeDataFactory,
        \Magento\Framework\Api\DataObjectHelper $dataObjectHelper,
        \Lofmp\SellerBadge\Model\ResourceModel\SellerBadge $resource,
        \Lofmp\SellerBadge\Model\ResourceModel\SellerBadge\Collection $resourceCollection,
        \Magento\Framework\Stdlib\DateTime $dateTime,
        \Lofmp\SellerRule\Model\Rule\Condition\CombineFactory $combineFactory,
        \Lofmp\SellerRule\Model\Rule\Action\CollectionFactory $actionCollectionFactory,
        \Magento\Framework\Model\ResourceModel\Iterator $resourceIterator,
        \Lof\MarketPlace\Model\ResourceModel\Seller\CollectionFactory $sellerCollectionFactory,
        \Lofmp\SellerBadge\Model\Indexer\SellerBadgeManagerIndexer $sellerBadgeManagerIndexer,
        \Lof\MarketPlace\Model\SellerFactory $sellerFactory,
        array $data = [],
        ExtensionAttributesFactory $extensionFactory = null,
        AttributeValueFactory $customAttributeFactory = null,
        \Magento\Framework\Serialize\Serializer\Json $serializer = null
    ) {
        $this->_sellerBadgeManagerIndexer = $sellerBadgeManagerIndexer;
        $this->_sellerFactory = $sellerFactory;
        $this->_sellerCollectionFactory = $sellerCollectionFactory;
        $this->dateTime = $dateTime;
        $this->_resourceIterator = $resourceIterator;
        $this->sellerBadgeDataFactory = $sellerBadgeDataFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        parent::__construct(
            $context,
            $registry,
            $formFactory,
            $localeDate,
            $combineFactory,
            $actionCollectionFactory,
            $resource,
            $resourceCollection,
            $data,
            $extensionFactory,
            $customAttributeFactory,
            $serializer
        );
    }

    /**
     * Init resource model and id field
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init(SellerBadgeResourceModel::class);
        $this->setIdFieldName('badge_id');
    }

    /**
     * Retrieve seller badge model with seller badge data
     *
     * @return SellerBadgeInterface
     */
    public function getDataModel()
    {
        $data = $this->getData();
        $sellerBadgeDataObject = $this->sellerBadgeDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $sellerBadgeDataObject,
            $data,
            SellerBadgeInterface::class
        );

        return $sellerBadgeDataObject;
    }

    /**
     * Prepare Seller Badge Available.
     *
     * @return array
     */
    public function getAvailableStatuses()
    {
        return [self::STATUS_ENABLED => __('Enabled'), self::STATUS_DISABLED => __('Disabled')];
    }

    /**
     * @return $this|SellerBadge
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function beforeSave()
    {
        parent::beforeSave();
        if ($this->isObjectNew()) {
            $this->setCreatedAt($this->dateTime->formatDate(true));
        }
        $this->setUpdatedAt($this->dateTime->formatDate(true));
        return $this;
    }

    /**
     * @return \Lofmp\SellerBadge\Model\SellerBadge
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function afterSave()
    {
        if (!$this->getIsActive()) {
            return parent::afterSave();
        }

        if ($this->isObjectNew() && !$this->_sellerBadgeManagerIndexer->isIndexerScheduled()) {
            $this->_sellerBadgeManagerIndexer->executeRow($this->getBadgeId());
        } else {
            $this->_sellerBadgeManagerIndexer->getIndexer()->invalidate();
        }
        return parent::afterSave();
    }

    /**
     * @param null $ids
     * @return array
     */
    public function getBadgeMatchingSellerIds($ids = null): array
    {
        $this->_sellersFilter = $ids;
        return $this->getMatchingSellerIdsByBadge();
    }

    /**
     * @return array
     */
    public function getMatchingSellerIdsByBadge(): array
    {
        if ($this->_sellerIds === null) {
            $this->_sellerIds = [];
            /** @var $sellerCollection SellerCollection */
            $sellerCollection = $this->_sellerCollectionFactory->create();

            if ($this->_sellersFilter) {
                $sellerCollection = $this->addIdFilter($sellerCollection, $this->_sellersFilter);
            }

            $this->_resourceIterator->walk(
                $sellerCollection->getSelect(),
                [[$this, 'callbackValidateSeller']],
                [
                    'attributes' => $this->getCollectedAttributes(),
                    'seller' => $this->_sellerFactory->create(),
                    'badge' => $this,
                ]
            );
        }

        return $this->_sellerIds;
    }

    /**
     * @param $args
     */
    public function callbackValidateSeller($args)
    {
        $seller = $args['seller'];
        $seller->setData($args['row']);
        $result = $this->getConditions()->validate($seller);

        if ($result) {
            $this->_sellerIds[$seller->getId()] = true;
        }
    }

    /**
     * @param $sellerCollection
     * @param $sellerId
     * @param false $exclude
     * @return mixed
     */
    public function addIdFilter($sellerCollection, $sellerId, $exclude = false)
    {
        if (empty($sellerId)) {
            return $sellerCollection;
        }
        if (is_array($sellerId)) {
            if (!empty($sellerId)) {
                if ($exclude) {
                    $condition = ['nin' => $sellerId];
                } else {
                    $condition = ['in' => $sellerId];
                }
            } else {
                $condition = '';
            }
        } else {
            if ($exclude) {
                $condition = ['neq' => $sellerId];
            } else {
                $condition = $sellerId;
            }
        }
        $sellerCollection->addFieldToFilter('seller_id', $condition);
        return $sellerCollection;
    }

    /**
     * @return array|mixed|string|null
     */
    public function getBadgeId()
    {
        return $this->getData(self::BADGE_ID);
    }

    /**
     * @param string $badgeId
     * @return SellerBadgeInterface|\Lofmp\SellerBadge\Model\SellerBadge
     */
    public function setBadgeId($badgeId)
    {
        return $this->setData(self::BADGE_ID, $badgeId);
    }

    /**
     * @return array|mixed|string|null
     */
    public function getName()
    {
        return $this->getData(self::NAME);
    }

    /**
     * @param string $name
     * @return SellerBadgeInterface|\Lofmp\SellerBadge\Model\SellerBadge
     */
    public function setName($name)
    {
        return $this->setData(self::NAME, $name);
    }

    /**
     * @return \Magento\Framework\Api\ExtensionAttributesInterface|null
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * @param \Lofmp\SellerBadge\Api\Data\SellerBadgeExtensionInterface $extensionAttributes
     * @return \Lofmp\SellerBadge\Model\SellerBadge
     */
    public function setExtensionAttributes(
        \Lofmp\SellerBadge\Api\Data\SellerBadgeExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }

    /**
     * @return array|mixed|string|null
     */
    public function getImage()
    {
        return $this->getData(self::IMAGE);
    }

    /**
     * @param string $image
     * @return SellerBadgeInterface|\Lofmp\SellerBadge\Model\SellerBadge
     */
    public function setImage($image)
    {
        return $this->setData(self::IMAGE, $image);
    }

    /**
     * @return array|mixed|string|null
     */
    public function getDescription()
    {
        return $this->getData(self::DESCRIPTION);
    }

    /**
     * @param string $description
     * @return SellerBadgeInterface|\Lofmp\SellerBadge\Model\SellerBadge
     */
    public function setDescription($description)
    {
        return $this->setData(self::DESCRIPTION, $description);
    }

    /**
     * @return array|mixed|string|null
     */
    public function getRank()
    {
        return $this->getData(self::RANK);
    }

    /**
     * @param string $rank
     * @return SellerBadgeInterface|\Lofmp\SellerBadge\Model\SellerBadge
     */
    public function setRank($rank)
    {
        return $this->setData(self::RANK, $rank);
    }

    /**
     * @return array|mixed|string|null
     */
    public function getIsActive()
    {
        return $this->getData(self::IS_ACTIVE);
    }

    /**
     * @param string $isActive
     * @return SellerBadgeInterface|\Lofmp\SellerBadge\Model\SellerBadge
     */
    public function setIsActive($isActive)
    {
        return $this->setData(self::IS_ACTIVE, $isActive);
    }

    /**
     * @return array|mixed|string|null
     */
    public function getCreatedAt()
    {
        return $this->getData(self::CREATED_AT);
    }

    /**
     * @param string $createdAt
     * @return SellerBadgeInterface|\Lofmp\SellerBadge\Model\SellerBadge
     */
    public function setCreatedAt($createdAt)
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }

    /**
     * @return array|mixed|string|null
     */
    public function getUpdatedAt()
    {
        return $this->getData(self::UPDATED_AT);
    }

    /**
     * @param string $updatedAt
     * @return SellerBadgeInterface|\Lofmp\SellerBadge\Model\SellerBadge
     */
    public function setUpdatedAt($updatedAt)
    {
        return $this->setData(self::UPDATED_AT, $updatedAt);
    }
}
