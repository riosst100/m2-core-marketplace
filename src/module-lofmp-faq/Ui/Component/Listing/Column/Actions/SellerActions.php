<?php
/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * http://landofcoder.com/license
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Landofcoder
 * @package    Lofmp_Faq
 * @copyright  Copyright (c) 2017 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */

namespace Lofmp\Faq\Ui\Component\Listing\Column\Actions;

use Lofmp\Faq\Ui\Component\Listing\Column\Actions as AbstractAction;

class SellerActions extends AbstractAction
{
    /** Url path */
    protected $urlPathEnable = 'lofmpfaq/seller/enable';
    protected $urlPathDisable = 'lofmpfaq/seller/disable';
    protected $idFieldName = 'seller_id';

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $name = $this->getData('name');
                if (isset($item["$this->idFieldName"])) {
                    if($item['faq_status'] == 0) {
                        $item[$name]['enable'] = [
                            'href' => $this->urlBuilder->getUrl($this->urlPathEnable, ['id' => $item["$this->idFieldName"]]),
                            'label' => __('Enable FAQ')
                        ];
                    }
                    else {
                        $item[$name]['disable'] = [
                            'href' => $this->urlBuilder->getUrl($this->urlPathDisable, ['id' => $item["$this->idFieldName"]]),
                            'label' => __('Disable FAQ')
                        ];
                    }
                }
            }
        }

        return $dataSource;
    }
}
