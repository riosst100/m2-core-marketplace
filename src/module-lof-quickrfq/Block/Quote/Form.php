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
 * @package    Lof_Quickrfq
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lof\Quickrfq\Block\Quote;

use Magento\Catalog\Model\ProductRepository;
use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Framework\View\Element\Template;
use Lof\Quickrfq\Helper\Data;
use Magento\Customer\Model\Session;
use Magento\Customer\Api\CustomerRepositoryInterface;

/**
 * Class Form
 * @package Lof\Quickrfq\Block\Quote
 */
class Form extends Template
{
    /**
     *
     */
    const CONFIG_CAPTCHA_ENABLE = 'quickrfq/google_options/captchastatus';
    /**
     *
     */
    const CONFIG_CAPTCHA_PUBLIC_KEY = 'quickrfq/google_options/googlepublickey';

    /**
     * @var ProductRepository
     */
    protected $_productRepository;
    /**
     * @var Data
     */
    protected $_helper;
    /**
     * @var Session
     */
    private $_customerSession;
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;
    /**
     * @var AddressRepositoryInterface
     */
    private $addressRepository;
    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * Form constructor.
     * @param Template\Context $context
     * @param Data $_helper
     * @param ProductRepository $productRepository
     * @param Session $session
     * @param CustomerRepositoryInterface $customerRepository
     * @param AddressRepositoryInterface $addressRepository
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Data $_helper,
        ProductRepository $productRepository,
        Session $session,
        CustomerRepositoryInterface $customerRepository,
        AddressRepositoryInterface $addressRepository,
        array $data = []
    ) {
        $this->customerRepository = $customerRepository;
        $this->addressRepository = $addressRepository;
        $this->scopeConfig = $context->getScopeConfig();
        $this->_productRepository = $productRepository;
        $this->_helper = $_helper;
        $this->_customerSession = $session;
        parent::__construct($context, $data);
    }

    /**
     * @return \Magento\Catalog\Api\Data\ProductInterface|mixed|void|null
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getProductById()
    {
        $id = $this->getRequest()->getParam('product_id');
        if ($id) {
            return $this->_productRepository->getById($id);
        }
        return;
    }

    /**
     * @return string
     */
    public function getFormAction()
    {
        return $this->getUrl('quickrfq/quickrfq/save', ['_secure' => true]);
    }

    /**
     * @return mixed
     */
    public function isCaptchaEnable()
    {
        $enable = $this->scopeConfig->getValue(self::CONFIG_CAPTCHA_ENABLE);
        return $enable;
    }

    /**
     * @return mixed
     */
    public function getUnit()
    {
        $enableUnit = $this->_helper->getConfig('option/enabled_unit');
        if ($enableUnit) {
            $unit = $this->_helper->getConfig('option/unit');
            return json_decode($unit, true);
        }
    }


    /**
     * @return mixed
     */
    public function getTerms()
    {
        $enableUnit = $this->_helper->getConfig('option/enabled_terms');
        if ($enableUnit) {
            return $this->_helper->getConfig('option/terms_label');
        }
    }

    /**
     * @return mixed
     */
    public function getTermsLabel()
    {
        return $this->_helper->getConfig('option/terms_label');
    }

    /**
     * @return mixed
     */
    public function getPublicKey()
    {
        return $this->scopeConfig->getValue(self::CONFIG_CAPTCHA_PUBLIC_KEY);
    }

    /**
     * @return array|\Magento\Customer\Api\Data\CustomerInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCustomer()
    {
        if ($this->_customerSession->isLoggedIn()) {
            $id = $this->_customerSession->getCustomerId();
            $customer = $this->customerRepository->getById($id);
            return $customer;
        } else {
            return [];
        }
    }

    /**
     * @param $customer
     * @return string|null
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getTelephone($customer)
    {
        $billingAddressId = $customer->getDefaultBilling();
        if ($billingAddressId) {
            $billingAddress = $this->addressRepository->getById($billingAddressId);
            return $billingAddress->getTelephone();
        } else {
            return '';
        }
    }
}
