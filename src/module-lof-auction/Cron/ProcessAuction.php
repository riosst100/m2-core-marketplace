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
namespace Lof\Auction\Cron;

use Lof\Auction\Helper\ProcessWinner;

/**
 * Class ProcessAuction
 * @package Lof\Auction\Console\Cron
 */
class ProcessAuction
{
    /**
     * @var ProcessWinner
     */
    protected $_processwinnerHelper;

    /**
     * ProcessAuction constructor.
     * @param ProcessWinner $processWinner
     */
    public function __construct(
        ProcessWinner $processWinner
    ) {
        $this->_processwinnerHelper = $processWinner;
    }

    /**
     * Excecute cron job
     *
     * @return void
     */
    public function execute()
    {
        $this->_processwinnerHelper->runAuction();
    }
}
