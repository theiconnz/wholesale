<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Theiconnz\Wholesale\Controller\Adminhtml;

abstract class Registrations extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Theiconnz_Wholesale::wholesale';

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     */
    public function __construct(\Magento\Backend\App\Action\Context $context, \Magento\Framework\Registry $coreRegistry)
    {
        parent::__construct($context);
    }

    /**
     * Init page
     *
     * @param \Magento\Backend\Model\View\Result\Page $resultPage
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function initPage($resultPage)
    {
        $resultPage->setActiveMenu('Theiconnz_Wholesale::wholesale')
            ->addBreadcrumb(__('Registrations'), __('Registrations'))
            ->addBreadcrumb(__('Registrations'), __('Registrations'));
        return $resultPage;
    }
}
