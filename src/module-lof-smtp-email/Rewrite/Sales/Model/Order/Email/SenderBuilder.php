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

namespace Lof\SmtpEmail\Rewrite\Sales\Model\Order\Email;

class SenderBuilder extends \Magento\Sales\Model\Order\Email\SenderBuilder
{

    /**
     * Configure email template
     *
     * @return void
     */
    protected function configureEmailTemplate()
    {
        $this->transportBuilder->setTemplateIdentifier($this->templateContainer->getTemplateId());
        $this->transportBuilder->setTemplateOptions($this->templateContainer->getTemplateOptions());
        $this->transportBuilder->setTemplateVars($this->templateContainer->getTemplateVars());
        /*Send From Email Issue #14952*/
        $this->transportBuilder->setFromByStore(
            $this->identityContainer->getEmailIdentity(),
            $this->identityContainer->getStore()->getId()
        );
        /*Send From Email Issue #14952*/

//        $this->transportBuilderByStore->setFromByStore(
//            $this->identityContainer->getEmailIdentity(),
//            $this->identityContainer->getStore()->getId()
//        );
    }
}
