<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Theiconnz\Wholesale\Model\Wholesale;

use Theiconnz\Wholesale\Api\Data\WholesaleInterface;
use Theiconnz\Wholesale\Api\WholesaleRepositoryInterface;
use Theiconnz\Wholesale\Model\WholesaleFactory;
use Theiconnz\Wholesale\Model\ResourceModel\Wholesale\CollectionFactory;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\AuthorizationInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Ui\DataProvider\Modifier\PoolInterface;
use Magento\Ui\DataProvider\ModifierPoolDataProvider;
use Psr\Log\LoggerInterface;

/**
 * Cms Page DataProvider
 */
class DataProvider extends ModifierPoolDataProvider
{
    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var array
     */
    protected $loadedData;

    /**
     * @var WholesaleRepositoryInterface
     */
    private $repository;

    /**
     * @var AuthorizationInterface
     */
    private $auth;

    /**
     * @var RequestInterface
     */
    private $request;


    /**
     * @var WholesaleFactory
     */
    private $factory;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $campaignCollectionRepository
     * @param DataPersistorInterface $dataPersistor
     * @param array $meta
     * @param array $data
     * @param PoolInterface|null $pool
     * @param AuthorizationInterface|null $auth
     * @param RequestInterface|null $request
     * @param WholesaleRepositoryInterface $repository
     * @param WholesaleFactory|null $factory
     * @param LoggerInterface|null $logger
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionRepository,
        DataPersistorInterface $dataPersistor,
        PoolInterface $pool,
        RequestInterface $request,
        WholesaleRepositoryInterface $repository,
        WholesaleFactory $factory,
        LoggerInterface $logger,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data, $pool);
        $this->collection = $collectionRepository->create();
        $this->dataPersistor = $dataPersistor;
        $this->meta = $this->prepareMeta($this->meta);
        $this->request = $request;
        $this->repository = $repository;
        $this->factory = $factory;
        $this->logger = $logger;
    }

    /**
     * Prepares Meta
     *
     * @param array $meta
     * @return array
     */
    public function prepareMeta(array $meta)
    {
        return $meta;
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }

        $page = $this->getCurrentPage();
        $this->loadedData[$page->getId()] = $page->getData();
        if ($page->getCustomLayoutUpdateXml() || $page->getLayoutUpdateXml()) {
            //Deprecated layout update exists.
            $this->loadedData[$page->getId()]['layout_update_selected'] = '_existing_';
        }

        return $this->loadedData;
    }

    /**
     * Return current page
     *
     * @return WholesaleInterface
     */
    private function getCurrentPage(): WholesaleInterface
    {
        $pageId = $this->getPageId();
        if ($pageId) {
            try {
                $page = $this->repository->getById($pageId);
            } catch (LocalizedException $exception) {
                $page = $this->factory->create();
            }

            return $page;
        }

        $data = $this->dataPersistor->get('Wholesale');
        if (empty($data)) {
            return $this->factory->create();
        }
        $this->dataPersistor->clear('Wholesale');

        return $this->factory->create()
            ->setData($data);
    }

    /**
     * Returns current page id from request
     *
     * @return int
     */
    private function getPageId(): int
    {
        if($this->request) {
            return (int)$this->request->getParam($this->getRequestFieldName());
        } else {
            return false;
        }
    }

}
