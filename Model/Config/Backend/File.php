<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Theiconnz\Wholesale\Model\Config\Backend;

/**
 * File model
 *
 */
class File extends \Magento\Config\Model\Config\Backend\File
{
    /**
     * @return string[]
     */
    public function _getAllowedExtensions() {
        return ['pdf'];
    }
}
