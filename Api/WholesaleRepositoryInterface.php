<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Theiconnz\Wholesale\Api;

use Theiconnz\Wholesale\Api\Data;

/**
 * Results CRUD interface.
 * @api
 * @since 100.0.2
 */
interface WholesaleRepositoryInterface
{
    /**
     * Save Results.
     *
     * @param \Theiconnz\Wholesale\Api\Data\WholesaleInterface $interface
     * @return \Theiconnz\Wholesale\Api\Data\WholesaleInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(\Theiconnz\Wholesale\Api\Data\WholesaleInterface $interface);

    /**
     * Retrieve Results.
     *
     * @param string $id
     * @return \Theiconnz\Wholesale\Api\Data\WholesaleInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($id);


    /**
     * Retrieve Result by email.
     *
     * @param string $email
     * @return \Theiconnz\Wholesale\Api\Data\WholesaleInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getByEmail($email);

    /**
     * Retrieve Resultss matching the specified criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Theiconnz\Wholesale\Api\Data\WholesaleSearchWholesaleInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * Delete.
     *
     * @param \Theiconnz\Wholesale\Api\Data\WholesaleInterface $interface
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(\Theiconnz\Wholesale\Api\Data\WholesaleInterface $interface);

    /**
     * Delete by ID.
     *
     * @param string $id
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($id);
}
