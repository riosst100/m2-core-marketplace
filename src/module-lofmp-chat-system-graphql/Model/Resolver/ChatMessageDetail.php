<?php

declare(strict_types=1);

namespace Lof\ChatSystemGraphQl\Model\Resolver;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlAuthorizationException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\GraphQl\Model\Query\ContextInterface;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Lof\ChatSystem\Model\ResourceModel\ChatMessage\CollectionFactory;
use Lof\MarketPlace\Model\ResourceModel\Seller\CollectionFactory as SellerCollectionFactory;

class ChatMessageDetail implements ResolverInterface
{

    /**
     * @var CollectionFactory
     */
    protected $chatMessageCollectionFactory;

    /**
     * @var SellerCollectionFactory
     */
    protected $sellerCollectionFactory;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param CollectionFactory $chatMessageCollectionFactory
     * @param SellerCollectionFactory $sellerCollectionFactory
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        CollectionFactory $chatMessageCollectionFactory,
        SellerCollectionFactory $sellerCollectionFactory
    ) {
        $this->request = $context->getRequest();
        $this->chatMessageCollectionFactory = $chatMessageCollectionFactory;
        $this->sellerCollectionFactory = $sellerCollectionFactory;
    }

    /**
     * Resolve product review rating values
     *
     * @param Field $field
     * @param ContextInterface $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     * @return array|Value|mixed
     *
     * @throws GraphQlInputException
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    )  {

        if (!isset($value['chat_id'])) {
            throw new GraphQlInputException(__('Value must contain "chat_id" property.'));
        }

        if ($args['currentPage'] < 1) {
            throw new GraphQlInputException(__('currentPage value must be greater than 0.'));
        }
        if ($args['pageSize'] < 1) {
            throw new GraphQlInputException(__('pageSize value must be greater than 0.'));
        }

        $collection = $this->detailCollectionFactory->create()
                        ->addFieldToFilter('chat_id', (int)$value['chat_id'])
                        ->setPageSize($args['pageSize'])
                        ->setCurPage($args['currentPage']);

        $totalPages = $args['pageSize'] ? ((int)ceil($collection->getSize() / $args['pageSize'])) : 0;

        $items = [];

        $sellerName = __("Bot");
        if (isset($value["seller_id"]) && !empty($value["seller_id"])) {
            /** @var \Lof\MarketPlace\Model\Seller|null*/
            $seller = $this->getSeller((int)$value["seller_id"]);
            $sellerName = $seller ? $seller->getName() : $sellerName;
        }

        foreach ($collection->getItems() as $messegedetail) {
            $items[] = [
                'body_msg' => $messegedetail->getBodyMsg(),
                'customer_name' => $messegedetail->getCustomerName(),
                'customer_email' => $messegedetail->getCustomerEmail(),
                'is_read' => $messegedetail->getIsRead(),
                'customer_id' => $messegedetail->getCustomerId(),
                'seller_id' => $messegedetail->getSellerId(),
                'sender_name' => $messegedetail->getSellerId() ? $sellerName : $messegedetail->getCustomerName(),
                'created_at' => $messegedetail->getCreatedAt()
            ];
        }

        return [
            'total_count' => $collection->getSize(),
            'items'       => $items,
            'page_info' => [
                'page_size' => $args['pageSize'],
                'current_page' => $args['currentPage'],
                'total_pages' => $totalPages
            ]
        ];
    }

    /**
     * Get seller by id
     *
     * @param int $sellerId
     * @return \Lof\MarketPlace\Model\Seller|null
     */
    protected function getSeller($sellerId)
    {
        $seller = $this->sellerCollectionFactory->create()
                            ->addFieldToFilter("seller_id", (int)$sellerId)
                            ->getFirstItem();
        return $seller && $seller->getId() ? $seller : null;
    }
}
