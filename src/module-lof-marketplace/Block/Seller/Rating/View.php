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
 * @package    Lof_MarketPlace
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lof\MarketPlace\Block\Seller\Rating;

class View extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $session;

    /**
     * @var \Lof\MarketPlace\Model\Seller
     */
    protected $seller;
    /**
     * @var \Lof\MarketPlace\Model\Rating
     */
    protected $rating;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var \Lof\MarketPlace\Helper\Data
     */
    public $helper;

    /**
     * View constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Lof\MarketPlace\Model\Seller $seller
     * @param \Lof\MarketPlace\Model\Rating $rating
     * @param \Lof\MarketPlace\Helper\Data $helper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Lof\MarketPlace\Model\Seller $seller,
        \Lof\MarketPlace\Model\Rating $rating,
        \Lof\MarketPlace\Helper\Data $helper,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->rating = $rating;
        $this->helper = $helper;
        $this->request = $context->getRequest();
        $this->seller = $seller;
        $this->session = $customerSession;
    }

    /**
     * @return mixed
     */
    public function getRating()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $rating = $objectManager->get(\Lof\MarketPlace\Model\Rating::class)->load($this->getRatingId());
        return $rating;
    }

    /**
     * @return mixed|string
     */
    public function getRatingId()
    {
        $path = trim($this->request->getPathInfo(), '/');
        $params = explode('/', $path);
        return end($params);
    }

    /**
     * @return \Magento\Framework\DataObject
     */
    public function getSeller()
    {
        $seller = $this->seller->getCollection()
            ->addFieldToFilter('customer_id', $this->session->getId())
            ->getFirstItem();

        return $seller;
    }

    /**
     * @return View
     */
    public function _prepareLayout()
    {
        $this->pageConfig->getTitle()->set('Detail Rating');
        return parent::_prepareLayout();
    }

    /**
     * @param $status
     * @return string
     */
    public function getStatus($status)
    {
        switch ($status) {
            case 'pending':
                return __('Pending');
            case 'accept':
                return __('Accepted');
            default:
                return __('Accepted');
        }
    }
}
