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

namespace Lof\MarketPlace\Model;

class Sender
{
    /**
     * @var \Lof\MarketPlace\Helper\Data
     */
    protected $helper;

    /**
     * @var string|null
     */
    protected $messageSubject = null;

    /**
     * @var string|null
     */
    protected $messageBody = null;
    /**
     * @var string|null
     */
    protected $emailSubject = null;

    /**
     * @var string|null
     */
    protected $emailContent = null;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

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
     * @param \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation
     * @param \Magento\Framework\Mail\Template\TransportBuilder $_transportBuilder
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Lof\MarketPlace\Helper\Data $helper
     */
    public function __construct(
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\Mail\Template\TransportBuilder $_transportBuilder,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Lof\MarketPlace\Helper\Data $helper
    ) {
        $this->messageManager = $messageManager;
        $this->inlineTranslation = $inlineTranslation;
        $this->_transportBuilder = $_transportBuilder;
        $this->_storeManager = $storeManager;
        $this->helper = $helper;
    }

    /**
     * @param $data
     */
    public function newMessage($data)
    {
        try {
            $postObject = new \Magento\Framework\DataObject();
            $postObject->setData($data);
            $transport = $this->_transportBuilder
                ->setTemplateIdentifier($this->helper->getConfig('email_settings/message_template'))
                ->setTemplateOptions(
                    [
                        'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                        'store' => $this->_storeManager->getStore()->getId(),
                    ]
                )
                ->setTemplateVars(['data' => $postObject])
                ->setFrom($this->helper->getConfig('email_settings/sender_email_identity'))
                ->addTo($data['sender_email'])
                ->setReplyTo($data['sender_email'])
                ->getTransport();
            try {
                $transport->sendMessage();
                $this->inlineTranslation->resume();
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(
                    __('We can\'t process your request right now. Sorry, that\'s all we know.')
                );
            }
        } catch (\Exception $e) {
            $this->inlineTranslation->resume();
            $this->messageManager->addErrorMessage(
                __('We can\'t process your request right now. Sorry, that\'s all we know.')
            );
            return;
        }
    }

    /**
     * @param $data
     */
    public function newRating($data)
    {
        try {
            $postObject = new \Magento\Framework\DataObject();
            $postObject->setData($data);
            $transport = $this->_transportBuilder
                ->setTemplateIdentifier($this->helper->getConfig('email_settings/rating_template'))
                ->setTemplateOptions(
                    [
                        'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                        'store' => $this->_storeManager->getStore()->getId(),
                    ]
                )
                ->setTemplateVars(['data' => $postObject])
                ->setFrom($this->helper->getConfig('email_settings/sender_email_identity'))
                ->addTo($data['seller_email'])
                ->setReplyTo($data['seller_email'])
                ->getTransport();
            try {
                $transport->sendMessage();
                $this->inlineTranslation->resume();
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(
                    __('We can\'t process your request right now. Sorry, that\'s all we know.')
                );
            }
        } catch (\Exception $e) {
            $this->inlineTranslation->resume();
            $this->messageManager->addErrorMessage(
                __('We can\'t process your request right now. Sorry, that\'s all we know.')
            );
            return;
        }
    }

    /**
     * @param mixed $data
     */
    public function noticeAdmin($data)
    {
        try {
            $this->inlineTranslation->suspend();
            $postObject = new \Magento\Framework\DataObject();
            $postObject->setData($data);
            $emailAdmin = $this->helper->getConfig('sales_settings/email_admin');
            if ($emailAdmin) {
                $transport = $this->_transportBuilder
                    ->setTemplateIdentifier($this->helper->getConfig('email_settings/register_seller_template'))
                    ->setTemplateOptions(
                        [
                            'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                            'store' => $this->_storeManager->getStore()->getId(),
                        ]
                    )
                    ->setTemplateVars(['data' => $postObject])
                    ->setFrom($this->helper->getConfig('email_settings/sender_email_identity'))
                    ->addTo($emailAdmin)
                    ->setReplyTo($emailAdmin)
                    ->getTransport();
                try {
                    $transport->sendMessage();
                    $this->inlineTranslation->resume();
                } catch (\Exception $e) {
                    $this->messageManager->addErrorMessage(
                        __('We can\'t process your request right now. Sorry, that\'s all we know.')
                    );
                }
            }
        } catch (\Exception $e) {
            $this->inlineTranslation->resume();
            $this->messageManager->addErrorMessage(
                __('We can\'t process your request right now. Sorry, that\'s all we know.')
            );
            return;
        }
    }

    /**
     * @param $data
     */
    public function thankForRegisterSeller($data)
    {
        try {
            $this->inlineTranslation->suspend();
            $postObject = new \Magento\Framework\DataObject();
            $postObject->setData($data);
            $emailAdmin = $this->helper->getConfig('sales_settings/email_admin');
            if ($emailAdmin) {
                $transport = $this->_transportBuilder
                    ->setTemplateIdentifier($this->helper->getConfig('email_settings/thankyou_register_seller_template'))
                    ->setTemplateOptions(
                        [
                            'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                            'store' => $this->_storeManager->getStore()->getId(),
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
                    $this->messageManager->addErrorMessage(
                        __('We can\'t process your request right now. Sorry, that\'s all we know.')
                    );
                }
            }
        } catch (\Exception $e) {
            $this->inlineTranslation->resume();
            $this->messageManager->addErrorMessage(
                __('We can\'t process your request right now. Sorry, that\'s all we know.')
            );
            return;
        }
    }

    /**
     * @param $data
     */
    public function registerSeller($data)
    {
        try {
            $postObject = new \Magento\Framework\DataObject();
            $postObject->setData($data);
            $transport = $this->_transportBuilder
                ->setTemplateIdentifier($this->helper->getConfig('email_settings/register_seller_template'))
                ->setTemplateOptions(
                    [
                        'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                        'store' => $this->_storeManager->getStore()->getId(),
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
                $this->messageManager->addErrorMessage(
                    __('We can\'t process your request right now. Sorry, that\'s all we know.')
                );
            }
        } catch (\Exception $e) {
            $this->inlineTranslation->resume();
            $this->messageManager->addErrorMessage(
                __('We can\'t process your request right now. Sorry, that\'s all we know.')
            );
            return;
        }
    }

    /**
     * @param $data
     */
    public function newOrder($data)
    {
        try {
            $postObject = new \Magento\Framework\DataObject();

            $postObject->setData($data);
            $transport = $this->_transportBuilder
                ->setTemplateIdentifier($this->helper->getConfig('email_settings/order_template'))
                ->setTemplateOptions(
                    [
                        'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                        'store' => $this->_storeManager->getStore()->getId(),
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
                $this->messageManager->addErrorMessage(
                    __('We can\'t process your request right now. Sorry, that\'s all we know.')
                );
            }
        } catch (\Exception $e) {
            $this->inlineTranslation->resume();
            $this->messageManager->addErrorMessage(
                __('We can\'t process your request right now. Sorry, that\'s all we know.')
            );
            return;
        }
    }

    /**
     * @param $data
     */
    public function newInvoice($data)
    {
        try {
            $postObject = new \Magento\Framework\DataObject();

            $postObject->setData($data);
            $transport = $this->_transportBuilder
                ->setTemplateIdentifier($this->helper->getConfig('email_settings/invoice_template'))
                ->setTemplateOptions(
                    [
                        'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                        'store' => $this->_storeManager->getStore()->getId(),
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
                $this->messageManager->addErrorMessage(
                    __('We can\'t process your request right now. Sorry, that\'s all we know.')
                );
            }
        } catch (\Exception $e) {
            $this->inlineTranslation->resume();
            $this->messageManager->addErrorMessage(
                __('We can\'t process your request right now. Sorry, that\'s all we know.')
            );
            return;
        }
    }

    /**
     * @param $data
     */
    public function newShipment($data)
    {
        try {
            $postObject = new \Magento\Framework\DataObject();

            $postObject->setData($data);
            $transport = $this->_transportBuilder
                ->setTemplateIdentifier($this->helper->getConfig('email_settings/shipment_template'))
                ->setTemplateOptions(
                    [
                        'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                        'store' => $this->_storeManager->getStore()->getId(),
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
                $this->messageManager->addErrorMessage(
                    __('We can\'t process your request right now. Sorry, that\'s all we know.')
                );
            }
        } catch (\Exception $e) {
            $this->inlineTranslation->resume();
            $this->messageManager->addErrorMessage(
                __('We can\'t process your request right now. Sorry, that\'s all we know.')
            );
            return;
        }
    }

    /**
     * @param $data
     */
    public function replyMessage($data)
    {
        try {
            $postObject = new \Magento\Framework\DataObject();
            $postObject->setData($data);
            $transport = $this->_transportBuilder
                ->setTemplateIdentifier($this->helper->getConfig('email_settings/reply_message_template'))
                ->setTemplateOptions(
                    [
                        'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                        'store' => $this->_storeManager->getStore()->getId(),
                    ]
                )
                ->setTemplateVars(['data' => $postObject])
                ->setFrom($this->helper->getConfig('email_settings/sender_email_identity'))
                ->addTo($data['receiver_email'])
                ->setReplyTo($data['receiver_email'])
                ->getTransport();
            try {
                $transport->sendMessage();
                $this->inlineTranslation->resume();
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(
                    __('We can\'t process your request right now. Sorry, that\'s all we know.')
                );
            }
        } catch (\Exception $e) {
            $this->inlineTranslation->resume();
            $this->messageManager->addErrorMessage(
                __('We can\'t process your request right now. Sorry, that\'s all we know.')
            );
            return;
        }
    }

    /**
     * @param $data
     */
    public function approveSeller($data)
    {
        try {
            $postObject = new \Magento\Framework\DataObject();
            $postObject->setData($data);
            $transport = $this->_transportBuilder
                ->setTemplateIdentifier($this->helper->getConfig('email_settings/approve_seller_template'))
                ->setTemplateOptions(
                    [
                        'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                        'store' => $this->_storeManager->getStore()->getId(),
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
                $this->messageManager->addErrorMessage(
                    __('We can\'t process your request right now. Sorry, that\'s all we know.')
                );
            }
        } catch (\Exception $e) {
            $this->inlineTranslation->resume();
            $this->messageManager->addErrorMessage(
                __('We can\'t process your request right now. Sorry, that\'s all we know.')
            );
            return;
        }
    }

    /**
     * @param $data
     */
    public function approveProduct($data)
    {
        try {
            $postObject = new \Magento\Framework\DataObject();
            $postObject->setData($data);
            $transport = $this->_transportBuilder
                ->setTemplateIdentifier($this->helper->getConfig('email_settings/approve_product_template'))
                ->setTemplateOptions(
                    [
                        'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                        'store' => $this->_storeManager->getStore()->getId(),
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
                $this->messageManager->addErrorMessage(
                    __('We can\'t process your request right now. Sorry, that\'s all we know.')
                );
            }
        } catch (\Exception $e) {
            $this->inlineTranslation->resume();
            $this->messageManager->addErrorMessage(
                __('We can\'t process your request right now. Sorry, that\'s all we know.')
            );
            return;
        }
    }

    /**
     * @param $data
     */
    public function unapproveSeller($data)
    {
        try {
            $postObject = new \Magento\Framework\DataObject();
            $postObject->setData($data);
            $transport = $this->_transportBuilder
                ->setTemplateIdentifier($this->helper->getConfig('email_settings/unapprove_seller_template'))
                ->setTemplateOptions(
                    [
                        'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                        'store' => $this->_storeManager->getStore()->getId(),
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
                $this->messageManager->addErrorMessage(
                    __('We can\'t process your request right now. Sorry, that\'s all we know.')
                );
            }
        } catch (\Exception $e) {
            $this->inlineTranslation->resume();
            $this->messageManager->addErrorMessage(
                __('We can\'t process your request right now. Sorry, that\'s all we know.')
            );
            return;
        }
    }

    /**
     * @param $data
     */
    public function pendingSellerProfile($data)
    {
        try {
            $postObject = new \Magento\Framework\DataObject();
            $postObject->setData($data);
            $transport = $this->_transportBuilder
                ->setTemplateIdentifier($this->helper->getConfig('email_settings/pending_seller_profile_template'))
                ->setTemplateOptions(
                    [
                        'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                        'store' => $this->_storeManager->getStore()->getId(),
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
                $this->messageManager->addErrorMessage(
                    __('We can\'t process your request right now. Sorry, that\'s all we know.')
                );
            }
        } catch (\Exception $e) {
            $this->inlineTranslation->resume();
            $this->messageManager->addErrorMessage(
                __('We can\'t process your request right now. Sorry, that\'s all we know.')
            );
            return;
        }
    }

    /**
     * @param $data
     */
    public function unapproveProduct($data)
    {
        try {
            $postObject = new \Magento\Framework\DataObject();
            $postObject->setData($data);
            $transport = $this->_transportBuilder
                ->setTemplateIdentifier($this->helper->getConfig('email_settings/unapprove_product_template'))
                ->setTemplateOptions(
                    [
                        'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                        'store' => $this->_storeManager->getStore()->getId(),
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
                $this->messageManager->addErrorMessage(
                    __('We can\'t process your request right now. Sorry, that\'s all we know.')
                );
            }
        } catch (\Exception $e) {
            $this->inlineTranslation->resume();
            $this->messageManager->addErrorMessage(
                __('We can\'t process your request right now. Sorry, that\'s all we know.')
            );
            return;
        }
    }

    /**
     * @param $data
     */
    public function sellerNewMessage($data)
    {
        try {
            $postObject = new \Magento\Framework\DataObject();
            $postObject->setData($data);
            $email_admin = $this->helper->getConfig('sales_settings/email_admin');
            if ($email_admin) {
                $transport = $this->_transportBuilder
                    ->setTemplateIdentifier($this->helper->getConfig('email_settings/seller_message_template'))
                    ->setTemplateOptions(
                        [
                            'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                            'store' => $this->_storeManager->getStore()->getId(),
                        ]
                    )
                    ->setTemplateVars(['data' => $postObject])
                    ->setFrom($this->helper->getConfig('email_settings/sender_email_identity'))
                    ->addTo($email_admin)
                    ->setReplyTo($email_admin)
                    ->getTransport();
                try {
                    $transport->sendMessage();
                    $this->inlineTranslation->resume();
                } catch (\Exception $e) {
                    $this->messageManager->addError(
                        __('We can\'t process your request right now. Sorry, that\'s all we know.')
                    );
                }
            } else {
                $this->messageManager->addWarning(
                    __('We can\'t process your request right now, because the admin email was not set.')
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
     * Send notification email to admin when seller create new product
     *
     * @param $data
     */
    public function newSellerProduct($data)
    {
        try {
            if ($this->helper->getConfig('email_settings/enable_send_email_new_product')) {
                $postObject = new \Magento\Framework\DataObject();
                $postObject->setData($data);

                $transport = $this->_transportBuilder
                    ->setTemplateIdentifier($this->helper->getConfig('email_settings/seller_create_product_template'))
                    ->setTemplateOptions(
                        [
                            'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                            'store' => $this->_storeManager->getStore()->getId(),
                        ]
                    )
                    ->setTemplateVars(['data' => $postObject])
                    ->setFrom($this->helper->getConfig('email_settings/sender_email_identity'))
                    ->addTo($this->helper->getConfig('sales_settings/email_admin'))
                    ->setReplyTo($this->helper->getConfig('sales_settings/email_admin'))
                    ->getTransport();
                try {
                    $transport->sendMessage();
                    $this->inlineTranslation->resume();
                } catch (\Exception $e) {
                    $this->messageManager->addErrorMessage(
                        __('We can\'t process your request right now. Sorry, that\'s all we know.')
                    );
                }
            }
        } catch (\Exception $e) {
            $this->inlineTranslation->resume();
            $this->messageManager->addErrorMessage(
                __('We can\'t process your request right now. Sorry, that\'s all we know.')
            );
            return;
        }
    }

    /**
     * Send notification email to admin when seller edit product
     *
     * @param $data
     */
    public function editSellerProduct($data)
    {
        try {
            if ($this->helper->getConfig('email_settings/enable_send_email_edit_product')) {
                $postObject = new \Magento\Framework\DataObject();
                $postObject->setData($data);

                $transport = $this->_transportBuilder
                    ->setTemplateIdentifier($this->helper->getConfig('email_settings/seller_edit_product_template'))
                    ->setTemplateOptions(
                        [
                            'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                            'store' => $this->_storeManager->getStore()->getId(),
                        ]
                    )
                    ->setTemplateVars(['data' => $postObject])
                    ->setFrom($this->helper->getConfig('email_settings/sender_email_identity'))
                    ->addTo($this->helper->getConfig('sales_settings/email_admin'))
                    ->setReplyTo($this->helper->getConfig('sales_settings/email_admin'))
                    ->getTransport();
                try {
                    $transport->sendMessage();
                    $this->inlineTranslation->resume();
                } catch (\Exception $e) {
                    $this->messageManager->addErrorMessage(
                        __('We can\'t process your request right now. Sorry, that\'s all we know.')
                    );
                }
            }
        } catch (\Exception $e) {
            $this->inlineTranslation->resume();
            $this->messageManager->addErrorMessage(
                __('We can\'t process your request right now. Sorry, that\'s all we know.')
            );
            return;
        }
    }

    /**
     * @param $queue
     * @return string|null
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getEmailContent($queue)
    {
        if ($this->emailContent == null) {
            // phpcs:disable Squiz.PHP.CommentedOutCode.Found
//            $this->getPreviewEmail($queue);
            return $this->_transportBuilder->getMessageContent();
        }
        return $this->emailContent;
    }

    /**
     * @param $queue
     * @return string|null
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getEmailSubject($queue)
    {
        if ($this->emailSubject == null) {
            // phpcs:disable Squiz.PHP.CommentedOutCode.Found
//            $this->getPreviewEmail($queue);
            return $this->_transportBuilder->getMessageSubject();
        }
        return $this->emailSubject;
    }

    /**
     * @param $queue
     * @return string|null
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getMessageContent($queue)
    {
        if ($this->messageBody == null) {
            // phpcs:disable Squiz.PHP.CommentedOutCode.Found
//            $this->getPreview($queue);
            return $this->_transportBuilder->getMessageContent();
        }
        return $this->messageBody;
    }

    /**
     * @param $queue
     * @return string|null
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getMessageSubject($queue)
    {
        if ($this->messageSubject == null) {
            // phpcs:disable Squiz.PHP.CommentedOutCode.Found
//            $this->getPreview($queue);
            return $this->_transportBuilder->getMessageSubject();
        }

        return $this->messageSubject;
    }
}
