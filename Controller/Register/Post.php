<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Theiconnz\Wholesale\Controller\Register;

use Theiconnz\Wholesale\Controller\Register\JsonFactory;
use Theiconnz\Wholesale\Controller\Register\LoggerInterface;
use Theiconnz\Wholesale\Api\Data\WholesaleInterface;
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
class Post extends Action implements HttpGetActionInterface, HttpPostActionInterface
{
    /**
     * Recipient email config path
     */
    const XML_PATH_ADMIN_EMAIL_RECIPIENT = Helper::XML_RECIPIENT;

    /**
     * Sender email config path
     */
    const XML_PATH_EMAIL_SENDER = Helper::XML_SENDER_EMAIL_IDENTITY;

    /**
     * Email template config path
     */
    const XML_PATH_EMAIL_TEMPLATE = Helper::XML_PATH_EMAIL_TEMPLATE;


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


    /**
     * @var CaptchaStringResolver
     */
    protected $captchaStringResolver;

    protected $customerFactory;

    /**
     * @var Validator
     */
    private $formKeyValidator;


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
     * @param StoreManagerInterface $storeManager
     * @param CustomerInterfaceFactory $customerInterfaceFactory
     * @param CustomerFactory $customerFactory
     * @param Validator $formKeyValidator
     * @param CaptchaStringResolver $captchaStringResolver
     * @param WholesaleFactory $factory
     * @param WholesaleRepository $wholesaleRepository
     * @param Encryptor $encryptor
     * @param Random $random
     * @param TransportBuilder $transportBuilder
     * @param StateInterface $inlinestate
     * @param DirectoryList $dir
     * @param LoggerInterface $logger
     */
    public function __construct(
        Context $context,
        RequestInterface $request,
        Helper $helper,
        StoreManagerInterface $storeManager,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        CustomerInterfaceFactory  $customerInterfaceFactory,
        CustomerFactory $customerFactory,
        Validator $formKeyValidator,
        CaptchaStringResolver                             $captchaStringResolver,
        WholesaleFactory                                  $wholesaleFactory,
        WholesaleRepository                               $wholesaleRepository,
        CustomerRepositoryInterface                       $customerRepository,
        Encryptor                                         $encryptor,
        Random                                            $random,
        TransportBuilder                                  $transportBuilder,
        StateInterface                                    $inlineTranslation,
        DirectoryList                                     $dir,
        \Psr\Log\LoggerInterface                          $logger
    ) {
        parent::__construct($context);
        $this->request = $request;
        $this->_storeManager = $storeManager;
        $this->_helper = $helper;
        $this->customerInterfaceFactory = $customerInterfaceFactory;
        $this->customerFactory = $customerFactory;
        $this->captchaStringResolver = $captchaStringResolver;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->formKeyValidator = $formKeyValidator;
        $this->_factory = $wholesaleFactory;
        $this->_repository = $wholesaleRepository;
        $this->customerRepository = $customerRepository;
        $this->encryptor = $encryptor;
        $this->random = $random;
        $this->_transportBuilder = $transportBuilder;
        $this->_inlineTranslation = $inlineTranslation;
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
            $this->checkCaptcha();


            if (!$this->getRequest()->isPost()
                || !$this->formKeyValidator->validate($this->getRequest())
            ) {
                throw new LocalizedException(
                    __('Form validation faliure')
                );
            }

            $result = false;
            $this->validatedParams();

            $storeid  = $this->_storeManager->getStore()->getId();

            $wholesale = $this->checkWholeSaleEmail($this->request->getParam('email'));
            if($wholesale && $wholesale->getStoreId()==$storeid){
                throw new LocalizedException(
                    __('Already registered for wholesale account')
                );
            }

            $model = $this->_factory->create();
            $model->setFirstname($this->validateInputFields($this->request->getParam('firstname')));
            $model->setLastname($this->validateInputFields($this->request->getParam('lastname')));
            $model->setStoreId($storeid);
            $model->setEmail($this->validateInputFields($this->request->getParam('email')));
            $model->setPhone($this->validateInputFields($this->request->getParam('phone')));
            $model->setBusinessName($this->validateInputFields($this->request->getParam('business-name')));
            if(!empty($this->request->getParam('location'))) {
                $model->setLocation($this->validateInputFields($this->request->getParam('location')));
            }
            if(!empty($this->request->getParam('describe'))) {
                $model->setDescribe($this->validateInputFields($this->request->getParam('describe')));
            }
            if(!empty($this->request->getParam('comment'))) {
                $model->setComment($this->validateInputFields($this->request->getParam('comment')));
            }
            $model = $this->_repository->save($model);
            // check customer is logged in
            $websiteId  = $this->_storeManager->getWebsite()->getWebsiteId();
            $customer = $this->customerFactory->create()->setWebsiteId($websiteId)->loadByEmail($this->request->getParam('email'));
            $model->setIsActive(0);
            $model->setSignEmailSent(0);
            $model->setDocumentAttached(0);
            if ($customer->getId()) {
                // if customer is registered, update the group
                $groupupdated=$this->updateCustomerGroup($customer);
                if($groupupdated){
                    $model->setIsActive(1);
                }
            } else {
                //check if the customer already registered
                $customer=$this->signUpCustomer($model);
                // if it is upgrade customer to new group
            }
            if($customer)
            {
                $welcome=$this->sendWSWelcomeEmail($model);
                if($welcome && $model->getId()){
                    $model->setSignEmailSent(1);
                    if($this->_signupAttached){
                        $model->setDocumentAttached(1);
                    }
                    $this->_repository->save($model);
                }
            }
            $this->generateAdminEmail($model);

            $resultPage->setHttpResponseCode(200);
            $response = ['success' => 200, 'message' => "Result submit success"];
            $resultPage->setData($response);
            $this->messageManager->addSuccessMessage("Thank you");

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

    public function updateCustomerGroup($customer)
    {
        $updateGroup = $this->_helper->getUpgradeCustomer();
        $cg = $this->_helper->getCustomerGroup();

        if($updateGroup && $cg && $customer->getId()) {
            if($customer->getGroupId()==$cg){
                return;
            }

            try {
                $customer->setGroupId($cg);
                $this->customerRepository->save($customer);
                return true;
            } catch (LocalizedException $exception) {
                $this->logger->error($exception);
            }
        }
        return false;
    }

    public function sendWSWelcomeEmail($model)
    {
        $systememail = $this->_helper->getStopEmailSentFromSystem();
        if( $systememail ) return false;

        $sendEmails = $this->_helper->getSenderEmail();
        if( !$sendEmails ) return false;

        $stopsendEmails = $this->_helper->stopWelcomeEmail();
        if( $stopsendEmails ) return false;

        //not stopping sending email, prep email messages
        return $this->generateTemplate($model);
    }


    public function generateTemplate($model)
    {
        try {
            $post=$this->request->getParams();
            $url=$this->_helper->getDownloadLink();
            $post['downloadlink']=sprintf($this->_helper::XML_DOWNLOAD_URLPARAMS, $url, $post["email"], $model->getCreationTime());

            die($post['downloadlink']);
            $this->_inlineTranslation->suspend();
            $_template = $this->_helper->getCustomerTemplate();
            $emailTemplateVariables = $post;
            $_emailSender=$this->_helper->getEmailSender();
            $_templateFile = $this->_transportBuilder->setTemplateIdentifier($_template)
                ->setTemplateOptions(
                    [
                        'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                        'store' => $this->_storeManager->getStore()->getId(),
                        ]
                )
                ->setTemplateVars($emailTemplateVariables)
                ->setFrom($_emailSender)
                ->addTo($post["email"], $post["firstname"]);

            //if($this->_helper->isAttachmentEnable()) {
            //    $_pdfFile= $this->_helper->getPdffile();
            //    $_pdfFilePath=$this->dir->getPath('media') . $_pdfFile;
            //    if(is_file($_pdfFilePath)){
            //        $_templateFile->addAttachment(file_get_contents($_pdfFilePath)); //Attachment goes here.
            //    }
            //}

            $transport = $this->_transportBuilder->getTransport();
            $transport->sendMessage();
            $this->_inlineTranslation->resume();
            $this->_signupAttached=true;
            return true;
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            return false;
        }
    }

    public function generateAdminEmail($post){
        try {
            $this->_inlineTranslation->suspend();
            $_template = $this->_helper->getAdminEmailTemplate();
            $emailTemplateVariables = $post;
            $_emailSender=$this->_helper->getEmailSender();
            $_templateFile = $this->_transportBuilder->setTemplateIdentifier($_template)
                ->setTemplateOptions(
                    [
                        'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                        'store' => $this->_storeManager->getStore()->getId(),
                    ]
                )
                ->setTemplateVars($emailTemplateVariables)
                ->setFrom($_emailSender)
                ->addTo($this->_helper->getAdminEmailNotificationTo());

            //if($this->_helper->isAttachmentEnable()) {
            //    $_pdfFile= $this->_helper->getPdffile();
            //    $_pdfFilePath=$this->dir->getPath('media') . $_pdfFile;
            //    if(is_file($_pdfFilePath)){
            //        $_templateFile->addAttachment(file_get_contents($_pdfFilePath)); //Attachment goes here.
            //    }
            //}

            $transport = $this->_transportBuilder->getTransport();
            $transport->sendMessage();
            $this->_inlineTranslation->resume();
            $this->_signupAttached=true;
            return true;
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            return false;
        }
    }


    public function signUpCustomer($model)
    {
        $signup = $this->_helper->getSignUp();
        if($signup){
            $customer=$this->createCustomer($model);
            $groupupdated=$this->updateCustomerGroup($customer);
            if($groupupdated){
                $model->setIsActive(1);
                $this->_repository->save($model);
            }
            return $customer;
        }
        return false;
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
        if (trim($request->getParam('firstname')) === '') {
            throw new LocalizedException(__('First name is missing'));
        }

        if (trim($request->getParam('lastname')) === '') {
            throw new LocalizedException(__('Last name is missing'));
        }

        if (trim($request->getParam('email')) === '') {
            throw new LocalizedException(__('Email is missing'));
        }

        if (false === \strpos($request->getParam('email'), '@')) {
            throw new LocalizedException(__('Invalid email address'));
        }

        return $request->getParams();
    }

    public function checkWholeSaleEmail($email)
    {
        return $this->_repository->getByEmail($email);
    }

    private function createCustomer(WholesaleInterface $data)
    {
        // Get Website ID
        $websiteId  = $this->_storeManager->getWebsite()->getWebsiteId();
        $customer = $this->customerInterfaceFactory->create();
        $customer->setWebsiteId($websiteId);

        // Preparing data for new customer
        $customer->setEmail($data->getEmail());
        $customer->setFirstname($data->getFirstname());
        $customer->setLastname($data->getLastname());
        $password = $this->random->getRandomString(8);
        $passwordHash = $this->encryptor->getHash($password, true);

        $customer = $this->customerRepository->save($customer, $passwordHash);
        return $customer;
    }



    private function checkCaptcha()
    {
        $formId = 'wholesale_form';
        $enabled = $this->_helper->isCaptchaEnable();
        if ($enabled) {
            $captcha = $this->_helper->getCaptcha($formId);
            if (!$captcha->isCorrect($this->captchaStringResolver->resolve($this->getRequest(), $formId))) {
                throw new LocalizedException(__('Incorrect captcha'));
            }
        }
    }

}
