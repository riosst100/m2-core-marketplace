<?php
/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * http://landofcoder.com/license
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Landofcoder
 * @package    Lofmp_Faq
 * @copyright  Copyright (c) 2017 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */
namespace Lofmp\Faq\Controller\Question;

use Magento\Framework\App\Action\Context;
use Magento\Store\Model\StoreManager;
use Magento\Framework\Controller\Result\JsonFactory;

class Save extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $_resultJsonFactory;

    protected $questionFactory;

    protected $sellerProductFactory;

    protected $_storeManager;

    /**
     * @var \Lof\MarketPlace\Model\SellerFactory
     */
    protected $sellerFactory;

    /**
     * @param \Magento\Framework\App\Action\Context
     * @param \Magento\Store\Model\StoreManager
     * @param \Magento\Framework\Controller\Result\JsonFactory
     * @param \Lofmp\Faq\Model\QuestionFactory $questionFactory
     * @param \Lof\MarketPlace\Model\SellerProductFactory $sellerProductFactory
     * @param \Lof\MarketPlace\Model\SellerFactory $sellerFactory
     */
    public function __construct(
        Context $context,
        StoreManager $storeManager,
        JsonFactory $resultJsonFactory,
        \Lofmp\Faq\Model\QuestionFactory $questionFactory,
        \Lof\MarketPlace\Model\SellerProductFactory $sellerProductFactory,
        \Lof\MarketPlace\Model\SellerFactory $sellerFactory
    ) {
        $this->_resultJsonFactory = $resultJsonFactory;
        $this->questionFactory = $questionFactory;
        $this->sellerProductFactory = $sellerProductFactory;
        $this->_storeManager = $storeManager;
        $this->sellerFactory = $sellerFactory;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        $response = $this->_resultJsonFactory->create();
        $questionModel = $this->questionFactory->create();
        $sellerProductModel = $this->sellerProductFactory->create();
        $sellerModel = $this->sellerFactory->create();
        if ($this->getRequest()->isAjax())
        {
            $request = $this->getRequest()->getPostValue();
            $productId = isset($request['product_id']) ? (int)$request['product_id'] : 0;
            $sellerId = isset($request['seller_id']) ? (int)$request['seller_id'] : 0;
            if ($productId && !$sellerId) {
                $sellerProduct = $sellerProductModel->load($productId, "product_id");
                $sellerId = $sellerProduct?$sellerProduct->getSellerId():0;
            }
            $data = [];
            $data['store_id'] = $this->_storeManager->getStore()->getId();
            $data["customer_name"] = isset($request["customer_name"]) ? $request["customer_name"] : "";
            $data["customer_email"] = isset($request["customer_email"]) ? $request["customer_email"] : "";
            $data["category_id"] = isset($request["category_id"]) ? (int)$request["category_id"] : "";
            $data["title"] = isset($request["title"]) ? strip_tags($request["title"]): "";

            if ((!$productId && !$sellerId) || !$data["customer_email"] || !$data["title"] ) {
                return $response->setData(['message' => __('Error: missing required data!')]);
            } else {

                if($sellerId){
                    $seller = $sellerModel->load($sellerId);
                    if ($seller && $seller->getId() && $seller->getStatus() == 1) {

                        $data['seller_id'] = $sellerId;
                        $data['product_id'] = $productId;
                        $questionModel->setData($data);
                        try {
                            $questionModel->save();
                            return $response->setData(['message' => 'OK']);
                        } catch (\Exception $e) {
                            return $response->setData(['message' => __('Error: %1', $e->getMessage())]);
                        }
                    } else {
                        return $response->setData(['message' => __('Error: wrong Seller Information!')]);
                    }
                } else {
                    return $response->setData(['message' => __('Error: missing Seller Info!')]);
                }
            }
        }
    }
}
