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

namespace Lof\MarketPermissions\Controller\Marketplace\Customer;

use Magento\Framework\Controller\ResultFactory;

/**
 * Class Manage.
 */
class Manage extends \Lof\MarketPermissions\Controller\Marketplace\AbstractAction
{
    /**
     * {@inheritdoc}
     * @throws \InvalidArgumentException
     */
    public function execute()
    {
        $customerId = $this->getRequest()->getParam('customer_id');
        $action = (int)$customerId ? 'save' : 'create';

        /** @var \Magento\Framework\Controller\Result\Forward $result */
        $result = $this->resultFactory->create(ResultFactory::TYPE_FORWARD);

        return $result->forward($action);
    }
}
