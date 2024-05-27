<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Company\CustomerImport\Model\Import\Profile;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterfaceFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Customer\Api\GroupRepositoryInterface;
use Company\CustomerImport\Helper\DataHelper;

/**
 * Company Json Profile Class
 */
class JsonProfile implements ProfileInterface
{
    /**
     * Cnost for customer generl group
     */
    const Customer_Group = 'General';

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
     * Json Parser Constructor
     * @param CustomerInterfaceFactory $customerFactory
     * @param CustomerRepositoryInterface $customerRepository
     * @param StoreManagerInterface $storeManager
     * @param GroupRepositoryInterface $groupRepository
     * @param DataHelper $helper
     */
    public function __construct(
        CustomerInterfaceFactory $customerFactory,
        CustomerRepositoryInterface $customerRepository,
        StoreManagerInterface $storeManager,
        GroupRepositoryInterface $groupRepository,
        DataHelper $helper
    ) {
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
        //parse json file
        $data = json_decode(file_get_contents($source), true);
        foreach ($data as $row) {
            $customer = $this->customerFactory->create();
            $customer->setFirstname($row['fname'])
                ->setLastname($row['lname'])
                ->setEmail($row['emailaddress'])
                ->setWebsiteId($defaultWebsiteId) // Default website ID
                ->setGroupId($generalCustomerGroupId); // General customer group ID;
            $this->customerRepository->save($customer);
        }
    }
}
