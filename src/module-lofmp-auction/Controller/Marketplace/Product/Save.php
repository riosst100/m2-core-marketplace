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
 * @package    Lofmp_Auction
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lofmp\Auction\Controller\Marketplace\Product ;

use Exception;
use Lof\Auction\Helper\Data;
use Lof\Auction\Helper\ProcessWinner;
use Lof\Auction\Model\ProductFactory;
use Lof\Auction\Model\ResourceModel\Product\Source\AuctionStatus;
use Lof\MarketPlace\Model\SellerFactory;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ActionFlag;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\Url;
use Magento\Framework\View\Result\PageFactory;

class Save extends Action
{
    const FLAG_IS_URLS_CHECKED = 'check_url_settings';

    /**
     * @var Session
     */
    protected $session;

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var SellerFactory
     */
    protected $sellerFactory;

    /**
     * @var Url
     */
    protected $_frontendUrl;

    /**
     * @var ActionFlag
     */
    protected $_actionFlag;

    /**
     * @var Data
     */
    protected $_assignHelper;

    /**
     * @var ProductFactory
     */
    private $auctionFactory;

    /**
     * @var TimezoneInterface
     */
    private $timezone;

    /**
     * @var ProcessWinner
     */
    private $_processWinnerHelper;

    /**
     *
     * @param Context $context
     * @param Session $customerSession
     * @param SellerFactory $sellerFactory
     * @param Url $frontendUrl
     * @param Data $helper
     * @param PageFactory $resultPageFactory
     * @param ProductFactory $auctionFactory
     * @param TimezoneInterface $timezone
     * @param ProcessWinner $processWinner
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        SellerFactory $sellerFactory,
        Url $frontendUrl,
        Data $helper,
        PageFactory $resultPageFactory,
        ProductFactory $auctionFactory,
        TimezoneInterface $timezone,
        ProcessWinner $processWinner
    ) {
        parent::__construct($context);
        $this->_assignHelper = $helper;
        $this->_frontendUrl = $frontendUrl;
        $this->_actionFlag = $context->getActionFlag();
        $this->sellerFactory = $sellerFactory;
        $this->session = $customerSession;
        $this->resultPageFactory = $resultPageFactory;
        $this->auctionFactory = $auctionFactory;
        $this->timezone = $timezone;
        $this->_processWinnerHelper = $processWinner;
    }

    /**
     * @param string $route
     * @param array $params
     * @return string|null
     */
    public function getFrontendUrl($route = '', $params = [])
    {
        return $this->_frontendUrl->getUrl($route, $params);
    }

    /**
     * Redirect to URL
     * @param string $url
     * @return ResponseInterface
     */
    protected function _redirectUrl($url)
    {
        $this->getResponse()->setRedirect($url);
        $this->session->setIsUrlNotice($this->_actionFlag->get('', self::FLAG_IS_URLS_CHECKED));
        return $this->getResponse();
    }

    /**
     * Customer login form page
     *
     * @return Redirect
     * @throws Exception
     */
    public function execute()
    {
        $customerSession = $this->session;
        $customerId = $customerSession->getId();
        $seller = $this->sellerFactory->create()->load($customerId, 'customer_id');
        $resultRedirect = $this->resultRedirectFactory->create();
        $status = $seller->getStatus();

        if ($customerSession->isLoggedIn() && $status == 1) {
            $id = $this->getRequest()->getParam('id');
            $auction = $this->auctionFactory->create();
            if ($id) {
                $auction->load($id);
            }
            if ($id && (!$auction->getId() || $auction->getSellerId() != $seller->getData('seller_id'))
            ) {
                $this->messageManager->addError(
                    __("This auction does not exist or you don't have permission to save this auction.")
                );
                return $this->_redirect('*/*');
            }
            $data = $this->getRequest()->getPostValue();
            if ($data && $this->validate($data)) {
                if ($this->getRequest()->getParam('back')) {
                    $resultRedirect->setPath(
                        '*/*/edit',
                        ['id' => $this->getRequest()->getParam('auction_entity_id')]
                    );
                }
                if (isset($data['auction_entity_id']) && $data['auction_entity_id']) {
                    $data['entity_id'] = $data['auction_entity_id'];
                } else {
                    unset($data['entity_id']);
                }

                if (! isset($data['entity_id']) && (! isset($data['product_id']) || ! $data['product_id'])) {
                    $this->session->setFormData($data);
                    $this->messageManager->addErrorMessage(__('Please choose a product.'));
                    return $resultRedirect;
                }

                $data['stop_auction_time'] = $this->_assignHelper->convertToTz(
                    $data['stop_auction_time'],
                    $this->timezone->getDefaultTimezone(),
                    $this->timezone->getConfigTimezone()
                );

                $data['start_auction_time'] = $this->_assignHelper->convertToTz(
                    $data['start_auction_time'],
                    $this->timezone->getDefaultTimezone(),
                    $this->timezone->getConfigTimezone()
                );

                $data['seller_id'] = $seller->getData('seller_id');

                if (! isset($data['max_amount']) || ! $data['max_amount']) {
                    $data['max_amount'] = null;
                }

                if (! isset($data['reserve_price']) || ! $data['reserve_price']) {
                    $data['reserve_price'] = null;
                }

                if ($auction->getProductId()) {
                    $data['product_id'] = $auction->getProductId();
                }

                $auction->setData($data);
                $auction->setAuctionStatus(AuctionStatus::STATUS_PROCESS);
                $auction->setStatus(0);

                if ($this->findAuction($data['product_id'], $id)) {
                    unset($data['product_id']);
                    $this->session->setFormData($data);
                    $this->messageManager->addErrorMessage(
                        __("Cannot save auction because there is another auction running with this product already")
                    );
                    return $resultRedirect->setPath('*/*/');
                }

                try {
                    $auction->save();
                    if ($this->_processWinnerHelper->checkIsStopDateEnd($auction->getStopAuctionTime())) {
                        $this->_processWinnerHelper->processFindWinner($auction->getProductId(), $auction);
                    }
                    $this->session->unsFormData();
                    $this->messageManager->addSuccessMessage(__('You saved this product.'));
                    $this->_eventManager->dispatch(
                        'lof_auction_controller_action_auction_product_save_entity_after',
                        ['controller' => $this, 'auction' => $auction]
                    );

                    return $resultRedirect->setPath('*/*/edit', ['id' => $auction->getEntityId()]);

                } catch (\Magento\Framework\Exception\LocalizedException $e) {
                    $this->messageManager->addErrorMessage($e->getMessage());
                } catch (\RuntimeException $e) {
                    $this->messageManager->addErrorMessage($e->getMessage());
                } catch (Exception $e) {
                    $this->messageManager->addExceptionMessage(
                        $e,
                        __('Something went wrong while saving the auction.')
                    );
                }

                if (isset($data['product_id'])) {
                    unset($data['product_id']);
                }

                $this->session->setFormData($data);
                return $resultRedirect->setPath('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);
            }

            if ($this->getRequest()->getParam('back')) {
                if (isset($data['product_id']) && !isset($data['auction_entity_id'])) {
                    unset($data['product_id']);
                }
            }
            $this->session->setFormData($data);
            return $resultRedirect->setUrl($this->_redirect->getRefererUrl());
        } elseif ($customerSession->isLoggedIn() && $status == 0) {
            $this->_redirectUrl($this->getFrontendUrl('lofmarketplace/seller/becomeseller'));
        } else {
            $this->messageManager->addNotice(__('You must have a seller account to access'));
            $this->_redirectUrl($this->getFrontendUrl('lofmarketplace/seller/login'));
        }
    }

    /**
     * @param $data
     * @return bool
     */
    public function validate($data)
    {
        if (!isset($data['starting_price']) || !isset($data['start_auction_time'])
            || !isset($data['stop_auction_time']) || !isset($data['days']) || !isset($data['days'])
            || !isset($data['min_qty']) || !isset($data['max_qty'])
        ) {
            $this->messageManager->addErrorMessage(__('Please filter data.'));
            return false;
        }
        if (isset($data['max_amount']) && !!$data['max_amount'] && $data['max_amount'] < $data['starting_price']) {
            $this->messageManager->addErrorMessage(__('Starting price must be less than max price.'));
            return false;
        }
        if (isset($data['max_amount']) && !!$data['max_amount'] && isset($data['reserve_price'])
            && $data['max_amount'] <= $data['reserve_price']) {
            $this->messageManager->addErrorMessage(__('Max price must be greater than reserve price.'));
            return false;
        }
        if (strtotime($data['start_auction_time']) >= strtotime($data['stop_auction_time'])) {
            $this->messageManager->addErrorMessage(__('Start auction time must be less than stop auction time.'));
            return false;
        }
        if ($data['min_qty'] >= $data['max_qty']) {
            $this->messageManager->addErrorMessage(__('Minimum quantity must be less than maximum quantity.'));
            return false;
        }
        return true;
    }

    /**
     * @param $productId
     * @param int $id
     * @return bool
     */
    public function findAuction($productId, $id = 0)
    {
        $auctionCollection = $this->auctionFactory->create()
            ->getCollection()
            ->addFieldToFilter(
                'auction_status',
                [
                    "in" => [
                        AuctionStatus::STATUS_WINNER_NOT_DEFINED,
                        AuctionStatus::STATUS_PROCESS,
                        AuctionStatus::STATUS_TIME_END,
                    ],
                ]
            )
            ->addFieldToFilter('product_id', $productId)
            ->addFieldToFilter('entity_id', ['neq' => $id]);
        return $auctionCollection->getData() ? true : false;
    }
}
