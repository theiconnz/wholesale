<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Theiconnz\Wholesale\Model\ResourceModel\Wholesale;

use Theiconnz\Wholesale\Api\Data\WholesaleInterface;
use Theiconnz\Wholesale\Model\ResourceModel\AbstractCollection;

/**
 * Wholesale Collection
 */
class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'id';

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'wholesale_collection';

    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject = 'wholesale_collection';


    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Theiconnz\Wholesale\Model\Wholesale::class, \Theiconnz\Wholesale\Model\ResourceModel\Wholesale::class);
        $this->_map['fields']['store_id'] = 'store_table.store_id';
    }

    /**
     * Returns pairs id - firstname
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_toOptionArray('id', 'firstname');
    }

    /**
     * Add filter by store
     *
     * @param int|array|\Magento\Store\Model\Store $store
     * @param bool $withAdmin
     * @return $this
     */
    public function addStoreFilter($store, $withAdmin = true)
    {
        $this->performAddStoreFilter($store, $withAdmin);
        return $this;
    }

    /**
     * Join store relation table if there is store filter
     *
     * @return void
     */
    protected function _renderFiltersBefore()
    {
        $entityMetadata = $this->metadataPool->getMetadata(WholesaleInterface::class);
    }
}
