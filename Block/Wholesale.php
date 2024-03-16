<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Theiconnz\Wholesale\Block;

use Magento\Framework\View\Element\Template;
use Magento\Cms\Model\Template\FilterProvider;
use Theiconnz\Wholesale\Helper\Data as Helper;

/**
 * Wholesale page content block
 *
 * @api
 * @since 100.0.2
 */
class Wholesale extends Template
{
    /**
     * @var \Magento\Cms\Model\Template\FilterProvider
     */
    protected $_filterProvider;

    /**
     * @var \Theiconnz\Wholesale\Helper\Data
     */
    protected $_helper;

    /**
     * Construct
     *
     * @param Template\Context $context
     * @param FilterProvider $filterProvider
     * @param Helper $helper
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        FilterProvider $filterProvider,
        Helper $helper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_filterProvider = $filterProvider;
        $this->_helper = $helper;
    }

    /**
     * Retrieve Wholesale instance
     *
     * @return \Theiconnz\Wholesale\Model\Campaign
     */
    public function getPage()
    {
        return $this->getData('page');
    }

    /**
     * Prepare global layout
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        $page = $this->getPage();
        $this->pageConfig->addBodyClass('wholesale-registration');
        return parent::_prepareLayout();
    }


    /**
     * Returns action url for contact form
     *
     * @return string
     */
    public function getFormAction()
    {
        return $this->getUrl('wholesale/register/post', ['_secure' => true]);
    }

    public function isEnable()
    {
        return $this->_helper->isEnable();
    }

}
