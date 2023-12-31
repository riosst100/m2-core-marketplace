<?php
/**
 * LandofCoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * https://landofcoder.com/license
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   LandofCoder
 * @package    Lofmp_CouponCode
 * @copyright  Copyright (c) 2021 Landofcoder (https://landofcoder.com/)
 * @license    https://landofcoder.com/LICENSE-1.0.html
 */
namespace Lofmp\CouponCode\Model;

use Lofmp\CouponCode\Api\Data\CouponInterface;
use Magento\SalesRule\Api\CouponRepositoryInterface;
use Magento\SalesRule\Api\RuleRepositoryInterface;

class Coupon extends \Magento\Framework\Model\AbstractModel implements CouponInterface
{
    /**
     * Blog's Statuses
     */
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;

    const STATUS_PUBLIC = 1;
    const STATUS_PRIVATE = 0;

    /** @var \Magento\Store\Model\StoreManagerInterface */
    protected $_storeManager;

    /**
     * URL Model instance
     *
     * @var \Magento\Framework\UrlInterface
     */
    protected $_url;

    protected $_resource;

    protected $_couponCollection;

    /**
     * @var CouponRepositoryInterface
     */
    protected $magentoCouponRepository;

    /**
     * @var RuleRepositoryInterface
     */
    protected $magentoRuleRepository;

    /**
     * @param \Magento\Framework\Model\Context                          $context
     * @param \Magento\Framework\Registry                               $registry
     * @param \Lofmp\CouponCode\Model\ResourceModel\Coupon|null                $resource
     * @param \Lofmp\CouponCode\Model\ResourceModel\Coupon\Collection|null $resourceCollection
     * @param \Lofmp\CouponCode\Model\ResourceModel\Coupon\CollectionFactory|null $couponCollection
     * @param \Magento\Store\Model\StoreManagerInterface                $storeManager
     * @param \Magento\Framework\UrlInterface                           $url
     * @param array                                                     $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Lofmp\CouponCode\Model\ResourceModel\Coupon $resource = null,
        \Lofmp\CouponCode\Model\ResourceModel\Coupon\Collection $resourceCollection = null,
        \Lofmp\CouponCode\Model\ResourceModel\Coupon\CollectionFactory $couponCollection,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\UrlInterface $url,
        CouponRepositoryInterface $magentoCouponRepository,
        RuleRepositoryInterface $magentoRuleRepository,
        array $data = []
        ) {
        $this->_storeManager = $storeManager;
        $this->_url = $url;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->_resource = $resource;
        $this->_couponCollection = $couponCollection;
        $this->magentoCouponRepository = $magentoCouponRepository;
        $this->magentoRuleRepository = $magentoRuleRepository;
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Lofmp\CouponCode\Model\ResourceModel\Coupon');
    }

    /**
     * Load object data
     * @param string $alias
     * @return $this
     */
    public function getCouponByAlias($alias){
        $this->_beforeLoad($alias, 'alias');
        $this->_getResource()->load($this, $alias, 'alias');
        $this->_afterLoad();
        $this->setOrigData();
        $this->_hasDataChanges = false;
        return $this;
    }

    /**
     * Get coupon_id
     * @return string
     */
    public function getCouponId()
    {
        return $this->getData(self::COUPON_ID);
    }

    /**
     * Set coupon_id
     * @param string $couponId
     * @return \Lofmp\CouponCode\Api\Data\CouponInterface
     */
    public function setCouponId($couponId)
    {
        return $this->setData(self::COUPON_ID, $couponId);
    }

    /**
     * Get couponcode_id
     * @return string
     */
    public function getCouponcodeId()
    {
        return $this->getData(self::COUPONCODE_ID);
    }

    /**
     * Set couponcode_id
     * @param string $couponcode_id
     * @return \Lofmp\CouponCode\Api\Data\CouponInterface
     */
    public function setCouponcodeId($couponcode_id)
    {
        return $this->setData(self::COUPONCODE_ID, $couponcode_id);
    }


    /**
     * {@inheritdoc}
     * */
    public function getAlias()
    {
        return $this->getData(self::ALIAS);
    }

    /**
     * {@inheritdoc}
     */
    public function setAlias($alias)
    {
        return $this->setData(self::ALIAS, $alias);
    }

    /**
     * {@inheritdoc}
     * */
    public function getCode()
    {
        return $this->getData(self::CODE);
    }

    /**
     * {@inheritdoc}
     */
    public function setCode($code)
    {
        return $this->setData(self::CODE, $code);
    }

    /**
     * {@inheritdoc}
     * */
    public function getRuleId()
    {
        return $this->getData(self::RULE_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setRuleId($rule_id)
    {
        return $this->setData(self::RULE_ID, $rule_id);
    }

    /**
     * {@inheritdoc}
     * */
    public function getCustomerId()
    {
        return $this->getData(self::CUSTOMER_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setCustomerId($customer_id)
    {
        return $this->setData(self::CUSTOMER_ID, $customer_id);
    }

    /**
     * {@inheritdoc}
     * */
    public function getIsPublic()
    {
        return $this->getData(self::IS_PUBLIC);
    }

    /**
     * {@inheritdoc}
     */
    public function setIsPublic($is_public)
    {
        return $this->setData(self::IS_PUBLIC, $is_public);
    }

    /**
     * {@inheritdoc}
     * */
    public function getSellerId()
    {
        return $this->getData(self::SELLER_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setSellerId($seller_id)
    {
        return $this->setData(self::SELLER_ID, $seller_id);
    }

    /**
     * {@inheritdoc}
     * */
    public function getEmail()
    {
        return $this->getData(self::EMAIL);
    }

    /**
     * {@inheritdoc}
     */
    public function setEmail($email)
    {
        return $this->setData(self::EMAIL, $email);
    }

    /**
     * {@inheritdoc}
     * */
    public function getDescription()
    {
        if  (!$this->getData(self::DESCRIPTION)) {
            $this->setData(self::DESCRIPTION, $this->getSalesRuleCoupon()->getDescription());
        }
        return $this->getData(self::DESCRIPTION);
    }

    /**
     * {@inheritdoc}
     */
    public function setDescription($description)
    {
        return $this->setData(self::DESCRIPTION, $description);
    }

    /**
     * {@inheritdoc}
     * */
    public function getFromDate()
    {
        return $this->getData(self::FROM_DATE);
    }

    /**
     * {@inheritdoc}
     */
    public function setFromDate($from_date)
    {
        return $this->setData(self::FROM_DATE, $from_date);
    }

    /**
     * {@inheritdoc}
     * */
    public function getToDate()
    {
        return $this->getData(self::TO_DATE);
    }

    /**
     * {@inheritdoc}
     */
    public function setToDate($to_date)
    {
        return $this->setData(self::TO_DATE, $to_date);
    }

    /**
     * {@inheritdoc}
     * */
    public function getTimesUsed()
    {
        return $this->getData(self::TIMES_USED);
    }

    /**
     * {@inheritdoc}
     */
    public function setTimesUsed($times_used)
    {
        return $this->setData(self::TIMES_USED, $times_used);
    }

    /**
     * {@inheritdoc}
     * */
    public function getDiscountAmount()
    {
        return $this->getData(self::DISCOUNT_AMOUNT);
    }

    /**
     * {@inheritdoc}
     */
    public function setDiscountAmount($discount_amount)
    {
        return $this->setData(self::DISCOUNT_AMOUNT, $discount_amount);
    }

    /**
     * {@inheritdoc}
     * */
    public function getSimpleAction()
    {
        return $this->getData(self::SIMPLE_ACTION);
    }

    /**
     * {@inheritdoc}
     */
    public function setSimpleAction($simple_action)
    {
        return $this->setData(self::SIMPLE_ACTION, $simple_action);
    }

    /**
     * {@inheritdoc}
     * */
    public function getUsageLimit()
    {
        return $this->getData(self::USAGE_LIMIT);
    }

    /**
     * {@inheritdoc}
     */
    public function setUsageLimit($usage_limit = null)
    {
        return $this->setData(self::USAGE_LIMIT, $usage_limit);
    }

    /**
     * {@inheritdoc}
     */
    public function getActionsSerialized()
    {
        return $this->getData(self::ACTIONS_SERIALIZED);
    }

    /**
     * {@inheritdoc}
     */
    public function setActionsSerialized($actions_serialized)
    {
        return $this->setData(self::ACTIONS_SERIALIZED, $actions_serialized);
    }

    /**
     * {@inheritdoc}
     */
    public function getConditionsSerialized()
    {
        return $this->getData(self::CONDITIONS_SERIALIZED);
    }

    /**
     * {@inheritdoc}
     */
    public function setConditionsSerialized($conditions_serialized)
    {
        return $this->setData(self::CONDITIONS_SERIALIZED, $conditions_serialized);
    }
    /**
     * {@inheritdoc}
     */
    public function getSalesRuleCoupon()
    {
        $rule_id = $this->magentoCouponRepository->getById($this->getCouponId())->getRuleId();
        if ($rule_id) return $this->magentoRuleRepository->getById($rule_id);
        return null;
    }
}
