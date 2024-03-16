<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Theiconnz\Wholesale\Controller\Adminhtml\Registrations;

use Theiconnz\Wholesale\Controller\Adminhtml\Registrations\Theiconnz;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Backend\App\Action;


/**
 * Edit Campaigns page action.
 */
class Edit extends \Magento\Backend\App\Action implements HttpGetActionInterface
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Theiconnz_Wholesale::wholesale';

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;


    /**
     * @var Theiconnz\Wholesale\Api\WholesaleRepositoryInterface $wholesaleRepository
     */
    private $wholesaleRepository;

    /**
     * @var Theiconnz\Wholesale\Model\WholesaleFactory $wholesaleFactory
     */
    private $wholesaleFactory;


    /**
     * @param Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Registry $registry
     * @param \Theiconnz\Wholesale\Api\WholesaleRepositoryInterface $wholesaleRepository
     * @param \Theiconnz\Wholesale\Model\WholesaleFactory $wholesaleFactory
     */
    public function __construct(
        Action\Context                                        $context,
        \Magento\Framework\View\Result\PageFactory            $resultPageFactory,
        \Magento\Framework\Registry                           $registry,
        \Theiconnz\Wholesale\Api\WholesaleRepositoryInterface $wholesaleRepository,
        \Theiconnz\Wholesale\Model\WholesaleFactory           $wholesaleFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->_coreRegistry = $registry;
        $this->wholesaleRepository = $wholesaleRepository;
        $this->wholesaleFactory = $wholesaleFactory;
        parent::__construct($context);
    }

    /**
     * Init actions
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function _initAction()
    {
        // load layout, set active menu and breadcrumbs
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Theiconnz_Wholesale::menu')
            ->addBreadcrumb(__('Wholesales'), __('Wholesales'))
            ->addBreadcrumb(__('Manage Wholesales'), __('Manage Wholesales'));
        return $resultPage;
    }

    /**
     * Edit Wholesales page
     *
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Backend\Model\View\Result\Redirect
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        // 1. Get ID and create model
        $id = $this->getRequest()->getParam('id');
        if($id) {
            $model = $this->wholesaleRepository->getById($id);
        } else {
            $model = $this->wholesaleFactory->create();
        }

        // 2. Initial checking
        if ($id) {
            $model = $this->wholesaleRepository->getById($id);
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addErrorMessage(__('Wholesale user not exists.'));
                /** \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        }
        $this->_coreRegistry->register('wholesale', $model);

        // 5. Build edit form
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->_initAction();
        $resultPage->addBreadcrumb(
            $id ? __('Edit Wholesales') : __('New Wholesales'),
            $id ? __('Edit Wholesales') : __('New Wholesales')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Wholesales'));
        $resultPage->getConfig()->getTitle()
            ->prepend($model->getId() ? $model->getTitle() : __('New Wholesales'));

        return $resultPage;
    }
}
