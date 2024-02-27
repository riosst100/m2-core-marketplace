<?php

namespace CoreMarketplace\ProductAttributesLink\Controller\Adminhtml\AttributeOption;

use Magento\Backend\App\Action;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Magento\Framework\Image\AdapterFactory;
use Magento\Store\Model\ScopeInterface; 

class RunImport extends \CoreMarketplace\ProductAttributesLink\Controller\Adminhtml\Index
{
    protected $fileSystem;
    protected $uploaderFactory;
    protected $request;
    protected $adapterFactory;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \CoreMarketplace\ProductAttributesLink\Model\ProductAttributesLinkFactory
     */
    protected $prdAttrLinkFactory;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory
     */
    protected $categoryCollFactory;

    /**
     * @var \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory
     */
    protected $attrSetCollFactory;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\ProductFactory
     */
    protected $prdResourceFactory;

    /**
     * @var \CoreMarketplace\ProductAttributesLink\Model\MappingGroupFactory
     */
    protected $mappingGroupFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Filesystem $fileSystem,
        \Magento\MediaStorage\Model\File\UploaderFactory $uploaderFactory,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        AdapterFactory $adapterFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \CoreMarketplace\ProductAttributesLink\Model\ProductAttributesLinkFactory $prdAttrLinkFactory,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollFactory,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory $attrSetCollFactory,
        \Magento\Catalog\Model\ResourceModel\ProductFactory $prdResourceFactory,
        \CoreMarketplace\ProductAttributesLink\Model\MappingGroupFactory $mappingGroupFactory
    ) {
        parent::__construct($context, $resultPageFactory);
        $this->fileSystem = $fileSystem;
        $this->request = $request;
        $this->scopeConfig = $scopeConfig;
        $this->adapterFactory = $adapterFactory;
        $this->uploaderFactory = $uploaderFactory;
        $this->resultPageFactory = $resultPageFactory;
        $this->prdAttrLinkFactory = $prdAttrLinkFactory;
        $this->categoryCollFactory = $categoryCollFactory;
        $this->attrSetCollFactory = $attrSetCollFactory;
        $this->prdResourceFactory = $prdResourceFactory;
        $this->mappingGroupFactory = $mappingGroupFactory;
    }

    public function getColumnNameAlias($columnName) 
    {
        if ($columnName == "category") {
            return "category_id";
        }
        
        if ($columnName == "attribute_option") {
            return "attribute_option_id";
        }
        
        if ($columnName == "target_attribute_set") {
            return "target_attribute_set_id";
        }

        if ($columnName == "target_attribute_code") {
            return "target_attribute_code";
        }

        if ($columnName == "group") {
            return "group_id";
        }
        
        return $columnName;
    }

    public function processCategoryId($categoryName) 
    {
        $categoryId = null;
        if ($categoryName) {
            $collection = $this->categoryCollFactory->create()
            ->addAttributeToFilter('name', $categoryName);
            if ($collection->getSize()) {
                return $collection->getFirstItem()->getId();
            }
        }

        return $categoryId;
    }

    public function processAttributeSetId($attributeSetName) 
    {
        if ($attributeSetName) {
            $collection = $this->attrSetCollFactory->create()
            ->addFieldToFilter('attribute_set_name', $attributeSetName);
            if ($collection->getSize()) {
                return $collection->getFirstItem()->getAttributeSetId();
            }
        }

        return null;
    }

    public function processAttributeOptionId($attributeCode, $optionLabel) 
    {
        if ($optionLabel && $attributeCode) {
            return $this->getAttributeOptionId($attributeCode, $optionLabel);
        }

        return null;
    }

    public function processAttributeOptions($targetAttributeCode, $optionLabels) 
    {
        if ($optionLabels && $targetAttributeCode) {
            $optionLabelsArr = explode(",", $optionLabels);
            if ($optionLabelsArr) {
                $optionIds = [];
                foreach($optionLabelsArr as $optionLabel) {
                    $optionIds[] = $this->getAttributeOptionId($targetAttributeCode, $optionLabel);
                }

                return implode(",", $optionIds);
            }
        }

        return null;
    }

    public function processData($columnName, $columnData, $attributeCode = null, $targetAttributeCode = null) 
    {
        // if ($columnName == "attribute_code") {
        //     return $this->processAttributeId($columnData);
        // }

        // if ($columnName == "target_attribute_code") {
        //     return $this->processAttributeCode($columnData);
        // }


        if ($columnName == "group_id") {
            return $this->processGroupId($columnData);
        }

        if ($columnName == "category_id") {
            return $this->processCategoryId($columnData);
        }

        if ($columnName == "attribute_option_id") {
            return $this->processAttributeOptionId($attributeCode, $columnData);
        }

        if ($columnName == "target_attribute_options") {
            return $this->processAttributeOptions($targetAttributeCode, $columnData);
        }

        if ($columnName == "target_attribute_set_id") {
            return $this->processAttributeSetId($columnData);
        }

        return $columnData;
    }

    public function processGroupId($groupName) 
    {
        if ($groupName) {
            $collection = $this->mappingGroupFactory->create()
            ->getCollection()
            ->addFieldToFilter('name', $groupName);
            if ($collection->getSize()) {
                return $collection->getFirstItem()->getId();
            } else {
                $mappingGroup = $this->mappingGroupFactory->create()
                ->setName($groupName)
                ->save();
                if ($mappingGroup) {
                    return $mappingGroup->getId();
                }
            }
        }

        return 0;
    }

    public function getAttributeOptionId($attributeCode, $optionLabel)
    {
        $attr = $this->prdResourceFactory->create()->getAttribute($attributeCode);
        if ($attr->usesSource()) {
            return $attr->getSource()->getOptionId(trim($optionLabel));
        }

        return null;
    }

    public function execute()
    {
        if ((isset($_FILES['importdata']['name'])) && ($_FILES['importdata']['name'] != '')) 
        {
            try {
                $uploaderFactory = $this->uploaderFactory->create(['fileId' => 'importdata']);
                $uploaderFactory->setAllowedExtensions(['csv', 'xls']);
                $uploaderFactory->setAllowRenameFiles(true);
                $uploaderFactory->setFilesDispersion(true);

                $mediaDirectory = $this->fileSystem->getDirectoryRead(DirectoryList::MEDIA);
                $destinationPath = $mediaDirectory->getAbsolutePath('productattributelink/import');

                $result = $uploaderFactory->save($destinationPath);

                if (!$result) {
                    throw new LocalizedException
                    (
                    __('File cannot be saved to path: $1', $destinationPath)
                    );

                } else {   
                    $imagePath = 'productattributelink/import'.$result['file'];

                    $mediaDirectory = $this->fileSystem->getDirectoryRead(DirectoryList::MEDIA);
                    
                    $destinationfilePath = $mediaDirectory->getAbsolutePath($imagePath);

                    /* file read operation */

                    $f_object = fopen($destinationfilePath, "r");
                    
                    $column = fgetcsv($f_object);

                    // This mapping column name
                    $columnMapping = [];
                    foreach($column as $index => $columnName) {
                        $columnName = $this->getColumnNameAlias($columnName);
                        $columnMapping[$columnName] = $index;
                    }

                    // die(print_r($columnMapping,true));
                    
                    // column name must be the same as the Sample filename 

                        if($f_object)
                        {
                            // if( ($column[0] == 'Col_name_1') && ($column[1] == 'Col_name_2') && ($column[2] == 'Col_name_3') && ($column[3] == 'Col_name_4') && ($column[4] == 'Col_name_5') )
                            // {   

                            $count = 0;
                            while (($dataColumns = fgetcsv($f_object)) !== FALSE) 
                            {
                                $count++;

                                $attributeCode = null;
                                if (isset($columnMapping['attribute_code'])) {
                                    $attributeCodeIndex = $columnMapping['attribute_code'];
                                    $attributeCode = $dataColumns[$attributeCodeIndex];
                                }

                                $targetAttributeCode = null;
                                if (isset($columnMapping['target_attribute_code'])) {
                                    $targetAttributeCodeIndex = $columnMapping['target_attribute_code'];
                                    $targetAttributeCode = $dataColumns[$targetAttributeCodeIndex];
                                }

                                $data = [];
                                foreach($columnMapping as $columnName => $index) {
                                    $columnData = $this->processData($columnName, $dataColumns[$index], $attributeCode, $targetAttributeCode);
                                    $data[$columnName] = $columnData;
                                }
                        
                                $prdAttrLink = $this->prdAttrLinkFactory->create();
                                $prdAttrLink->setData($data);
                                $prdAttrLink->save();
                            } 
                                
                            $this->messageManager->addSuccess(__('A total of %1 record(s) have been Added.', $count));
                            $this->_redirect('productattributeslink/index/index');
                            // } else {
                            //     $this->messageManager->addError(__("Invalid Formated File"));
                            //     $this->_redirect('productattributeslink/index/import');
                            // }

                        } 
                        else
                        {
                            $this->messageManager->addError(__("File hase been empty"));
                            $this->_redirect('productattributeslink/index/import');
                        }
                
                    }                   
            } catch (\Exception $e) {   
                $this->messageManager->addError(__($e->getMessage()));
                $this->_redirect('productattributeslink/index/import');
            }
        } else {
            $this->messageManager->addError(__("Please try again."));
            $this->_redirect('productattributeslink/index/import');
        }
    }
}