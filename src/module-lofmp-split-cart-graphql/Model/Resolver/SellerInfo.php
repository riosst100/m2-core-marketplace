<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Lofmp\SplitCartGraphQl\Model\Resolver;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Exception\GraphQlNoSuchEntityException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Quote\Api\CartManagementInterface;
use Magento\Quote\Model\Quote\Item;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Lofmp\SplitCart\Api\QuoteRepositoryInterface;
use Lofmp\SplitCart\Helper\ConfigData;
use Lof\MarketPlace\Model\ResourceModel\Seller\CollectionFactory as SellerCollectionFactory;
use Lof\MarketPlace\Model\Seller;

/**
 * SellerInfo data reslover
 */
class SellerInfo implements ResolverInterface
{

    /**
     * @var QuoteRepositoryInterface
     */
    protected $splitQuoteRepository;

    /**
     * @var ConfigData
     */
    protected $dataHelper;

    /**
     * @var SellerCollectionFactory
     */
    private $sellerCollectionFactory;

    /**
     * @var ConfigData
     */
    private $moduleConfig;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var mixed|array
     */
    private $sellers = [];

    /**
     * @param QuoteRepositoryInterface $splitQuoteRepository
     * @param ConfigData $data
     * @param SellerCollectionFactory $sellerCollectionFactory
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(
        QuoteRepositoryInterface $splitQuoteRepository,
        ConfigData $data,
        SellerCollectionFactory $sellerCollectionFactory,
        ProductRepositoryInterface $productRepository
    ) {
        $this->splitQuoteRepository = $splitQuoteRepository;
        $this->dataHelper = $data;
        $this->sellerCollectionFactory = $sellerCollectionFactory;
        $this->productRepository = $productRepository;
    }

    /**
     * @inheritDoc
     */
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        if (!isset($value['model'])) {
            throw new LocalizedException(__('"model" value should be specified'));
        }
        /** @var Item $cartItem */
        $cartItem = $value['model'];
        $product = $cartItem->getProduct();
        $sellerId = 0;
        $sellerUrl = "";
        $sellerName = "";

        if ($this->dataHelper->isEnabled() && $this->dataHelper->isAllowAddSellerData() ) {
            $seller = $this->getSellerByProductSku($product->getSku());
            $sellerId = $seller["sellerId"];
            $sellerUrl = $seller["sellerUrlKey"];
            $sellerName = $seller["sellerName"];
        }
        return [
            'model' => $cartItem,
            'seller_id' => $sellerId,
            'seller_url' => $sellerUrl,
            'seller_name' => $sellerName
        ];
    }

    /**
     * Get seller data info
     * @param string $sku
     * @return mixed
     */
    protected function getSellerByProductSku($sku)
    {
        $sellerId = 0;
        $sellerUrlKey = "";
        $sellerName = "";
        $product = $this->productRepository->get($sku);
        if ($product && $product->getSellerId()) {
            $seller = $this->getSellerById($product->getSellerId());
            if ($seller && $seller->getId()) {
                $sellerId = $product->getSellerId();
                $sellerName = $seller->getName();
                $sellerUrlKey = $seller->getUrlKey();
            }
        }
        return [
            "sellerId" => $sellerId,
            "sellerUrlKey" => $sellerUrlKey,
            "sellerName" => $sellerName
        ];
    }

    /**
     * get seller by seller id
     *
     * @param int $sellerId
     * @return \Lof\MarketPlace\Model\Seller
     */
    protected function getSellerById($sellerId)
    {
        if (!isset($this->sellers[$sellerId])) {
            $this->sellers[$sellerId] = $this->sellerCollectionFactory->create()
                                        ->addFieldToFilter('seller_id', $sellerId)
                                        ->addFieldToFilter("status", Seller::STATUS_ENABLED)
                                        ->getFirstItem();
        }

        return $this->sellers[$sellerId];
    }
}
