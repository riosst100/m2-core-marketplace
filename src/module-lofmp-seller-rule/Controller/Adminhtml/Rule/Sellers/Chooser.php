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
 * @package    Lofmp_SellerRule
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lofmp\SellerRule\Controller\Adminhtml\Rule\Sellers;

use Lofmp\SellerRule\Block\Adminhtml\Rule\Edit\Tab\SellerConditions\SpecifiedGrid;
use Magento\Backend\App\Action;

class Chooser extends Action
{
    public function execute()
    {
        $request = $this->getRequest();
        switch ($request->getParam('choose_type')) {
            case 'specified':
                $block = $this->_view->getLayout()->createBlock(
                    SpecifiedGrid::class,
                    'rule_sellers_chooser_specified',
                    ['data' => ['js_form_object' => $request->getParam('form')]]
                );
                break;
            default:
                $block = false;
                break;
        }
        if ($block) {
            $this->getResponse()->setBody($block->toHtml());
        }
    }
}
