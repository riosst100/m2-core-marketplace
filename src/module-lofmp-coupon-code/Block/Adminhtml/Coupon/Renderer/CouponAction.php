<?php
/**
 * Venustheme
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Venustheme.com license that is
 * available through the world-wide-web at this URL:
 * https://landofcoder.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category   Venustheme
 * @package    Ves_Blog
 * @copyright  Copyright (c) 2016 Venustheme (https://landofcoder.com/)
 * @license    https://landofcoder.com/LICENSE-1.0.html
 */
namespace Lofmp\CouponCode\Block\Adminhtml\Coupon\Renderer;
use Magento\Framework\UrlInterface;

class CouponAction extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\Text
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
                                'lofmpcouponcode/coupon/edit',
                                [
                                    'couponcode_id' => $row['couponcode_id']
                                ]
                            );
		return sprintf("<a target='_blank' href='%s'>Edit</a>", $editUrl);
	}
}