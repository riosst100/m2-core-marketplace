<?php
/**
 * LandOfCoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * https://landofcoder.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   LandOfCoder
 * @package    Lofmp_Rma
 * @copyright  Copyright (c) 2021 Landofcoder (https://landofcoder.com/)
 * @license    https://landofcoder.com/LICENSE-1.0.html
 */

namespace Lofmp\Rma\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $_sellers = [];
    protected $_rma_products = [];
    protected $_countryFactory;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productFactory;

    public function __construct(
        \Magento\Framework\Api\SearchCriteriaBuilder          $searchCriteriaBuilder,
        \Magento\Framework\Api\SortOrderBuilder               $sortOrderBuilder,
        \Lofmp\Rma\Api\Repository\ConditionRepositoryInterface  $conditionRepository,
        \Lofmp\Rma\Api\Repository\ReasonRepositoryInterface     $reasonRepository,
        \Lofmp\Rma\Api\Repository\ResolutionRepositoryInterface $resolutionRepository,
        \Lofmp\Rma\Api\Repository\MessageRepositoryInterface    $messageRepository,
        \Lofmp\Rma\Api\Repository\ItemRepositoryInterface       $itemRepository,
        \Lofmp\Rma\Api\Repository\AttachmentRepositoryInterface $attachmentRepository,
        \Lofmp\Rma\Api\Repository\FieldRepositoryInterface      $fieldRepository,
        \Lofmp\Rma\Model\ResourceModel\OrderStatusHistory\CollectionFactory $historyCollectionFactory,
        \Lofmp\Rma\Model\RmaFactory $rmaFactory,
        \Lof\MarketPlace\Model\SellerFactory                           $sellerFactory,
        \Lofmp\Rma\Helper\Help                                              $helper,
        \Magento\Catalog\Model\ProductFactory                           $productFactory,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\User\Model\UserFactory $userFactory,
        \Lofmp\Rma\Api\Repository\StatusRepositoryInterface             $statusRepository,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Framework\Message\ManagerInterface          $messageManager,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Framework\App\Helper\Context                 $context,
        \Magento\Framework\Api\FilterBuilder $filterBuilder,
        \Lofmp\Rma\Model\AddressFactory  $rmaAddress,
        \Magento\Directory\Model\CountryFactory $countryFactory
    ) {
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->sortOrderBuilder      = $sortOrderBuilder;
        $this->conditionRepository   = $conditionRepository;
        $this->reasonRepository      = $reasonRepository;
        $this->resolutionRepository  = $resolutionRepository;
        $this->messageRepository     = $messageRepository;
        $this->itemRepository        = $itemRepository;
        $this->attachmentRepository  = $attachmentRepository;
        $this->fieldRepository       = $fieldRepository;
        $this->customerFactory       = $customerFactory;
        $this->userFactory           = $userFactory;
        $this->productFactory        = $productFactory;
        $this->sellerFactory         =  $sellerFactory;
        $this->statusRepository      = $statusRepository;
        $this->historyCollectionFactory = $historyCollectionFactory;
        $this->rmaFactory      = $rmaFactory;
        $this->helper                   = $helper;
        $this->localeDate              = $localeDate;
        $this->messageManager          = $messageManager;
        $this->_resource      = $resource;
        $this->context = $context;
        $this->filterBuilder = $filterBuilder;
        $this->rmaAddress = $rmaAddress;
        $this->_countryFactory = $countryFactory;
        parent::__construct($context);
    }

    /**
     * get country name by code
     *
     * @param string $countryCode
     * @return string
     */
    public function getCountryname($countryCode)
    {
        if ($countryCode) {
            try {
                $country = $this->_countryFactory->create()->loadByCode($countryCode);
                return $country->getName();
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                return $countryCode;
            }
        }
        return $countryCode;
    }

    /**
     * Get order by rma id
     *
     * @param int $rma_id
     * @return \Magento\Sales\Model\Order
     */
    public function getOrderByRmaId($rma_id)
    {
        $order_id =  $this->rmaFactory->create()->load($rma_id)->getOrderId();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $order = $objectManager->get(\Magento\Sales\Model\Order::class)->load($order_id);
        return $order;
    }

    /**
     * get rma product ids
     *
     * @param int $rma_id
     * @return mixed|array
     */
    public function getRmaProductIds($rma_id)
    {
        if (!isset($this->_rma_products[$rma_id])) {
            $searchCriteria = $this->searchCriteriaBuilder
                                    ->addFilter('rma_id', $rma_id)
                                    ->addFilter('qty_requested', 0, 'neq');
            $stack = [];
            $items = $this->itemRepository->getList($searchCriteria->create())->getItems();
            if ($items) {
                foreach ($items as $item) {
                    $stack[] = $item->getProductId();
                }
            }
            $this->_rma_products[$rma_id] = $stack;
        }
        return $this->_rma_products[$rma_id];
    }

    /**
     * Get product by id
     *
     * @param int $id
     * @return \Magento\Catalog\Model\Product
     */
    public function getProductById($id)
    {
        $product = $this->productFactory->create()->load($id);
        return $product;
    }

    /**
     * @return \Lofmp\Rma\Api\Data\ConditionInterface[]
     */
    public function getConditions()
    {
        $sortOrderSort = $this->sortOrderBuilder
            ->setField('sort_order')
            ->setDirection(\Magento\Framework\Api\SortOrder::SORT_ASC)
            ->create();

        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter('is_active', 1)
            ->addSortOrder($sortOrderSort)
        ;

        return $this->conditionRepository->getList($searchCriteria->create())->getItems();
    }

    /**
     * @return \Lofmp\Rma\Api\Data\ReasonInterface[]
     */
    public function getReasons()
    {
        $sortOrderSort = $this->sortOrderBuilder
            ->setField('sort_order')
            ->setDirection(\Magento\Framework\Api\SortOrder::SORT_ASC)
            ->create();

        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter('is_active', 1)
            ->addSortOrder($sortOrderSort)
        ;

        return $this->reasonRepository->getList($searchCriteria->create())->getItems();
    }

    /**
     * @return \Lofmp\Rma\Api\Data\ResolutionInterface[]
     */
    public function getResolutions()
    {
        $sortOrderSort = $this->sortOrderBuilder
            ->setField('sort_order')
            ->setDirection(\Magento\Framework\Api\SortOrder::SORT_ASC)
            ->create();

        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter('is_active', 1)
            ->addSortOrder($sortOrderSort)
        ;

        return $this->resolutionRepository->getList($searchCriteria->create())->getItems();
    }

    /**
     * @return \Lofmp\Rma\Api\Data\StatusInterface[]
     */
    public function getStatusList()
    {
        $sortOrderSort = $this->sortOrderBuilder
            ->setField('sort_order')
            ->setDirection(\Magento\Framework\Api\SortOrder::SORT_ASC)
            ->create();

        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter('is_active', 1)
            ->addSortOrder($sortOrderSort)
        ;

        return $this->statusRepository->getList($searchCriteria->create())->getItems();
    }

    /**
     * @return array
     */
    public function getConditionOptionArray()
    {
        $array = [];
        $conditions = $this->getConditions();
        /** @var \Lofmp\Rma\Api\Data\ConditionInterface $condition */
        foreach ($conditions as $condition) {
            $array[$condition->getId()] = $condition->getName();
        }

        return $array;
    }

    /**
     * @return array
     */
    public function getReasonOptionArray()
    {
        $array = [];
        $reasons = $this->getReasons();
        /** @var \Lofmp\Rma\Api\Data\ReasonInterface $reason */
        foreach ($reasons as $reason) {
            $array[$reason->getId()] = $reason->getName();
        }

        return $array;
    }

    /**
     * @return array
     */
    public function getResolutionOptionArray()
    {
        $array = [];
        $resolutions = $this->getResolutions();
        /** @var \Lofmp\Rma\Api\Data\ResolutionInterface $resolution */
        foreach ($resolutions as $resolution) {
            $array[$resolution->getId()] = $resolution->getName();
        }

        return $array;
    }

        /**
         * {@inheritdoc}
         */
    public function getMessages(\Lofmp\Rma\Api\Data\RmaInterface $rma, $isfrontend = false)
    {
        $order = $this->sortOrderBuilder
            ->setField('message_id')
            ->setDirection(\Magento\Framework\Data\Collection::SORT_ORDER_ASC)
            ->create();

        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter('rma_id', $rma->getId());
        if ($isfrontend) {
            $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter('internal', '0');
        }
        $searchCriteria->addSortOrder($order);

        return $this->messageRepository->getList($searchCriteria->create())->getItems();
    }
     /**
      * {@inheritdoc}
      */
    public function getItems(\Lofmp\Rma\Api\Data\RmaInterface $rma)
    {
        /*$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $filterBuilder = $objectManager->get(\Magento\Framework\Api\FilterBuilder::class);
       $filterQtyRequest = $filterBuilder->setField()
            ->setValue('0')
            ->setConditionType('neq')
            ->create();*/
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter('rma_id', $rma->getId())
            ->addFilter('qty_requested', 0, 'neq')
        ;
        return $this->itemRepository->getList($searchCriteria->create())->getItems();
    }

    public function getItemQuantityAvaiable($orderItem)
    {
        $qtyShipped = $orderItem->getData('qty_shipped');

        if ($this->helper->getConfig($store = null, 'rma/policy/return_only_shipped')) {
            $qty = $qtyShipped - $this->getQtyReturned($orderItem);
        } else {
            $qty = $orderItem->getData('qty_ordered') - $this->getQtyReturned($orderItem);
        }

        if ($qty < 0) {
            $qty = 0;
        }
        return (int)$qty;
    }

    public function getConfig($path, $store = null)
    {
        return $this->helper->getConfig($store, 'rma/'.$path);
    }

      /**
       * {@inheritdoc}
       */
    public function getQtyReturned($orderItem)
    {
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter('order_item_id', $orderItem->getData('item_id'))
        ;

        $items = $this->itemRepository->getList($searchCriteria->create())->getItems();
        $sum = 0;
        foreach ($items as $item) {
            $sum += $item->getQtyRequested();
        }

        return $sum;
    }
       /**
        * {@inheritdoc}
        */
    public function getRmaItems($orderItem, $rmaid)
    {
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter('order_item_id', $orderItem->getData('item_id'))
            ->addFilter('rma_id', $rmaid)
        ;

        $items = $this->itemRepository->getList($searchCriteria->create())->getItems();
        return $items;
    }



    public function getQtyReturnedRma($orderItem, $rmaid)
    {
        $items =  $this->getRmaItems($orderItem, $rmaid);
        $sum = 0;
        foreach ($items as $item) {
            $sum += $item->getQtyRequested();
        }

        return $sum;
    }
    public function getRmaItemData($orderItem, $rmaid)
    {
        $items =  $this->getRmaItems($orderItem, $rmaid);
         $itemData = [];
        foreach ($items as $item) {
            $itemData = $item->getData();
        }
        return $itemData;
    }
    public function getCreditMemoIds($rmaId = 0)
    {
        if ($rmaId) {
            $connection = $this->_resource->getConnection();
            $select = 'SELECT rc_credit_memo_id  FROM ' . $this->_resource->getTableName('lofmp_rma_rma_creditmemo') . ' WHERE rc_rma_id = ' .$rmaId . ' ORDER BY rc_credit_memo_id ASC';
            $this->rc_credit_memo_id  = $connection->fetchAll($select);
            $result = array_column($this->rc_credit_memo_id, 'rc_credit_memo_id');
            return $result;
        }
        return [];
    }
    public function getExchangeOrderIds($rmaId = 0)
    {
        if ($rmaId) {
            $connection = $this->_resource->getConnection();
            $select = 'SELECT re_exchange_order_id  FROM ' . $this->_resource->getTableName('lofmp_rma_rma_order') . ' WHERE re_rma_id = ' .$rmaId . ' ORDER BY re_exchange_order_id ASC';
            $this->_exorderid = $connection->fetchAll($select);

            $result = array_column($this->_exorderid, 're_exchange_order_id');
            return $result;
        }
        return [];
    }
    /**
     * {@inheritdoc}
     */
    public function getLastMessage(\Lofmp\Rma\Api\Data\RmaInterface $rma)
    {
        $messages = $this->getMessages($rma);

        return array_pop($messages);
    }
    /**
     * {@inheritdoc}
     */
    public function getAttachments($itemType, $itemId)
    {
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter('item_id', $itemId)
            ->addFilter('item_type', $itemType)
            ->create();

        return $this->attachmentRepository->getList($searchCriteria)->getItems();
    }
    /**
     * {@inheritdoc}
     */
    public function getCustomerEmail($id)
    {
        return $this->customerFactory->create()->load($id)->getEmail();
    }

    /**
     * {@inheritdoc}
     */
    public function getUserName($user_id)
    {

            return $this->userFactory->create()->load($user_id)->getName();
    }

    /**
     * {@inheritdoc}
     */
    public function getSellerName($seller_id)
    {
        if ($seller_id) {
            $seller = $this->getSellerById($seller_id);
            return $seller?$seller->getName():"";
        }
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function getSellerById($seller_id)
    {
        if ($seller_id) {
            if (!isset($this->_sellers[$seller_id])) {
                $this->_sellers[$seller_id] = $this->sellerFactory->create()->load($seller_id);
            }
            return $this->_sellers[$seller_id];
        }
        return null;
    }

    public function getSellerAddress($seller_id = 0)
    {
        if ($seller_id) {
            $seller = $this->getSellerById($seller_id);
            if ($seller->getAddress()) {
                $country_name = $this->getCountryname($seller->getCountryId());
                return $seller->getAddress().", ".$seller->getCity()." ".$seller->getPostcode().", ".$seller->getRegionId()." ".$country_name;//Address, City Postcode, State, Country
            }
        }
        return null;
    }

    /**
     * @param string $Option
     * @return array
     */
    public function getAdminOptionArray($Option = false)
    {
        $arr = $this->userFactory->create()->getCollection()->toArray();
        $result = [];
        foreach ($arr['items'] as $value) {
            $result[$value['user_id']] = $value['firstname'] . ' ' . $value['lastname'];
        }
        if ($Option) {
            $result[0] = __('-- Please Select --');
        }
        return $result;
    }
        /**
         * {@inheritdoc}
         */
    public function getFields()
    {
        $sortOrder = $this->sortOrderBuilder
            ->setField('sort_order')
            ->setDirection(\Magento\Framework\Data\Collection::SORT_ORDER_ASC)
            ->create();
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter('is_active', true)
            ->addSortOrder($sortOrder)
        ;

        return $this->fieldRepository->getList($searchCriteria->create())->getItems();
    }

    /**
     * {@inheritdoc}
     */
    public function getVisibleFields($status, $isEdit)
    {
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter('is_active', true)
            ->addSortOrder($this->sortOrderBuilder
            ->setField('sort_order')
            ->setDirection(\Magento\Framework\Data\Collection::SORT_ORDER_ASC)
            ->create())
        ;
             $searchCriteria->addFilter('is_editable_customer', true)
             ->addFilter('visible_customer_status', "%,$status,%", 'like');


        return $this->fieldRepository->getList($searchCriteria->create())->getItems();
    }

    /**
     * {@inheritdoc}
     */
    public function getInputParams($field, $staff = true, $object = false)
    {
        $value = $object ? $object->getData($field->getCode()) : '';
        switch ($field->getType()) {
            case 'checkbox':
                $value = 1;
                break;
            case 'date':
                if ($value == '0000-00-00 00:00:00') {
                    $value = time();
                }
                break;
        }

        return [
            'label'        => __($field->getName()),
            'name'         => $field->getCode(),
            'required'     => $staff ? $field->getIsRequiredStaff() : $field->IsCustomerRequired(),
            'value'        => $value,
            'checked'      => $object ? $object->getData($field->getCode()) : false,
            'values'       => $field->getValues(),
            'note'         => $field->getDescription(),
            'date_format'  => $this->localeDate->getDateFormat(\IntlDateFormatter::SHORT),
            'input_format' => \Magento\Framework\Stdlib\DateTime::DATE_INTERNAL_FORMAT,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function processPost($post, $object)
    {
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter('is_active', true)
            ->addFilter('is_editable_customer', true)
            ->addSortOrder($this->getSortOrder())
        ;

        $collection = $this->fieldRepository->getList($searchCriteria->create())->getItems();
        foreach ($collection as $field) {
            if (isset($post[$field->getCode()])) {
                $value = $post[$field->getCode()];
                $object->setData($field->getCode(), $value);
            }
            if ($field->getType() == 'checkbox') {
                if (!isset($post[$field->getCode()])) {
                    $object->setData($field->getCode(), 0);
                }
            } elseif ($field->getType() == 'date') {
                $value = $object->getData($field->getCode());
                try {
                    $value = $this->localeDate->formatDate($value, \IntlDateFormatter::SHORT);
                } catch (\Exception $e) { //we have exception if input date is in incorrect format
                    $value = '';
                }
                $object->setData($field->getCode(), $value);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getFieldValue($rma, $field)
    {
        $value = $rma->getData($field->getCode());
        if (!$value) {
            return false;
        }
        if ($field->getType() == 'checkbox') {
            $value = $value ? __('Yes') : __('No');
        } elseif ($field->getType() == 'date') {
                $value = $this->localeDate->formatDate($value, \IntlDateFormatter::MEDIUM);
        } elseif ($field->getType() == 'select') {
            $values = $field->getValues();
            $value = $values[$value];
        }

        return $value;
    }

        /**
         * @param \Lofmp\Rma\Api\Data\RmaInterface $rma
         * @return string
         */
    public function getAddressHtml(\Lofmp\Rma\Api\Data\RmaInterface $rma)
    {
        $returnAddress = $rma->getReturnAddress();
        if (!$returnAddress) {
            $returnAddress = $this->helper->getConfig($rma->getStoreId(), 'rma/general/return_address');
        } else {
            $returnAddressModel = $this->rmaAddress->create();
            $returnAddressModel = $returnAddressModel->load((int)$returnAddress);
            if ($returnAddressModel->getId()) {
                $returnAddress = $returnAddressModel->getAddress();
            }

        }
        return nl2br($returnAddress);
    }



    /**
     * @return \Lofmp\Rma\Model\ResourceModel\OrderStatusHistory\Collection
     */
    public function getAllowOrderId()
    {
        $allowedStatuses = $this->helper->getConfig(null, 'rma/policy/allow_in_statuses');
        $allowedStatuses = explode(',', $allowedStatuses);

        $returnPeriod    = (int)$this->helper->getConfig(null, 'rma/policy/return_period');

        /** @var \Lofmp\Rma\Model\ResourceModel\OrderStatusHistory\Collection $collection */
        $collection = $this->historyCollectionFactory->create();
        $collection->removeAllFieldsFromSelect()
            ->addFieldToSelect('order_id')
            ->addFieldToFilter('status', ['in' => $allowedStatuses])
            ->addFieldToFilter(
                new \Zend_Db_Expr('ADDDATE(created_at, '.$returnPeriod.')'),
                ['gt' => new \Zend_Db_Expr('NOW()')]
            )
        ;
        return $collection->getColumnValues('order_id');
    }

    /**
     * {@inheritdoc}
     */
    public function getStatus($rma)
    {
        return $this->statusRepository->getById($rma->getStatusId());
    }

    /**
     * {@inheritdoc}
     */
    public function RmaReasonCount(RmaInterface $rma, $reasonId)
    {
        /** @var \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteria */
        $searchCriteria = $this->searchCriteriaBuilder->addFilters(
            [
                $this->filterBuilder
                    ->setField('rma_id')
                    ->setValue($rma->getId())
                    ->create(),
            ]
        )->addFilters(
            [
                $this->filterBuilder
                    ->setField('main_table.reason_id')
                    ->setValue($reasonId)
                    ->create(),
            ]
        );

        return $this->itemRepository->getList($searchCriteria->create())->getTotalCount();
    }

    /**
     * {@inheritdoc}
     */
    public function RmaConditionCount(RmaInterface $rma, $conditionId)
    {
        /** @var \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteria */
        $searchCriteria = $this->searchCriteriaBuilder->addFilters(
            [
                $this->filterBuilder
                    ->setField('rma_id')
                    ->setValue($rma->getId())
                    ->create(),
            ]
        )->addFilters(
            [
                $this->filterBuilder
                    ->setField('main_table.condition_id')
                    ->setValue($conditionId)
                    ->create(),
            ]
        );

        return $this->itemRepository->getList($searchCriteria->create())->getTotalCount();
    }

    /**
     * {@inheritdoc}
     */
    public function RmaResolutionCount(RmaInterface $rma, $resolutionId)
    {
        /** @var \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteria */
        $searchCriteria = $this->searchCriteriaBuilder->addFilters(
            [
                $this->filterBuilder
                    ->setField('rma_id')
                    ->setValue($rma->getId())
                    ->create(),
            ]
        )->addFilters(
            [
                $this->filterBuilder
                    ->setField('main_table.resolution_id')
                    ->setValue($resolutionId)
                    ->create(),
            ]
        );

        return $this->itemRepository->getList($searchCriteria->create())->getTotalCount();
    }

    /**
     * Validate post data
     *
     * @param array $data
     * @return bool     Return FALSE if someone item is invalid
     */
    public function validate($data)
    {
        return $this->validateRequireEntry($data) && $this->validateItemsQty($data);
    }

    /**
     * Check if required fields is not empty
     *
     * @param array $data
     * @return bool
     */
    public function validateRequireEntry(array $data)
    {
        $requiredFields = [
            'items' => __('Items'),
        ];
        $errorNo = true;
        foreach ($data as $field => $value) {
            if (in_array($field, array_keys($requiredFields)) && $value == '') {
                $errorNo = false;
                $this->messageManager->addErrorMessage(
                    __('To apply changes you should fill in required "%1" field', $requiredFields[$field])
                );
            }
        }
        return $errorNo;
    }

    /**
     * Check if any item has qty > 0
     *
     * @param array $data
     * @return bool
     */
    public function validateItemsQty(array $data)
    {
        $isEmpty = true;
        if ($data && isset($data["items"]) && count($data["items"])) {
            foreach ($data['items'] as $item) {
                if ((int)$item['qty_requested'] > 0) {
                    $isEmpty = false;
                    break;
                }
            }
        }
        if ($isEmpty) {
            $this->messageManager->addErrorMessage(
                __("Please, add order items to the RMA (set 'Qty to Return')")
            );
            return false;
        }
        return true;
    }

    /**
     * @param \Lofmp\Rma\Api\Data\RmaInterface $rma
     * @return string
     */
    public function generateIncrementId(\Lofmp\Rma\Api\Data\RmaInterface $rma)
    {
        $id = $rma->getId();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $order = $objectManager->get('Magento\Sales\Model\Order')->load($rma->getOrderId());
        $result =  $order->getIncrementId();
        $result .= '-' .$id;

        return $result;
    }

    public function CheckFile($type, $size)
    {
        $allowedFiles =$this->helper->getConfig($store = null, 'rma/general/file_allowed_extensions');
         $allowedFiles = explode(',', $allowedFiles);
         $allowedFiles = array_map('trim', $allowedFiles);
         $SizeLimit = $this->helper->getConfig($store = null, 'rma/general/file_size_limit') * 1024 * 1024;
        if (count($allowedFiles)) {
               $exit = 0;
            foreach ($allowedFiles as $allowedType) {
                if (strcmp($allowedType, $type)==0) {
                    $exit = 1;
                }
            }
            if ($exit = 0) {
                return false;
            }
        }

        if ($SizeLimit && $size > $SizeLimit) {
            return false;
        }
        return true;
    }
}
