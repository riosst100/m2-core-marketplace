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

class CategoryActions extends AbstractAction
{
    /** Url path */
    protected $urlPathEnable = 'lofmpfaq/category/enable';
    protected $urlPathDisable = 'lofmpfaq/category/disable';
    protected $urlPathDelete = 'lofmpfaq/category/delete';
    protected $idFieldName = 'category_id';
    protected $urlPathEdit = 'lofmpfaq/category/edit';

}
