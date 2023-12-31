<?php
namespace Lofmp\CouponCode\Block\MarketPlace\Rule;

use Magento\Customer\Model\Context as CustomerContext;

class Edit extends \Magento\Framework\View\Element\Template
{

    protected $_sellerCollection;

    protected $_coreRegistry = null;

    protected $_sellerHelper = null;

    protected $_resource = null;

    protected $storeManager = null;

    protected $request;

    protected $rule;

    protected $_customerGroup;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Lof\MarketPlace\Helper\Data $sellerHelper,
        \Lof\MarketPlace\Model\Seller $sellerCollection,
        \Lofmp\CouponCode\Model\Rule $rule,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Customer\Model\ResourceModel\Group\Collection $customerGroup,

        array $data = []
        ) {
        parent::__construct($context, $data); 

        $this->_sellerCollection = $sellerCollection;
        $this->_sellerHelper  	 = $sellerHelper;
        $this->_coreRegistry	 = $registry;
        $this->request 			 = $resource;
        $this->storeManager 	 =  $context->getStoreManager();
        $this->storeManager 	 =  $request;
        $this->rule 			 =  $rule;
        $this->_customerGroup 	 = $customerGroup;

    }

    public function _prepareLayout() {
        if($this->getRequest()->getParam('coupon_rule_id')){
            $this->pageConfig->getTitle()->set(__('Update Rule'));
        }else {
            $this->pageConfig->getTitle()->set(__('Create New Rule'));
        }

        return parent::_prepareLayout ();
    }

    public function getRuleByID() {
        $data = [];
        $coupon_rule_id = $this->getRequest()->getParam('rule_id');
        $model = $this->rule->load($coupon_rule_id,"coupon_rule_id");
        if($model->getId()){
            $data = $model->getData();
        }
        return $data;
    }

    public function getCustomerGroups() {
	    $customerGroups = $this->_customerGroup->toOptionArray();
	    return $customerGroups;
	}

}