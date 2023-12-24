<?php
/**
 * Landofcoder
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
 * @category   Landofcoder
 * @package    Lof_Blog
 * @copyright  Copyright (c) 2016 Landofcoder (https://landofcoder.com/)
 * @license    https://landofcoder.com/LICENSE-1.0.html
 */

namespace Lof\SmtpEmail\Model;

class TransportBuilderPlugin
{
    /** @var \Lof\SmtpEmail\Model\Store */
    protected $storeModel;
    /**
     * @param \Lof\SmtpEmail\Model\Store $storeModel
     */
    public function __construct(\Lof\SmtpEmail\Model\Store $storeModel)
    {
        $this->storeModel = $storeModel;
    }

    /**
     * @param \Magento\Framework\Mail\Template\TransportBuilder $subject
     * @param $templateOptions
     * @return array
     */
    public function beforeSetTemplateOptions(
        \Magento\Framework\Mail\Template\TransportBuilder $subject,
        $templateOptions
    ) 
    {
        if (array_key_exists('store', $templateOptions)) {
            $this->storeModel->setStoreId($templateOptions['store']);
        } else {
            $this->storeModel->setStoreId(null);
        }
        return [$templateOptions];
    }
}