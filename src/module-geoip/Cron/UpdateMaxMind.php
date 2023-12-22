<?php

namespace CoreMarketplace\GeoIp\Cron;

/**
 * Class UpdateMaxMind
 * @package CoreMarketplace\GeoIp\Cron
 */
class UpdateMaxMind
{
    /**
     * @var \CoreMarketplace\GeoIp\Model\GeoIpDatabase\MaxMind
     */
    protected $maxMind;
    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger;

    /**
     * UpdateMaxMind constructor.
     * @param \CoreMarketplace\GeoIp\Model\GeoIpDatabase\MaxMind $maxMind
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \CoreMarketplace\GeoIp\Model\GeoIpDatabase\MaxMind $maxMind,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->maxMind = $maxMind;
        $this->_logger = $logger;
    }

    /**
     * Execute Cron UpdateMaxMind
     */
    public function execute()
    {
        try {
            $this->maxMind->update();
        } catch (\Exception $e) {
            $this->_logger->debug($e->getMessage());
            return false;
        }
        return true;
    }
}
