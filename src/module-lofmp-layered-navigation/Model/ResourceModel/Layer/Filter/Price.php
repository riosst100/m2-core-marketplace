<?php
/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * https://landofcoder.com/terms
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Landofcoder
 * @package    Lofmp_LayeredNavigation
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */
namespace Lofmp\LayeredNavigation\Model\ResourceModel\Layer\Filter;

class Price extends \Magento\Catalog\Model\ResourceModel\Layer\Filter\Price
{

    protected function getAllowState($reFormat=false)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $allowState = $objectManager->create('Lof\MarketPlace\Model\Plugin\ViewProduct')->getAllowedApprovalStatus();
        if($reFormat && !empty($allowState)) {
            return implode(', ', $allowState);
        }
        return $allowState;
    }

    /**
     * {@inheritdoc}
     */
    protected function _getSelect()
    {
        $select = parent::_getSelect();
        $vendor = \Magento\Framework\App\ObjectManager::getInstance()
                    ->get('Magento\Framework\Registry')->registry('current_seller');
        if ($vendor) {
            $vendorId = $vendor->getId();

            $storeId = \Magento\Framework\App\ObjectManager::getInstance()
            ->get('Magento\Store\Model\StoreManagerInterface')->getStore()->getId();

            $wherePart = $select->getPart(\Magento\Framework\DB\Select::WHERE);

            //check flat product mode
            $flatMode = \Magento\Framework\App\ObjectManager::getInstance()
                        ->get('Magento\Framework\App\Config\ScopeConfigInterface')
                        ->getValue('catalog/frontend/flat_catalog_product');

            //remove vendor part of where part
            foreach($wherePart as $id => $where) {
                //Remove seller id
                if (strpos($where, "AND (e.seller_id") !== false) {
                    unset($wherePart[$id]);
                }elseif (strpos($where, "AND (`e`.`seller_id`") !== false) {
                    unset($wherePart[$id]);
                }elseif (strpos($where, "(e.seller_id") !== false) {
                    $wherePart[$id] = '1';
                }elseif (strpos($where, "(`e`.`seller_id`") !== false) {
                    $wherePart[$id] = '1';
                }elseif (strpos($where, "`e`.`seller_id`") !== false) {
                    $wherePart[$id] = '1';
                }


                //Remove approval query
                if (strpos($where, "AND (e.approval") !== false) {
                    unset($wherePart[$id]);
                }elseif (strpos($where, "AND (`e`.`approval`") !== false) {
                    unset($wherePart[$id]);
                }elseif (strpos($where, "AND (approval") !== false) {
                    unset($wherePart[$id]);
                }elseif (strpos($where, "AND (`approval`") !== false) {
                    unset($wherePart[$id]);
                }elseif (strpos($where, "AND") !== false && (strpos($where, "`e`.`approval`") !== false || (strpos($where, "e.approval") !== false))) {
                    unset($wherePart[$id]);
                }elseif (strpos($where, "(approval") !== false) {
                    $wherePart[$id] = '1';
                }elseif (strpos($where, "(`approval`") !== false) {
                    $wherePart[$id] = '1';
                }elseif (strpos($where, "(e.approval") !== false) {
                    $wherePart[$id] = '1';
                }elseif (strpos($where, "(`e`.`approval`") !== false) {
                    $wherePart[$id] = '1';
                }elseif (strpos($where, "`e`.`approval`") !== false) {
                    unset($wherePart[$id]);
                }elseif ((strpos($where, "`e`.`approval`") !== false || (strpos($where, "e.approval") !== false))) {
                    unset($wherePart[$id]);
                }
            }

            if($flatMode)
            {
                $firstWhere = current($wherePart);
                $wherePart[key($wherePart)] = trim($firstWhere, 'AND ');
            }
            $select->reset(\Magento\Framework\DB\Select::WHERE);
            $fromPart = $select->getPart(\Magento\Framework\DB\Select::FROM);


            $select->setPart(\Magento\Framework\DB\Select::WHERE, $wherePart);
            if(!isset($fromPart['product_entity'])) {

                $select->join(
                    ['product_entity'=>$this->getTable('catalog_product_entity')],
                    "product_entity.entity_id = e.entity_id AND product_entity.seller_id = '".$vendorId."'",
                    []
                );
            }

            if ($flatMode) {
                if(!isset($fromPart['at_approval'])) {
                    $approvalValue = '('.$this->getAllowState(true).')';
                    $select->join(
                        ['at_approval'=>$this->getTable('catalog_product_entity_int')],
                        "at_approval.entity_id = e.entity_id AND at_approval.attribute_id = '".$this->getIdOfAttributeCode('catalog_product','approval')."'"
                        ." AND at_approval.value IN".$approvalValue." AND at_approval.store_id = '0'", //@todo dont know why need to 0
                        []
                    );
                }
            }
        }
        return $select;
    }

    public function getIdOfAttributeCode($entityCode, $code)
    {
        return \Magento\Framework\App\ObjectManager::getInstance()
            ->get('Magento\Eav\Model\ResourceModel\Entity\Attribute')
            ->getIdByCode($entityCode,$code);
    }
}
