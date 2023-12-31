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
 * @package    Lof_MarketPermissions
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lof\MarketPermissions\Ui\Component;

/**
 * Class MassAction
 */
class MassAction extends \Magento\Ui\Component\MassAction
{
    /**
     * {@inheritdoc}
     */
    public function prepare()
    {
        parent::prepare();
        $config = $this->getData('config');
        if (isset($config['actions'])) {
            $this->sort($config['actions']);
        }
        $this->setData('config', $config);
    }

    /**
     * Sort actions
     *
     * @param array $actions
     * @return array
     */
    protected function sort(array &$actions)
    {
        usort($actions, function (array $a, array $b) {
            $a['sortOrder'] = isset($a['sortOrder']) ? $a['sortOrder'] : 0;
            $b['sortOrder'] = isset($b['sortOrder']) ? $b['sortOrder'] : 0;

            return $a['sortOrder'] - $b['sortOrder'];
        });

        return $actions;
    }
}
