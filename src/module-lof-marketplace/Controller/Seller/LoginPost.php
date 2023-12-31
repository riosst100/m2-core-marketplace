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
use Magento\Customer\Model\Session;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Model\Url as CustomerUrl;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\Exception\EmailNotConfirmedException;
use Magento\Framework\Exception\AuthenticationException;
use Magento\Framework\Data\Form\FormKey\Validator;

class LoginPost extends \Magento\Customer\Controller\AbstractAccount
{
    /**
     * @var AccountManagementInterface
     */
    protected $customerAccountManagement;

    /**
     * @var Validator
     */
    protected $formKeyValidator;

    /**
     * @var Session
     */
    protected $session;
    /**
     * @var TypeListInterface
     */
    private $_cacheTypeList;
    /**
     * @var CustomerUrl
     */
    private $customerUrl;

    /**
     * LoginPost constructor.
     *
     * @param Context $context
     * @param Session $customerSession
     * @param AccountManagementInterface $customerAccountManagement
     * @param TypeListInterface $cacheTypeList
     * @param CustomerUrl $customerHelperData
     * @param Validator $formKeyValidator
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        AccountManagementInterface $customerAccountManagement,
        TypeListInterface $cacheTypeList,
        CustomerUrl $customerHelperData,
        Validator $formKeyValidator
    ) {
        $this->session = $customerSession;
        $this->_cacheTypeList = $cacheTypeList;
        $this->customerAccountManagement = $customerAccountManagement;
        $this->customerUrl = $customerHelperData;
        $this->formKeyValidator = $formKeyValidator;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        if ($this->session->isLoggedIn()) {
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('*/*/');
            return $resultRedirect;
        }
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($this->getRequest()->isPost()) {
            $login = $this->getRequest()->getPost('login');
            if (!empty($login ['username']) && !empty($login ['password'])) {
                try {
                    $customer = $this->customerAccountManagement
                        ->authenticate($login ['username'], $login ['password']);
                    $this->session->setCustomerDataAsLoggedIn($customer);
                    $this->session->regenerateId();
                    if ($url = $this->session->getBeforeAuthUrl()) {
                        $resultRedirect->setPath($url);
                    } else {
                        $resultRedirect->setPath('marketplace/catalog/dashboard');
                    }
                    $this->_cacheTypeList->cleanType('full_page');
                    $this->_cacheTypeList->cleanType('block_html');
                    $this->_cacheTypeList->cleanType('config');
                    return $resultRedirect;
                } catch (EmailNotConfirmedException $e) {
                    $value = $this->customerUrl->getEmailConfirmationUrl($login ['username']);
                    $message = __('This account is not confirmed.'
                        . ' <a href="%1">Click here</a> to resend confirmation email.', $value);
                    $this->messageManager->addErrorMessage($message);
                    $this->session->setUsername($login ['username']);
                } catch (AuthenticationException $e) {
                    $message = __('Invalid login or password.');
                    $this->messageManager->addErrorMessage($message);
                    $this->session->setUsername($login ['username']);
                } catch (\Exception $e) {
                    $this->messageManager->addErrorMessage(__('Invalid login or password.'));
                }
            } else {
                $this->messageManager->addErrorMessage(__('A login and a password are required.'));
            }
        }

        $resultRedirect->setPath('lofmarketplace/seller/login');
        return $resultRedirect;
    }
}
