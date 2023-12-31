<?php
/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * http://landofcoder.com/license
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Landofcoder
 * @package    Lof_SocialLogin
 *
 * @copyright  Copyright (c) 2016 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */
namespace Lof\SocialLogin\Controller\Wordpress;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Model\Url as CustomerUrl;
use Magento\Customer\Model\Session;
use Magento\Customer\Model\CustomerFactory;
use Symfony\Component\Config\Definition\Exception\Exception;
use Lof\SocialLogin\Model\Wordpress as Wordpress;
use Lof\SocialLogin\Helper\Wordpress\Data as SocialHelper;
use Lof\SocialLogin\Model\SocialFactory as WordpressModelFactory;
use Lof\SocialLogin\Model\ResourceModel\Social\CollectionFactory as WordpressCollectionFactory;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Visitor;

class Callback extends Action
{
    const SOCIAL_TYPE = 'wordpress';
    protected $resultPageFactory;
    protected $wordpress;
    protected $socialHelper;
    protected $accountManagement;
    protected $customerUrl;
    protected $session;
    protected $wordpressCustomerCollectionFactory;
    protected $wordpressCustomerModelFactory;
    protected $customerFactory;

    /**
     * @var \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory
     */
    private $cookieMetadataFactory;

    /**
     * @var \Magento\Framework\Stdlib\Cookie\PhpCookieManager
     */
    private $cookieMetadataManager;

    public function __construct(
        Context $context,
        Wordpress $wordpress,
        StoreManagerInterface $storeManager,
        SocialHelper $socialHelper,
        PageFactory $resultPageFactory,
        AccountManagementInterface $accountManagement,
        CustomerUrl $customerUrl,
        WordpressModelFactory $wordpressCustomerModelFactory,
        WordpressCollectionFactory $wordpressCustomerCollectionFactory,
        CustomerFactory $customerFactory,
        CustomerRepositoryInterface $customerRepository,
        Session $customerSession,
        Visitor $visitor
    ) {
        parent::__construct($context);
        $this->wordpress                          = $wordpress;
        $this->storeManager                       = $storeManager;
        $this->socialHelper                       = $socialHelper;
        $this->resultPageFactory                  = $resultPageFactory;
        $this->accountManagement                  = $accountManagement;
        $this->customerUrl                        = $customerUrl;
        $this->session                            = $customerSession;
        $this->wordpressCustomerModelFactory      = $wordpressCustomerModelFactory;
        $this->wordpressCustomerCollectionFactory = $wordpressCustomerCollectionFactory;
        $this->customerFactory                    = $customerFactory;
        $this->customerRepository                 = $customerRepository;
        $this->visitor                            = $visitor;
    }

    public function execute()
    {
        $client_id = $this->socialHelper->getClientId();
        $redirect_uri = $this->socialHelper->getAuthUrl();
        $client_secret = $this->socialHelper->getClientSecret();

        $code = $this->getRequest()->getParam('code');
        $state = $this->getRequest()->getParam('state');
        if (! isset($code)) {
            die('Warning! Visitor may have declined access or navigated to the page without being redirected.');
        }
        if ($_GET[ 'state' ] !== $_SESSION[ 'wpcc_state_wordpress' ]) {
            die('Warning! State mismatch. Authentication attempt may have been compromised.');
        }
        $request = [
            'client_id'     => $client_id,
            'redirect_uri'  => $redirect_uri,
            'client_secret' => $client_secret,
            'code'          => $code,
            'grant_type'    => 'authorization_code'
        ];
        $curl = curl_init('https://public-api.wordpress.com/oauth2/token');
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $request);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $auth = curl_exec($curl);
        if (curl_errno($curl)) {
            echo curl_error($curl);
        }
        $secret = json_decode($auth);
        $access_token = $secret->access_token;
        $curl = curl_init('https://public-api.wordpress.com/rest/v1/me/');
        curl_setopt($curl, CURLOPT_HTTPHEADER, [ 'Authorization: Bearer ' . $access_token ]);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $dataUser = json_decode(curl_exec($curl));
        $data = [];
        $redirect = $this->socialHelper->getConfig(('general/redirect_page'));
        if(empty($redirect)){
            $link_redirect = "window.opener.location.reload();";
        }else{
            $link_redirect = "window.opener.location= '".$redirect."';";
        };
        $user_id = $dataUser && isset($dataUser->ID)?$dataUser->ID:0;
        $customerId = $this->getCustomerIdByWordpressId($user_id);
        if ($customerId) {
            $customer = $this->customerRepository->getById($customerId);
            $customer1 = $this->customerFactory->create()->load($customerId);
            if ($customer->getConfirmation()) {
                try {
                    $customer1->setConfirmation(null);
                    $customer1->save();
                } catch (\Exception $e) {
                    $this->messageManager->addError(__('We can\'t process your request right now. Sorry, that\'s all we know.'));
                }
            }
            if ($this->getCookieManager()->getCookie('mage-cache-sessid')) {
                $metadata = $this->getCookieMetadataFactory()->createCookieMetadata();
                $metadata->setPath('/');
                $this->getCookieManager()->deleteCookie('mage-cache-sessid', $metadata);
            }
            $this->session->setCustomerDataAsLoggedIn($customer);
            $this->messageManager->addSuccess(__('Login successful.'));
            $this->session->regenerateId(); 
            $this->_eventManager->dispatch('customer_data_object_login', ['customer' => $customer]);
            $this->_eventManager->dispatch('customer_login', ['customer' => $customer1]);

            /** VISITOR */
            $visitor = $this->visitor;
            $visitor->setData($this->session->getVisitorData());
            $visitor->setLastVisitAt((new \DateTime())->format(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT));
            $visitor->setSessionId($this->session->getSessionId());
            $visitor->save();
            $this->_eventManager->dispatch('visitor_init', ['visitor' => $visitor]);
            $this->_eventManager->dispatch('visitor_activity_save', ['visitor' => $visitor]);
        }
        if ($dataUser && ($user_id || isset($dataUser->email))) {
            if (isset($dataUser->email)) {
                $data['id'] = $user_id;
                $data['email']= $dataUser->email;
                $data['password'] =  $this->socialHelper->createPassword();
                $data['password_confirmation'] = $data['password'];
                $data['first_name'] = $dataUser->display_name;
                $data['last_name']  = '.';
            } else {
                $data['id'] = $user_id;
                $data['email']= $dataUser->username.'@wordpress.com';
                $data['password'] =  $this->socialHelper->createPassword();
                $data['password_confirmation'] = $data['password'];
                $data['first_name'] = $dataUser->display_name;
                $data['last_name']  = '.';
            }
            $store_id   = $this->storeManager->getStore()->getStoreId();
            $website_id = $this->storeManager->getStore()->getWebsiteId();
            $customer   = $this->socialHelper->getCustomerByEmail($data['email'], $website_id);
            if (!$customer || !$customer->getId()) {
                $customer = $this->socialHelper->createCustomerMultiWebsite($data, $website_id, $store_id);
                if ($this->socialHelper->sendPassword()) {
                    try {
                        $this->accountManagement->sendPasswordReminderEmail($customer);
                    } catch (Exception $e) {
                        $this->messageManager->addError(__('We can\'t process your request right now. Sorry, that\'s all we know.'));
                    }
                }
            }
            $this->setAuthorCustomer($data['id'], $customer->getId(), $dataUser->username);
            $confirmationStatus = $this->accountManagement->getConfirmationStatus($customer->getId());
            if ($confirmationStatus === AccountManagementInterface::ACCOUNT_CONFIRMATION_REQUIRED) {
                $this->customerUrl->getEmailConfirmationUrl($customer->getEmail());
                                // @codingStandardsIgnoreStart
                $this->messageManager->addSuccess(
                    __( 
                        'You must confirm your account. Please check your email for the confirmation link or <a href="%1">click here</a> for a new link.',
                        $email
                    )
                );
            } else {
                $this->session->setCustomerDataAsLoggedIn($customer);
                $this->messageManager->addSuccess(__('Login successful.'));
                $this->session->regenerateId();
            }
        }
        echo "<script type=\"text/javascript\">window.close();".$link_redirect."</script>";
        exit;
    }

    public function getCustomerIdByWordpressId($wordpressId)
    {
        $customer = $this->wordpressCustomerCollectionFactory->create();
        $dataUser     = $customer
        ->addFieldToFilter('social_id', $wordpressId)
        ->addFieldToFilter('type', self::SOCIAL_TYPE)
        ->getFirstItem();
        if ($dataUser && $dataUser->getId()) {
            return $dataUser->getCustomerId();
        } else {
            return null;
        }
    }

    public function setAuthorCustomer($wordpressId, $customerId, $username)
    {
        $wordpressCustomer = $this->wordpressCustomerModelFactory->create();
        $wordpressCustomer->setData('social_id', $wordpressId);
        $wordpressCustomer->setData('username', $username);
        $wordpressCustomer->setData('customer_id', $customerId);
        $wordpressCustomer->setData('type', self::SOCIAL_TYPE);
        $wordpressCustomer->setData('is_send_password_email', $this->socialHelper->sendPassword());
        try {
            $wordpressCustomer->save();
        } catch (Exception $e) {
            $this->messageManager->addError(__('We can\'t process your request right now. Sorry, that\'s all we know.'));
        }
        return;
    }

    /**
     * Retrieve cookie manager
     *
     * @deprecated
     * @return \Magento\Framework\Stdlib\Cookie\PhpCookieManager
     */
    private function getCookieManager()
    {
        if (!$this->cookieMetadataManager) {
            $this->cookieMetadataManager = \Magento\Framework\App\ObjectManager::getInstance()->get(
                \Magento\Framework\Stdlib\Cookie\PhpCookieManager::class
            );
        }
        return $this->cookieMetadataManager;
    }

    /**
     * Retrieve cookie metadata factory
     *
     * @deprecated
     * @return \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory
     */
    private function getCookieMetadataFactory()
    {
        if (!$this->cookieMetadataFactory) {
            $this->cookieMetadataFactory = \Magento\Framework\App\ObjectManager::getInstance()->get(
                \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory::class
            );
        }
        return $this->cookieMetadataFactory;
    }
}
