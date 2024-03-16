<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Theiconnz\Wholesale\Controller\Adminhtml\Registrations;

use Theiconnz\Wholesale\Controller\Adminhtml\Registrations\Theiconnz;
use Magento\Framework\App\Action\HttpGetActionInterface;

/**
 * Index action.
 */
class View extends \Magento\Backend\App\Action implements HttpGetActionInterface
{
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
     * @var Theiconnz\Wholesale\Api\WholesaleRepositoryInterface $resultsRepository
     */
    private $resultsRepository;

    /**
     * @var Theiconnz\Wholesale\Model\WholesaleFactory $resultsFactory
     */
    private $resultsFactory;


    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Registry $registry
     * @param \Theiconnz\Wholesale\Api\WholesaleRepositoryInterface $resultsRepository
     * @param \Theiconnz\Wholesale\Model\WholesaleFactory $resultsFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context                   $context,
        \Magento\Framework\View\Result\PageFactory            $resultPageFactory,
        \Magento\Framework\Registry                           $registry,
        \Theiconnz\Wholesale\Api\WholesaleRepositoryInterface $resultsRepository,
        \Theiconnz\Wholesale\Model\WholesaleFactory           $resultsFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->_coreRegistry = $registry;
        $this->resultsRepository = $resultsRepository;
        $this->resultsFactory = $resultsFactory;
        parent::__construct($context);
    }

    /**
     * Index action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $this->initPage($resultPage)->getConfig()->getTitle()->prepend(__('Registrations'));

        return $resultPage;
    }
}
