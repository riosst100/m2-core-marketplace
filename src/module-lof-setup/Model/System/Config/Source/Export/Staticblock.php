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

class Staticblock implements \Magento\Framework\Option\ArrayInterface
{
    protected  $_blockModel;

    /**
     * @param \Magento\Cms\Model\Block $blockModel
     */
    public function __construct(
        \Magento\Cms\Model\Block $blockModel
    ) {
        $this->_blockModel = $blockModel;
    }

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $collection = $this->_blockModel->getCollection();
        $blocks = array();
        foreach ($collection as $_block) {
            $blocks[] = [
            'value' => $_block->getId(),
            'label' => addslashes($_block->getTitle())
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
        $collection = $this->_blockModel->getCollection();
        $blocks = array();
        foreach ($collection as $_block) {
            $blocks[$_block->getId()] = addslashes($_block->getTitle());
        }
        return $blocks;
    }
}