<?php
/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * http://www.landofcoder.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Landofcoder
 * @package    Lofmp_FavoriteSeller
 * @copyright  Copyright (c) 2018 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */
namespace Lofmp\FavoriteSeller\Helper;

class Sender extends \Magento\Framework\App\Helper\AbstractHelper
{
    CONST EMAILIDENTIFIER = 'favoriteseller_config_email_email_template';
    /**
     * @var \Magento\Framework\Translate\Inline\StateInterface
     */
    protected $inlineTranslation;

    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    protected $_transportBuilder;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface 
     */
    protected $_localeDate;

    /**
     * @var \Lof\MarketPlace\Helper\Data
     */
    protected $marketplaceHelperData;

    /**
     * @var 
     */
    protected $_scopeConfig;

    /**
     * @var ConfigData
     */
    protected $configData;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Lof\MarketPlace\Helper\Data $marketplaceHelperData,
        ConfigData $configData,
        \Magento\Store\Model\StoreManagerInterface $storeManager
        ) {
        parent::__construct($context);
        $this->_localeDate     = $localeDate;
        $this->_scopeConfig    = $context->getScopeConfig();
        $this->inlineTranslation    = $inlineTranslation;
        $this->_transportBuilder    = $transportBuilder;
        $this->marketplaceHelperData = $marketplaceHelperData;
        $this->configData = $configData;
        $this->_storeManager   = $storeManager;
    }

    /**
     * Send email
     */
    public function sendMail($emailFrom, $emailTo, $emailidentifier, $templateVar, $replyTo = null )
    {
        $replyTo = $replyTo?$replyTo:$emailTo;
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
            ->setReplyTo($replyTo)
            ->getTransport();
        $transport->sendMessage();
        $this->inlineTranslation->resume();
        return true;
    }

    /**
     * Send email to subscriber
     */
    public function sendEmailToSubscriber($emailTo, $seller, $data = [] ) 
    {
        $templateVar = array(
            'customer_name' => isset($data['customer_name'])?$data['customer_name']:"",
            'subject' => isset($data['subject'])?$data['subject']:"",
            'message' => isset($data['message'])?$data['message']:"",
            'seller_name' => $seller->getName(),
            'seller_link' => $seller->getUrl(),
            'link_website' => $this->_storeManager->getStore()->getBaseUrl()
        );
        $emailFrom = $this->configData->getEmailConfig('sender_email_identity');
        $emailIdentifier = $this->configData->getEmailConfig("email_template");
        $emailIdentifier = $emailIdentifier?$emailIdentifier:self::EMAILIDENTIFIER;

        return $this->sendMail($emailFrom, $emailTo, $emailIdentifier, $templateVar, $seller->getEmail());
    }

}