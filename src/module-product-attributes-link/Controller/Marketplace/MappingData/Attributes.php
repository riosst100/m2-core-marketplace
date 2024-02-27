<?php
namespace CoreMarketplace\ProductAttributesLink\Controller\Marketplace\MappingData;

use Magento\Framework\App\Action\Context;

class Attributes extends \Magento\Framework\App\Action\Action
{
    const DEFAULT_ATTRIBUTE_SET_ID = 4;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $jsonFactory;

    /**
     * @var \CoreMarketplace\ProductAttributesLink\Model\ResourceModel\ProductAttributesLink\CollectionFactory
     */
    protected $prdAttrLinkCollFactory;

    /**
     * @var \Lof\MarketPlace\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Eav\Model\ResourceModel\Entity\Attribute
     */
    protected $eavAttribute;

    /**
     * Save constructor.
     * @param Context $context
     * @param \Magento\Framework\Controller\Result\JsonFactory $jsonFactory
     * @param \CoreMarketplace\ProductAttributesLink\Model\ResourceModel\ProductAttributesLink\CollectionFactory $prdAttrLinkCollFactory
     * @param \Lof\MarketPlace\Helper\Data $helper
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute $eavAttribute
     */
    public function __construct(
        Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $jsonFactory,
        \CoreMarketplace\ProductAttributesLink\Model\ResourceModel\ProductAttributesLink\CollectionFactory $prdAttrLinkCollFactory,
        \Lof\MarketPlace\Helper\Data $helper,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute $eavAttribute
    ) {
        $this->jsonFactory = $jsonFactory;
        $this->prdAttrLinkCollFactory = $prdAttrLinkCollFactory;
        $this->helper = $helper;
        $this->eavAttribute = $eavAttribute;
        parent::__construct(
            $context
        );
    }

    public function getAttributeIdByCode($code) 
    {
        return $this->eavAttribute->getIdByCode(
            \Magento\Catalog\Model\Product::ENTITY,
            $code
        );
    }
    
	public function execute()
	{
        $resultJson = $this->jsonFactory->create();
        $resultJson->setHttpResponseCode(200);

        $defaultAttributeSetId = (int)$this->helper->getConfig("seller_settings/default_attribute_set_id");
        $defaultAttributeSetId = $defaultAttributeSetId != 1 ? $defaultAttributeSetId : self::DEFAULT_ATTRIBUTE_SET_ID;

        $data = [
            'attribute_set_id' => null,
            'mapping_group_id' => null,
            'mapping_priority' => 0
        ];

        try {
            $selectedOptionId = $this->getRequest()->getParam('option_id');
            $attributeCode = $this->getRequest()->getParam('code');
            if ($selectedOptionId && $attributeCode) {
                $collection = $this->prdAttrLinkCollFactory->create()
                ->addFieldToFilter('attribute_code', $attributeCode)
                ->addFieldToFilter('mapping_type', 'attribute_set');
                if ($collection->getSize()) {
                    $mapping = null;
                    foreach($collection as $attributeLink) {
                        if ($attributeLink->getAttributeOptionId() == $selectedOptionId) {
                            $mapping = $attributeLink;
                        }
                    }

                    if ($mapping) {
                        $data['attribute_set_id'] = $mapping->getTargetAttributeSetId();
                        $data['mapping_group_id'] = $mapping->getGroupId();
                        $data['mapping_priority'] = $mapping->getPriority();

                        $resultJson->setData(['success' => true, 'data' => $data]);
                        return $resultJson;
                    }

                    $mappingGroupId = $this->getRequest()->getParam('mapping_group_id');
                    $mappingPriority = $this->getRequest()->getParam('mapping_priority') ?: 1;
                    if ($mappingGroupId) {
                        $collection = $this->prdAttrLinkCollFactory->create()
                        ->addFieldToFilter('group_id', $mappingGroupId)
                        ->addFieldToFilter('attribute_code', ['neq' => $attributeCode])
                        ->addFieldToFilter('priority', ['lt' => $mappingPriority])
                        ->addFieldToFilter('mapping_type', 'attribute_set')
                        ->setOrder('priority', 'ASC');
                        if ($collection->getSize()) {
                            $mapping = $collection->getFirstItem();
                            if ($mapping) {
                                $data['attribute_set_id'] = $mapping->getTargetAttributeSetId();
                                $data['mapping_group_id'] = $mapping->getGroupId();
                                $data['mapping_priority'] = $mapping->getPriority();

                                $resultJson->setData(['success' => true, 'data' => $data]);
                                return $resultJson;
                            }
                        }
                    }
                    $selectedCategoryId = $this->getRequest()->getParam('category_id');
                    if ($selectedCategoryId) {
                        $collection = $this->prdAttrLinkCollFactory->create()
                        ->addFieldToFilter('category_id', $selectedCategoryId);
                        if ($collection->getSize()) {
                            $mapping = $collection->getFirstItem();
                            if ($mapping) {
                                $data['attribute_set_id'] = $mapping->getTargetAttributeSetId();
                                $data['mapping_group_id'] = $mapping->getGroupId();
                                $data['mapping_priority'] = $mapping->getPriority();

                                $resultJson->setData(['success' => true, 'data' => $data]);
                                return $resultJson;
                            }
                        } else {
                            $data['attribute_set_id'] = $defaultAttributeSetId;
                            $data['mapping_group_id'] = $mapping->getGroupId();
                            $data['mapping_priority'] = $mapping->getPriority();

                            $resultJson->setData(['success' => true, 'data' => $data]);
                            return $resultJson;
                        }
                    }
                } 
                
                // else {
                //     $attributeSetId = $this->getRequest()->getParam('attribute_set_id');
                //     $selectedCategoryId = $this->getRequest()->getParam('category_id');
                //     if ($selectedCategoryId) {
                //         $collection = $this->prdAttrLinkCollFactory->create()
                //         ->addFieldToFilter('attribute_id', $attributeId)
                //         ->addFieldToFilter('target_attribute_set', $attributeSetId);
                //         if ($collection->getSize()) {
                //             $selectedCategoryId = $this->getRequest()->getParam('category_id');
                //             if ($selectedCategoryId) {
                //                 $collection = $this->prdAttrLinkCollFactory->create()
                //                 ->addFieldToFilter('category_id', $selectedCategoryId);
                //                 if ($collection->getSize()) {
                //                     $mapping = $collection->getFirstItem();
                //                     if ($mapping) {
                //                         if ($mapping->getTargetAttributeSetId() != $attributeSetId) {
                //                             $data['attribute_set_id'] = $mapping->getTargetAttributeSetId();

                //                             $resultJson->setData(['success' => true, 'data' => $data]);
                //                             return $resultJson;
                //                         }
                //                     }
                //                 }
                //             }
                //         }
                //     }
                // }
            }
        } catch (\Exception $e) {}

        $resultJson->setData(['success' => false, 'data' => $data]);
        return $resultJson;
    }
}