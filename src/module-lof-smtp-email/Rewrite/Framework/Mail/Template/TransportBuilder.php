<?php
/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * https://landofcoder.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Landofcoder
 * @package    Lof_Blog
 * @copyright  Copyright (c) 2016 Landofcoder (https://landofcoder.com/)
 * @license    https://landofcoder.com/LICENSE-1.0.html
 */

namespace Lof\SmtpEmail\Rewrite\Framework\Mail\Template;

class TransportBuilder extends \Magento\Framework\Mail\Template\TransportBuilder
{

    /**
     * @param $from
     * @param $store
     * @return $this
     * @throws \Magento\Framework\Exception\MailException
     */
    public function setFromByStore($from, $store)
    {
        $result = $this->_senderResolver->resolve($from, $store);
        $this->_fromByStore = [
            'email' => $email,
            'name'  => $name
        ];


        //$this->message->setFrom($result['email'], $result['name']);

        return $this;
    }
}
