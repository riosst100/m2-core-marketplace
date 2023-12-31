<?php
namespace Lofmp\DeliverySlot\Model\Config\Backend\Field;

use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Value as ConfigValue;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\Framework\Serialize\SerializerInterface;

/**
 * Class DeliverySlot
 *
 * @package Emc\DeliverySlot\Model\Config\Backend\Field
 */
class DeliverySlot extends ConfigValue
{
    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * @var \Magento\Backend\Model\View\Result\Redirect
     */
    protected $resultRedirect;

    /**
     * @var \Magento\Framework\Controller\ResultFactory
     */
    protected $resultFactory;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $_messageManager;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * PackagingForm constructor.
     *
     * @param SerializerInterface $serializer
     * @param Context $context
     * @param ScopeConfigInterface $scopeconfig
     * @param Registry $registry
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param ScopeConfigInterface $config
     * @param \Magento\Framework\Controller\ResultFactory $resultFactory
     * @param \Magento\Backend\Model\View\Result\Redirect $resultRedirect
     * @param TypeListInterface $cacheTypeList
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        SerializerInterface $serializer,
        Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeconfig,
        Registry $registry,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        ScopeConfigInterface $config,
        \Magento\Framework\Controller\ResultFactory $resultFactory,
        \Magento\Backend\Model\View\Result\Redirect $resultRedirect,
        TypeListInterface $cacheTypeList,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->serializer = $serializer;
        $this->resultRedirect = $resultRedirect;
        $this->resultFactory = $resultFactory;
        $this->_messageManager = $messageManager;
        $this->scopeConfig = $scopeconfig;
        parent::__construct($context, $registry, $config, $cacheTypeList, $resource, $resourceCollection, $data);
    }

    /**
     * @return $this|void
     */
    public function beforeSave()
    {
        /** Stored Packaging Slot detail **/
        $oldStoreValue = $this->scopeConfig->getValue('delivery_slot/lofmp_delivery_slot_settings/vacation');
        if ($oldStoreValue) {
            $oldPackageValue = $this->serializer->unserialize($oldStoreValue);
            /** Current Packaging Form  detail */
            $newPackageValue = $this->getValue();
            unset($newPackageValue['__empty']);
            /** Compare and Differentiate current  and store packaging detail */
            $newKey = array_diff(array_keys($newPackageValue), array_keys($oldPackageValue));
            foreach ($oldPackageValue as $key => $value) {
                $codes[] = $value['disable_date'];
            }
            /** Check code exists or not if any new field add */
            if (!empty($newKey)) {
                foreach ($newKey as $keys => $keyValues) {
                    foreach ($newPackageValue as $key => $value) {
                        if ($key == $keyValues) {
                            if (in_array($value['disable_date'], $codes)) {
                                /** If already exists packaging code, unset value */
                                unset($newPackageValue[$key]);
                                $this->_messageManager->addErrorMessage(__('Already this Date Exists'));
                            }
                        }
                    }
                }
                $encodedValue = $this->serializer->serialize($newPackageValue);
            } else {
                $encodedValue = $this->serializer->serialize($newPackageValue);
            }
        } else {
            $value = $this->getValue();
            unset($value['__empty']);
            $encodedValue = $this->serializer->serialize($value);
        }
        $this->setValue($encodedValue);
    }

    /**
     * @return $this|void
     */
    protected function _afterLoad()
    {
        /** @var string $value */
     /*   $value = $this->getValue();
        if ($value) {
            $decodedValue = $this->serializer->unserialize($value);
            $this->setValue($decodedValue);
        }*/
    }
}
