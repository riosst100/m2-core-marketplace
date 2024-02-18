<?php

namespace CoreMarketplace\MarketplaceGraphQl\Model\Resolver;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Lof\MarketPlace\Model\ResourceModel\Seller\CollectionFactory as SellerCollection;
use Magento\Framework\GraphQl\Query\Resolver\Argument\SearchCriteria\Builder as SearchCriteriaBuilder;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Lof\MarketPlace\Api\SellersFrontendRepositoryInterface;
use Lof\MarketPlace\Api\SellerProductsRepositoryInterface;
use Lof\MarketPlace\Api\SellersRepositoryInterface;
use Lof\MarketPlace\Helper\Data;

class SellerByUrl extends \Lof\MarketplaceGraphQl\Model\Resolver\SellerByUrl
{
    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var ProductRepositoryInterface
     */
    protected $_productRepository;

    /**
     * @var int
     */
    protected $_labelFlag;
    /**
     * @var SellersFrontendRepositoryInterface
     */
    protected $_sellerRepository;

    /**
     * @var SellerProductsRepositoryInterface
     */
    protected $_productSeller;

    /**
     * @var SellersRepositoryInterface
     */
    protected $_sellerManagementRepository;

    /**
     * @var SellerCollection
     */
    protected $sellerCollectionFactory;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * AbstractSellerQuery constructor.
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param SellersFrontendRepositoryInterface $seller
     * @param SellerProductsRepositoryInterface $productSeller
     * @param ProductRepositoryInterface $productRepository
     * @param SellersRepositoryInterface $sellerManagementRepository
     * @param SellerCollection $sellerCollectionFactory
     * @param Data $helper
     */
    public function __construct(
        SearchCriteriaBuilder $searchCriteriaBuilder,
        SellersFrontendRepositoryInterface $seller,
        SellerProductsRepositoryInterface $productSeller,
        ProductRepositoryInterface $productRepository,
        SellersRepositoryInterface $sellerManagementRepository,
        SellerCollection $sellerCollectionFactory,
        Data $helper
    ) {
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->_sellerRepository = $seller;
        $this->_productSeller = $productSeller;
        $this->_productRepository = $productRepository;
        $this->_sellerManagementRepository = $sellerManagementRepository;
        $this->sellerCollectionFactory = $sellerCollectionFactory;
        $this->helper = $helper;

        parent::__construct(
            $searchCriteriaBuilder,
            $seller,
            $productSeller,
            $productRepository,
            $sellerManagementRepository
        );
    }

    /**
     * @inheritDoc
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        $this->_labelFlag = 1;
        if (!isset($args['seller_url']) || (isset($args['seller_url']) && !$args['seller_url'])) {
            throw new GraphQlInputException(__('seller_url is required.'));
        }

        $isGetProducts = isset($args['get_products']) ? (bool)$args['get_products'] : false;
        $isGetOtherInfo = isset($args['get_other_info']) ? (bool)$args['get_other_info'] : false;
        $sellerData = $this->_sellerRepository->getByUrl($args['seller_url'], $isGetOtherInfo, $isGetProducts);
        $data = $sellerData ? $sellerData->__toArray() : [];

        $data["model"] = $sellerData;

        $sellerId = $sellerData ? $sellerData->getId() : null;
        if ($sellerId) {
            $sellerColl = $this->sellerCollectionFactory->create()
            ->addFieldToFilter('seller_id', ['eq' => $sellerId]);
            if ($sellerColl->getSize()) {
                $seller = $sellerColl->getFirstItem();
                if ($seller) {
                    $data = $seller->getData();
                    $data['store_id'] = $sellerData->getStoreId();
                    $data['image'] = $sellerData->getImage();
                    $data['thumbnail'] = $sellerData->getThumbnail();
                    $data['model'] = $seller;
                    $data['creation_time'] = date("F Y", strtotime($sellerData->getCreationTime()));

                    $shipTo = $seller->getShipTo() ? explode(",", $seller->getShipTo()) : ['domestic'];
                    if ($shipTo) {
                        if (count($shipTo) > 1) {
                            $data['ship_to'] = __('Domestic & International');
                        } else {
                            if ($shipTo[0] == "domestic") {
                                $data['ship_to'] = __('Domestic');
                            } else {
                                $data['ship_to'] = __('International');
                            }
                        }
                    }

                    $country = [];

                    $shipToCountry = $seller->getShipToCountry() ? explode(",", $seller->getShipToCountry()) : [];
                    if ($shipToCountry) {
                        foreach($shipToCountry as $countryCode) {
                            $country[] = $this->helper->getCountryName($countryCode);
                        }
                    }

                    if ($country) {
                        $data['ship_to_country'] = implode(", ", $country);
                    } else {
                        $data['ship_to_country'] = null;
                    }
                }
            }
        }

        return $data;
    }
}
