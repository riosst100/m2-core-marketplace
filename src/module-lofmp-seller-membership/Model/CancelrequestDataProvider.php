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
 * @package    Lofmp_SellerMembership
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lofmp\SellerMembership\Model;

use Magento\Framework\App\Action\Context;
use Magento\Framework\UrlInterface;

class CancelrequestDataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $_customerFactory;

    /**
     * @var MembershipFactory
     */
    protected $_membershipFactory;

    /**
     * @var null
     */
    protected $_loadedData = null;

    /**
     * @var CancelrequestFactory
     */
    protected $_cancelrequestFactory;

    /**
     * CancelrequestDataProvider constructor.
     * @param Context $context
     * @param CancelrequestFactory $cancelrequestFactory
     * @param  MembershipFactory $membershipFactory
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param UrlInterface $urlBuilder
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        Context $context,
        CancelrequestFactory $cancelrequestFactory,
        MembershipFactory $membershipFactory,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        UrlInterface $urlBuilder,
        $name,
        $primaryFieldName,
        $requestFieldName,
        array $meta = [],
        array $data = []
    ) {
        $this->_customerFactory = $customerFactory;
        $this->_membershipFactory = $membershipFactory;
        $this->urlBuilder = $urlBuilder;

        $this->_request             = $context->getRequest();
        $this->_cancelrequestFactory = $cancelrequestFactory;
        $this->collection = $cancelrequestFactory->create()->getCollection();
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * @return array
     */
    public function getData()
    {
        if ($this->_loadedData && $this->_loadedData !==null) {
            return $this->_loadedData;
        }
        $entity_id = $this->_request->getParam('entity_id');
        if ($entity_id) {
            $model = $this->_cancelrequestFactory->create()->load($entity_id);
            $data[$entity_id] = $model->getData();

            $membership_id = $model->getData('membership_id');
            $membership_model = $this->_membershipFactory->create()->load($membership_id);
            $customer_id = $membership_model->getData('customer_id');

            $data[$entity_id]['customer_id'] = $customer_id;
            $data[$entity_id]['customer_name'] = $this->getCustomerName($customer_id);
            $data[$entity_id]['price'] = $membership_model->getData('price');
            $data[$entity_id]['duration'] = $membership_model->getData('duration');
            $data[$entity_id]['name'] = $membership_model->getData('name');
            $data[$entity_id]['expiration_date'] = $membership_model->getData('expiration_date');
            $data[$entity_id]['group_name'] = $this->getGroupName($membership_model->getData('group_id'));
        }
        $this->_loadedData = $data;
        return $data;
    }

    /**
     * @param int $customer_id
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getCustomerName($customer_id = 0)
    {
        $html = '';
        if ($customer_id) {
            $customer_model = $this->_customerFactory->create()->load($customer_id);
            $html = "<a href='" . $this->urlBuilder->getUrl('customer/index/edit', ['id' => $customer_id]) . "' target='blank' title='" . __('View Customer') . "'>" . $customer_model->getName() . '</a>';
        }
        return $html;
    }

    /**
     * @param int $group_id
     * @return \Magento\Framework\Phrase
     */
    public function getGroupName($group_id = 0)
    {
        return __('Membership Group - General');
    }
}
