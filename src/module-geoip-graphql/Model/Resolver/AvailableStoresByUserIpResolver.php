<?php
declare(strict_types=1);

namespace CoreMarketplace\GeoIpGraphQl\Model\Resolver;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Store\Model\StoreManagerInterface;
use Magefan\GeoIp\Model\IpToCountryRepository;
use Magento\StoreGraphQl\Model\Resolver\Store\StoreConfigDataProvider;

class AvailableStoresByUserIpResolver implements ResolverInterface
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
        $userIpAddress = $args['ip_address'] ?: null;
        if (str_contains($currentStoreCode, 'default') && $userIpAddress) {
            $countryCode = $this->ipToCountryRepository->getCountryCode($userIpAddress);
            if ($countryCode) {
                return $this->storeConfigDataProvider->getAvailableStoreConfigByCountryCode(
                    $countryCode,
                    null
                );
            }
        }

        return [];
    }
}
