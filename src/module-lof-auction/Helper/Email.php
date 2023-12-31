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
 * @package    Lof_Auction
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lof\Auction\Helper;

use Magento\Catalog\Model\Product;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Lof Auction Email helper
 */
class Email extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @param Context                                $context           ,
     * @param tateInterface                          $inlineTranslation ,
     * @param TransportBuilder                       $transportBuilder  ,
     * @param StoreManagerInterface                  $storeManager      ,
     * @param CustomerRepositoryInterface            $customer          ,
     * @param Product                                $product           ,
     * @param \Magento\Framework\Pricing\Helper\Data $priceHelper       ,
     * @param Data                                   $helperData
     */
    protected $messageManager;
    /**
     * @var StateInterface
     */
    private $_inlineTranslation;
    /**
     * @var TransportBuilder
     */
    private $_transportBuilder;
    /**
     * @var StoreManagerInterface
     */
    private $_storeManager;
    /**
     * @var Product
     */
    private $_product;
    /**
     * @var CustomerRepositoryInterface
     */
    private $_customer;
    /**
     * @var \Magento\Framework\Pricing\Helper\Data
     */
    private $_priceHelper;
    /**
     * @var Data
     */
    private $_helperData;

    /**
     * Email constructor.
     *
     * @param Context                                $context
     * @param StateInterface                         $inlineTranslation
     * @param TransportBuilder                       $transportBuilder
     * @param StoreManagerInterface                  $storeManager
     * @param CustomerRepositoryInterface            $customer
     * @param Product                                $product
     * @param \Magento\Framework\Pricing\Helper\Data $priceHelper
     * @param ManagerInterface                       $messageManager
     * @param Data                                   $helperData
     */
    public function __construct(
        Context $context,
        StateInterface $inlineTranslation,
        TransportBuilder $transportBuilder,
        StoreManagerInterface $storeManager,
        CustomerRepositoryInterface $customer,
        Product $product,
        \Magento\Framework\Pricing\Helper\Data $priceHelper,
        ManagerInterface $messageManager,
        Data $helperData
    ) {
        parent::__construct($context);
        $this->messageManager     = $messageManager;
        $this->_inlineTranslation = $inlineTranslation;
        $this->_transportBuilder  = $transportBuilder;
        $this->_storeManager      = $storeManager;
        $this->_customer          = $customer;
        $this->_product           = $product;
        $this->_priceHelper       = $priceHelper;
        $this->_helperData        = $helperData;
    }

    /**
     * [generateTemplate description]
     *
     * @param Mixed $emailTemplateVariables
     * @param Mixed $senderInfo
     * @param Mixed $receiverInfo
     * @param       $emailTempId
     * @return Email
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function generateTemplate(
        $emailTemplateVariables,
        $senderInfo,
        $receiverInfo,
        $emailTempId
    ) {
        $this->_transportBuilder
            ->setTemplateIdentifier($emailTempId)
            ->setTemplateOptions(
                [
                    'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                    'store' => $this->_storeManager->getStore()->getId(),
                ]
            )->setTemplateVars($emailTemplateVariables)
            ->setFrom($senderInfo)
            ->addTo($receiverInfo['email'], $receiverInfo['name']);

        return $this;
    }

    /**
     * send mail to Winner
     *
     * @param int   $winnerId    winner customer id
     * @param int   $productId   product id which bid win
     * @param float $winnerPrice bid amount on which user win auction
     * @return void
     */
    public function sendWinnerMail(
        $winnerId,
        $productId,
        $winnerPrice
    ) {
        try {
            $customer      = $this->_customer->getById($winnerId);
            $product       = $this->_product->load($productId);
            $auctionConfig = $this->_helperData->getAuctionConfiguration();
            $senderInfo    = $this->getSender();
            $receiverInfo  = [
                'name' => $customer->getFirstName() . " " . $customer->getLastName(),
                'email' => $customer->getEmail(),
            ];

            $emailTempVariables = [
                'name' => $receiverInfo['name'],
                'productName' => $product->getName(),
                'proUrl' => $product->getProductUrl(),
                'message' => __('you have won this product in ') . $this->formatPrice($winnerPrice),
                'comment' => __('Please go and buy this product.'),
            ];
            $this->generateTemplate(
                $emailTempVariables,
                $senderInfo,
                $receiverInfo,
                $auctionConfig['winner_notify_email_template']
            );
            $transport = $this->_transportBuilder->getTransport();
            try {
                $transport->sendMessage();
                $this->_inlineTranslation->resume();
            } catch (\Exception $e) {
                $error = true;
                $this->messageManager->addError(
                    __('We can\'t process your request right now. Sorry, that\'s all we know.')
                );
                $this->messageManager->addError($e->getMessage());
            }
        } catch (\Exception $e) {
            $this->_inlineTranslation->resume();
            $this->messageManager->addError(
                __('We can\'t process your request right now. Sorry, that\'s all we know.')
            );
            $this->messageManager->addError($e->getMessage());

            return;
        }
    }

    /**
     * send mail to admin
     *
     * @param int   $customerId customer id of bidder
     * @param int   $productId  product id on which bid apply
     * @param float $bidAmount  bid amount which user bid
     * @return void
     */

    public function sendMailToAdmin($customerId, $productId, $bidAmount)
    {
        try {
            $customer                             = $this->_customer->getById($customerId);
            $product                              = $this->_product->load($productId);
            $auctionConfig                        = $this->_helperData->getAuctionConfiguration();
            $senderInfo                           = $this->getSender();
            $auctionConfig['admin_email_address'] = $auctionConfig['admin_email_address'] ? $auctionConfig['admin_email_address'] : $senderInfo['email'];
            $auctionConfig['admin_email_address'] = $auctionConfig['admin_email_address'] ? $auctionConfig['admin_email_address'] : $senderInfo['email'];
            $receiverInfo                         = [
                'name' => 'Admin',
                'email' => $auctionConfig['admin_email_address'],
            ];

            $emailTempVariables = [
                'name' => $senderInfo['name'],
                'productName' => $product->getName(),
                'proUrl' => $product->getProductUrl(),
                'message' => $customer->getFirstName() . " " . $customer->getLastName()
                             . __(' has bidded ') . $this->formatPrice($bidAmount)
                             . __(' on this product'),
                'comment' => __('Please go and see more bidders.'),
            ];

            $this->generateTemplate(
                $emailTempVariables,
                $senderInfo,
                $receiverInfo,
                $auctionConfig['admin_notify_email_template']
            );
            $transport = $this->_transportBuilder->getTransport();
            try {
                $transport->sendMessage();
                $this->_inlineTranslation->resume();
            } catch (\Exception $e) {
                $error = true;
                $this->messageManager->addError(
                    __('We can\'t process your request right now. Sorry, that\'s all we know.')
                );
                $this->messageManager->addError($e->getMessage());
            }
        } catch (\Exception $e) {
            $this->_inlineTranslation->resume();
            $this->messageManager->addError(
                __('We can\'t process your request right now. Sorry, that\'s all we know.')
            );
            $this->messageManager->addError($e->getMessage());

            return;
        }
    }

    /**
     * sendAutoMailToAdmin sends mail to admin of auto bid
     *
     * @param int   $customerId customer id of bidder
     * @param int   $productId  product id on which bid apply
     * @param float $bidAmount  bid amount which user bid
     */

    public function sendAutoMailToAdmin($customerId, $productId, $bidAmount)
    {
        try {
            $customer                             = $this->_customer->getById($customerId);
            $product                              = $this->_product->load($productId);
            $auctionConfig                        = $this->_helperData->getAuctionConfiguration();
            $senderInfo                           = $this->getSender();
            $auctionConfig['admin_email_address'] = $auctionConfig['admin_email_address'] ? $auctionConfig['admin_email_address'] : $senderInfo['email'];
            $receiverInfo                         = [
                'name' => 'Admin',
                'email' => $auctionConfig['admin_email_address'],
            ];

            $emailTempVariables = [
                'name' => $receiverInfo['name'],
                'productName' => $product->getName(),
                'productUrl' => $product->getProductUrl(),
                'message' => $customer->getFirstName() . " " . $customer->getLastName()
                             . __(' has bidded auto bid ') . $this->formatPrice($bidAmount) . __(' on this product'),
                'comment' => __('Please go and see more bidders.'),
            ];

            $this->generateTemplate(
                $emailTempVariables,
                $senderInfo,
                $receiverInfo,
                $auctionConfig['admin_notify_email_template']
            );
            $transport = $this->_transportBuilder->getTransport();
            try {
                $transport->sendMessage();
                $this->_inlineTranslation->resume();
            } catch (\Exception $e) {
                $error = true;
                $this->messageManager->addError(
                    __('We can\'t process your request right now. Sorry, that\'s all we know.')
                );
                $this->messageManager->addError($e->getMessage());
            }
        } catch (\Exception $e) {
            $this->_inlineTranslation->resume();
            $this->messageManager->addError(
                __('We can\'t process your request right now. Sorry, that\'s all we know.')
            );
            $this->messageManager->addError($e->getMessage());

            return;
        }
    }

    /**
     * sendOutBidAutoBidder send mail to auto bidder whose auto bid is out
     *
     * @param int    $bidUserId of whom mail has been send
     * @param int    $userId    in of customer who places higher bid
     * @param string $productId stores product id
     */

    public function sendOutBidAutoBidder($bidUserId, $userId, $productId)
    {
        try {
            $bidUser       = $this->_customer->getById($bidUserId);
            $customer      = $this->_customer->getById($userId);
            $customerName  = $customer->getFirstName() . " " . $customer->getLastName();
            $product       = $this->_product->load($productId);
            $auctionConfig = $this->_helperData->getAuctionConfiguration();
            $senderInfo    = $this->getSender();
            $receiverInfo  = [
                'name' => $bidUser->getFirstName() . " " . $bidUser->getLastName(),
                'email' => $bidUser->getEmail(),
            ];

            $emailTempVariables = [
                'name' => $receiverInfo['name'],
                'productName' => $product->getName(),
                'ProductUrl' => $product->getProductUrl(),
                'message' => $customerName . __(' has bidded higher price than you on following product. So your auto bid is outbid'),
                'comment' => __('Please bid again and win following product.'),
            ];

            $this->generateTemplate(
                $emailTempVariables,
                $senderInfo,
                $receiverInfo,
                $auctionConfig['outbid_notify_email_template']
            );
            $transport = $this->_transportBuilder->getTransport();
            try {
                $transport->sendMessage();
                $this->_inlineTranslation->resume();
            } catch (\Exception $e) {
                $error = true;
                $this->messageManager->addError(
                    __('We can\'t process your request right now. Sorry, that\'s all we know.')
                );
                $this->messageManager->addError($e->getMessage());
            }
        } catch (\Exception $e) {
            $this->_inlineTranslation->resume();
            $this->messageManager->addError(
                __('We can\'t process your request right now. Sorry, that\'s all we know.')
            );
            $this->messageManager->addError($e->getMessage());

            return;
        }
    }

    /**
     * sendMailToMembers use to send mails to all the members of normal bid]
     *
     * @param [int] $bidUserId  [holds customer id which place last bid]
     * @param [int] $bidUserId   [to whom mail has been send]
     * @param [string] $productId [product id on which customer places bid]
     */
    public function sendMailToMembers($bidUserId, $userId, $productId)
    {
        try {
            $bidUser       = $this->_customer->getById($bidUserId);
            $customer      = $this->_customer->getById($userId);
            $customerName  = $customer->getFirstName() . " " . $customer->getLastName();
            $product       = $this->_product->load($productId);
            $auctionConfig = $this->_helperData->getAuctionConfiguration();
            $senderInfo    = $this->getSender();
            $receiverInfo  = [
                'name' => $bidUser->getFirstName() . " " . $bidUser->getLastName(),
                'email' => $bidUser->getEmail(),
            ];

            $emailTempVariables = [
                'name' => $receiverInfo['name'],
                'productName' => $product->getName(),
                'productUrl' => $product->getProductUrl(),
                'message' => $customerName . __(' has bidded higher price than you on following product.'),
                'comment' => __('Please bid again and win following product.'),
            ];
            $this->generateTemplate(
                $emailTempVariables,
                $senderInfo,
                $receiverInfo,
                $auctionConfig['outbid_notify_email_template']
            );
            $transport = $this->_transportBuilder->getTransport();
            try {
                $transport->sendMessage();
                $this->_inlineTranslation->resume();
            } catch (\Exception $e) {
                $error = true;
                $this->messageManager->addError(
                    __('We can\'t process your request right now. Sorry, that\'s all we know.')
                );
                $this->messageManager->addError($e->getMessage());
            }
        } catch (\Exception $e) {
            $this->_inlineTranslation->resume();
            $this->messageManager->addError(
                __('We can\'t process your request right now. Sorry, that\'s all we know.')
            );
            $this->messageManager->addError($e->getMessage());

            return;
        }
    }

    /**
     * @param $customerId
     * @param $productId
     * @param $bidAmount
     */
    public function sendMailToBidder($customerId, $productId, $bidAmount)
    {
        try {
            $customer      = $this->_customer->getById($customerId);
            $product       = $this->_product->load($productId);
            $auctionConfig = $this->_helperData->getAuctionConfiguration();
            $senderInfo    = $this->getSender();
            $receiverInfo  = [
                'name' => $customer->getFirstName() . " " . $customer->getLastName(),
                'email' => $customer->getEmail(),
            ];

            $emailTempVariables = [
                'name' => $senderInfo['name'],
                'productName' => $product->getName(),
                'proUrl' => $product->getProductUrl(),
                'message' => __('You have bidded ') . $this->formatPrice($bidAmount)
                             . __(' on this product'),
                'comment' => __('Please go and see more.'),
            ];

            $this->generateTemplate(
                $emailTempVariables,
                $senderInfo,
                $receiverInfo,
                $auctionConfig['confirm_email_template']
            );
            $transport = $this->_transportBuilder->getTransport();
            try {
                $transport->sendMessage();
                $this->_inlineTranslation->resume();
            } catch (\Exception $e) {
                $error = true;
                $this->messageManager->addError(
                    __('We can\'t process your request right now. Sorry, that\'s all we know.')
                );
                $this->messageManager->addError($e->getMessage());
            }
        } catch (\Exception $e) {
            $this->_inlineTranslation->resume();
            $this->messageManager->addError(
                __('We can\'t process your request right now. Sorry, that\'s all we know.')
            );
            $this->messageManager->addError($e->getMessage());

            return;
        }
    }

    /**
     * @param $customerId
     * @param $productId
     * @param $stopTime
     */
    public function sendMailNotifyMinDay($customerId, $productId, $stopTime)
    {
        try {
            $customer      = $this->_customer->getById($customerId);
            $product       = $this->_product->load($productId);
            $auctionConfig = $this->_helperData->getAuctionConfiguration();
            $senderInfo    = $this->getSender();
            $receiverInfo  = [
                'name' => $customer->getFirstName() . " " . $customer->getLastName(),
                'email' => $customer->getEmail(),
            ];

            $emailTempVariables = [
                'name' => $customer->getFirstName(),
                'productName' => $product->getName(),
                'proUrl' => $product->getProductUrl(),
                'stopTime' => $stopTime,
                'message' => __('This auction will end at ' . $stopTime . '.'),
                'comment' => __('Let\'s continue to bid to become the winner.'),
            ];

            $this->generateTemplate(
                $emailTempVariables,
                $senderInfo,
                $receiverInfo,
                $auctionConfig['min_day_notify_email_template']
            );
            $transport = $this->_transportBuilder->getTransport();
            try {
                $transport->sendMessage();
                $this->_inlineTranslation->resume();
            } catch (\Exception $e) {
                $error = true;
                $this->messageManager->addError(
                    __('We can\'t process your request right now. Sorry, that\'s all we know.')
                );
                $this->messageManager->addError($e->getMessage());
            }
        } catch (\Exception $e) {
            $this->_inlineTranslation->resume();
            $this->messageManager->addError(
                __('We can\'t process your request right now. Sorry, that\'s all we know.')
            );
            $this->messageManager->addError($e->getMessage());

            return;
        }
    }

    /**
     * sendAutoMailUsers sends mail to users of auto bid
     *
     * @param int $bidUserId holds customer id which place last bid
     * @param int $userId    customer id
     * @param int $productId holds product id on which bid has been placed
     */
    public function sendAutoMailUsers($bidUserId, $userId, $productId)
    {
        try {
            $bidUser                              = $this->_customer->getById($bidUserId);
            $customer                             = $this->_customer->getById($userId);
            $customerName                         = $customer->getFirstName() . " " . $customer->getLastName();
            $product                              = $this->_product->load($productId);
            $auctionConfig                        = $this->_helperData->getAuctionConfiguration();
            $senderInfo                           = $this->getSender();
            $auctionConfig['admin_email_address'] = $auctionConfig['admin_email_address'] ? $auctionConfig['admin_email_address'] : $senderInfo['email'];

            $receiverInfo = [
                'name' => $bidUser->getFirstName() . " " . $bidUser->getLastName(),
                'email' => $bidUser->getEmail(),
            ];

            $emailTempVariables = [
                'name' => $receiverInfo['name'],
                'productName' => $product->getName(),
                'productUrl' => $product->getProductUrl(),
                'message' => $customerName . __(' has bidded auto bid higher price than you on following product.'),
                'comment' => __('Please bid again and get chance to win following product.'),
            ];
            $this->generateTemplate(
                $emailTempVariables,
                $senderInfo,
                $receiverInfo,
                $auctionConfig['outbid_notify_email_template']
            );
            $transport = $this->_transportBuilder->getTransport();
            try {
                $transport->sendMessage();
                $this->_inlineTranslation->resume();
            } catch (\Exception $e) {
                $error = true;
                $this->messageManager->addError(
                    __('We can\'t process your request right now. Sorry, that\'s all we know.')
                );
                $this->messageManager->addError($e->getMessage());
            }
        } catch (\Exception $e) {
            $this->_inlineTranslation->resume();
            $this->messageManager->addError(
                __('We can\'t process your request right now. Sorry, that\'s all we know.')
            );

            $this->messageManager->addError($e->getMessage());

            return;
        }
    }

    /**
     * get currency in format
     *
     * @param $amount float
     * @return string
     *
     */
    public function formatPrice($amount)
    {
        return $this->_priceHelper->currency($amount, true, false);
    }

    /**
     * @return array
     */
    public function getSender()
    {
        $senderName  = $this->scopeConfig->getValue('trans_email/ident_general/name');
        $senderEmail = $this->scopeConfig->getValue('trans_email/ident_general/email');
        $senderName  = $senderName ? $senderName : __('Admin');

        return [
            'name' => $senderName,
            'email' => $senderEmail,
        ];
    }
}
