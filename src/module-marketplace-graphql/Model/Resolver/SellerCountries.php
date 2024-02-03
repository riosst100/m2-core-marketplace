<?php
declare(strict_types=1);

namespace CoreMarketplace\MarketplaceGraphQl\Model\Resolver;

use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Directory\Api\CountryInformationAcquirerInterface;
use Magento\Directory\Api\Data\CountryInformationInterface;
use Lof\MarketPlace\Helper\Seller as SellerHelper;

/**
 * SellerCountries field resolver, used for GraphQL request processing.
 */
class SellerCountries implements ResolverInterface
{
    /**
     * @var DataObjectProcessor
     */
    private $dataProcessor;

    /**
     * @var CountryInformationAcquirerInterface
     */
    private $countryInformationAcquirer;

    /**
     * @var Data
     */
    protected $_sellerHelper;

    /**
     * @param DataObjectProcessor $dataProcessor
     * @param CountryInformationAcquirerInterface $countryInformationAcquirer
     * @param SellerHelper $sellerHelper
     */
    public function __construct(
        DataObjectProcessor $dataProcessor,
        CountryInformationAcquirerInterface $countryInformationAcquirer,
        SellerHelper $sellerHelper
    ) {
        $this->dataProcessor = $dataProcessor;
        $this->countryInformationAcquirer = $countryInformationAcquirer;
        $this->_sellerHelper = $sellerHelper->getHelperData();
    }

    /**
     * @inheritdoc
     */
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        $countries = $this->countryInformationAcquirer->getCountriesInfo();

        $availableCountries = $this->_sellerHelper->getConfig('available_countries/available_countries');
        $enableAvailableCountries = $this->_sellerHelper->getConfig('available_countries/enable_available_countries');

        $allowedSellerCountriesArr = $availableCountries ? explode(",", $availableCountries) : null;

        $output = [];
        foreach ($countries as $country) {
            if ($enableAvailableCountries && !in_array($country->getTwoLetterAbbreviation(), $allowedSellerCountriesArr)) {
                continue;
            }

            if (!empty($country->getFullNameLocale())) {
                $output[] = $this->dataProcessor->buildOutputDataArray($country, CountryInformationInterface::class);
            }
        }

        return $output;
    }
}
