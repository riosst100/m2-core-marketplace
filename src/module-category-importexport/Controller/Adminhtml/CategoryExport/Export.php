<?php
/**
 * Webcastle_CategoryImportExport
 *
 * @category   Webcastle
 * @package    Webcastle_CategoryImportExport
 * @author     Anjaly K V - Webcastle Media
 * @copyright  2023
 */
namespace Webcastle\CategoryImportExport\Controller\Adminhtml\CategoryExport;

class Export extends \Magento\Backend\App\Action
{
    /**
     * Redirect result factory
     *
     * @var \Magento\Backend\Model\View\Result\ForwardFactory
     */
    protected $_resultForwardFactory;

    /**
     * @var \Magento\Catalog\Api\CategoryRepositoryInterface
     */
    private $categoryRepository;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category\Tree
     */
    private $tree;
    
    /**
     * constructor
     *
     * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManagerInterface,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository,
        \Magento\Catalog\Model\ResourceModel\Category\Tree $tree,
        \Magento\Catalog\Model\ResourceModel\Product\Collection $prodcollection,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Backend\App\Action\Context $context
    ) {
    
        $this->_resultForwardFactory = $resultForwardFactory;
        $this->_storeManager = $storeManagerInterface;
        $this->_categoryFactory = $categoryFactory;
        $this->categoryRepository = $categoryRepository;
        $this->tree = $tree;
        $this->_productcollection = $prodcollection;
        $this->resultRawFactory = $resultRawFactory;
        $this->fileFactory  = $fileFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $store_id = $this->getRequest()->getPost('store_id');
        $singlestoremode = $this->_storeManager->isSingleStoreMode();
        $_stores = [];
        if (!$singlestoremode) {
            $stores = $this->_storeManager->getStores();
            foreach ($stores as $key => $store) {
                $_stores[$store->getId()] = $store->getCode();
            }
        }
         $fileName = 'categories.csv';
        $content = '"category_id","parent_id"';
        $content .= ',"store"';
        $content .= ',"name","path","image","is_active","is_anchor","include_in_menu","meta_title","meta_keywords","meta_description","display_mode","custom_use_parent_settings","custom_apply_to_products","custom_design","custom_design_from","custom_design_to","default_sort_by","page_layout","description","products"'."\n";
        $collection = $this->_categoryFactory->create()->getCollection()->addAttributeToSort('entity_id', 'asc');
        
        foreach ($collection as $key => $cat) {
            $categoryitem = $this->_categoryFactory->create();
            if ($cat->getId()>=2) {
                $categoryitem->setStoreId($store_id);
                $categoryitem->load($cat->getId());
                if ($categoryitem->getId()) {
                    $prodids = '';
                    $productids = $this->_productcollection->addCategoryFilter($categoryitem)->getAllIds();
                    if (isset($productids) && !empty($productids)) {
                        $prodids = $productids = implode('|', $productids);
                    }       
                    $content .= '"'.$categoryitem->getId().'","'.$categoryitem->getParentId().'","';
                    $content .= $_stores[$categoryitem->getStoreId()].'","';
                    $content .= $categoryitem->getName().'","'.$this->getTreeByCategoryId($categoryitem->getId()).'","'.$categoryitem->getImage().'","'.$categoryitem->getIsActive().'","'.$categoryitem->getIsAnchor().'","'.$categoryitem->getIncludeInMenu().'","'.$categoryitem->getMetaTitle().'","'.$categoryitem->getMetaKeywords().'","'.$categoryitem->getMetaDescription().'","'.$categoryitem->getDisplayMode().'","'.$categoryitem->getCustomUseParentSettings().'","'.$categoryitem->getCustomApplyToProducts().'","'.$categoryitem->getCustomDesign().'","'.$categoryitem->getCustomDesignFrom().'","'.$categoryitem->getCustomDesignTo().'","'.$categoryitem->getDefaultSortBy().'","'.$categoryitem->getPageLayout().'","'.str_replace('"',"'",$categoryitem->getDescription() ?? '').'","'.$prodids.'"'."\n";
                }
            }
        }
        $this->_prepareDownloadResponse($fileName, $content);
    }

    public function _prepareDownloadResponse($name, $content)
    {
        $fileName = $name;
        $this->fileFactory->create(
            $fileName,
            $content,
            'var',
            'text/csv', 
            strlen($content)
        );
        $resultRaw = $this->resultRawFactory->create();
        return $resultRaw;
    }

    public function getTreeByCategoryId($categoryId)
    {
        $storeId = $this->getRequest()->getPost('store_id');
        $category = $this->categoryRepository->get($categoryId, $storeId);
        $categoryTree = $this->tree->setStoreId($storeId)->loadBreadcrumbsArray($category->getPath());

        $categoryTreepath = '';
        foreach($categoryTree as $eachCategory){

            $categoryTreepath = $categoryTreepath. '/'.$eachCategory['name'];
            
        }
        return ltrim($categoryTreepath,"/");
    }

}
