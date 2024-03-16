<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Theiconnz\Wholesale\Controller\View;

use Theiconnz\Wholesale\Controller\Register\JsonFactory;
use Theiconnz\Wholesale\Controller\Register\LoggerInterface;
use Theiconnz\Wholesale\Model\WholesaleFactory;
use Theiconnz\Wholesale\Model\WholesaleRepository;
use Magento\Captcha\Observer\CaptchaStringResolver;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Framework\Exception\LocalizedException;
use Magento\Customer\Api\Data\CustomerInterfaceFactory;
use Magento\Customer\Model\CustomerFactory;
use Magento\Store\Model\StoreManagerInterface;
use Theiconnz\Wholesale\Helper\Data as Helper;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\ForwardFactory;
use Magento\Framework\Controller\ResultsInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Encryption\Encryptor;
use Magento\Framework\Math\Random;
use Theiconnz\Wholesale\Model\Mail\TransportBuilder;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Framework\Filesystem\DirectoryList;

/**
 * Custom page for storefront. Needs to be accessible by POST because of the store switching.
 */
class Download extends Action implements HttpGetActionInterface, HttpPostActionInterface
{
    /**
     * @var ForwardFactory
     */
    protected $resultForwardFactory;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var JsonFactory
     */
    private $resultJsonFactory;

    private $_storeManager;

    /**
     * @var \Theiconnz\Wholesale\Helper\Data
     */
    protected $_helper;

    protected $customerFactory;

    protected $_factory;

    protected $_repository;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;


    /**
     * @var CustomerInterfaceFactory
     */
    private $customerInterfaceFactory;

    private $encryptor;

    private $random;

    protected $_transportBuilder;

    protected $_inlineTranslation;

    protected $_dir;

    protected $_signupAttached=false;

    protected $logger;

    /**
     * @param Context $context
     * @param RequestInterface $request
     * @param Helper $helper
     * @param ForwardFactory $resultForwardFactory
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param WholesaleFactory $factory
     * @param WholesaleRepository $wholesaleRepository
     * @param DirectoryList $dir
     * @param LoggerInterface $logger
     */
    public function __construct(
        Context $context,
        RequestInterface $request,
        Helper $helper,
        WholesaleFactory                                  $wholesaleFactory,
        WholesaleRepository                               $wholesaleRepository,
        DirectoryList                                     $dir,
        \Psr\Log\LoggerInterface                          $logger
    ) {
        parent::__construct($context);
        $this->request = $request;
        $this->_helper = $helper;
        $this->_factory = $wholesaleFactory;
        $this->_repository = $wholesaleRepository;
        $this->_dir = $dir;
        $this->logger=$logger;
    }

    /**
     *
     * @return ResultsInterface
     */
    public function execute()
    {
        $resultPage = $this->resultJsonFactory->create();
        $resultPage->setHttpResponseCode(500);
        $resultPage->setData([]);
        try{

        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage( $e->getMessage() );
            $resultPage->setHttpResponseCode(500);
            $resultPage->setJsonData(
                json_encode([
                    'error'   => 400,
                    'message' => $e->getMessage(),
                ])
            );
            return $resultPage;
        }
        return $resultPage;
    }

    /**
     * Validate and strip html tags
     * @param String $context
     *
     * @return string
     */
    public function validateInputFields($field){
        if(!$field) return '';
        return trim(strip_tags($field));
    }

    /**
     * Validate and strip html tags
     * @param String $context
     *
     * @return string
     */
    private function validatedParams()
    {
        $request = $this->getRequest();
        if (trim($request->getParam('m')) === '') {
            throw new LocalizedException(__('Missing parameter'));
        }

        if (trim($request->getParam('e')) === '') {
            throw new LocalizedException(__('Missing parameter'));
        }

        if (trim($request->getParam('m')) === '') {
            throw new LocalizedException(__('Invalid parameter value'));
        }

        if (false === \strpos($request->getParam('m'), '@')) {
            throw new LocalizedException(__('Invalid parameter value'));
        }

        return $request->getParams();
    }

}
