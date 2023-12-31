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

namespace Lof\MarketPermissions\Model\Email;

use Magento\Framework\Escaper;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Exception\MailException;
use Psr\Log\LoggerInterface as PsrLogger;

/**
 * Intermediate class for sending emails using transport
 */
class Transporter
{
    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    private $transportBuilder;

    /**
     * @var \Magento\Framework\Escaper
     */
    private $escaper;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @param TransportBuilder $transportBuilder
     * @param Escaper $escaper
     * @param PsrLogger $logger
     */
    public function __construct(
        TransportBuilder $transportBuilder,
        Escaper $escaper,
        PsrLogger $logger
    ) {
        $this->transportBuilder = $transportBuilder;
        $this->escaper = $escaper;
        $this->logger = $logger;
    }

    /**
     * Sends an email using transport
     *
     * @param string $customerEmail
     * @param string $customerName
     * @param string|array $from
     * @param string $templateId
     * @param array $templateParams
     * @param int|null $storeId
     * @param array $bcc
     * @return void
     */
    public function sendMessage(
        $customerEmail,
        $customerName,
        $from,
        $templateId,
        array $templateParams = [],
        $storeId = null,
        $bcc = []
    ) {
        $templateParams = array_merge(
            $templateParams,
            ['escaper' => $this->escaper]
        );

        $transport = $this->transportBuilder
            ->setTemplateIdentifier($templateId)
            ->setTemplateOptions(['area' => \Magento\Framework\App\Area::AREA_FRONTEND, 'store' => $storeId])
            ->setTemplateVars($templateParams)
            ->setFromByScope($from, $storeId)
            ->addTo($customerEmail, $customerName)
            ->addBcc($bcc)
            ->getTransport();

        try {
            $transport->sendMessage();
        } catch (MailException $e) {
            // If we are not able to send a new account email, this should be ignored
            $this->logger->critical($e);
        }
    }
}
