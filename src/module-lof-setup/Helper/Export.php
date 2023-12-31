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

class Export extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
 * @var \Magento\Framework\Xml\Parser
*/
    protected $parser;

    /**
 * @var \Magento\Store\Model\StoreManagerInterface
*/
    protected $_storeManager;

    /**
     * DB connection
     *
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected $_conn;

    /**
     * @var \Magento\Framework\Module\Dir
     */
    protected $_moduleDir;

    /**
     * @param \Magento\Framework\App\Helper\Context              $context
     * @param \Magento\Store\Model\StoreManagerInterface         $storeManager
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\App\ResourceConnection          $resource
     * @param Dir                                                $moduleDir
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\ResourceConnection $resource,
        Dir $moduleDir
    ) {
        parent::__construct($context);
        $this->_moduleDir = $moduleDir;
        $this->parser = new \Magento\Framework\Xml\Parser();
        $this->_storeManager = $storeManager;
        $this->_resource = $resource;
    }

    public function exportModules($data)
    {
        $moduleTables = $this->getModuleTables();
        $configs = [];
        if(!empty($data['modules'])) {
            $store = $this->_storeManager->getStore($data['store_id']);
            foreach ($data['modules'] as $k => $v) {
                if(isset($moduleTables[$v])) {
                    $tables = $moduleTables[$v];
                }
                $systemFileDir = $this->_moduleDir->getDir($v, Dir::MODULE_ETC_DIR). DIRECTORY_SEPARATOR . 'adminhtml' . DIRECTORY_SEPARATOR . 'system.xml';

                if(file_exists($systemFileDir)) {

                    $systemConfigs = $this->parser->load($systemFileDir)->xmlToArray();
                    if($systemConfigs['config']['_value']['system']['section']) {
                        foreach ($systemConfigs['config']['_value']['system']['section'] as $_section) {
                            $groups = [];
                            if(isset($_section['_value']['group'])) {
                                $groups = $_section['_value']['group'];
                            }elseif(isset($_section['group'])) {
                                $groups = $_section['group'];
                            }

                            $_sectionId = '';
                            if(isset($_section['_attribute']['id'])) {
                                $_sectionId = $_section['_attribute']['id'];
                            }elseif(isset($systemConfigs['config']['_value']['system']['section']['_attribute']['id'])) {
                                $_sectionId = $systemConfigs['config']['_value']['system']['section']['_attribute']['id'];
                            }

                            if(empty($groups)) { continue;
                            }
                            foreach ($groups as $_group) {
                                if(!isset($_group['_value']['field'])) { continue;
                                }
                                foreach ($_group['_value']['field'] as $_field) {
                                    if(isset($_sectionId) && isset($_group['_attribute']['id']) && isset($_field['_attribute']['id'])) {
                                        $key = $_sectionId . '/' . $_group['_attribute']['id'] . '/' . $_field['_attribute']['id'];
                                        $result = $this->scopeConfig->getValue(
                                            $key,
                                            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                                            $store
                                        );
                                        if($result=='') { continue;
                                        }
                                        $configs[$v]['system_configs'][] = [
                                        'key' => $key,
                                        'value' => $result
                                        ];
                                    }
                                }
                            }
                        }
                    }
                }
                if(isset($moduleTables[$v]) && is_array($moduleTables[$v])) {
                    foreach ($moduleTables[$v] as $key => $tableName) {
                        $connection = $this->_resource->getConnection();
                        if($this->checkTableExists($tableName, $connection)) {
                            $select = 'SELECT * FROM ' . $this->_resource->getTableName($tableName);
                            $rows = $connection->fetchAll($select);
                            $configs[$v]['tables'][$tableName] = $rows;
                        }
                    }
                }
            }
        }
        return $configs;
    }

    public function checkTableExists($tableName,  $connection = null)
    {
        if(!$connection) {
            $connection = $this->_resource->getConnection();
        }

        $select = "SHOW TABLES LIKE '" . $this->_resource->getTableName($tableName)."'";
        $rows = $connection->fetchAll($select);
        if(count($rows) > 0 ) {
            return true;
        }

        return false;
    }

    public function exportCmsPages($data)
    {
        $configs = [];
        if(!empty($data['cmspages'])) {
            $pageIds = implode(',', $data['cmspages']);
            $moduleTables = $this->getModuleTables();
            if(isset($moduleTables["Magento_Cms_Page"])) {
                foreach ($moduleTables["Magento_Cms_Page"] as $k => $tableName) {
                    $connection = $this->_resource->getConnection();
                    $select = 'SELECT * FROM ' . $this->_resource->getTableName($tableName) . ' WHERE page_id IN (' . $pageIds . ') ';
                    $rows = $connection->fetchAll($select);
                    $configs['Magento_Cms_Page']['tables'][$tableName] = $rows;
                }
            }
        }
        return $configs;
    }


    public function exportStaticBlocks($data)
    {
        $configs = [];
        if(!empty($data['cmsblocks'])) {
            $blockIds = implode(',', $data['cmsblocks']);
            $moduleTables = $this->getModuleTables();
            if(isset($moduleTables["Magento_Cms_Block"])) {
                foreach ($moduleTables["Magento_Cms_Block"] as $k => $tableName) {
                    $connection = $this->_resource->getConnection();
                    $select = 'SELECT * FROM ' . $this->_resource->getTableName($tableName) . ' WHERE block_id IN (' . $blockIds . ') ';
                    $rows = $connection->fetchAll($select);
                    $configs['Magento_Cms_Block']['tables'][$tableName] = $rows;
                }
            }
        }
        return $configs;
    }

    public function exportWidgets($data)
    {
        $configs = [];
        if(!empty($data['widgets'])) {
            $moduleTables = $this->getModuleTables();
            if(isset($moduleTables["Magento_Widget"])) {

                // Widget Instance
                $connection = $this->_resource->getConnection();
                $select = 'SELECT * FROM ' . $this->_resource->getTableName('widget_instance') . ' WHERE instance_id IN (' .  implode(',', $data['widgets']) . ') ';
                $rows = '';
                $configs['Magento_Widget']['tables']['widget_instance'] = $connection->fetchAll($select);
                $widgetInstanceIds = [];
                foreach ($configs['Magento_Widget']['tables']['widget_instance'] as $k => $v) {
                    $widgetInstanceIds[] = $v['instance_id'];
                }

                // Widget Instance Page
                if(!empty($widgetInstanceIds)) {
                    $connection = $this->_resource->getConnection();
                    $select = 'SELECT * FROM ' . $this->_resource->getTableName('widget_instance_page') . ' WHERE instance_id IN (' .  implode(',', $widgetInstanceIds) . ') ';
                    $rows = '';
                    $configs['Magento_Widget']['tables']['widget_instance_page'] = $connection->fetchAll($select);
                    $widgetInstancePageIds = [];
                    foreach ($configs['Magento_Widget']['tables']['widget_instance_page'] as $k => $v) {
                        $widgetInstancePageIds[] = $v['page_id'];
                    }
                }

                // Widget Instance Page Layout
                $widgetInstancePageLayoutIds = [];
                if(!empty($widgetInstancePageIds)) {
                    $connection = $this->_resource->getConnection();
                    $select = 'SELECT * FROM ' . $this->_resource->getTableName('widget_instance_page_layout') . ' WHERE page_id IN (' . implode(',', $widgetInstancePageIds) . ') ';
                    $rows = '';
                    $configs['Magento_Widget']['tables']['widget_instance_page_layout'] = $connection->fetchAll($select);
                    foreach ($configs['Magento_Widget']['tables']['widget_instance_page_layout'] as $k => $v) {
                        $widgetInstancePageLayoutIds[] = $v['layout_update_id'];
                    }
                }

                // Widget Core Layout Link
                $widgetLayoutUpdateId = [];
                if(!empty($widgetInstancePageLayoutIds)) {
                    $connection = $this->_resource->getConnection();
                    $select = 'SELECT * FROM ' . $this->_resource->getTableName('layout_link') . ' WHERE layout_link_id IN (' .  implode(',', $widgetInstancePageLayoutIds) . ') ';
                    $rows = '';
                    $configs['Magento_Widget']['tables']['layout_link'] = $connection->fetchAll($select);
                    $widgetInstancePageLayoutIds = [];
                    foreach ($configs['Magento_Widget']['tables']['layout_link'] as $k => $v) {
                        $widgetLayoutUpdateId[] = $v['layout_update_id'];
                    }
                }

                // Widget Core Layout Update
                if(!empty($widgetLayoutUpdateId)) {
                    $connection = $this->_resource->getConnection();
                    $select = 'SELECT * FROM ' . $this->_resource->getTableName('layout_update') . ' WHERE layout_update_id IN (' .  implode(',', $widgetLayoutUpdateId) . ') ';
                    $configs['Magento_Widget']['tables']['layout_update'] = $connection->fetchAll($select);
                }
            }
        }
        return $configs;
    }

    public function getModuleTables()
    {
        $sql_tables = [
            "Lof_Formbuilder" => [
            "lof_formbuilder_form", "lof_formbuilder_form_customergroup","lof_formbuilder_form_store","lof_formbuilder_message","lof_formbuilder_model_category","lof_formbuilder_model"],
            "Lof_Faq" => ["lof_faq_category","lof_faq_category_store","lof_faq_question","lof_faq_question_category","lof_faq_question_product", "lof_faq_question_relatedquestion","lof_faq_question_store", "lof_faq_question_tag", "lof_faq_question_vote"],
            "Lof_FollowUpEmail" => ["lof_followupemail_email","lof_followupemail_email_store","lof_followupemail_queue","lof_followupemail_sms","lof_followupemail_email_product_types", "lof_followupemail_email_customer_groups","lof_followupemail_email_order_status", "lof_followupemail_history", "lof_followupemail_blacklist"],
            "Lof_Gallery" => ["lof_gallery_banner_category","lof_gallery_banner","lof_gallery","lof_gallery_category_store","lof_gallery_banner_product", "lof_gallery_banner","lof_gallery_album","lof_gallery_album_category","lof_gallery_album_tag","lof_gallery_album_product","lof_gallery_album_image","lof_gallery_album_store","lof_gallery_album_post","lof_gallery_tag","lof_gallery_category","lof_gallery_album_corestore"],
            "Lof_Affiliate" => [
            "lof_affiliate_commission", "lof_affiliate_campaign","lof_affiliate_store","lof_affiliate_group","lof_affiliate_banner","lof_affiliate_account","lof_affiliate_transaction","lof_affiliate_withdraw"],
            "Lof_RewardPoints" => [
            "lof_rewardpoints_earning_rule", "lof_rewardpoints_product_earning_points","lof_rewardpoints_earning_rule_relationships","lof_rewardpoints_earning_rule_customer_group","lof_rewardpoints_purchase","lof_rewardpoints_spending_rule","lof_rewardpoints_spending_rule_customer_group","lof_rewardpoints_product_spending_points","lof_rewardpoints_spending_rule_relationships","lof_rewardpoints_transaction","lof_rewardpoints_customer","lof_rewardpoints_email"],
            "Lof_CouponCode" => [
            "lof_coupon_code_log", "lof_couponcode_coupon","lof_couponcode_rule"],
            "Lof_ProductTags" => [
                "lof_producttags_tag", "lof_producttags_product","lof_producttags_store"],
            "Lof_StoreLocator" => [
            "lof_storelocator_storelocator", "lof_storelocator_storelocator_store","lof_storelocator_storelocator_tag","lof_storelocator_storelocator_category","lof_storelocator_dealer","lof_storelocator_dealer_category","lof_storelocator_dealer_review"],
            "Lof_RequestForQuote" => ["lof_rfq_quote"],
            "Lof_Supplier" => ["lof_supplier_group", "lof_supplier","lof_supplier_store","lof_supplier_product","lof_supplier_price_lists"],
            "Lof_PromotionBar" => ["lof_promotionbar_banner", "lof_promotionbar_store","lof_promotionbar_customer_group","lof_promotionbar_product","lof_promotionbar_category"],
            "Lofmp_Blog" => [
                "lofmp_blog_category",
                "lofmp_blog_category_store",
                "lofmp_blog_tag",
                "lofmp_blog_tag_store",
                "lofmp_blog_post",
                "lofmp_blog_post_category",
                "lofmp_blog_post",
                "lofmp_blog_post_products_related",
                "lofmp_blog_post_related",
                "lofmp_blog_post_store",
                "lofmp_blog_post_tag",
                "lofmp_blog_post_vote",
                "lofmp_blog_comment",
                "lofmp_blog_comment_store",
                "lofmp_blog_setting",
                "lofmp_sellerblog_setting"
            ],
        ];
        return $sql_tables;
    }
}
