<?php
/**
 * LandofCoder
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * https://landofcoder.com/license
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category   LandofCoder
 * @package    Lofmp_CouponCode
 * @copyright  Copyright (c) 2016 Landofcoder (https://landofcoder.com/)
 * @license    https://landofcoder.com/LICENSE-1.0.html
 */
namespace Lofmp\CouponCode\Controller\Marketplace\Generate;

use Magento\Eav\Model\Entity\Collection\AbstractCollection;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;

/**
 * Class AbstractMassStatus
 */
abstract class AbstractMassAction extends \Lofmp\CouponCode\Controller\Marketplace\Generate
{
    /**
     * Execute action to collection items
     *
     * @param AbstractCollection $collection
     * @return ResponseInterface|ResultInterface
     */
    abstract protected function massAction(AbstractCollection $collection);
}
