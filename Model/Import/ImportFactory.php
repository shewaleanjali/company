<?php

namespace Company\CustomerImport\Model\Import;

use Company\CustomerImport\Model\Import\Profile\ProfileInterface;
use Magento\Framework\Exception\LocalizedException;

class ImportFactory
{
    private $profileHandlers;

    public function __construct(array $profileHandlers)
    {
        $this->profileHandlers = $profileHandlers;
    }

    public function create(string $profileName): ProfileInterface
    {
        if (!isset($this->profileHandlers[$profileName])) {
            throw new LocalizedException(__('Profile "%1" not found', $profileName));
        }
        return $this->profileHandlers[$profileName];
    }
}
