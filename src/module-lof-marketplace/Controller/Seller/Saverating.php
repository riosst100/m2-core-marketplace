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

namespace Lof\MarketPlace\Controller\Seller;

use Magento\Framework\App\Action\Context;
use Lof\MarketPlace\Model\Rating;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Saverating extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $session;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Lof\MarketPlace\Model\SellerFactory
     */
    protected $sellerFactory;

    /**
     * @var \Lof\MarketPlace\Model\Sender
     */
    protected $sender;

    /**
     * @var \Lof\MarketPlace\Helper\Data
     */
    protected $helper;

    /**
     * @var \Lof\MarketPlace\Helper\Rating
     */
    protected $ratingHelper;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $_fileSystem;

    /**
     * Saverating constructor.
     *
     * @param Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Lof\MarketPlace\Model\SellerFactory $sellerFactory
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Lof\MarketPlace\Model\Sender $sender
     * @param \Lof\MarketPlace\Helper\Data $helper
     * @param \Lof\MarketPlace\Helper\Rating $ratingHelper
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Lof\MarketPlace\Model\SellerFactory $sellerFactory,
        \Magento\Framework\Filesystem $filesystem,
        \Lof\MarketPlace\Model\Sender $sender,
        \Lof\MarketPlace\Helper\Data $helper,
        \Lof\MarketPlace\Helper\Rating $ratingHelper,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        $this->helper = $helper;
        $this->sender = $sender;
        $this->sellerFactory = $sellerFactory;
        $this->session = $customerSession;
        $this->_fileSystem = $filesystem;
        $this->resultPageFactory = $resultPageFactory;
        $this->ratingHelper = $ratingHelper;
        parent::__construct($context);
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $postData = $this->getRequest()->getPostValue();
        $customerSession = $this->session;
        if ($postData && isset($postData['seller_id']) && $postData['seller_id']) {
            $data = [];
            $customerId = 0;
            $customerEmail = '';
            $customerName = '';
            if ($customerSession->isLoggedIn()) {
                $customerId = $customerSession->getId();
                $customerObject = $customerSession->getCustomer();
                $customerEmail = $customerObject->getEmail();
                $customerName = $customerObject->getName();
                $seller = $this->sellerFactory->create()->load($customerId, 'customer_id');
                $sellerId = $seller ? $seller->getId() : 0;

                if ((int)$sellerId == (int)$postData['seller_id']) {
                    $this->messageManager->addErrorMessage(
                        __('You can not rate for your account.')
                    );
                    $this->_redirect($postData['currUrl']);
                    return;
                }
            }
            $data['like_about'] = isset($postData['like_about']) ? $postData['like_about'] : "";
            $data['not_like_about'] = isset($postData['not_like_about']) ? $postData['not_like_about'] : "";
            $data['is_recommended'] = isset($postData['is_recommended']) ? (int)$postData['is_recommended'] :0;
            $data['rate1'] = intval($postData['rate1']);
            $data['rate2'] = intval($postData['rate2']);
            $data['rate3'] = intval($postData['rate3']);
            $data['currUrl'] = $postData['currUrl'];
            $data['seller_id'] = (int)$postData['seller_id'];

            $currentSeller = $objectManager->create(\Lof\MarketPlace\Model\Seller::class)
                ->load($data['seller_id']);

            $data['seller_email'] = $currentSeller->getEmail();
            $data['seller_name'] = $currentSeller->getName();
            $data['title'] = strip_tags($postData['title']);
            $data['detail'] = strip_tags($postData['detail']);
            $data['customer_id'] = $customerId;
            $data['email'] = isset($postData['email']) && $postData['email'] ? strip_tags($postData['email']) : $customerEmail;
            $data['nickname'] = isset($postData['nickname']) && $postData['nickname'] ? strip_tags($postData['nickname']) : $customerName;
            if (!$data['email'] || !$data['nickname']) {
                $this->messageManager->addErrorMessage(
                    __('Missing information!')
                );
                $this->_redirect($data['currUrl']);
                return;
            }

            $this->_eventManager->dispatch(
                'marketplace_controller_before_save_rating',
                [
                    'data' => $data,
                    'request' => $this
                ]
            );

            try {
                $data["verified_buyer"] = $this->ratingHelper->checkPurchasedOrder((int)$currentSeller->getId(), $customerId, $data['email']);
                $ratingHelper = $this->ratingHelper->setVerifiedBuyer($data["verified_buyer"]);
                if ($ratingHelper->checkAllowRating((int)$data['seller_id'], $data['email'])) {

                    $data = $this->helper->xss_clean_array($data);

                    $ratingModel = $objectManager->get(\Lof\MarketPlace\Model\Rating::class);

                    $data['rating'] = ($data['rate1'] + $data['rate2'] + $data['rate3']) / 3;
                    if ($this->helper->getConfig('general_settings/rating_approval')) {
                        $data['status'] = Rating::STATUS_PENDING;
                        $message = __('Your rating has been submitted for approval.');
                    } else {
                        $data['status'] = Rating::STATUS_ACCEPT;
                        $message = __('Your rating has been submitted successfully!');
                    }

                    $ratingModel->setData($data);

                    /** validate rating data before save */
                    if ($this->validateRating($ratingModel)) {

                        $ratingModel->save();

                        $data['namestore'] = $this->helper->getStoreName();
                        $data['urllogin'] = $this->helper->getBaseStoreUrl() . 'customer/account/login';

                        if ($this->helper->getConfig('email_settings/enable_send_email')) {
                            $this->sender->newRating($data);
                        }

                        $this->_eventManager->dispatch(
                            'marketplace_controller_after_save_rating',
                            [
                                'data' => $data,
                                'rating' => $ratingModel,
                                'request' => $this
                            ]
                        );

                        $this->messageManager->addSuccessMessage($message);
                    } else {
                        $this->messageManager->addErrorMessage(__("You can not rating for this seller at now. Please try again later!"));
                    }

                } else {
                    $this->messageManager->addErrorMessage(__("You can not rating for this seller at now. Please try again later!"));
                }
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage(
                    $e,
                    __('Something went wrong while submit rating for the seller.')
                );
            }
            $this->_redirect($postData['currUrl']);
            return;
        }
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('/');
        return $resultRedirect;
    }

    /**
     * validate data
     *
     * @param \Lof\MarketPlace\Model\Rating $rating
     * @return bool
     */
    protected function validateRating($rating)
    {
        $flag = true;
        if (empty($rating->getSellerId())) {
            $flag = false;
        }
        if (empty($rating->getTitle())) {
            $flag = false;
        }
        if (empty($rating->getDetail())) {
            $flag = false;
        }
        if (0 >= (int)$rating->getRate1() || 5 < (int)$rating->getRate1()) {
            $flag = false;
        }
        if (0 >= (int)$rating->getRate2() || 5 < (int)$rating->getRate2()) {
            $flag = false;
        }
        if (0 >= (int)$rating->getRate3() || 5 < (int)$rating->getRate3()) {
            $flag = false;
        }
        if ($email = $rating->getEmail()) {
            if (!$this->helper->validateEmailAddress($email)) {
                $flag = false;
            }
        }
        return $flag;
    }
}
