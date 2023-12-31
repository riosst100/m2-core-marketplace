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

namespace Lofmp\SellerMembership\Helper;

use Magento\TestFramework\Inspection\Exception;

class Email
{
    /**
     * @var \Lof\MarketPlace\Helper\Data
     */
    protected $helper;

    /**
     * @var null
     */
    protected $messageSubject = null;

    /**
     * @var null
     */
    protected $messageBody = null;

    /**
     * @var null
     */
    protected $emailSubject = null;

    /**
     * @var null
     */
    protected $emailContent = null;

    /**
     * @var
     */
    public $_storeManager;

    /**
     * @var
     */
    protected $_priceCurrency;

    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    protected $_transportBuilder;

    /**
     * @var \Magento\Framework\Translate\Inline\StateInterface
     */
    protected $inlineTranslation;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @var \Magento\Framework\Url
     */
    protected $_urlBuilder;

    /**
     * Email constructor.
     * @param \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation
     * @param \Magento\Framework\Mail\Template\TransportBuilder $_transportBuilder
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magento\Framework\Url $urlBuilder
     * @param \Lof\MarketPlace\Helper\Data $helper
     */
    public function __construct(
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\Mail\Template\TransportBuilder $_transportBuilder,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\Url $urlBuilder,
        \Lof\MarketPlace\Helper\Data $helper
    ) {
        $this->messageManager = $messageManager;
        $this->inlineTranslation = $inlineTranslation;
        $this->_transportBuilder = $_transportBuilder;
        $this->helper = $helper;
        $this->_urlBuilder = $urlBuilder;
    }

    /**
     * @param $seller
     * @param $expiration_date
     */
    public function sendExpiryNotificationEmail($seller, $expiration_date)
    {
        try {
            $data = [];
            $data['email'] = $seller->getEmail();
            $data['name'] = $seller->getName();
            $data['expiration_date'] = $expiration_date;
            $pricingUrl = $this->_urlBuilder->getUrl('lofmpmembership/buy/index');
            $data['pricing_url'] = $pricingUrl;
            $postObject = new \Magento\Framework\DataObject();

            $postObject->setData($data);
            $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;

            $transport = $this->_transportBuilder
                ->setTemplateIdentifier($this->helper->getConfig('buy_membership_page/expiry_notification'))
                ->setTemplateOptions(
                    [
                        'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                        'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                    ]
                )
                ->setTemplateVars(['data' => $postObject])
                ->setFrom($this->helper->getConfig('email_settings/sender_email_identity'))
                ->addTo($data['email'])
                ->setReplyTo($data['email'])
                ->getTransport();
            try {
                $transport->sendMessage();
                $this->inlineTranslation->resume();
            } catch (\Exception $e) {
                $error = true;
                $this->messageManager->addError(
                    __('We can\'t process your request right now. Sorry, that\'s all we know.')
                );
            }
        } catch (\Exception $e) {
            $this->inlineTranslation->resume();
            $this->messageManager->addError(
                __('We can\'t process your request right now. Sorry, that\'s all we know.')
            );
            return;
        }
    }


    /**
     * Get email body
     *
     * @return string
     */
    public function getEmailContent($queue)
    {
        if ($this->emailContent == null) {
            $this->getPreviewEmail($queue);
            return $this->transportBuilder->getMessageContent();
        }
        return $this->emailContent;
    }

    /**
     * Get email subject
     *
     * @return null|string
     */
    public function getEmailSubject($queue)
    {

        if ($this->emailSubject == null) {
            $this->getPreviewEmail($queue);
            return $this->transportBuilder->getMessageSubject();
        }
        return $this->emailSubject;
    }

    /**
     * Get email body
     *
     * @return string
     */
    public function getMessageContent($queue)
    {
        if ($this->messageBody == null) {
            $this->getPreview($queue);
            return $this->transportBuilder->getMessageContent();
        }
        return $this->messageBody;
    }

    /**
     * Get email subject
     *
     * @return null|string
     */
    public function getMessageSubject($queue)
    {

        if ($this->messageSubject == null) {
            $this->getPreview($queue);
            return $this->transportBuilder->getMessageSubject();
        }
        return $this->messageSubject;
    }
}
