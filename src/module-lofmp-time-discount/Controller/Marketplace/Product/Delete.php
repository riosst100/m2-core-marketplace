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
 * @package    Lofmp_TimeDiscount
 * @copyright  Copyright (c) 2017 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */
namespace Lofmp\TimeDiscount\Controller\Marketplace\Product;
use Magento\Framework\App\Action\Action;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Lofmp\TimeDiscount\Model\ProductFactory;
use Magento\Customer\Model\Url;
use Magento\Framework\App\RequestInterface;
class Delete extends Action
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;
    /**
     * @var Lofmp\TimeDiscount\Model\Product
     */
    protected $_mpproductModel;
    /**
     * @var Magento\Customer\Model\Url
     */
    protected $_customerUrl;

    /**
     * @param Context           $context
     * @param Session           $customerSession
     * @param TimeDiscountFactory $mpproductFactory
     * @param Url               $customerUrl
     */

    public function __construct(
        Context $context,
        Session $customerSession,
        ProductFactory $mpproductFactory,
        Url $customerUrl
    ) {
        parent::__construct($context);
        $this->_customerSession = $customerSession;
        $this->_mpproductModel = $mpproductFactory;
        $this->_customerUrl = $customerUrl;
    }

    /**
     * Retrieve customer session object.
     *
     * @return \Magento\Customer\Model\Session
     */
    protected function _getSession()
    {
        return $this->_customerSession;
    }

    /**
     * Check customer authentication.
     *
     * @param RequestInterface $request
     *
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function dispatch(RequestInterface $request)
    {
        $urlModel = $this->_customerUrl;
        $loginUrl = $urlModel->getLoginUrl();
        if (!$this->_customerSession->authenticate($loginUrl)) {
            $this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);
        }
        return parent::dispatch($request);
    }

    /**
     * Default Product rate
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        try {
            $fields = $this->getRequest()->getParams();
            if (!empty($fields)) {
                $productModel = $this->_mpproductModel->create()
                    ->load($fields['id']);
                if (!empty($productModel)) {
                    $productModel->delete();
                    $this->messageManager->addSuccess(__('Product lofmptimediscount is successfully Deleted!'));
                    return $resultRedirect->setPath('lofmptimediscount/product/index');
                } else {
                    $this->messageManager->addError(__('No record Found!'));
                    return $resultRedirect->setPath('lofmptimediscount/product/index');
                }
            } else {
                $this->messageManager->addSuccess(__('Please try again!'));
                return $resultRedirect->setPath('lofmptimediscount/product/index');
            }
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
            return $resultRedirect->setPath('lofmptimediscount/product/index');
        }
    }
}
