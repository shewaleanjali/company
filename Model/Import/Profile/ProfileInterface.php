<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Company\CustomerImport\Model\Import\Profile;

/**
 * Interface ProfileInterface
 *
 */
interface ProfileInterface
{
     /**
     * Import the customer data
     *
     * @param string $source
     * @return void
     */
    public function import(string $source): void;
}
