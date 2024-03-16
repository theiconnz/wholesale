<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Theiconnz\Wholesale\Controller\Adminhtml\Registrations;

use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Backend\App\Action;
use Magento\Framework\Exception\LocalizedException;


/**
 * Edit Wholesale delete action.
 */
class Delete extends \Magento\Backend\App\Action implements HttpPostActionInterface
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'AdobeFlow_Wholesale::wholesale';

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
        $resultPage->setActiveMenu('AdobeFlow_Wholesale::menu')
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
        $id = $this->getRequest()->getParam('id');
        if($id) {
            $model = $this->wholesaleRepository->getById($id);
            $model->load($id);
        }

        $resultRedirect = $this->resultRedirectFactory->create();

        if ($model->getId()) {
            try {
                $this->wholesaleRepository->delete($model);
            } catch (LocalizedException $exception) {
                $this->messageManager->addErrorMessage($exception->getMessage());
            }

            $this->messageManager->addSuccessMessage(
                __('Record have been deleted.')
            );
        } else {
            $this->messageManager->addErrorMessage(__('Wholesale user not exists.'));
        }
        return $resultRedirect->setPath('*/*/');
    }
}
