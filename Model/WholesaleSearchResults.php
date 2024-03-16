<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Theiconnz\Wholesale\Model;

use Theiconnz\Wholesale\Api\Data\WholesaleSearchResultsInterface;
use Magento\Framework\Api\SearchResults;

/**
 * Service Data Object with Block search results.
 */
class WholesaleSearchResults extends SearchResults implements WholesaleSearchResultsInterface
{
}
