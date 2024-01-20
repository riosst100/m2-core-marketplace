<?php
declare(strict_types=1);

namespace CoreMarketplace\MarketplaceGraphQl\Model\Resolver;

use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Psr\Log\LoggerInterface;
use Lof\MarketPlace\Model\ResourceModel\Seller\CollectionFactory as SellerCollection;

/**
 * Customer is_seller field resolver
 */
class IsCustomerSeller implements ResolverInterface
{
    /**
     * @var SellerCollection
     */
    private SellerCollection $sellerCollectionFactory;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @param SellerCollection $sellerCollectionFactory
     * @param LoggerInterface|null $logger
     */
    public function __construct(
        SellerCollection $sellerCollectionFactory,
        LoggerInterface $logger = null
    ) {
        $this->sellerCollectionFactory = $sellerCollectionFactory;
        $this->logger = $logger ?? ObjectManager::getInstance()->get(LoggerInterface::class);
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
        if (!isset($value['model'])) {
            throw new LocalizedException(__('"model" value should be specified'));
        }

        /** @var CustomerInterface $customer */
        $customer = $value['model'];
        $customerId = (int)$customer->getId();

        return $this->isSeller($customerId);
    }

    /**
     * Get customer seller status
     *
     * @param int $customerId
     * @return bool
     */
    public function isSeller(int $customerId): bool
    {
        $sellerData = $this->sellerCollectionFactory->create()
        ->addFieldToFilter('customer_id', $customerId);
        if ($sellerData->getSize()) {
            return true;
        }

        return false;
    }
}
