<?php

namespace Theiconnz\Wholesale\Helper;

use Theiconnz\Wholesale\Helper\CaptchaFactory;
use Theiconnz\Wholesale\Helper\State;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Filesystem\DirectoryList;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Api\Data\StoreInterface;

/**
 * Class Data
 */
class Data extends AbstractHelper
{
    const XML_IS_ENABLE = 'wholesale/general/enabled';

    const XML_SIGNUP = 'wholesale/general/signup';

    const XML_SENDER_EMAIL_IDENTITY = 'wholesale/email/sender_email_identity';

    const XML_RECIPIENT = 'wholesale/email/recipient_email';

    const XML_PATH_EMAIL_TEMPLATE = 'wholesale/email/customernotification_template';

    const XML_PATH_ADMIN_EMAIL_TEMPLATE = 'wholesale/email/adminnotification_template';

    const XML_CUSTOMER_GROUP = 'wholesale/general/customer_group';

    const XML_CAPTCHA_ENABLE = 'wholesale/general/captcha';

    const XML_SIGNUP_ENABLE = 'wholesale/general/signup';

    const XML_UPGRADE_CUSTOMER = 'wholesale/general/upgrade_customer_group';

    const XML_STOP_CUSTOMER_UPGRADE = 'wholesale/general/stop_customer_upgrade_email';

    const XML_SYSTEM_EMAIL_SENT = 'wholesale/email/stop_email_sent';

    const DEFAULT_CAPTCHA_TYPE = 'Zend';

    const PDF_ATTACHMENT_ENABLE = 'wholesale/wholesale/enable';

    const PDF_ATTACHMENT = 'wholesale/wholesale/signupfile';


    const PDF_ATTACHMENT_DOWNLOAD = 'wholesale';

    const XML_DOWNLOAD_URLPARAMS = "%s/view/download/m/%s/e/%s";

    const XML_UPLOAD_LINK = "wholesale/wholesale/linktofile";

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Captcha\Model\CaptchaFactory
     */
    protected $_factory;

    /**
     * List uses Models of Captcha
     * @var array
     */
    protected $_captcha = [];

    protected $_scopeConfig;

    protected $_request;
    protected $_state;
    protected $_storeId;

    /**
     * Data constructor.
     * @param Context $context
     * @param StoreManagerInterface $storeManager
     * @param State $state
     * @param CaptchaFactory $factory
     * @param DirectoryList $dir
     */
    public function __construct
    (
        Context $context,
        StoreManagerInterface $storeManager,
        \Magento\Framework\App\State $state,
        \Magento\Captcha\Model\CaptchaFactory $factory,
        DirectoryList $dir
    )
    {
        $this->_scopeConfig = $context->getScopeConfig();
        $this->_request = $context->getRequest();
        $this->storeManager = $storeManager;
        $this->_state = $state;
        $this->_storeId = $storeManager->getStore()->getId();
        $this->_factory = $factory;
        $this->_dir = $dir;
        parent::__construct($context);
    }


    /**
     * Getter method for a given scope setting
     * @param string $path
     * @param int|null $storeId
     * @return
     */
    protected function getScopeSetting($path, $storeId = null)
    {
        $this->checkAreaCode();

        if (isset($storeId)) {
            $scopedStoreCode = $storeId;
        } elseif ($this->_state->getAreaCode() == \Magento\Framework\App\Area::AREA_ADMINHTML) {
            $scopedStoreCode = $this->_request->getParam('store');
            $scopedWebsiteCode = $this->_request->getParam('website');
        } else {
            // In frontend area. Only concerned with store for frontend.
            $scopedStoreCode = $this->_storeId;
        }

        if (isset($scopedStoreCode)) {
            $scope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
            return $this->_scopeConfig->getValue($path, $scope, $scopedStoreCode);
        } elseif (isset($scopedWebsiteCode)) {
            $scope = \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE;
            return $this->_scopeConfig->getValue($path, $scope, $scopedWebsiteCode);
        } else {
            return $this->_scopeConfig->getValue($path);
        };
    }

    /**
     * @param StoreInterface|null $store
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function isEnable()
    {
        return $this->getScopeSetting(
            self::XML_IS_ENABLE
        );
    }


    public function stopWelcomeEmail()
    {
        return $this->getScopeSetting(
            self::XML_STOP_CUSTOMER_UPGRADE
        );
    }

    public function getCustomerGroup()
    {
        return $this->getScopeSetting(
            self::XML_CUSTOMER_GROUP
        );
    }

    public function getSignUp()
    {
        return $this->getScopeSetting(
            self::XML_SIGNUP_ENABLE
        );
    }

    public function getUpgradeCustomer()
    {
        return $this->getScopeSetting(
            self::XML_UPGRADE_CUSTOMER
        );
    }

    public function getSenderEmail()
    {
        return $this->getScopeSetting(
            self::XML_SENDER_EMAIL_IDENTITY
        );
    }

    public function getAdminEmailTemplate(){
        return $this->getScopeSetting(
            self::XML_PATH_ADMIN_EMAIL_TEMPLATE
        );
    }


    public function getAdminEmailNotificationTo(){
        return $this->getScopeSetting(
            self::XML_RECIPIENT
        );
    }

    public function getWholesaleGroup(){
        return $this->getScopeSetting(
            self::XML_CUSTOMER_GROUP
        );
    }


    public function isCaptchaEnable(){
        return $this->getScopeSetting(
            self::XML_CAPTCHA_ENABLE
        );
    }

    /**
     * Get store
     *
     * @param null $storeId
     * @return StoreInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getStore($storeId = null)
    {
        return $this->storeManager->getStore($storeId);
    }

    /**
     * Get Captcha
     *
     * @param string $formId
     * @return \Magento\Captcha\Model\CaptchaInterface
     */
    public function getCaptcha($formId)
    {
        if (!array_key_exists($formId, $this->_captcha)) {
            $captchaType = ucfirst($this->getScopeSetting('customer/captcha/type'));
            if (!$captchaType) {
                $captchaType = self::DEFAULT_CAPTCHA_TYPE;
            } elseif ($captchaType == 'Default') {
                $captchaType = $captchaType . 'Model';
            }

            $this->_captcha[$formId] = $this->_factory->create($captchaType, $formId);
        }
        return $this->_captcha[$formId];
    }


    /**
     * helper function to allow this class to be used in Setup files
     */
    protected function checkAreaCode()
    {
        /**
         * when this class is accessed from cli commands, there is no area code set
         * (since there is no actual session running persay)
         * this try-catch block is needed to allow this helper to be used in setup files
         */
        try{
            $this->_state->getAreaCode();
        }
        catch (\Magento\Framework\Exception\LocalizedException $ex) {
            $this->_state->setAreaCode(\Magento\Framework\App\Area::AREA_ADMINHTML);
        }
    }

    public function isAttachmentEnable()
    {
        return $this->getScopeSetting(
            self::PDF_ATTACHMENT_ENABLE
        );
    }

    public function getPdffile()
    {
        $_fileLocation=$this->getScopeSetting(
            self::PDF_ATTACHMENT
        );
        return self::PDF_ATTACHMENT_DOWNLOAD ."/".$_fileLocation;
    }

    public function getCustomerTemplate()
    {
        return $this->getScopeSetting(
            self::XML_PATH_EMAIL_TEMPLATE
        );
    }


    public function getEmailSender()
    {
        return $this->getScopeSetting(
            self::XML_SENDER_EMAIL_IDENTITY
        );
    }

    public function getDownloadLink(){
        $_pdfFile=$this->getPdffile();
        $_pdfFilePath=$this->dir->getPath('media') . $_pdfFile;
        if(is_file($_pdfFilePath)) {
            return $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB . $_pdfFile );
        }
        return $this->getDownloadUrl();
    }

    public function getDownloadUrl()
    {
        return $this->getScopeSetting(
            self::XML_UPLOAD_LINK
        );
    }


    public function getStopEmailSentFromSystem()
    {
        return $this->getScopeSetting(
            self::XML_SYSTEM_EMAIL_SENT
        );
    }
}
