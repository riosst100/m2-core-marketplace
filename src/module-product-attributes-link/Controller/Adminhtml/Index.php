<?php
declare(strict_types=1);

namespace CoreMarketplace\ProductAttributesLink\Controller\Adminhtml;

use Magento\Backend\App\Action;

abstract class Index extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'CoreMarketplace_ProductAttributesLink::product_attributes_link';

    /**
     * @param Action\Context $context
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context
    ) {
        parent::__construct($context);
    }
}
