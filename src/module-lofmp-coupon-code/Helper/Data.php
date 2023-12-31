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
 * @copyright  Copyright (c) 2016 Landofcoder (https://landofcoder.com/)
 * @license    https://landofcoder.com/LICENSE-1.0.html
 */
namespace Lofmp\CouponCode\Helper;

use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\Validator\EmailAddress;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    const REDEEM_PREFIX = "redeem-";
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;
    /**
    * @var \Magento\Framework\View\Element\BlockFactory
    */
    protected $_blockFactory;
    /**
    *@var \Magento\Store\Model\StoreManagerInterface
    */
    protected $_storeManager;

    protected $_scopeConfig;

    /**
     * @var \Magento\Framework\Translate\Inline\StateInterface
     */
    protected $inlineTranslation;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $_localeDate;
    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    protected $_transportBuilder;

    /**
     * @var \Magento\SalesRule\Model\ResourceModel\Rule\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * Sales rule coupon
     *
     * @var \Magento\SalesRule\Helper\Coupon
     */
    protected $salesRuleCoupon;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $resource;

    protected $_coupon_rule_model = [];

    /**
     * @var \Magento\Cms\Model\Template\FilterProvider
     */
    protected $_filterProvider;

    /**
     * @var array|mixed|null
     */
    protected $_sellerRules = null;

    /**
     * @var \Lof\MarketPlace\Helper\Data
     */
    protected $marketplaceHelperData;

    /**
     * @var \Magento\Framework\Serialize\SerializerInterface
     */
    protected $_serializer;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_dateTime;


    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\View\Element\BlockFactory $blockFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\SalesRule\Model\ResourceModel\Rule\CollectionFactory $collectionFactory,
        \Magento\SalesRule\Helper\Coupon $salesRuleCoupon,
        \Magento\Framework\App\ResourceConnection $resource,
        \Lof\MarketPlace\Helper\Data $marketplaceHelperData,
        \Magento\Framework\Serialize\SerializerInterface $serializer,
        DateTime $dateTime
    ) {
        parent::__construct($context);
        $this->_localeDate     = $localeDate;
        $this->_scopeConfig    = $context->getScopeConfig();
        $this->_blockFactory   = $blockFactory;
        $this->_storeManager   = $storeManager;
        $this->_filterProvider = $filterProvider;
        $this->_objectManager  = $objectManager;
        $this->inlineTranslation    = $inlineTranslation;
        $this->_transportBuilder    = $transportBuilder;
        $this->collectionFactory = $collectionFactory;
        $this->salesRuleCoupon = $salesRuleCoupon;
        $this->resource                     = $resource;
        $this->marketplaceHelperData = $marketplaceHelperData;
        $this->_serializer = $serializer;
        $this->_dateTime = $dateTime;
    }

    public function getDateTime()
    {
        return $this->_dateTime;
    }

    public function getTimezoneDateTime($dateTime = "today")
    {
        if($dateTime === "today" || !$dateTime){
            $dateTime = $this->_dateTime->gmtDate();
        }

        $today = $this->_localeDate
            ->date(
                new \DateTime($dateTime)
            )->format('Y-m-d H:i:s');
        return $today;
    }

    public function sendMail($emailFrom,$emailTo,$emailidentifier,$templateVar)
    {
        $this->inlineTranslation->suspend();
        $transport = $this->_transportBuilder->setTemplateIdentifier($emailidentifier)
            ->setTemplateOptions(
                [
                    'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                    'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                ]
            )
            ->setTemplateVars($templateVar)
            ->setFrom($emailFrom)
            ->addTo($emailTo)
            ->setReplyTo($emailTo)
            ->getTransport();
        $transport->sendMessage();
        $this->inlineTranslation->resume();
    }

    public function getConfig($key, $store = null)
    {
        $store = $this->_storeManager->getStore($store);
        $websiteId = $store->getWebsiteId();
        $result = $this->scopeConfig->getValue(
            'lofmpcouponcode/'.$key,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store);
        return $result;
    }

    public function getRuleData($ruleId)
    {
        $modelRule = $this->_objectManager->create('Magento\SalesRule\Model\Rule');
        $collection = $modelRule->load($ruleId);
        return $collection;

    }

    public function filter($str)
    {
        $html = $this->_filterProvider->getPageFilter()->filter($str);
        return $html;
    }

    public function getCouponHelperText()
    {
        $helper_text = $this->getConfig("general_settings/helper_text");
        if($helper_text){
            $helper_text = $this->filter($helper_text);
        }
        return $helper_text;
    }

    /**
     * Validate email address is valid
     *
     * @param string $value
     * @return bool
     */
    public function validateEmailAddress($value)
    {
        $validator = new EmailAddress();
        $validator->setMessage(
            __('"%1" invalid type entered.', $value),
            \Zend_Validate_EmailAddress::INVALID
        );
        $phpValidateEmail = filter_var($value, FILTER_VALIDATE_EMAIL);
        $coreValidateEmail = true;
        if (!$validator->isValid($value)) {
            $coreValidateEmail = false;
        }

        return $phpValidateEmail && $coreValidateEmail;
    }

    /**
     * Get coupon rule data by ID
     *
     * @param int|string $ruleId
     * @return Lofmp\CouponCode\Model\Rule|null
     */
    public function getCouponRuleData($ruleId)
    {
        if (!isset($this->_coupon_rule_model[$ruleId])) {
            $model = $this->_objectManager->create('Lofmp\CouponCode\Model\Rule');
            if (is_numeric($ruleId)) {
                $ruleModel = $model->load($ruleId, 'rule_id');
            } else {
                $ruleModel = $model->loadByAlias($ruleId);
            }
            $this->_coupon_rule_model[$ruleId] = $ruleModel;
        }
        return $this->_coupon_rule_model[$ruleId];
    }

     /**
     * Generate coupon code
     *
     * @return string
     */
    public function generateCode($ruleId)
    {
        $rule_data = $this->getCouponRuleData($ruleId);
        $format = $rule_data->getCouponsFormat();
        $code  = '';
        $splitChar = '-';
        $split = $rule_data->getCouponsDash();
        $length = $rule_data->getCouponsLength();

        if ($format == null) {
            $format = \Magento\SalesRule\Helper\Coupon::COUPON_FORMAT_ALPHANUMERIC;
        }
        $charset = $this->salesRuleCoupon->getCharset($format);

        $charsetSize = count($charset);
        for ($i = 0; $i < $length; ++$i) {
            $char = $charset[\Magento\Framework\Math\Random::getRandomNumber(0, $charsetSize - 1)];
            if (($split > 0) && (($i % $split) === 0) && ($i !== 0)) {
                $char = $splitChar . $char;
            }
            $code .= $char;
        }
        $prefix = $this->getCouponRuleData($ruleId)->getCouponsPrefix();
        $suffix = $this->getCouponRuleData($ruleId)->getCouponsSuffix();
        return $prefix . $code . $suffix;
    }

    /**
     * Get all rules
     * @return mixed|array|null
     */
    public function getAllRule()
    {
        if (!$this->_sellerRules) {
            $sellerhelper = $this->marketplaceHelperData;
            $check_admin = $this->_objectManager->get('Magento\Framework\App\State');
            $salesruleTable = $this->resource->getTableName('salesrule');
            $lofRuleTable = $this->resource->getTableName('lofmp_couponcode_rule');
            $collection = $this->collectionFactory->create();
            if($check_admin->getAreaCode() !== "adminhtml"){
                $collection->getSelect()->join(
                        ['lofmp_couponcode_rule' => $lofRuleTable],
                        'main_table.rule_id = lofmp_couponcode_rule.rule_id',
                        ['coupon_rule_id']
                        )->where('lofmp_couponcode_rule.seller_id = (?)', $sellerhelper->getSellerId());
            }else{
                $collection->getSelect()->join(
                        ['lofmp_couponcode_rule' => $lofRuleTable],
                        'main_table.rule_id = lofmp_couponcode_rule.rule_id',
                        ['coupon_rule_id']
                        )->where('main_table.is_active = 1');
            }
            $param = array();
            foreach ($collection as $rule ) {
                $param[$rule['rule_id']] = $rule['name'];
            }
            $this->_sellerRules = $param;
        }
        return $this->_sellerRules;
    }

    public function getListActiveRules($sellerId)
    {
        $salesruleTable = $this->resource->getTableName('salesrule');
        $lofRuleTable = $this->resource->getTableName('lofmp_couponcode_rule');
        $collection = $this->collectionFactory->create();
        $collection->getSelect()->join(
                        ['lofmp_couponcode_rule' => $lofRuleTable],
                        'main_table.rule_id = lofmp_couponcode_rule.rule_id',
                        ['coupon_rule_id', 'seller_id']
                    );
        $collection->getSelect()->where("main_table.is_active = 1");
        $collection->getSelect()->where("lofmp_couponcode_rule.seller_id = $sellerId or lofmp_couponcode_rule.seller_id is null" );
        $param = array();
        foreach ($collection as $rule ) {
            $param[$rule['rule_id']] = $rule['name'];
        }
        return $param;
    }

    public function getBaseUrl()
    {
        return $this->_storeManager->getStore()->getBaseUrl();
    }

    public function getTrackLink()
    {
        $base_url = $this->getBaseUrl();
        $route = $this->getConfig("general_settings/track_route");
        if(!$route){
            $route = "lofmpcouponcode/track/trackcode";
        }
        return $base_url.$route;
    }

    /**
     * Is enabled module on frontend
     * @return int|bool
     */
    public function isEnabled()
    {
        return (int)$this->getConfig("general_settings/show");
    }

    public function discountAmountFormat($couponRuleId, $discount_amount)
    {
        $couponRuleData = $this->getCouponRuleData($couponRuleId);
        $simple_action = $couponRuleData->getSimpleAction();
        $discount_amount = number_format((float)$discount_amount, 2, '.', '');
        if($simple_action == 'by_percent') {
            $discount_amount .='%';
        }elseif($simple_action == 'by_fixed'){
            $discount_amount .= $this->_storeManager->getStore()->getCurrentCurrency()->getCode();
        }
        return $discount_amount;
    }

    /**
     * Get config allow seller manager
     * @return int|boolean|null
     */
    public function allowSellerManage($storeId = null)
    {
        return (int)$this->getConfig("general_settings/allow_seller_manage");
        //return $this->getConfigValue("allow_seller_manage", $storeId);
    }

    /**
     * Generate action condition data before save
     *
     * @param mixed|array $actions
     * @param int|null $sellerId
     * @return mixed|array
     */
    public function generateActionCondition($actions, $sellerId = null)
    {
        if ($sellerId) {
            $defaultCondition = [
                "type" => \Magento\SalesRule\Model\Rule\Condition\Product\Combine::class,
                "aggregator" => "all",
                "value" => 1,
                "new_child" => ""
            ];
            if (!is_array($actions)) {
                $actions = $actions?$this->_serializer->unserialize($actions):[];
                $actionValue = isset($actions["value"])?$actions["value"]:1;
                $conditions = isset($actions["conditions"]) && $actions["conditions"] ? $actions["conditions"]: [];
                $tmpActions = [];
                if ($conditions) {
                    $tmpActions["1"] = [
                        "type" => \Magento\SalesRule\Model\Rule\Condition\Product\Combine::class,
                        "aggregator" => "all",
                        "value" => 1,
                        "new_child" => ""
                    ];
                    foreach ($conditions as $index => $val) {
                        $index++;
                        $tmpActions[$actionValue."--".$index] = $val;
                    }
                    if ($tmpActions) {
                        $actions = $tmpActions;
                    }
                } else {
                    $actions = [
                        "1" => $defaultCondition
                    ];
                }

            }
            $sellerCondition = [
                "type" => \Magento\SalesRule\Model\Rule\Condition\Product::class,
                "attribute" => "seller_id",
                "operator" => "==",
                "value" => $sellerId,
                "is_value_processed" => false,
                "attribute_scope" => ""
            ];
            $flag = false;
            $index = 1;
            if ($actions) {
                $existIndex = [];
                foreach ($actions as $key => $_condition) {
                    $path = explode('--', $key);
                    if ($_condition["type"] == \Magento\SalesRule\Model\Rule\Condition\Product::class && $_condition["attribute"] == "seller_id") {
                        $actions[$key] = $sellerCondition;
                        $flag = true;
                        break;
                    } else {
                        if (count($path) == 2) {
                            $existIndex[] = (int)$path[1];
                        }
                    }
                }
                if ($existIndex) {
                    $minIndex = min($existIndex);
                    $maxIndex = max($existIndex);
                    $index = (int)$minIndex == 1?($maxIndex + 1): 1;
                }

            } else {
                $actions["1"] = $defaultCondition;
            }
            if (!$flag) {
                $actions["1--".$index] = $sellerCondition;
            }

        }
        return $actions;
    }

}
