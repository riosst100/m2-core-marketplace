<?php
/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * http://landofcoder.com/license
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Landofcoder
 * @package    Lofmp_TimeDiscount
 * @copyright  Copyright (c) 2017 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */

namespace Lofmp\TimeDiscount\Ui\Component\Listing\Column;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class TimeDiscount.
 */
class TimeDiscount extends Column
{
    /**
     * @var UrlInterface
     */
    protected $_urlBuilder;

     protected $_productloader;

    protected $helper;
    /**
     * Constructor.
     *
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
         \Magento\Catalog\Model\ProductFactory $_productloader,
        UrlInterface $urlBuilder,
        \Lofmp\TimeDiscount\Helper\Data $helper,
        array $components = [],
        array $data = []
    )
    {
        $this->helper = $helper;
        $this->_productloader = $_productloader;
        $this->_urlBuilder = $urlBuilder;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source.
     *
     * @param array $dataSource
     *
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');
            foreach ($dataSource['data']['items'] as &$item) {
                if (isset($item['data']) && !empty($item['data'])) {
                    $product = $this->_productloader->create()->load($item['product_id']);
                    $data_time = json_decode($item['data'],true);
                    $item[$fieldName] = '';

                    foreach($data_time as  &$data) {
                        if($data['type'] == 'percent') {
                            $discount = $data['discount'].'%';
                        } else {
                            $discount = $this->helper->getPriceFomat($product->getPrice() - $data['discount']);
                        }
                        $item[$fieldName] .= "<div><span>".__('Time Start').":".$data['start']."</span> -- <span>".__('Time End').":".$data['end']."</span> -- <span>Discount:".$discount."</span></div>";
                    }

                }
            }
        }
        return $dataSource;
    }
}
