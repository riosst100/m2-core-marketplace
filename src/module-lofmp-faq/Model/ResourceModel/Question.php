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
 * @category   Landofcoder
 * @package    Lofmp_Faq
 * @copyright  Copyright (c) 2017 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */

namespace Lofmp\Faq\Model\ResourceModel;

class Question extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Construct
     *
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param string $connectionName
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        $connectionName = null
    ) {
        parent::__construct($context, $connectionName);
        $this->_storeManager = $storeManager;
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('lofmp_faq_question', 'question_id');
    }

    /**
     * Perform operations after object save
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _afterSave(\Magento\Framework\Model\AbstractModel $object)
    {
        $questionId = $object->getData('question_id');
        $productIds = $object->getData('productIds');

        // product_id from customer faq form
        $productId = $object->getData('product_id');

        $table = $this->getTable('lofmp_faq_question_product');
        $where = ['question_id = ?' => $questionId];
        $this->getConnection()->delete($table, $where);
        if(is_array($productIds)){
            $table = $this->getTable('lofmp_faq_question_product');
            $data = [];
            foreach ($productIds as $productId) {
                $data[] = ['question_id' => $questionId, 'product_id' => $productId];
            }
            $this->getConnection()->insertMultiple($table, $data);
        }else if($productId){
            $table = $this->getTable('lofmp_faq_question_product');
            $data =  ['question_id' => $questionId, 'product_id' => $productId];
            $this->getConnection()->insert($table, $data);
        }

        return parent::_afterSave($object);
    }
}
