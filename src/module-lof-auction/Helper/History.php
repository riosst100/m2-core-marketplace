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

namespace Lof\Auction\Helper;
use Exception;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\DataObject;
use Lof\Auction\Model\HistoryFactory;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;
/**
 * Class History
 * @package Lof\Auction\Helper
 */
class History extends AbstractHelper
{
    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var HistoryFactory
     */
    private $_historyFactory;

    /**
     * @var RemoteAddress
     */
    protected $_remoteAddress;


    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        HistoryFactory $historyFactory,
        RemoteAddress $remoteAddress
    ) {
        $this->_storeManager = $storeManager;
        $this->_historyFactory = $historyFactory;
        $this->_remoteAddress       = $remoteAddress;
        parent::__construct($context);
    }
    /**
     * @param $bidCusrRecord
     * @throws Exception
     */
    public function saveHistory($bidCusrRecord = null, $browser = null)
    {
        if($bidCusrRecord){
            $bidCusrRecord->load($bidCusrRecord->getId());
            $data = $bidCusrRecord->getData();
            $dataHistory['status'] = 1;
            $dataHistory['auction_id'] = $data['auction_id'];
            if (isset($data['auction_amount'])) {
                $dataHistory['is_auto'] = 0;
                $dataHistory['auction_amount'] = $data['auction_amount'];
                $dataHistory['amount_id'] = $data['entity_id'];
            } else {
                $dataHistory['is_auto'] = 1;
                $dataHistory['auction_amount'] = $data['amount'];
                $dataHistory['auto_amount_id'] = $data['entity_id'];
            }
            $dataHistory['amount_id'] = $data['entity_id'];
            $dataHistory['customer_id'] = $data['customer_id'];
            $dataHistory['created_at'] = $data['created_at'];

            $client_ip = $this->_remoteAddress->getRemoteAddress();
            if($browser) {
                if (is_array($browser)) {
                    $tmp = "";
                    foreach ($browser as $key => $val) {
                        $val = $this->xss_clean($val);
                        $key = $this->xss_clean($key);
                        $tmp .= $key.": ".$val."\n";
                    }
                    $browser = $tmp;
                } else {
                    $browser = $this->xss_clean($browser);
                }
                $dataHistory["customer_browser"] = $browser;
            }
            $dataHistory["customer_ip"] = $client_ip;

            $history = $this->_historyFactory->create();
            $history->setData($dataHistory);
            $history->save();

            return $history;
        }
    }

    /**
     * @param $data
     * @return string|string[]|null
     */
    public function xss_clean($data)
    {
        if (!is_string($data)) {
            return $data;
        }
        // Fix &entity\n;
        $data = str_replace(['&amp;','&lt;','&gt;'], ['&amp;amp;','&amp;lt;','&amp;gt;'], $data);
        $data = preg_replace('/(&#*\w+)[\x00-\x20]+;/u', '$1;', $data);
        $data = preg_replace('/(&#x*[0-9A-F]+);*/iu', '$1;', $data);
        $data = html_entity_decode($data, ENT_COMPAT, 'UTF-8');

        // Remove any attribute starting with "on" or xmlns
        $data = preg_replace('#(<[^>]+?[\x00-\x20"\'])(?:on|xmlns)[^>]*+>#iu', '$1>', $data);

        // Remove javascript: and vbscript: protocols
        $data = preg_replace('#([a-z]*)[\x00-\x20]*=[\x00-\x20]*([`\'"]*)[\x00-\x20]*j[\x00-\x20]*a[\x00-\x20]*v[\x00-\x20]*a[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2nojavascript...', $data);
        $data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*v[\x00-\x20]*b[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2novbscript...', $data);
        $data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*-moz-binding[\x00-\x20]*:#u', '$1=$2nomozbinding...', $data);

        // Only works in IE: <span style="width: expression(alert('Ping!'));"></span>
        $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?expression[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
        $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?behaviour[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
        $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:*[^>]*+>#iu', '$1>', $data);

        // Remove namespaced elements (we do not need them)
        $data = preg_replace('#</*\w+:\w[^>]*+>#i', '', $data);

        do {
            // Remove really unwanted tags
            $old_data = $data;
            $data = preg_replace('#</*(?:applet|b(?:ase|gsound|link)|embed|frame(?:set)?|i(?:frame|layer)|l(?:ayer|ink)|meta|object|s(?:cript|tyle)|title|xml)[^>]*+>#i', '', $data);
        } while ($old_data !== $data);

        // we are done...
        return $data;
    }
}