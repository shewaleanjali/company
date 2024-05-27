<?php

namespace Company\CustomerImport\Test\Unit\Model\Import\Profile;

use Company\CustomerImport\Model\Import\Profile\JsonProfile;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Api\Data\CustomerInterfaceFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Api\Data\StoreInterface;
use Company\CustomerImport\Helper\CustomerGroupHelper;
use PHPUnit\Framework\TestCase;

class JsonProfileTest extends TestCase
{
    private $customerFactoryMock;
    private $customerRepositoryMock;
    private $storeManagerMock;
    private $storeMock;
    private $customerGroupHelperMock;
    private $customerMock;

    protected function setUp(): void
    {
        $this->customerFactoryMock = $this->createMock(CustomerInterfaceFactory::class);
        $this->customerRepositoryMock = $this->createMock(CustomerRepositoryInterface::class);
        $this->storeManagerMock = $this->createMock(StoreManagerInterface::class);
        $this->storeMock = $this->createMock(StoreInterface::class);
        $this->customerGroupHelperMock = $this->createMock(CustomerGroupHelper::class);
        $this->customerMock = $this->createMock(CustomerInterface::class);

        $this->storeManagerMock->method('getDefaultStoreView')->willReturn($this->storeMock);
        $this->storeMock->method('getWebsiteId')->willReturn(1);
        $this->customerGroupHelperMock->method('getCustomerGroupIdByCode')->with('General')->willReturn(1);
    }

    public function testImport(): void
    {
        $data = [
            ['fname' => 'Emma', 'lname' => 'Waters', 'emailaddress' => 'emma.waters@example.com'],
            ['fname' => 'Rami', 'lname' => 'Basara', 'emailaddress' => 'rami.basara@example.com'],
        ];
        $filePath = tempnam(sys_get_temp_dir(), 'json');
        file_put_contents($filePath, json_encode($data));

        $this->customerFactoryMock->method('create')->willReturn($this->customerMock);

        $this->customerMock->expects($this->exactly(2))->method('setFirstname')->withConsecutive(
            ['Emma'],
            ['Rami']
        )->willReturnSelf();
        $this->customerMock->expects($this->exactly(2))->method('setLastname')->withConsecutive(
            ['Waters'],
            ['Basara']
        )->willReturnSelf();
        $this->customerMock->expects($this->exactly(2))->method('setEmail')->withConsecutive(
            ['emma.waters@example.com'],
            ['rami.basara@example.com']
        )->willReturnSelf();
        $this->customerMock->expects($this->exactly(2))->method('setWebsiteId')->with(1)->willReturnSelf();
        $this->customerMock->expects($this->exactly(2))->method('setGroupId')->with(1)->willReturnSelf();

        $this->customerRepositoryMock->expects($this->exactly(2))->method('save')->with($this->customerMock);

        $jsonProfile = new JsonProfile(
            $this->customerFactoryMock,
            $this->customerRepositoryMock,
            $this->storeManagerMock,
            $this->customerGroupHelperMock
        );

        $jsonProfile->import($filePath);

        unlink($filePath);
    }
}
