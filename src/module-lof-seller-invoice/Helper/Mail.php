<?php
/**
 * Landofcoder
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * https://landofcoder.com/license
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category   Landofcoder
 * @package    Lof_RequestForQuote
 * @copyright  Copyright (c) 2018 Landofcoder (https://www.landofcoder.com/)
 * @license    https://www.landofcoder.com/LICENSE-1.0.html
 */

namespace Lof\SellerInvoice\Helper;

class Mail extends \Magento\Framework\App\Helper\AbstractHelper
{

    protected $_currency;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $timezone;

    /**
     * @var \Magento\Framework\View\LayoutInterface
     */
    protected $_layout;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    protected $logger;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Filter\FilterManager $filterManager,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \Magento\Framework\View\LayoutInterface $layout,
        \Magento\Framework\Url $urlBuilder,
        \Lof\SellerInvoice\Model\Mail\UploadTransportBuilder $transportBuilder
        ) {
        parent::__construct($context);
        $this->context           = $context;
        $this->filterManager     = $filterManager;
        $this->inlineTranslation = $inlineTranslation;
        $this->dateTime          = $dateTime;
        $this->messageManager    = $messageManager;
        $this->transportBuilder  = $transportBuilder;
        $this->_storeManager     = $storeManager;
        $this->timezone          = $timezone;
        $this->_layout           = $layout;
        $this->_urlBuilder       = $urlBuilder;
        $this->logger            = $context->getLogger();
    }

    /**
     * Return brand config value by key and store
     *
     * @param string $key
     * @param \Magento\Store\Model\Store|int|string $store
     * @return string|null
     */
    public function getConfig($key, $store = null)
    {
        $store = $this->_storeManager->getStore($store);
        $websiteId = $store->getWebsiteId();

        $result = $this->scopeConfig->getValue(
            'sellerinvoice/' . $key,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store);
        return $result;
    }

    public function send( $templateName, $senderName, $senderEmail, $recipientEmail, $recipientName, $variables, $storeId, $file = [], $filetype="PDF")
    {
        $this->inlineTranslation->suspend();

        try {
            $attach_type = "";
            if($filetype == "PDF") {
                $attach_type = 'application/pdf';  
            }
            $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
            $this->transportBuilder
            ->setTemplateIdentifier($templateName)
            ->setTemplateOptions([
                'area'  => \Magento\Framework\App\Area::AREA_FRONTEND,
                'store' => $storeId,
                ])
            ->setTemplateVars($variables)
            ->setFrom([
                'name'  => $senderName,
                'email' => $senderEmail
                ])
            ->addTo($recipientEmail, $recipientName)
            ->setReplyTo($senderEmail);

            if($file && $attach_type) {
                $file_content = isset($file['output'])?$file['output']:'';
                $file_name = isset($file['filename'])?$file['filename']:'';
                $this->transportBuilder->addAttachment($file_content, $file_name, $attach_type);
            }
            $transport = $this->transportBuilder->getTransport();
            $transport->sendMessage();
            $this->inlineTranslation->resume();
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addError(__('We can\'t send the email invoice right now.'));
            $this->logger->critical($e);
        }

        $this->inlineTranslation->resume();
        return true;
    }

    /**
     * Get formatted order created date in store timezone
     *
     * @param   string $format date format type (short|medium|long|full)
     * @return  string
     */
    public function getCreatedAtFormatted($time, $store, $format)
    {
        return $this->timezone->formatDateTime(
            new \DateTime($time),
            $format,
            $format,
            null,
            $this->timezone->getConfigTimezone('store', $store)
            );
    }

    public function sendNotificationNewInvoiceEmail($mageInvoice, $invoice, $file, $file_type="PDF")
    {
        $templateName = $this->getConfig('general/new_invoice');
        $storeScope   = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $senderId     = $this->getConfig('general/sender_email_identity');

        if ($senderId) {
            $sender_email   = $this->scopeConfig->getValue('trans_email/ident_' . $senderId . '/name', $storeScope);
            $recipientEmail = $mageInvoice->getOrder()->getCustomerEmail();
            $recipientName  = $mageInvoice->getOrder()->getCustomerName();

            $variables      = [
                'increment_id' => $invoice->getIncrementId(),
                'created_at'   => $this->getCreatedAtFormatted($mageInvoice->getCreatedAt(), $mageInvoice->getstore(), \IntlDateFormatter::MEDIUM)
            ];
            $variables      = [];

            $storeId        = $mageInvoice->getOrder()->getStoreId();
            $senderName     = $this->context->getScopeConfig()->getValue("trans_email/ident_" . $senderId . "/name");
            $senderEmail    = $this->context->getScopeConfig()->getValue("trans_email/ident_" . $senderId . "/email");

            $this->send($templateName, $senderName, $senderEmail, $recipientEmail, $recipientName, $variables, $storeId, $file, $file_type);

            return true;
        }
        return false;
    }

    
}