<?php
/**
 * LandOfCoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * https://landofcoder.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   LandOfCoder
 * @package    Lofmp_Rma
 * @copyright  Copyright (c) 2021 Landofcoder (https://landofcoder.com/)
 * @license    https://landofcoder.com/LICENSE-1.0.html
 */



namespace Lofmp\Rma\Helper;

class Message extends \Magento\Framework\App\Helper\AbstractHelper
{

    public function __construct(
        \Magento\Framework\Registry                              $registry,
        \Magento\Framework\Api\SearchCriteriaBuilder             $searchCriteriaBuilder,
        \Lofmp\Rma\Api\Repository\QuickResponseRepositoryInterface $quickResponseRepository,
        \Lofmp\Rma\Model\QuickResponseFactory                      $responseFactory,
        \Lof\MarketPlace\Model\SellerFactory                           $sellerFactory,
        \Magento\Customer\Model\CustomerFactory                               $customerFactory,
        \Magento\User\Model\UserFactory $userFactory,
        \Magento\Store\Api\StoreRepositoryInterface                   $storeRepository,
        \Magento\Framework\Pricing\Helper\Data $pricingHelper,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Framework\App\Helper\Context                    $context
    ) {
        $this->registry                = $registry;
        $this->searchCriteriaBuilder   = $searchCriteriaBuilder;
        $this->quickResponseRepository = $quickResponseRepository;
        $this->responseFactory         = $responseFactory;
        $this->pricingHelper           = $pricingHelper;
        $this->eavConfig               = $eavConfig;
        $this->storeRepository         = $storeRepository;
        $this->customerFactory         = $customerFactory;
        $this->userFactory             = $userFactory;
        $this->sellerFactory           = $sellerFactory;
        $this->context                 = $context;

        parent::__construct($context);
    }

    /**
     * @return \Lofmp\Rma\Api\Data\RmaInterface
     */
    public function getRma()
    {
        return $this->registry->registry('current_rma');
    }

    /**
     * @return \Lofmp\Rma\Api\Data\QuickResponseInterface[]
     */
    public function getOptionsList()
    {
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter('is_active', 1)
        ;

        $items = [
            $this->responseFactory->create()->setId(0)->setName(__('-- Please Select --'))
        ];
        $items = array_merge($items, $this->quickResponseRepository->getList($searchCriteria->create())->getItems());
        foreach ($items as $response) {
            $response->setTemplate($this->parseTemplate($response));
        }

        return $items;
    }

    /**
     * @param \Lofmp\Rma\Model\QuickResponse $response
     * @return string
     */
    public function parseTemplate(\Lofmp\Rma\Model\QuickResponse $response)
    {
        $template = $response->getTemplate();
        $rma = $this->getRma();
        if ($rma) {
             $rmaUrl = $this->_urlBuilder->getUrl('rma/rma/view', ['id' => $rma->getId(), '_nosid' => true]);
            $data = [
                'rma'      => $rma,
                'store'    => $this->storeRepository->getById($rma->getStoreId()),
                'customer' => $this->customerFactory->create()->load($rma->getCustomerId()),
                'user'     => $this->userFactory->create()->load($rma->getUserId()),
                'seller'     => $this->sellerFactory->create()->load($rma->getSellerId()),
            ];
            $template = $this->parse($template, $data);
        }

        return $template;
    }

    public function parse($str, $objects, $additional = [], $storeId = false)
    {
        if (trim($str) == '') {
            return $str;
        }

        $bAOpen = '[ZZZZZ';
        $bAClose = 'ZZZZZ]';
        $bBOpen = '{WWWWW';
        $bBClose = 'WWWWW}';

        $str = str_replace('[', $bAOpen, $str);
        $str = str_replace(']', $bAClose, $str);
        $str = str_replace('{', $bBOpen, $str);
        $str = str_replace('}', $bBClose, $str);

        $pattern = '/\[ZZZZZ[^ZZZZZ\]]*ZZZZZ\]/';

        preg_match_all($pattern, $str, $matches, PREG_SET_ORDER);

        $vars = [];
        foreach ($matches as $match) {
            $vars[$match[0]] = $match[0];
        }

        foreach ($objects as $key => $object) {
            $data = $object->getData();
            if (isset($additional[$key])) {
                $data = array_merge($data, $additional[$key]);
            }

            foreach ($data as $dataKey => $value) {
                if (is_array($value) || is_object($value)) {
                    continue;
                }

                $kA = $bBOpen . $key . '_' . $dataKey . $bBClose;
                $kB = $bAOpen . $key . '_' . $dataKey . $bAClose;
                $skip = true;

                foreach ($vars as $k => $v) {
                    if (stripos($v, $kA) !== false || stripos($v, $kB) !== false) {
                        $skip = false;
                        break;
                    }
                }

                if ($skip) {
                    continue;
                }

                if ($key == 'product' || $key == 'category') {
                    if ($key == 'product') {
                        $attribute = $this->eavConfig->getAttribute(\Magento\Catalog\Model\Product::ENTITY, $dataKey);
                    } else {
                        $attribute = $this->eavConfig->getAttribute(\Magento\Catalog\Model\Category::ENTITY, $dataKey);
                    }

                    if ($storeId) {
                        $attribute->setStoreId($storeId);
                    }

                    if ($attribute->getId() > 0) {
                        try {
                            $valueId = $object->getDataUsingMethod($dataKey);
                            $value = $attribute->getFrontend()->getValue($object);
                        } catch (\Exception $e) {
                            $value = '';
                        }

                        if ($value == 'No' && $valueId == '') {
                            $value = '';
                        }

                        switch ($dataKey) {
                            case 'price':
                                $value = $this->pricingHelper->currency($value, true, false);
                                break;
                            case 'special_price':
                                $value = $this->pricingHelper->currency($value, true, false);
                                break;
                        }
                    } else {
                        switch ($dataKey) {
                            case 'final_price':
                                $value = $this->pricingHelper->currency($value, true, false);
                                break;
                        }
                    }
                }

                if (is_array($value)) {
                    if (isset($value['label'])) {
                        $value = $value['label'];
                    } else {
                        $value = '';
                    }
                }
                foreach ($vars as $k => $v) {
                    if ($value == '') {
                        if (stripos($v, $kA) !== false || stripos($v, $kB) !== false) {
                            $vars[$k] = '';
                            continue;
                        }
                    }

                    $v = str_replace($kA, $value, $v);
                    $v = str_replace($kB, $value, $v);
                    $vars[$k] = $v;
                }
            }
        }

        foreach ($vars as $k => $v) {
            if ($v == $k) {
                $v = '';
            }

            if (substr($v, 0, strlen($bAOpen)) == $bAOpen) {
                $v = substr($v, strlen($bAOpen), strlen($v));
            }

            if (strpos($v, $bAClose) === strlen($v) - strlen($bAClose)) {
                $v = substr($v, 0, strlen($v) - strlen($bAClose));
            }
            if (stripos($v, $bBOpen) !== false || stripos($v, $bAOpen) !== false) {
                $v = '';
            }

            $str = str_replace($k, $v, $str);
        }

        return $str;
    }

    protected function checkForConvert($object, $key, $dataKey, $value, $storeId)
    {
        

        return $value;
    }

     
    /**
     * @return string
     */
    public function getPostUrl()
    {
        return $this->_urlBuilder->getUrl('rma/rma/savemessage');
    }

    /**
     * @param \Lofmp\Rma\Api\Data\RmaInterface $rma
     * @return string
     */
    public function getConfirmationUrl($rma)
    {
        return $this->_urlBuilder->getUrl(
            'rma/rma/savemessage',
            ['id' => $rma->getId(), 'shipping_confirmation' => true]
        );
    }
}
