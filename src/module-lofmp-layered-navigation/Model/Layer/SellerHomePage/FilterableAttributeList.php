<?php

namespace Lofmp\LayeredNavigation\Model\Layer\SellerHomePage;

class FilterableAttributeList extends \Magento\Catalog\Model\Layer\Category\FilterableAttributeList
{
    public function getList()
    {
        $collection = parent::getList();
       // $collection->addFieldToFilter('main_table.attribute_code', ['notlike' => '%category_ids%']);

        return $collection;
    }
}