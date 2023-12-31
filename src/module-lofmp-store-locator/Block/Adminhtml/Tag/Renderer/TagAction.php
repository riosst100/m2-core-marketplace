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
 * @package    Lofmp_StoreLocator
 * @copyright  Copyright (c) 2016 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */
namespace Lofmp\StoreLocator\Block\Adminhtml\Tag\Renderer;
use Magento\Framework\UrlInterface;

class TagAction extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\Text
{

    /**
     * @var Magento\Framework\UrlInterface
     */
    protected $_urlBuilder;

    /**
     * @param \Magento\Backend\Block\Context
     * @param UrlInterface
     */
    public function __construct(
        \Magento\Backend\Block\Context $context,
        \Magento\Framework\Url $urlBuilder
        ){
        $this->_urlBuilder = $urlBuilder;
        parent::__construct($context);
    }

    public function _getValue(\Magento\Framework\DataObject $row){
        $editUrl = $this->_urlBuilder->getUrl(
                                'storelocator/tag/edit',
                                [
                                    'tag_id' => $row['tag_id']
                                ]
                            );
        return sprintf("<a target='_blank' href='%s'>Edit</a>", $editUrl);
    }
}