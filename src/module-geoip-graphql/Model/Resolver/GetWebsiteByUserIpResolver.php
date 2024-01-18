<?php
declare(strict_types=1);

namespace CoreMarketplace\GeoIpGraphQl\Model\Resolver;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Store\Model\StoreManagerInterface;
use Magefan\GeoIp\Model\IpToCountryRepository;
use CoreMarketplace\GeoIpGraphQl\Model\Resolver\Store\StoreConfigDataProvider;

class GetWebsiteByUserIpResolver implements ResolverInterface
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var IpToCountryRepository
     */
    private $ipToCountryRepository;

    /**
     * @var StoreConfigDataProvider
     */
    private $storeConfigDataProvider;

    /**
     * @param StoreManagerInterface $storeManager
     * @param IpToCountryRepository $ipToCountryRepository
     * @param StoreConfigDataProvider $storeConfigsDataProvider
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        IpToCountryRepository $ipToCountryRepository,
        StoreConfigDataProvider $storeConfigsDataProvider
    ) {
        $this->storeManager = $storeManager;
        $this->ipToCountryRepository = $ipToCountryRepository;
        $this->storeConfigDataProvider = $storeConfigsDataProvider;
    }

    /**
     * @inheritdoc
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        $currentStoreCode = $this->storeManager->getStore()->getCode();
        $userIpAddress = isset($args['ip_address']) ? $args['ip_address'] : null;
        $countryCode = $userIpAddress ? $this->ipToCountryRepository->getCountryCode($userIpAddress) : null;
        
        return $this->storeConfigDataProvider->getWebsiteByCountryCode(
            $countryCode,
            null
        );
    }
}
