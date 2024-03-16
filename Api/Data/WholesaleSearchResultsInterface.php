<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Theiconnz\Wholesale\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface for wholesale search results.
 * @api
 * @since 0.0.1
 */
interface WholesaleSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get list.
     *
     * @return \Theiconnz\Wholesale\Api\Data\WholesaleInterface[]
     */
    public function getItems();

    /**
     * Set list.
     *
     * @param \Theiconnz\Wholesale\Api\Data\WholesaleInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
