<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Company\CustomerImport\Model\Import\Profile;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterfaceFactory;
use Magento\Framework\File\Csv;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Customer\Api\GroupRepositoryInterface;
use Company\CustomerImport\Helper\DataHelper;

/**
 * Company Csv Profile Class
 */
class CsvProfile implements ProfileInterface
{

    /**
     * Cnost for customer generl group
     */
    const Customer_Group = 'General';

    /**
     * @var Csv $csvParser
     */
    private $csvParser;

    /**
     * @var CustomerInterfaceFactory $customerFactory
     */
    private $customerFactory;

    /**
     * @var CustomerRepositoryInterface $customerRepository
     */
    private $customerRepository;

    /**
     * @var StoreManagerInterface $storeManager
     */
    private $storeManager;

    /**
     * @var GroupRepositoryInterface $groupRepository
     */
    private $groupRepository;

    /**
     * @var DataHelper $helper
     */
    private $helper;

    /**
     * Csv Parser Constructor
     * @param Csv $csvParser
     * @param CustomerInterfaceFactory $customerFactory
     * @param CustomerRepositoryInterface $customerRepository
     * @param StoreManagerInterface $storeManager
     * @param GroupRepositoryInterface $groupRepository
     * @param DataHelper $helper
     */
    public function __construct(
        Csv $csvParser,
        CustomerInterfaceFactory $customerFactory,
        CustomerRepositoryInterface $customerRepository,
        StoreManagerInterface $storeManager,
        GroupRepositoryInterface $groupRepository,
        DataHelper $helper
    ) {
        $this->csvParser = $csvParser;
        $this->customerFactory = $customerFactory;
        $this->customerRepository = $customerRepository;
        $this->storeManager = $storeManager;
        $this->groupRepository = $groupRepository;
        $this->helper = $helper;
    }

    /**
     * Import function
     * @aram string $source
     * 
     * return void 
     */
    public function import(string $source): void
    {
        //get default website id
        $defaultWebsiteId = $this->storeManager->getDefaultStoreView()->getWebsiteId();
        //get customer general group
        $generalCustomerGroupId = $this->helper->getCustomerGroupIdByCode(self::Customer_Group);       
        //parse csv data
        $data = $this->csvParser->getData($source);       
        $dataHeader = $data['0'];
        unset($data['0']);        
        foreach ($data as $row) {
            $customer = $this->customerFactory->create();
            $customer->setFirstname($row[0])
                ->setLastname($row[1])
                ->setEmail($row[2])
                ->setWebsiteId($defaultWebsiteId) // Default website ID
                ->setGroupId($generalCustomerGroupId); // General customer group ID;
            $this->customerRepository->save($customer);
        }
    }
}
