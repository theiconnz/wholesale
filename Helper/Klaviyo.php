<?php

namespace Theiconnz\Wholesale\Helper;

use Theiconnz\Wholesale\Helper\CaptchaFactory;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Api\Data\StoreInterface;

/**
 * Class Data
 */
class Klaviyo extends AbstractHelper
{
    const XML_KLAVIYO_ENABLE = 'wholesale/klaviyo/enable';

    const XML_KLAVIYO_LIST = 'wholesale/klaviyo/list';


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

    /**
     * Data constructor.
     * @param Context $context
     * @param CaptchaFactory $factory
     * @param StoreManagerInterface $storeManager
     */
    public function __construct
    (
        Context $context,
        StoreManagerInterface $storeManager,
        \Magento\Captcha\Model\CaptchaFactory $factory
    )
    {
        $this->storeManager = $storeManager;
        $this->_factory = $factory;
        parent::__construct($context);
    }

    /**
     * @param StoreInterface|null $store
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function isEnable()
    {
        return $this->getStoreConfig(
            self::XML_IS_ENABLE
        );
    }

    public function getSenderEmail()
    {
        return $this->getStoreConfig(
            self::XML_SENDER_EMAIL_IDENTITY
        );
    }

    public function getAdminEmailTemplate(){
        return $this->getStoreConfig(
            self::XML_RECIPIENT
        );
    }

    public function getWholesaleGroup(){
        return $this->getStoreConfig(
            self::XML_CUSTOMER_GROUP
        );
    }


    public function isCaptchaEnable(){
        return $this->getStoreConfig(
            self::XML_CAPTCHA_ENABLE
        );
    }

    /**
     * @param $path
     * @param StoreInterface|null $store
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getStoreConfig($path, StoreInterface $store = null)
    {
        if ($store === null) {
            $store = $this->getStore();
        }

        return $store->getConfig($path);
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
            $captchaType = ucfirst($this->getStoreConfig('customer/captcha/type'));
            if (!$captchaType) {
                $captchaType = self::DEFAULT_CAPTCHA_TYPE;
            } elseif ($captchaType == 'Default') {
                $captchaType = $captchaType . 'Model';
            }

            $this->_captcha[$formId] = $this->_factory->create($captchaType, $formId);
        }
        return $this->_captcha[$formId];
    }
}
