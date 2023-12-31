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
namespace Lof\Setup\Model\System\Config\Source\Export;

class Cmspage implements \Magento\Framework\Option\ArrayInterface
{
    protected  $_pageModel;

    /**
     * @param \Magento\Cms\Model\Page $pageModel
     */
    public function __construct(
        \Magento\Cms\Model\Page $pageModel
    ) {
        $this->_pageModel = $pageModel;
    }

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $collection = $this->_pageModel->getCollection();
        $blocks = array();
        foreach ($collection as $_page) {
            $blocks[] = [
            'value' => $_page->getId(),
            'label' => addslashes($_page->getTitle())
            ];
        }
        return $blocks;
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