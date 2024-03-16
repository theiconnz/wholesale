<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Theiconnz\Wholesale\Model;

use Theiconnz\Wholesale\Api\WholesaleRepositoryInterface;
use Theiconnz\Wholesale\Api\Data;
use Theiconnz\Wholesale\Model\Block;
use Theiconnz\Wholesale\Model\ResourceModel\Wholesale as ResourceBlock;
use Theiconnz\Wholesale\Model\ResourceModel\Wholesale\CollectionFactory as WholesaleCollectionFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\EntityManager\HydratorInterface;
use Theiconnz\Wholesale\Model\WholesaleFactory;

/**
 * Default block repo impl.
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class WholesaleRepository implements WholesaleRepositoryInterface
{
    /**
     * @var ResourceBlock
     */
    protected $resource;

    /**
     * @var WholesaleFactory
     */
    protected $wholesaleFactory;

    /**
     * @var WholesaleCollectionFactory
     */
    protected $wholesaleCollectionFactory;

    /**
     * @var Data\BlockSearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * @var DataObjectProcessor
     */
    protected $dataObjectProcessor;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var CollectionProcessorInterface
     */
    private $collectionProcessor;

    /**
     * @var HydratorInterface
     */
    private $hydrator;

    /**
     * @param ResourceBlock $resource
     * @param WholesaleFactory $wholesaleFactory
     * @param WholesaleCollectionFactory $wholesaleCollectionFactory
     * @param Data\BlockSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param CollectionProcessorInterface $collectionProcessor
     * @param HydratorInterface $hydrator
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        ResourceBlock $resource,
        WholesaleFactory $wholesaleFactory,
        WholesaleCollectionFactory $wholesaleCollectionFactory,
        Data\WholesaleSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        CollectionProcessorInterface $collectionProcessor = null,
        ?HydratorInterface $hydrator = null
    ) {
        $this->resource = $resource;
        $this->wholesaleFactory = $wholesaleFactory;
        $this->resultsCollectionFactory = $wholesaleCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
        $this->hydrator = $hydrator;
    }

    /**
     * Save Block data
     *
     * @param \Theiconnz\Wholesale\Api\Data\WholesaleInterface $wholesale
     * @return Block
     * @throws CouldNotSaveException
     */
    public function save(\Theiconnz\Wholesale\Api\Data\WholesaleInterface $wholesale)
    {
        if (empty($wholesale->getStoreId())) {
            $wholesale->setStoreId($this->storeManager->getStore()->getId());
        }

        try {
            $this->resource->save($wholesale);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }
        return $wholesale;
    }

    /**
     * Load result by given id
     *
     * @param string $id
     * @return Block
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($id)
    {
        $wholesale = $this->wholesaleFactory->create();
        $this->resource->load($wholesale, $id);
        if (!$wholesale->getId()) {
            throw new NoSuchEntityException(__('The wholesaller with the "%1" ID doesn\'t exist.', $id));
        }
        return $wholesale;
    }

    /**
     * Load Result data by given Email
     *
     * @param string $email
     * @return Block
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getByEmail($email)
    {
        $wholesale = $this->wholesaleFactory->create();
        $this->resource->load($wholesale, $email, 'email');
        if (!$wholesale->getId()) {
            return false;
        }
        return $wholesale;
    }

    /**
     * Load Block data collection by given search criteria
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @param \Magento\Framework\Api\SearchCriteriaInterface $criteria
     * @return \Theiconnz\Wholesale\Api\Data\BlockSearchResultsInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $criteria)
    {
        /** @var \Theiconnz\Wholesale\Model\ResourceModel\Wholesale\Collection $collection */
        $collection = $this->resultsCollectionFactory->create();

        $this->collectionProcessor->process($criteria, $collection);

        /** @var Data\BlockSearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * Delete Block
     *
     * @param \Theiconnz\Wholesale\Api\Data\WholesaleInterface $wholesale
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(\Theiconnz\Wholesale\Api\Data\WholesaleInterface $wholesale)
    {
        try {
            $this->resource->delete($wholesale);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }
        return true;
    }

    /**
     * Delete Block by given Block Identity
     *
     * @param string $id
     * @return bool
     * @throws CouldNotDeleteException
     * @throws NoSuchEntityException
     */
    public function deleteById($id)
    {
        return $this->delete($this->getById($id));
    }

}
