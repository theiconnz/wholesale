<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Theiconnz\Wholesale\Api\Data;

use Theiconnz\Wholesale\Api\Data\ResultsInterface;

/**
 * Results interface.
 * @api
 * @since 0.0.1
 */
interface WholesaleInterface
{
    /**#@+
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const ID          = 'id';
    const CUSTOMER_ID = 'customer_id';
    const STORE_ID      = 'store_id';
    const FIRSTNAME     = 'firstname';
    const LASTNAME      = 'lastname';
    const EMAIL         = 'email';
    const PHONE         = 'phone';
    const BUSINESS_NAME       = 'business_name';
    const LOCATION       = 'location';
    const DESCRIBE       = 'describe';
    const COMMENT     = 'comment';
    const CREATION_TIME = 'creation_time';
    const UPDATE_TIME   = 'update_time';

    const ISACTIVE   = 'is_active';
    const SGEMAILSENT   = 'signup_email_sent';

    const SGNATTACHED   = 'documentattached';
    /**#@-*/

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId();

    /**
     * Get customer id
     *
     * @return int
     */
    public function getCustomerId();

    /**
     * Get store id
     *
     * @return int
     */
    public function getStoreId();

    /**
     * Get first name
     *
     * @return string|null
     */
    public function getFirstname();

    /**
     * Get last name
     *
     * @return string|null
     */
    public function getLastname();

    /**
     * Get email
     *
     * @return string|null
     */
    public function getEmail();

    /**
     * Get phone
     *
     * @return string|null
     */
    public function getPhone();

    /**
     * Get Business name
     *
     * @return string|null
     */
    public function getBusinessName();


    /**
     * Get Location
     *
     * @return string|null
     */
    public function getLocation();


    /**
     * Get describe
     *
     * @return string|null
     */
    public function getDescribe();


    /**
     * Get comment
     *
     * @return string|null
     */
    public function getComment();

    /**
     * Get creation time
     *
     * @return string|null
     */
    public function getCreationTime();

    /**
     * Get update time
     *
     * @return string|null
     */
    public function getUpdateTime();


    /**
     * Get is active wholesale customer
     *
     * @return int
     */
    public function getIsActive();


    /**
     * Get is sign email sent
     *
     * @return int
     */
    public function getSignEmailSent();

    /**
     * Sign up document attached
     *
     * @return int
     */
    public function getDocumentAttached();

    /**
     * Set ID
     *
     * @param int $id
     * @return ResultsInterface
     */
    public function setId($id);

    /**
     * Set customer id
     *
     * @param int $value
     * @return ResultsInterface
     */
    public function setCustomerId($value);

    /**
     * Set store id
     *
     * @param int $value
     * @return ResultsInterface
     */
    public function setStoreId($value);

    /**
     * Set first name
     *
     * @param string $name
     * @return ResultsInterface
     */
    public function setFirstname($value);

    /**
     * Set last name
     *
     * @param string $name
     * @return ResultsInterface
     */
    public function setLastname($value);

    /**
     * Set email
     *
     * @param string $value
     * @return ResultsInterface
     */
    public function setEmail($value);

    /**
     * Set phone
     *
     * @param string $value
     * @return ResultsInterface
     */
    public function setPhone($value);

    /**
     * Set business name
     *
     * @param string $value
     * @return ResultsInterface
     */
    public function setBusinessName($value);

    /**
     * Set location
     *
     * @param string $value
     * @return ResultsInterface
     */
    public function setLocation($value);


    /**
     * Set describe
     *
     * @param string $value
     * @return ResultsInterface
     */
    public function setDescribe($value);


    /**
     * Set Comment
     *
     * @param string $value
     * @return ResultsInterface
     */
    public function setComment($value);


    /**
     * Set creation time
     *
     * @param string $value
     * @return ResultsInterface
     */
    public function setCreationTime($value);

    /**
     * Set update time
     *
     * @param string $value
     * @return ResultsInterface
     */
    public function setUpdateTime($value);

    /**
     * Set is active wholesale customer
     *
     * @param int $value
     * @return ResultsInterface
     */
    public function setIsActive($value);

    /**
     * Set is sign email sent
     *
     * @param int $value
     * @return ResultsInterface
     */
    public function setSignEmailSent($value);

    /**
     * Signup document attached
     *
     * @param int $value
     * @return ResultsInterface
     */
    public function setDocumentAttached($value);


}
