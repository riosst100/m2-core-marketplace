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
 * @package    Lofmp_SellerBadge
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lofmp\SellerBadge\Controller\Adminhtml\SellerBadge;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\Cache\Manager as CacheManager;
use Magento\Framework\App\Cache\TypeListInterface as CacheTypeListInterface;
use Magento\Framework\View\Result\PageFactory;

class FlushCache extends Action
{

    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Lofmp_SellerBadge::SellerBadge_update';

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var CacheTypeListInterface
     */
    protected $cache;

    /**
     * @var CacheManager
     */
    protected $cacheManager;

    /**
     * FlushCache constructor.
     *
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param CacheTypeListInterface $cache
     * @param CacheManager $cacheManager
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        CacheTypeListInterface $cache,
        CacheManager $cacheManager
    ) {
        parent::__construct($context);
        $this->cache = $cache;
        $this->cacheManager = $cacheManager;
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * @return Redirect
     */
    public function execute()
    {
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        $id = $this->getRequest()->getParam('badge_id');
        if ($id) {
            try {
                /* Invalidate Full Page Cache */
                $this->cacheManager->clean([
                    'layout',
                    'block_html',
                    'full_page',
                    'config',
                    'eav',
                    'translate',
                    'db_ddl',
                    'collections',
                    'compiled_config',
                    'customer_notification',
                    'config_webservice',
                    'vertex',
                    'reflection',
                    'config_integration',
                    'config_integration_api'
                ]);

                $this->messageManager->addSuccessMessage(__('Cache has been cleaned.'));

                return $resultRedirect->setPath('*/*/edit', ['badge_id' => $id]);
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());

                return $resultRedirect->setPath('*/*/edit', ['badge_id' => $id]);
            }
        } else {
            $this->messageManager->addErrorMessage(__('We can\'t find a Seller Badge to clean cache.'));
        }

        return $resultRedirect->setPath('*/*/edit', ['badge_id' => $id]);
    }
}
