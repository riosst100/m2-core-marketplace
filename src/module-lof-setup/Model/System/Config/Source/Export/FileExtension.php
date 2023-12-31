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
 * @category  Landofcoder
 * @package   Lof_Setup
 * @copyright Copyright (c) 2016 Landofcoder (http://www.landofcoder.com/)
 * @license   http://www.landofcoder.com/LICENSE-1.0.html
 */
namespace Lof\Setup\Model\System\Config\Source\Export;

class FileExtension
{

    /**
     * Options getter
     *
     * @return array
     */
    public function toArray()
    {
        $extension = [];
        $extension['.json'] = __('.json');
        return $extension;
    }
}