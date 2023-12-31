<?php

namespace Lofmp\Quickrfq\Block\Product;

use Lof\Quickrfq\Helper\Data;
use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\File\Size;
use Magento\Framework\Registry;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Template\Context;

/**
 * Class Popup
 *
 * @package Lofmp\Quickrfq\Block\Form
 */
class Popup extends \Lof\Quickrfq\Block\Product\Popup
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
     * @var \Lofmp\Quickrfq\Helper\Data
     */
    private $_helperSeller;

    /**
     * Popup constructor
     *
     * @param Context $context
     * @param Registry $registry
     * @param Size $fileSize
     * @param UrlInterface $urlInterface
     * @param Data $helperConfig
     * @param \Lofmp\Quickrfq\Helper\Data $helperSeller
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
        \Lofmp\Quickrfq\Helper\Data $helperSeller,
        CustomerRepositoryInterface $customerRepository,
        AddressRepositoryInterface $addressRepository,
        Session $session,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $fileSize,
            $urlInterface,
            $helperConfig,
            $customerRepository,
            $addressRepository,
            $session,
            $data
        );
        $this->_helperSeller = $helperSeller;
    }

    /**
     * @return string
     */
    public function _toHtml()
    {
        $isEnable = $this->_helperSeller->getConfig('general/enabled');
        $currentProduct = $this->getCurrentProduct();
        $sellerId = $currentProduct?$currentProduct->getSellerId():0;
        if ($sellerId && !$isEnable) {
            return '';
        } else {
            return parent::_toHtml();
        }
    }
}
