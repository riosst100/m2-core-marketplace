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
namespace Lof\Auction\Console\Command;

use Lof\Auction\Helper\ProcessWinner;
use Magento\Framework\App\CacheInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\App\State;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ProcessAuction
 * @package Lof\Auction\Console\Command
 */
class ProcessAuction extends Command
{
    /**
     *
     */
    const NAME_ARGUMENT = "name";
    /**
     *
     */
    const NAME_OPTION = "option";

    /**
     * @var ResourceConnection
     */
    protected $_resource;

    /**
     * Cache
     *
     * @var CacheInterface
     */
    protected $_cache;

    /**
     * @var ProcessWinner
     */
    protected $_processwinnerHelper;

    /** @var State * */
    private $state;

    /**
     * Constructor.
     *
     * @param ResourceConnection $resource
     * @param CacheInterface $cache
     * @param ProcessWinner $processwinnerHelper
     * @param State $state
     * @api
     */
    public function __construct(
        ResourceConnection $resource,
        CacheInterface $cache,
        ProcessWinner $processwinnerHelper,
        State $state
    ) {
        $this->_resource = $resource;
        $this->_cache = $cache;
        $this->_processwinnerHelper = $processwinnerHelper;
        $this->state = $state;
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ) {
        try {
            $this->state->setAreaCode(\Magento\Framework\App\Area::AREA_FRONTEND); // or \Magento\Framework\App\Area::AREA_FRONTEND, depending on your needs
            $return = $this->_processwinnerHelper->runAuction();
            if (isset($return['error']) && $return['error']) {
                foreach ($return['error'] as $val) {
                    $output->writeln("Warning: " . $val);
                }
            }
            if (isset($return['success']) && $return['success']) {
                foreach ($return['success'] as $val) {
                    $output->writeln("Success: " . $val);
                }
            }
            $output->writeln("The auction winner processing has been done.");
        } catch (\Exception $e) {
            $output->writeln("Warning: ".$e->getMessage());
            $output->writeln("The auction winner processing has been done.");
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName("lofauction:process");
        $this->setDescription("Run Auction Winner when auction is expired");
        $this->setDefinition([
            new InputArgument(self::NAME_ARGUMENT, InputArgument::OPTIONAL, "Name"),
            new InputOption(self::NAME_OPTION, "-a", InputOption::VALUE_NONE, "Option functionality")
        ]);
        parent::configure();
    }
}
