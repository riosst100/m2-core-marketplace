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
namespace Lof\Setup\Helper;
use Magento\Framework\App\Filesystem\DirectoryList;
use \Magento\Framework\Module\Dir;

class Import extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Lof\Setup\Helper\Data
     */
    protected $_lofData;

    /**
     * @param \Magento\Framework\App\Helper\Context     $context  
     * @param \Magento\Framework\App\ResourceConnection $resource 
     * @param \Lof\Setup\Helper\Data                    $lofData  
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\ResourceConnection $resource,
        \Lof\Setup\Helper\Data $lofData
    ) {
        parent::__construct($context);
        $this->_resource = $resource;
        $this->_lofdata = $lofData;
    }

    public function buildQueryImport($data = array(), $table_name = "", $override = true, $store_id = 0, $ignore_columns = null,  $where = '') 
    {
        $query = false;
        $binds = array();
        if($data) {
            $table_name = $this->_resource->getTableName($table_name);

            if($override) {
                $query = "REPLACE INTO `".$table_name."` ";
            } else {
                $query = "INSERT IGNORE INTO `".$table_name."` ";
            }
            $stores = $this->_lofdata->getAllStores();
            $fields = $values = array();
            foreach($data as $key=>$val) {
                if($val) {
                    if($key == "store_id" && !in_array($val, $stores)) {
                        $val = $store_id;
                    }
                    if($ignore_columns && in_array($key, $ignore_columns)){
						continue;
					}
                    $fields[] = "`".$key."`";
                    $values[] = ":".strtolower($key);
                    $binds[strtolower($key)] = $val;
                }
            }
            $query .= " (".implode(",", $fields).") VALUES (".implode(",", $values).")";
        }
        return array($query, $binds);
    }
}