<?php
/**
 * Landofcoder
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * http://landofcoder.com/license
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category  Landofcoder
 * @package   Lof_Setup
 * @copyright Copyright (c) 2016 Landofcoder (http://www.landofcoder.com/)
 * @license   http://www.landofcoder.com/LICENSE-1.0.html
 */
namespace Lof\Setup\Model\System\Config\Source\Import;
use Magento\Framework\App\Filesystem\DirectoryList;

class Files
{

    /**
     * @param \Magento\Cms\Model\Page $pageModel
     */
    protected $_filesystem;
    public function __construct(
        \Magento\Framework\Filesystem $filesystem
    ) {
        $this->_filesystem = $filesystem;
    }

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray($key)
    {
        $path = "design" . DIRECTORY_SEPARATOR . "frontend" . DIRECTORY_SEPARATOR . $key . DIRECTORY_SEPARATOR . 'backup' . DIRECTORY_SEPARATOR;
        $files = [];
        $importFolderDir = $this->_filesystem->getDirectoryWrite(DirectoryList::APP)->getAbsolutePath($path);
        $files = glob($importFolderDir.'*.json');
        $outputs = [];
        foreach ($files as $k => $v) {
            $labelFile = str_replace($importFolderDir, "", $v);
            $file_content = file_get_contents($v);
            $file_content =  \Laminas\Json\Json::decode($file_content);
            $created_at = $comment = '';
            if(isset($file_content['created_at']) && $file_content['created_at']!='') {
                $created_at = ' - '.$file_content['created_at'];
            }
            if(isset($file_content['comment']) && $file_content['comment']!='') {
                $comment = ' - '.$file_content['comment'];
            }
            $labelFile = $labelFile.' '.$created_at.' '.$comment;
            $outputs[] = array(
                'label' => $labelFile,
                'value' => $v,
                );
        }
        $outputs[] = [
            'value' => 'data_import_file',
            'label' => __('Upload custom file...')];

        return $outputs;
    }

    /**
     * Options getter
     *
     * @return array
     */
    public function toArray()
    {
        $collection = $this->_pageModel->getCollection();
        $blocks = array();
        foreach ($collection as $_page) {
            $blocks[$_page->getId()] = addslashes($_page->getTitle());
        }
        return $blocks;
    }
}