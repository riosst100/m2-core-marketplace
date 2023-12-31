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

namespace Lof\MarketPermissions\Model;

/**
 * Seller save handler pool.
 */
class SaveHandlerPool
{
    /**
     * @var SaveHandlerInterface[]
     */
    private $handlers;

    /**
     * @param SaveHandlerInterface[] $handlers [optional]
     */
    public function __construct(
        $handlers = []
    ) {
        $this->handlers = $handlers;
    }

    /**
     * Execute save handlers.
     *
     * @param $seller
     * @param $initialSeller
     */
    public function execute($seller, $initialSeller)
    {
        foreach ($this->handlers as $saveHandler) {
            if (!$saveHandler instanceof \Lof\MarketPermissions\Model\SaveHandlerInterface) {
                throw new \InvalidArgumentException(__(
                    'Type %1 is not an instance of %2',
                    get_class($saveHandler),
                    \Lof\MarketPermissions\Model\SaveHandlerInterface::class
                ));
            }
            $saveHandler->execute($seller, $initialSeller);
        }
    }
}
