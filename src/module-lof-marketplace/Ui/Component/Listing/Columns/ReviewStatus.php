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

namespace Lof\MarketPlace\Ui\Component\Listing\Columns;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

class ReviewStatus extends Column
{
    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var \Magento\Review\Model\Review
     */
    protected $review;

    /**
     * @var \Lof\MarketPlace\Model\Review
     */
    protected $_review;

    /**
     * ReviewStatus constructor.
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param \Magento\Review\Model\Review $review
     * @param \Lof\MarketPlace\Model\Review $_review
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        \Magento\Review\Model\Review $review,
        \Lof\MarketPlace\Model\Review $_review,
        array $components = [],
        array $data = []
    ) {
        $this->_review = $_review;
        $this->review = $review;
        $this->urlBuilder = $urlBuilder;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');

            foreach ($dataSource['data']['items'] as &$item) {
                if (isset($item['status'])) {
                    $status_id = $item['status'];
                    if ($status_id == 1) {
                        $status = __('Approved');
                    } elseif ($status_id == 2) {
                        $status = __('Pending');
                    } elseif ($status_id == 3) {
                        $status = __('Unapproved');
                    } else {
                        $status = __('Undefined');
                    }
                    $item[$fieldName] = $status;
                }
            }
        }

        return $dataSource;
    }
}
