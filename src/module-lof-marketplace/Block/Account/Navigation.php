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

namespace Lof\MarketPlace\Block\Account;

class Navigation extends \Magento\Framework\View\Element\Html\Link\Current
{
    /**
     * @var \Lof\MarketPlace\Model\SellerFactory
     */
    protected $sellerFactory;

    /**
     * @var Magento\Framework\App\Action\Session
     */
    protected $session;

    /**
     * @var \Lof\MarketPlace\Model\Seller
     */
    protected $seller;

    /**
     * @var \Lof\MarketPlace\Model\Message
     */
    protected $message;

    /**
     * @var \Lof\MarketPlace\Model\MessageDetail
     */
    protected $detail;

    /**
     * @var
     */
    protected $currentSeller = null;

    /**
     * Navigation constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\App\DefaultPathInterface $defaultPath
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Lof\MarketPlace\Model\SellerFactory $sellerFactory
     * @param \Lof\MarketPlace\Model\Message $message
     * @param \Lof\MarketPlace\Model\MessageDetail $detail
     * @param \Lof\MarketPlace\Model\Seller $seller
     * @param array $data
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\App\DefaultPathInterface $defaultPath,
        \Magento\Customer\Model\Session $customerSession,
        \Lof\MarketPlace\Model\SellerFactory $sellerFactory,
        \Lof\MarketPlace\Model\Message $message,
        \Lof\MarketPlace\Model\MessageDetail $detail,
        \Lof\MarketPlace\Model\Seller $seller,
        array $data = []
    ) {
        $this->message = $message;
        $this->detail = $detail;
        $this->sellerFactory = $sellerFactory;
        $this->seller = $seller;
        $this->session = $customerSession;
        parent::__construct($context, $defaultPath);
    }

    /**
     *  Get Seller Colection
     *
     * @return Object
     */
    public function getSellerCollection()
    {
        return $this->seller->getCollection();
    }

    /**
     * Get Seller by customer
     *
     * @return Object
     */
    public function getSeller()
    {
        if (!$this->currentSeller && $this->session->isLoggedIn()) {
            $customerId = $this->session->getCustomerId();
            $this->currentSeller = $this->sellerFactory->create()->load($customerId, 'customer_id');
        }
        return $this->currentSeller;
    }

    /**
     * @return bool|int|mixed
     */
    public function isSeller()
    {
        if ($this->session->isLoggedIn()) {
            $seller = $this->getSeller();
            return $seller && $seller->getId() ? true : false;
        }

        return false;
    }

    /**
     * @return bool|int|mixed
     */
    public function isActiveSeller()
    {
        if ($this->session->isLoggedIn()) {
            $seller = $this->getSeller();
            return $seller && (1 == (int)$seller->getStatus()) ? true : false;
        }

        return false;
    }

    /**
     * @return bool|int|mixed
     */
    public function getSellerId()
    {
        if ($this->session->isLoggedIn()) {
            $seller = $this->getSeller();
            $sellerId = $seller ? $seller->getSellerId() : 0;
            return $sellerId;
        }

        return false;
    }

    /**
     * @return string
     */
    public function getCurrentUrl()
    {
        return $this->_urlBuilder->getCurrentUrl();
    }

    /**
     * @return \Magento\Framework\Data\Collection\AbstractDb|\Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection|null
     */
    public function getDetail()
    {
        return $this->detail->getCollection()->addFieldToFilter('sender_id', $this->getSellerId());
    }

    /**
     * @return \Magento\Framework\Data\Collection\AbstractDb|\Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection|null
     */
    public function getMessage()
    {
        return $this->message->getCollection()->addFieldToFilter('owner_id', $this->getSellerId());
    }

    /**
     * @return \Magento\Framework\Data\Collection\AbstractDb|\Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
     */
    public function getDetailUnRead()
    {
        return $this->getDetail()->addFieldToFilter('is_read', 0)->addFieldToFilter('seller_send', 0);
    }

    /**
     * @return \Magento\Framework\Data\Collection\AbstractDb|\Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection|null
     */
    public function getDetailAdminUnRead()
    {
        return $this->detail->getCollection()
            ->addFieldToFilter('is_read', 0)
            ->addFieldToFilter('seller_send', 0)
            ->addFieldToFilter('message_admin', 1);
    }

    /**
     * @return array
     */
    public function getMessageUnRead()
    {
        $data = $this->message->getCollection()
            ->addFieldToFilter('sender_id', $this->session->getCustomerId())->addFieldToFilter('is_read', 0);

        return $data->getData();
    }

    /**
     * Render block HTML
     *
     * @return string
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function _toHtml()
    {
        if (false != $this->getTemplate()) {
            return parent::_toHtml();
        }

        $isSeller = $this->isSeller();
        $isActiveSeller = $this->isActiveSeller();
        $highlight = '';
        if (!$isSeller) {
            $html = '<li class="nav item' . $highlight . ' lrw-nav-item"><a href="'
                . $this->getUrl('lofmarketplace/seller/becomeseller') . '"';
            $html .= $this->getTitle()
                ? ' title="' . $this->escapeHtml((string)new \Magento\Framework\Phrase($this->getTitle())) . '"'
                : '';
            $html .= $this->getAttributesHtml() . '>';

            if ($this->getIsHighlighted()) {
                $html .= '<strong>';
            }

            $html .= '<span>' . __("Become A Seller") . '</span>';

            if ($this->getIsHighlighted()) {
                $html .= '</strong>';
            }
            $html .= '</a></li>';

            return $html;
        }
        if (!$isActiveSeller) {
            $html = '<li class="nav item' . $highlight . ' lrw-nav-item"><a href="'
            . $this->getUrl('lofmarketplace/seller/becomeseller/approval') . '"';
            $html .= '<a href="#"><strong><span>' . __("Seller Registration Under Review") . '</span></strong></a>';
            $html .= '</li>';
            return $html;
        }

        if ($this->getIsHighlighted()) {
            $highlight = ' current';
        }

        if ($this->isCurrent()) {
            $html = '<li class="nav item current lrw-nav-item">';
            $html .= '<strong>'
                . '<span>' . $this->escapeHtml((string)new \Magento\Framework\Phrase($this->getLabel())) . '</span>';
            $html .= '</strong>';
            $html .= '</li>';
        } else {
            // phpcs:disable Generic.Files.LineLength.TooLong
            $html = '<li class="nav item' . $highlight . ' lrw-nav-item"><a href="' . $this->escapeHtml($this->getHref()) . '"';
            $html .= $this->getTitle()
                ? ' title="' . $this->escapeHtml((string)new \Magento\Framework\Phrase($this->getTitle())) . '"'
                : '';
            $html .= $this->getAttributesHtml() . '>';

            if ($this->getIsHighlighted()) {
                $html .= '<strong>';
            }

            $html .= '<span>' . $this->escapeHtml((string)new \Magento\Framework\Phrase($this->getLabel())) . '</span>';

            if ($this->getIsHighlighted()) {
                $html .= '</strong>';
            }

            $html .= '</a></li>';
        }

        return $html;
    }
}
