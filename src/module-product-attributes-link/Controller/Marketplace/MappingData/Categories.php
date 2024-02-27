<?php
namespace CoreMarketplace\ProductAttributesLink\Controller\Marketplace\MappingData;

use Magento\Framework\App\Action\Context;

class Categories extends \Magento\Framework\App\Action\Action
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
     * Save constructor.
     * @param Context $context
     * @param \Magento\Framework\Controller\Result\JsonFactory $jsonFactory
     * @param \CoreMarketplace\ProductAttributesLink\Model\ResourceModel\ProductAttributesLink\CollectionFactory $prdAttrLinkCollFactory
     * @param \Lof\MarketPlace\Helper\Data $helper
     */
    public function __construct(
        Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $jsonFactory,
        \CoreMarketplace\ProductAttributesLink\Model\ResourceModel\ProductAttributesLink\CollectionFactory $prdAttrLinkCollFactory,
        \Lof\MarketPlace\Helper\Data $helper
    ) {
        $this->jsonFactory = $jsonFactory;
        $this->prdAttrLinkCollFactory = $prdAttrLinkCollFactory;
        $this->helper = $helper;
        parent::__construct(
            $context
        );
    }
    
	public function execute()
	{
        $resultJson = $this->jsonFactory->create();
        $resultJson->setHttpResponseCode(200);

        $defaultAttributeSetId = (int)$this->helper->getConfig("seller_settings/default_attribute_set_id");
        $defaultAttributeSetId = $defaultAttributeSetId != 1 ? $defaultAttributeSetId : self::DEFAULT_ATTRIBUTE_SET_ID;

        $data = [
            'attribute_set_id' => $defaultAttributeSetId,
            'mapping_group_id' => null,
            'mapping_priority' => 0
        ];

        try {
            $selectedCategoryId = $this->getRequest()->getParam('category_id');
            if ($selectedCategoryId) {
                $collection = $this->prdAttrLinkCollFactory->create()
                ->addFieldToFilter('mapping_type', 'attribute_set')
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
                }
            }
        } catch (\Exception $e) {}

        $resultJson->setData(['success' => false, 'data' => $data]);
        return $resultJson;
    }
}