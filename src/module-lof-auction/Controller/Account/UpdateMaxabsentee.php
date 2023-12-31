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
 * @package    Lof_Auction
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lof\Auction\Controller\Account;

use Exception;
use Lof\Auction\Helper\Data;
use Lof\Auction\Helper\Email;
use Lof\Auction\Helper\History as HistoryHelper;
use Lof\Auction\Model\AmountFactory;
use Lof\Auction\Model\AutoAuctionFactory;
use Lof\Auction\Model\HistoryFactory;
use Lof\Auction\Model\ProductFactory;
use Lof\Auction\Api\MaxAbsenteeBidRepositoryInterface;
use Lof\Auction\Model\ResourceModel\MixAmount\CollectionFactory as MixAmountCollectionFactory;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Customer\Controller\AbstractAccount;
use Magento\Customer\Model\Session;
use Magento\Directory\Model\CurrencyFactory;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class UpdateMaxabsentee
 * Lof\Auction\Controller\Account
 */
class UpdateMaxabsentee extends AbstractAccount
{
    /**
     * @var Session
     */
    protected $_customerSession;

    /**
     * @var PageFactory
     */
    protected $_resultPageFactory;

    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var ProductFactory
     */
    protected $_auctionProductFactory;

    /**
     * @var Data
     */
    protected $_helperData;

    /**
     * @var MaxAbsenteeBidRepositoryInterface
     */
    protected $_maxAbsenteeBidRepository;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param Session $customerSession
     * @param StoreManagerInterface $storeManager
     * @param Data $helperData
     * @param MaxAbsenteeBidRepositoryInterface $maxAbsenteeBidRepository
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Session $customerSession,
        StoreManagerInterface $storeManager,
        Data $helperData,
        MaxAbsenteeBidRepositoryInterface $maxAbsenteeBidRepository
    ) {
        $this->_customerSession = $customerSession;
        $this->_resultPageFactory = $resultPageFactory;
        $this->_storeManager = $storeManager;
        $this->_helperData = $helperData;
        $this->_maxAbsenteeBidRepository = $maxAbsenteeBidRepository;
        parent::__construct($context);
    }

    /**
     * Check customer authentication
     *
     * @param RequestInterface $request
     * @return ResponseInterface
     * @throws NotFoundException
     */
    public function dispatch(RequestInterface $request)
    {
        $loginUrl = $this->_objectManager->get('Magento\Customer\Model\Url')->getLoginUrl();
        if (!$this->_customerSession->authenticate($loginUrl)) {
            $this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);
        }
        return parent::dispatch($request);
    }

    /**
     * Default customer account page
     * @return Redirect $resultRedirect
     * @throws NoSuchEntityException
     * @throws Exception
     * @var $cuntCunyCode current Currency Code
     * @var $baseCunyCode base Currency Code
     */
    public function execute()
    {
        $data = $this->getRequest()->getParams();
        $data['current_url'] = isset($data['current_url']) ? $data['current_url'] : $this->_storeManager->getStore()->getBaseUrl();
        $auctionIds = isset($data["auction_id"]) ? $data["auction_id"] : [];
        $maxBidAmounts = isset($data["max_absentee_bid"]) ? $data["max_absentee_bid"] : [];
        $customerId = $this->_customerSession->getCustomerId();
        if ($auctionIds && $customerId && $maxBidAmounts && $this->_helperData->isEnabled() && $this->_helperData->getConfig("auto/enable_max_absentee_bid")) {
            try {
                $count = 0;
                foreach ($auctionIds as $auctionId) {
                    if (isset($maxBidAmounts[$auctionId]) && $maxBidAmounts[$auctionId]) {
                        $this->_maxAbsenteeBidRepository->setMaxBidAmount($customerId, $auctionId, (float)$maxBidAmounts[$auctionId]);
                        $count++;
                    }
                }
                
                $this->messageManager->addSuccess(__('Your max abstentee bid amount for %1 auction(s) successfully saved.', $count));
            } catch (\Exception $e) {
                // display error message
                $this->messageManager->addError($e->getMessage());
            }
        }
        $this->_redirect($data['current_url']);
        return;
    }
}
