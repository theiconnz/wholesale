<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Theiconnz\Wholesale\Controller\Adminhtml\Registrations;

use Theiconnz\Wholesale\Controller\Adminhtml\Registrations\Campaign;
use Theiconnz\Wholesale\Controller\Adminhtml\Registrations\CampaignInterface;
use Theiconnz\Wholesale\Controller\Adminhtml\Registrations\CampaignRepositoryInterface;
use Theiconnz\Wholesale\Controller\Adminhtml\Registrations\factory;
use Magento\Backend\App\Action;
use Magento\Backend\Model\View\Result\Redirect;
use Theiconnz\Wholesale\Api\Data\WholesaleInterface;
use Theiconnz\Wholesale\Api\WholesaleRepositoryInterface;
use Theiconnz\Wholesale\Model\Wholesale;
use Theiconnz\Wholesale\Model\WholesaleFactory;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Save CMS page action.
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Save extends Action implements HttpPostActionInterface
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'AdobeFlow_Wholesale::wholesale';

    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var WholesaleFactory
     */
    private $factory;

    /**
     * @var CampaignRepositoryInterface
     */
    private $campaignRepository;

    /**
     * @param Action\Context $context
     * @param DataPersistorInterface $dataPersistor
     * @param factory|null $campaignFactory
     * @param WholesaleRepositoryInterface $campaignRepository
     */
    public function __construct(
        Action\Context $context,
        DataPersistorInterface $dataPersistor,
        WholesaleFactory $factory,
        WholesaleRepositoryInterface $campaignRepository
    ) {
        $this->dataPersistor = $dataPersistor;
        $this->factory = $factory;
        $this->repository = $campaignRepository;
        parent::__construct($context);
    }

    /**
     * Save action
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @return ResultInterface
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            if (isset($data['is_active']) && $data['is_active'] === 'true') {
                $data['is_active'] = Wholesale::STATUS_ENABLED;
            }
            if (empty($data['id'])) {
                $data['id'] = null;
            }

            /** @var Campaign $model */
            $model = $this->factory->create();

            $id = $this->getRequest()->getParam('id');
            if ($id) {
                try {
                    $model = $this->repository->getById($id);
                } catch (LocalizedException $e) {
                    $this->messageManager->addErrorMessage(__('This Wholesale entry no longer exists.'));
                    return $resultRedirect->setPath('*/*/');
                }
            }

            $model->setData($data);

            try {
                $this->repository->save($model);
                $this->messageManager->addSuccessMessage(__('You saved the Wholesale entry.'));
                return $this->processResultRedirect($model, $resultRedirect, $data);
            } catch (LocalizedException $e) {
                $this->messageManager->addExceptionMessage($e->getPrevious() ?: $e);
            } catch (\Throwable $e) {
                $this->messageManager->addErrorMessage(__('Something went wrong while saving the Wholesale entry.'));
            }

            $this->dataPersistor->set('campaign', $data);
            return $resultRedirect->setPath('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }

    /**
     * Process result redirect
     *
     * @param CampaignInterface $model
     * @param Redirect $resultRedirect
     * @param array $data
     * @return Redirect
     * @throws LocalizedException
     */
    private function processResultRedirect($model, $resultRedirect, $data)
    {
        $this->dataPersistor->clear('wholesale');
        if ($this->getRequest()->getParam('back')) {
            return $resultRedirect->setPath('*/*/edit', ['id' => $model->getId(), '_current' => true]);
        }
        return $resultRedirect->setPath('*/*/');
    }
}
