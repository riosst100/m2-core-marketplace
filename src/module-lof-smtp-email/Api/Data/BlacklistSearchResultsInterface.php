<?php
/**
 * Copyright © landofcoder All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Lof\SmtpEmail\Api\Data;

interface BlacklistSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get Blacklist list.
     * @return \Lof\SmtpEmail\Api\Data\BlacklistInterface[]
     */
    public function getItems();

    /**
     * Set email list.
     * @param \Lof\SmtpEmail\Api\Data\BlacklistInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}

