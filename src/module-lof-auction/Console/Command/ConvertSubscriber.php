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

use Magento\Framework\App\CacheInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\App\State;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Lof\Auction\Model\SubscriberAddressFactory;
use Lof\Auction\Model\MixAmountFactory;
use Magento\Customer\Model\CustomerFactory;

/**
 * Class ConvertSubscriber
 * @package Lof\Auction\Console\Command
 */
class ConvertSubscriber extends Command
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
     * @var SubscriberAddressFactory
     */
    protected $_subscriberFactory;

    /**
     * @var CustomerFactory
     */
    private $_customerFactory;

    /**
     * @var MixAmountFactory 
     */
    private $_mixAmountFactory;
    /**
     * Constructor.
     *
     * @param ResourceConnection $resource
     * @param CacheInterface $cache
     * @param State $state
     * @param SubscriberAddressFactory $subscriberAddress
     * @param CustomerFactory $customerFactory
     * @param MixAmountFactory $mixAmountFactory
     * @api
     */
    public function __construct(
        ResourceConnection $resource,
        CacheInterface $cache,
        State $state,
        SubscriberAddressFactory $subscriberFactory,
        CustomerFactory $customerFactory,
        MixAmountFactory $mixAmountFactory
    ) {
        $this->_resource = $resource;
        $this->_cache = $cache;
        $this->state = $state;
        $this->_subscriberFactory = $subscriberFactory;
        $this->_customerFactory = $customerFactory;
        $this->_mixAmountFactory = $mixAmountFactory;
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
            
            $collection = $this->getAuctionBidders();
            if($collection){
                foreach($collection as $object){
                    $customer_email = "";
                    if($object->getCustomerId()){
                        $customer = $this->_customerFactory->create()->load((int)$object->getCustomerId());
                        if($customer && $customer->getId()){
                            $customer_email = $customer->getEmail();
                        }
                    }
                    $this->_subscriberFactory->create()->processSubscriber($object, $customer_email);
                    $updated_at = $object->getUpdatedAt();
                    $mixAmountModel = $this->_mixAmountFactory->create()->load($object->getId());
                    $mixAmountModel->setIsSubscribed(1);//is_subscribed
                    $mixAmountModel->setUpdatedat($updated_at);
                    $mixAmountModel->save();
                }
            }
            
            $output->writeln("The auction subscriber processing has been done.");
        } catch (\Exception $e) {
            $output->writeln("Warning: ".$e->getMessage());
            $output->writeln("The auction subscriber processing has been done.");
        }
    }
    /**
     * Get available Auction Bidders
     */
    protected function getAuctionBidders(){
        $collection = $this->_mixAmountFactory->create()->getCollection();
        $collection->addFieldToFilter("is_spam", 0)
                   ->addFieldToFilter("winning_status", 0)
                   ->addFieldToFilter("is_subscribed", 0)
                   ->setOrder("created_at", "DESC")
                   ->setPageSize(500);//limit 500 items of each time

        return $collection;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName("lofauction:subscriber");
        $this->setDescription("Run Auction convert bidder to subscriber!");
        $this->setDefinition([
            new InputArgument(self::NAME_ARGUMENT, InputArgument::OPTIONAL, "Name"),
            new InputOption(self::NAME_OPTION, "-a", InputOption::VALUE_NONE, "Option functionality")
        ]);
        parent::configure();
    }
}
