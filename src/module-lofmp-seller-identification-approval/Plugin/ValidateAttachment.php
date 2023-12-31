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
 * @package    Lofmp_SellerIdentificationApproval
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lofmp\SellerIdentificationApproval\Plugin;

use Lof\MarketPlace\Controller\Seller\CreatePost;
use Lofmp\SellerIdentificationApproval\Helper\Data;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseFactory;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Message\ManagerInterface as MessageManagerInterface;
use Magento\Framework\UrlInterface;
use Magento\Customer\Model\Session;

/**
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class ValidateAttachment
{
    /**
     * @var Data
     */
    private $helper;

    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @var MessageManagerInterface
     */
    private $messageManager;

    /**
     * @var UrlInterface
     */
    private $url;

    /**
     * @var ResponseFactory
     */
    private $responseFactory;

    /**
     * @var RedirectFactory
     */
    private $resultRedirectFactory;

    /**
     * @var Session
     */
    protected $customerSession;

    /**
     * SaveAttachment constructor.
     * @param UrlInterface $url
     * @param Data $helper
     * @param DataPersistorInterface $dataPersistor
     * @param ResponseFactory $responseFactory
     * @param MessageManagerInterface $messageManager
     * @param RedirectFactory $redirectFactory
     * @param Session $customerSession
     */
    public function __construct(
        UrlInterface $url,
        Data $helper,
        DataPersistorInterface $dataPersistor,
        ResponseFactory $responseFactory,
        MessageManagerInterface $messageManager,
        RedirectFactory $redirectFactory,
        Session $customerSession
    ) {
        $this->url = $url;
        $this->helper = $helper;
        $this->dataPersistor = $dataPersistor;
        $this->responseFactory = $responseFactory;
        $this->messageManager = $messageManager;
        $this->resultRedirectFactory = $redirectFactory;
        $this->customerSession = $customerSession;
    }

    /**
     * @param CreatePost $subject
     * @param callable $proceed
     * @param RequestInterface $request
     * @return
     */
    public function aroundDispatch(CreatePost $subject, callable $proceed, RequestInterface $request)
    {
        $isAllowForBecome = (bool)$this->helper->getConfig("general/show_when_becomeseller");
        if ($this->customerSession->isLoggedIn() && $isAllowForBecome) {
            $request = $subject->getRequest();
            $data = $request->getPost();
            $filesArray = (array)$request->getFiles();
            $isUploaded = false;
            foreach ($filesArray as $value) {
                if (is_array($value) && count($value) > 1) {
                    $isUploaded = true;
                }
            }

            if ($this->helper->isRequire() && !$isUploaded) {
                $this->messageManager->addErrorMessage(__(
                    'Become Seller: Please upload some personal info files to verify your identity.'
                ));
                $this->dataPersistor->set('seller-form-validate', $data);
                $resultRedirect = $this->resultRedirectFactory->create();
                $resultRedirect->setPath('lofmarketplace/seller/create/');
                return $resultRedirect;
            }
        }
        return $proceed($request);
    }
}
