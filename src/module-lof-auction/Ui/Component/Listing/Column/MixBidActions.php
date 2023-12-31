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

namespace Lof\Auction\Ui\Component\Listing\Column;

use Lof\Auction\Helper\Data;
use Lof\Auction\Model\MixAmountFactory;
use Lof\Auction\Model\ProductFactory;
use Lof\Auction\Model\ResourceModel\Product\Source\AuctionStatus;
use Magento\Cms\Block\Adminhtml\Page\Grid\Renderer\Action\UrlBuilder;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class MixBidActions
 */
class MixBidActions extends Column
{
    /** Url path */
    const CMS_URL_PATH_SET_WINNING = 'lofauction/mixamount/setwinning';

    /** @var UrlBuilder */
    protected $actionUrlBuilder;

    /** @var UrlInterface */
    protected $urlBuilder;
    /**
     * @var string
     */
    private $winningUrl;
    /**
     * @var ProductFactory
     */
    protected $auction;
    /**
     * @var Data
     */
    protected $_helperData;
    /**
     * @var MixAmountFactory
     */
    protected $mixAmountFactory;
    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlBuilder $actionUrlBuilder
     * @param UrlInterface $urlBuilder
     * @param ProductFactory $auction
     * @param Data $helperData
     * @param MixAmountFactory $mixAmountFactory
     * @param CustomerRepositoryInterface $customerRepository
     * @param array $components
     * @param array $data
     * @param string $winningUrl
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlBuilder $actionUrlBuilder,
        UrlInterface $urlBuilder,
        ProductFactory $auction,
        Data $helperData,
        MixAmountFactory $mixAmountFactory,
        CustomerRepositoryInterface $customerRepository,
        array $components = [],
        array $data = [],
        $winningUrl = self::CMS_URL_PATH_SET_WINNING
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->actionUrlBuilder = $actionUrlBuilder;
        $this->winningUrl = $winningUrl;
        $this->auction = $auction;
        $this->_helperData = $helperData;
        $this->mixAmountFactory = $mixAmountFactory;
        $this->customerRepository = $customerRepository;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $name = $this->getData('name');
                if (isset($item['entity_id'])) {
                    $mixModel = $this->mixAmountFactory->create()->load($item['entity_id']);
                    if ($item['winning_status'] == "PENDING") {
                        $auction_id = $item['auction_id'];
                        if ($auction_id) {
                            $auction = $this->auction->create()->load($auction_id);
                            if ($auction->getId() && in_array(
                                $auction->getAuctionStatus(),
                                [AuctionStatus::STATUS_PROCESS, AuctionStatus::STATUS_WINNER_NOT_DEFINED]
                            )) {
                                $resPrice = $auction->getReservePrice();
                                $starPrice = $auction->getStartingPrice();
                                $resPrice = $resPrice ? $resPrice : $starPrice;
                                $bidAmount = $mixModel->getAmount();
                                $customer = $this->customerRepository->getById($mixModel->getCustomerId());
                                $customerName = $customer->getFirstname() . ' ' . $customer->getLastname();
                                if ($bidAmount && ($resPrice <= $bidAmount)) {
                                    $item[$name]['setwinning'] = [
                                        'href' => $this->urlBuilder->getUrl(
                                            $this->winningUrl,
                                            [
                                                'id' => $item['entity_id']
                                            ]
                                        ),
                                        'label' => __('Set Win'),
                                        'confirm' => [
                                            'title' => __(
                                                'Set %1 to win auction %2"',
                                                $customerName,
                                                $mixModel->getAuctionId()
                                            ),
                                            'message' => __(
                                                'Are you sure you wan\'t to set %1 to win auction %2?',
                                                $customerName,
                                                $mixModel->getAuctionId()
                                            )
                                        ]
                                    ];
                                }
                            }
                        }
                    }
                }
            }
        }
        return $dataSource;
    }
}
