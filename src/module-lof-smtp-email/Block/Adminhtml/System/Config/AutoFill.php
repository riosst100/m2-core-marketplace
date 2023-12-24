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
 * @package    Lof_SmtpEmail
 * @copyright  Copyright (c) 2016 Landofcoder (https://landofcoder.com/)
 * @license    https://landofcoder.com/LICENSE-1.0.html
 */

namespace Lof\SmtpEmail\Block\Adminhtml\System\Config;


use Magento\Framework\Data\Form\Element\AbstractElement;
/**
 * "Reset to Defaults" button renderer
 *
 */
class AutoFill extends \Magento\Config\Block\System\Config\Form\Field
{
    /** @var UrlInterface */
    protected $_urlBuilder;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_urlBuilder = $context->getUrlBuilder();


    }

    /**
     * Set template
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
    }

   /**
     * Return element html
     *
     * @param  AbstractElement $element
     * @return string
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $button = $this->getLayout()->createBlock(
            'Magento\Backend\Block\Widget\Button'
        )->setData([
            'id' => 'synchronize_button',
            'label' => __('Autofill'),
        ])
            ->setDataAttribute(
                ['role' => 'lofsmtpemail-fill-button']
            )
        ;

        $selectHtml = parent::_getElementHtml($element);
        $buttonHtml = $button->toHtml();

        return "$buttonHtml<div class='lofsmtpemail-buttoned-input'>$selectHtml</div>
        <script>
            require([
                'jquery'
            ], function (jQuery) {
                jQuery(document).ready(function() {
                    jQuery('#synchronize_button').click(function(){
                        if(jQuery('#lofsmtpemail_smtp_config_provider').val() == 0) {
                            jQuery('#lofsmtpemail_smtp_config_auth').val('NONE');
                        } else if(jQuery('#lofsmtpemail_smtp_config_provider').val() == 1) {
                            jQuery('#lofsmtpemail_smtp_config_smtphost').val('smtp.aol.com');
                            jQuery('#lofsmtpemail_smtp_config_port').val('587');
                            jQuery('#lofsmtpemail_smtp_config_ssl').val('');
                            jQuery('#lofsmtpemail_smtp_config_auth').val('LOGIN');
                        } else if(jQuery('#lofsmtpemail_smtp_config_provider').val() == 2) {
                            jQuery('#lofsmtpemail_smtp_config_smtphost').val('smtp.comcast.net');
                            jQuery('#lofsmtpemail_smtp_config_port').val('587');
                            jQuery('#lofsmtpemail_smtp_config_ssl').val('');
                            jQuery('#lofsmtpemail_smtp_config_auth').val('LOGIN');
                        } else if(jQuery('#lofsmtpemail_smtp_config_provider').val() == 3) {
                            jQuery('#lofsmtpemail_smtp_config_smtphost').val('mail.gmx.net');
                            jQuery('#lofsmtpemail_smtp_config_port').val('587');
                            jQuery('#lofsmtpemail_smtp_config_ssl').val('tls');
                            jQuery('#lofsmtpemail_smtp_config_auth').val('LOGIN');
                        } else if(jQuery('#lofsmtpemail_smtp_config_provider').val() == 4) {
                            jQuery('#lofsmtpemail_smtp_config_smtphost').val('smtp.gmail.com');
                            jQuery('#lofsmtpemail_smtp_config_port').val('465');
                            jQuery('#lofsmtpemail_smtp_config_ssl').val('ssl');
                            jQuery('#lofsmtpemail_smtp_config_auth').val('LOGIN');
                        } else if(jQuery('#lofsmtpemail_smtp_config_provider').val() == 5) {
                            jQuery('#lofsmtpemail_smtp_config_smtphost').val('smtp.live.com');
                            jQuery('#lofsmtpemail_smtp_config_port').val('465');
                            jQuery('#lofsmtpemail_smtp_config_ssl').val('ssl');
                            jQuery('#lofsmtpemail_smtp_config_auth').val('LOGIN');
                        } else if(jQuery('#lofsmtpemail_smtp_config_provider').val() == 6) {
                            jQuery('#lofsmtpemail_smtp_config_smtphost').val('smtp.mail.com');
                            jQuery('#lofsmtpemail_smtp_config_port').val('465');
                            jQuery('#lofsmtpemail_smtp_config_ssl').val('ssl');
                            jQuery('#lofsmtpemail_smtp_config_auth').val('LOGIN');
                        } else if(jQuery('#lofsmtpemail_smtp_config_provider').val() == 7) {
                            jQuery('#lofsmtpemail_smtp_config_smtphost').val('smtp.o2.ie');
                            jQuery('#lofsmtpemail_smtp_config_port').val('25');
                            jQuery('#lofsmtpemail_smtp_config_ssl').val('');
                            jQuery('#lofsmtpemail_smtp_config_auth').val('LOGIN');
                        } else if(jQuery('#lofsmtpemail_smtp_config_provider').val() == 8) {
                            jQuery('#lofsmtpemail_smtp_config_smtphost').val('smtp.office365.com');
                            jQuery('#lofsmtpemail_smtp_config_port').val('587');
                            jQuery('#lofsmtpemail_smtp_config_ssl').val('tls');
                            jQuery('#lofsmtpemail_smtp_config_auth').val('LOGIN');
                        } else if(jQuery('#lofsmtpemail_smtp_config_provider').val() == 9) {
                            jQuery('#lofsmtpemail_smtp_config_smtphost').val('smtp.orange.net');
                            jQuery('#lofsmtpemail_smtp_config_port').val('25');
                            jQuery('#lofsmtpemail_smtp_config_ssl').val('');
                            jQuery('#lofsmtpemail_smtp_config_auth').val('LOGIN');
                        } else if(jQuery('#lofsmtpemail_smtp_config_provider').val() == 10) {
                            jQuery('#lofsmtpemail_smtp_config_smtphost').val('smtp-mail.outlook.com');
                            jQuery('#lofsmtpemail_smtp_config_port').val('587');
                            jQuery('#lofsmtpemail_smtp_config_ssl').val('tls');
                            jQuery('#lofsmtpemail_smtp_config_auth').val('LOGIN');
                        } else if(jQuery('#lofsmtpemail_smtp_config_provider').val() == 11) {
                            jQuery('#lofsmtpemail_smtp_config_smtphost').val('smtp.mail.yahoo.de');
                            jQuery('#lofsmtpemail_smtp_config_port').val('465');
                            jQuery('#lofsmtpemail_smtp_config_ssl').val('ssl');
                            jQuery('#lofsmtpemail_smtp_config_auth').val('LOGIN');
                        } else if(jQuery('#lofsmtpemail_smtp_config_provider').val() == 12) {
                            jQuery('#lofsmtpemail_smtp_config_smtphost').val('smtp.zoho.com');
                            jQuery('#lofsmtpemail_smtp_config_port').val('465');
                            jQuery('#lofsmtpemail_smtp_config_ssl').val('ssl');
                            jQuery('#lofsmtpemail_smtp_config_auth').val('LOGIN');
                        } else if(jQuery('#lofsmtpemail_smtp_config_provider').val() == 13) {
                            jQuery('#lofsmtpemail_smtp_config_smtphost').val('smtp.sendgrid.net');
                            jQuery('#lofsmtpemail_smtp_config_port').val('587');
                            jQuery('#lofsmtpemail_smtp_config_ssl').val('ssl');
                            jQuery('#lofsmtpemail_smtp_config_auth').val('LOGIN');
                        }  else if(jQuery('#lofsmtpemail_smtp_config_provider').val() == 14) {
                            jQuery('#lofsmtpemail_smtp_config_smtphost').val('smtp-relay.sendinblue.com');
                            jQuery('#lofsmtpemail_smtp_config_port').val('587');
                            jQuery('#lofsmtpemail_smtp_config_ssl').val('tls');
                            jQuery('#lofsmtpemail_smtp_config_auth').val('LOGIN');
                        } else if(jQuery('#lofsmtpemail_smtp_config_provider').val() == 15) {
                            jQuery('#lofsmtpemail_smtp_config_smtphost').val('smtp.mandrillapp.com');
                            jQuery('#lofsmtpemail_smtp_config_port').val('587');
                            jQuery('#lofsmtpemail_smtp_config_ssl').val('tls');
                            jQuery('#lofsmtpemail_smtp_config_auth').val('LOGIN');
                        }  else if(jQuery('#lofsmtpemail_smtp_config_provider').val() == 16) {
                            jQuery('#lofsmtpemail_smtp_config_smtphost').val('smtp.elasticemail.com');
                            jQuery('#lofsmtpemail_smtp_config_port').val('2525');
                            jQuery('#lofsmtpemail_smtp_config_ssl').val('');
                            jQuery('#lofsmtpemail_smtp_config_auth').val('LOGIN');
                        }  else if(jQuery('#lofsmtpemail_smtp_config_provider').val() == 17) {
                            jQuery('#lofsmtpemail_smtp_config_smtphost').val('smtp.sparkpostmail.com');
                            jQuery('#lofsmtpemail_smtp_config_port').val('587');
                            jQuery('#lofsmtpemail_smtp_config_ssl').val('TLS');
                            jQuery('#lofsmtpemail_smtp_config_auth').val('LOGIN');
                        } else if(jQuery('#lofsmtpemail_smtp_config_provider').val() == 18) {
                            jQuery('#lofsmtpemail_smtp_config_smtphost').val('in-v3.mailjet.com');
                            jQuery('#lofsmtpemail_smtp_config_port').val('587');
                            jQuery('#lofsmtpemail_smtp_config_ssl').val('TLS');
                            jQuery('#lofsmtpemail_smtp_config_auth').val('LOGIN');
                        } else if(jQuery('#lofsmtpemail_smtp_config_provider').val() == 19) {
                            jQuery('#lofsmtpemail_smtp_config_smtphost').val('smtp.mailgun.com');
                            jQuery('#lofsmtpemail_smtp_config_port').val('587');
                            jQuery('#lofsmtpemail_smtp_config_ssl').val('TLS');
                            jQuery('#lofsmtpemail_smtp_config_auth').val('LOGIN');
                        } else if(jQuery('#lofsmtpemail_smtp_config_provider').val() == 20) {
                            jQuery('#lofsmtpemail_smtp_config_smtphost').val('smtp.postmarkapp.com');
                            jQuery('#lofsmtpemail_smtp_config_port').val('587');
                            jQuery('#lofsmtpemail_smtp_config_ssl').val('TLS');
                            jQuery('#lofsmtpemail_smtp_config_auth').val('LOGIN');
                        } else if(jQuery('#lofsmtpemail_smtp_config_provider').val() == 21) {
                            jQuery('#lofsmtpemail_smtp_config_smtphost').val('plus.smtp.mail.yahoo.com');
                            jQuery('#lofsmtpemail_smtp_config_port').val('465');
                            jQuery('#lofsmtpemail_smtp_config_ssl').val('SSL');
                            jQuery('#lofsmtpemail_smtp_config_auth').val('LOGIN');
                        } else if(jQuery('#lofsmtpemail_smtp_config_provider').val() == 22) {
                            jQuery('#lofsmtpemail_smtp_config_smtphost').val('smtp.mail.yahoo.com.au');
                            jQuery('#lofsmtpemail_smtp_config_port').val('465');
                            jQuery('#lofsmtpemail_smtp_config_ssl').val('SSL');
                            jQuery('#lofsmtpemail_smtp_config_auth').val('LOGIN');
                        } else if(jQuery('#lofsmtpemail_smtp_config_provider').val() == 23) {
                            jQuery('#lofsmtpemail_smtp_config_smtphost').val('smtp.att.yahoo.com');
                            jQuery('#lofsmtpemail_smtp_config_port').val('465');
                            jQuery('#lofsmtpemail_smtp_config_ssl').val('SSL');
                            jQuery('#lofsmtpemail_smtp_config_auth').val('LOGIN');
                        } else if(jQuery('#lofsmtpemail_smtp_config_provider').val() == 24) {
                            jQuery('#lofsmtpemail_smtp_config_smtphost').val('smtp.ntlworld.com');
                            jQuery('#lofsmtpemail_smtp_config_port').val('465');
                            jQuery('#lofsmtpemail_smtp_config_ssl').val('SSL');
                            jQuery('#lofsmtpemail_smtp_config_auth').val('LOGIN');
                        } else if(jQuery('#lofsmtpemail_smtp_config_provider').val() == 25) {
                            jQuery('#lofsmtpemail_smtp_config_smtphost').val('pop3.btconnect.com');
                            jQuery('#lofsmtpemail_smtp_config_port').val('25');
                            jQuery('#lofsmtpemail_smtp_config_ssl').val('');
                            jQuery('#lofsmtpemail_smtp_config_auth').val('LOGIN');
                        } else if(jQuery('#lofsmtpemail_smtp_config_provider').val() == 26) {
                            jQuery('#lofsmtpemail_smtp_config_smtphost').val('outgoing.verizon.net');
                            jQuery('#lofsmtpemail_smtp_config_port').val('465');
                            jQuery('#lofsmtpemail_smtp_config_ssl').val('SSL');
                            jQuery('#lofsmtpemail_smtp_config_auth').val('LOGIN');
                        } else if(jQuery('#lofsmtpemail_smtp_config_provider').val() == 27) {
                            jQuery('#lofsmtpemail_smtp_config_smtphost').val('mail.btopenworld.com');
                            jQuery('#lofsmtpemail_smtp_config_port').val('25');
                            jQuery('#lofsmtpemail_smtp_config_ssl').val('');
                            jQuery('#lofsmtpemail_smtp_config_auth').val('LOGIN');
                        } else if(jQuery('#lofsmtpemail_smtp_config_provider').val() == 28) {
                            jQuery('#lofsmtpemail_smtp_config_smtphost').val('O2 Online Deutschland');
                            jQuery('#lofsmtpemail_smtp_config_port').val('25');
                            jQuery('#lofsmtpemail_smtp_config_ssl').val('');
                            jQuery('#lofsmtpemail_smtp_config_auth').val('LOGIN');
                        }
                    });
                });
            });
        </script>
        ";
    }

    public function getAdminUrl(){
        return $this->_urlBuilder->getUrl('lofsmtpemail/test', ['store' => $this->_request->getParam('store')]);
    }

    /**
     * Render button
     *
     * @param  \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        // Remove scope label
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();
        return parent::render($element);
    }

}
