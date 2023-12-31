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
 * @package    Lof_MarketPlace
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lof\MarketPlace\Controller\Marketplace\Index\Render;

use Magento\Framework\View\Element\Template;
use Magento\Ui\Component\Control\ActionPool;
use Magento\Ui\Component\Wrapper\UiComponent;
use Lof\MarketPlace\Controller\Marketplace\AbstractAction;

class Handle extends AbstractAction
{
    /**
     * Render UI component by namespace in handle context
     *
     * @return void
     */
    public function execute()
    {
        $handle = $this->_request->getParam('handle');
        $namespace = $this->_request->getParam('namespace');
        $buttons = $this->_request->getParam('buttons', false);

        $this->_view->loadLayout(['default', $handle], true, true, false);

        $uiComponent = $this->_view->getLayout()->getBlock($namespace);
        $response = $uiComponent instanceof UiComponent ? $uiComponent->toHtml() : '';

        if ($buttons) {
            $actionsToolbar = $this->_view->getLayout()->getBlock(ActionPool::ACTIONS_PAGE_TOOLBAR);
            $response .= $actionsToolbar instanceof Template ? $actionsToolbar->toHtml() : '';
        }

        $this->_response->appendBody($response);
    }
}
