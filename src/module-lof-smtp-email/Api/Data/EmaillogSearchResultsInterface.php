<?php
/**
 * Copyright © landofcoder All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Lof\SmtpEmail\Api\Data;

interface EmaillogSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get Emaillog list.
     * @return \Lof\SmtpEmail\Api\Data\EmaillogInterface[]
     */
    public function getItems();

    /**
     * Set subject list.
     * @param \Lof\SmtpEmail\Api\Data\EmaillogInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}

