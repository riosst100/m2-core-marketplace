<?php
/**
 * Copyright © landofcoder All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Lof\SmtpEmail\Api\Data;

interface SpamSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get Spam list.
     * @return \Lof\SmtpEmail\Api\Data\SpamInterface[]
     */
    public function getItems();

    /**
     * Set name list.
     * @param \Lof\SmtpEmail\Api\Data\SpamInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}

