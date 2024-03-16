<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Theiconnz\Wholesale\Model;

use Theiconnz\Wholesale\Model\id;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Data\Collection\AbstractDb;
use Theiconnz\Wholesale\Api\Data\WholesaleInterface;

/**
 * Results model
 *
 */
class Wholesale extends AbstractModel implements WholesaleInterface, IdentityInterface
{
    /**
     * CMS block cache tag
     */
    const CACHE_TAG = 'wholesale';

    /**
     * New file name  prefix
     */
    const PREFIX = 'wholesale_';

    const STATUS_ENABLED=1;

    /**#@-*/

    /**#@-*/
    protected $_cacheTag = self::CACHE_TAG;

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'wholesale_';

    /**
     * @param Context $context
     * @param \Magento\Framework\Registry $registry,
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        \Magento\Framework\Registry $registry,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Construct.
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Theiconnz\Wholesale\Model\ResourceModel\Wholesale::class);
    }

    /**
     * Prevent blocks recursion
     *
     * @return AbstractModel
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function beforeSave()
    {
        if ($this->hasDataChanges()) {
            $this->setUpdateTime(null);
        }
        parent::beforeSave();
        return $this;
    }

    /**
     * Get identities
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId(), self::CACHE_TAG ];
    }

    /**
     * Retrieve id
     *
     * @return int
     */
    public function getId()
    {
        return $this->getData(self::ID);
    }

    /**
     * Retrieve customer id
     *
     * @return id
     */
    public function getCustomerId()
    {
        return (int)$this->getData(self::CUSTOMER_ID);
    }

    /**
     * Retrieve store id
     *
     * @return id
     */
    public function getStoreId()
    {
        return (int)$this->getData(self::STORE_ID);
    }

    /**
     * Retrieve name
     *
     * @return string
     */
    public function getFirstname()
    {
        return (string)$this->getData(self::FIRSTNAME);
    }

    /**
     * Retrieve name
     *
     * @return string
     */
    public function getLastname()
    {
        return (string)$this->getData(self::LASTNAME);
    }

    /**
     * Retrieve email
     *
     * @return string
     */
    public function getEmail()
    {
        return (string)$this->getData(self::EMAIL);
    }

    /**
     * Retrieve phone
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->getData(self::PHONE);
    }


    /**
     * Retrieve business name
     *
     * @return string
     */
    public function getBusinessName()
    {
        return $this->getData(self::BUSINESS_NAME);
    }

    /**
     * Retrieve location
     *
     * @return string
     */
    public function getLocation()
    {
        return $this->getData(self::LOCATION);
    }

    /**
     * Retrieve describe
     *
     * @return string
     */
    public function getDescribe()
    {
        return $this->getData(self::DESCRIBE);
    }

    /**
     * Retrieve comment
     *
     * @return string
     */
    public function getComment()
    {
        return $this->getData(self::COMMENT);
    }

    /**
     * Retrieve block creation time
     *
     * @return string
     */
    public function getCreationTime()
    {
        return $this->getData(self::CREATION_TIME);
    }

    /**
     * Retrieve block update time
     *
     * @return string
     */
    public function getUpdateTime()
    {
        return $this->getData(self::UPDATE_TIME);
    }

    /**
     * Retrieve is active
     *
     * @return int
     */
    public function getIsActive()
    {
        return $this->getData(self::ISACTIVE);
    }

    /**
     * Retrieve is sign email sent
     *
     * @return int
     */
    public function getSignEmailSent()
    {
        return $this->getData(self::SGEMAILSENT);
    }

    /**
     * Get is sign email sent
     *
     * @return int
     */
    public function getDocumentAttached()
    {
        return $this->getData(self::SGEMAILSENT);
    }

    /**
     * Set ID
     *
     * @param int $value
     * @return WholesaleInterface
     */
    public function setId($id)
    {
        return $this->setData(self::ID, $id);
    }

    /**
     * Set customer id
     *
     * @param int $value
     * @return WholesaleInterface
     */
    public function setCustomerId($value)
    {
        return $this->setData(self::CUSTOMER_ID, $value);
    }

    /**
     * Set store id
     *
     * @param int $value
     * @return WholesaleInterface
     */
    public function setStoreId($value)
    {
        return $this->setData(self::STORE_ID, $value);
    }
    /**
     * Set name
     *
     * @param string $value
     * @return WholesaleInterface
     */
    public function setFirstname($name)
    {
        return $this->setData(self::FIRSTNAME, $name);
    }


    /**
     * Set last name
     *
     * @param string $value
     * @return WholesaleInterface
     */
    public function setLastname($name)
    {
        return $this->setData(self::LASTNAME, $name);
    }


    /**
     * Set email
     *
     * @param string $value
     * @return WholesaleInterface
     */
    public function setEmail($value)
    {
        return $this->setData(self::EMAIL, $value);
    }
    /**
     * Set phone
     *
     * @param string $value
     * @return WholesaleInterface
     */
    public function setPhone($value)
    {
        return $this->setData(self::PHONE, $value);
    }
    /**
     * Set business name
     *
     * @param string $value
     * @return WholesaleInterface
     */
    public function setBusinessName($value)
    {
        return $this->setData(self::BUSINESS_NAME, $value);
    }
    /**
     * Set location
     *
     * @param string $value
     * @return WholesaleInterface
     */
    public function setLocation($value)
    {
        return $this->setData(self::LOCATION, $value);
    }
    /**
     * Set describe
     *
     * @param string $value
     * @return WholesaleInterface
     */
    public function setDescribe($value)
    {
        return $this->setData(self::DESCRIBE, $value);
    }
    /**
     * Set comment
     *
     * @param string $value
     * @return WholesaleInterface
     */
    public function setComment($value)
    {
        return $this->setData(self::COMMENT, $value);
    }
    /**
     * Set creation time
     *
     * @param string $value
     * @return WholesaleInterface
     */
    public function setCreationTime($value)
    {
        return $this->setData(self::CREATION_TIME, $value);
    }

    /**
     * Set update time
     *
     * @param string $value
     * @return WholesaleInterface
     */
    public function setUpdateTime($value)
    {
        return $this->setData(self::UPDATE_TIME, $value);
    }

    /**
     * Set is active
     *
     * @param int $value
     * @return WholesaleInterface
     */
    public function setIsActive($value)
    {
        return $this->setData(self::ISACTIVE, $value);
    }


    /**
     * Set is sign email sent
     *
     * @param int $value
     * @return WholesaleInterface
     */
    public function setSignEmailSent($value)
    {
        return $this->setData(self::SGEMAILSENT, $value);
    }



    /**
     * Signup document attached
     *
     * @param int $value
     * @return WholesaleInterface
     */
    public function setDocumentAttached($value)
    {
        return $this->setData(self::SGNATTACHED, $value);
    }
}
