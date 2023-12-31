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

namespace Lof\Quickrfq\Block\Product;

use Lof\Quickrfq\Helper\Data;
use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\File\Size;
use Magento\Framework\Registry;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Template\Context;

/**
 * Class Popup
 *
 * @package Lof\Quickrfq\Block\Form
 */
class Popup extends AbstractView
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
     * @var Size
     */
    private $fileSize;

    /**
     * @var UrlInterface
     */
    protected $_urlInterface;

    /**
     * @var Data
     */
    protected $_helperConfig;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;
    /**
     * @var CustomerRepositoryInterface
     */

    private $customerRepository;

    /**
     * @var AddressRepositoryInterface
     */
    private $addressRepository;

    /**
     * @var Session
     */
    private $_customerSession;

    /**
     * Popup constructor
     *
     * @param Context $context
     * @param Registry $registry
     * @param Size $fileSize
     * @param UrlInterface $urlInterface
     * @param Data $helperConfig
     * @param CustomerRepositoryInterface $customerRepository
     * @param AddressRepositoryInterface $addressRepository
     * @param Session $session
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        Size $fileSize,
        UrlInterface $urlInterface,
        Data $helperConfig,
        CustomerRepositoryInterface $customerRepository,
        AddressRepositoryInterface $addressRepository,
        Session $session,
        array $data = []
    ) {
        parent::__construct($context, $registry, $helperConfig, $data);
        $this->fileSize = $fileSize;
        $this->_customerSession = $session;
        $this->addressRepository = $addressRepository;
        $this->customerRepository = $customerRepository;
        $this->scopeConfig = $context->getScopeConfig();
        $this->_urlInterface = $urlInterface;
    }

    /**
     * Retrieve request negotiable quote URL.
     *
     * @return string
     */
    public function getCreateQuoteUrl()
    {
        $productId = 0;
        if ($product = $this->getCurrentProduct()) {
            $productId = $product->getId();
        }

        return $this->getUrl('quickrfq/quickrfq/save/', ['product_id' => $productId]);
    }

    /**
     * @return mixed|null
     */
    public function getCurrentProduct()
    {
        if (!$this->hasData('current_product')) {
            $this->setData('current_product', $this->_coreRegistry->registry('current_product'));
        }

        return $this->getData('current_product');
    }

    /**
     * Retrieve request negotiable quote URL.
     *
     * @return string
     */
    public function getCurrentUrl()
    {
        return $this->_urlInterface->getCurrentUrl();
    }

    /**
     * Get max file size
     *
     * @return float
     */
    public function getMaxFileSize()
    {
        return $this->fileSize->convertSizeToInteger($this->getMaxFileSizeMb() . 'M');
    }

    /**
     * Get allowed file extensions
     *
     * @return string
     */
    public function getAllowedExtensions()
    {
        return $this->_helperConfig->getAllowedExtensions();
    }

    /**
     * Get max file size in Mb
     *
     * @return float
     */
    public function getMaxFileSizeMb()
    {
        $configSize = $this->_helperConfig->getMaxFileSize();
        $phpLimit = $this->fileSize->getMaxFileSizeInMb();
        if ($configSize) {
            return min($configSize, $phpLimit);
        }

        return $phpLimit;
    }

    /**
     * @return array|\Magento\Customer\Api\Data\CustomerInterface
     * @throws LocalizedException
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
     * @throws LocalizedException
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

    /**
     * @return mixed
     */
    public function getTerms()
    {
        $enableUnit = $this->_helperConfig->getConfig('option/enabled_terms');
        if ($enableUnit) {
            return $this->_helperConfig->getConfig('option/terms');
        }
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
    public function getTermsLabel()
    {
        return $this->_helperConfig->getConfig('option/terms_label');
    }

    /**
     * @return mixed
     */
    public function getPublicKey()
    {
        return $this->scopeConfig->getValue(self::CONFIG_CAPTCHA_PUBLIC_KEY);
    }

    /**
     * @return mixed
     */
    public function isRequiredLoggedIn()
    {
        return $this->_helperConfig->getConfig('option/required_customer_login');
    }

    /**
     * @return bool
     */
    public function isLoggedIn()
    {
        return $this->_customerSession->isLoggedIn();
    }

    /**
     * @return string
     */
    public function _toHtml()
    {
        if (!$this->isLoggedIn() && $this->isRequiredLoggedIn()) {
            return '';
        }
        return parent::_toHtml(); // TODO: Change the autogenerated stub
    }
}
