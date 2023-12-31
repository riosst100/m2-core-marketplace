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
 * @package    Lof_SocialLogin
 *
 * @copyright  Copyright (c) 2016 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */

namespace Lof\SocialLogin\Model;

use Lof\SocialLogin\Helper\Github\Data as DataHelper;

class Google
{
    protected $storeManagerInterface; 
    
    public function __construct(
        DataHelper $dataHelper,
        \Magento\Store\Model\StoreManagerInterface $storeManagerInterface
    )
    {
        $this->dataHelper = $dataHelper;
        $this->storeManagerInterface = $storeManagerInterface;
    }

    public function getBaseUrl()
    {
        $baseurl = $this->storeManagerInterface
            ->getStore()
            ->getBaseUrl();

        return $baseurl.'lofsociallogin/google/callback';
    }
}
