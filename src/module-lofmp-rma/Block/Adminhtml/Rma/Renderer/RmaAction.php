<?php
/**
 * LandOfCoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * https://landofcoder.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   LandOfCoder
 * @package    Lofmp_Rma
 * @copyright  Copyright (c) 2020 Landofcoder (https://landofcoder.com/)
 * @license    https://landofcoder.com/LICENSE-1.0.html
 */

namespace Lofmp\Rma\Block\Adminhtml\Rma\Renderer;

class RmaAction extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\Text
{
    /**
     * @var \Magento\Framework\Url
     */
    protected $_urlBuilder;

    /**
     * RmaAction constructor.
     *
     * @param \Magento\Backend\Block\Context $context
     * @param \Magento\Framework\Url $urlBuilder
     */
    public function __construct(
        \Magento\Backend\Block\Context $context,
        \Magento\Framework\Url $urlBuilder
    ) {
        $this->_urlBuilder = $urlBuilder;
        parent::__construct($context);
    }

    public function _getValue(\Magento\Framework\DataObject $row)
    {
        $editUrl = $this->_urlBuilder->getUrl(
            'rma/rma/edit',
            [
                'id' => $row['rma_id']
            ]
        );
        return sprintf("<a target='_blank' href='%s'>Edit</a>", $editUrl);
    }
}
