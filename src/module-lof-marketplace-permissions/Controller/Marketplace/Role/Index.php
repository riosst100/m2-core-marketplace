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
 * @package    Lof_MarketPermissions
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lof\MarketPermissions\Controller\Marketplace\Role;

use Magento\Framework\App\Action\HttpGetActionInterface as HttpGetActionInterface;

class Index extends \Lof\MarketPermissions\Controller\Marketplace\AbstractAction implements HttpGetActionInterface
{
    /**
     * Authorization level of a seller session.
     */
    const SELLER_RESOURCE = 'Lof_MarketPermissions::roles_view';

    /**
     * Roles and permissions grid.
     *
     * @return void
     * @throws \RuntimeException
     */
    public function execute()
    {
        $this->_view->loadLayout();
        $this->_view->loadLayoutUpdates();
        $this->_view->getPage()->getConfig()->getTitle()->set(__('Roles and Permissions'));
        $this->_view->renderLayout();
    }
}
