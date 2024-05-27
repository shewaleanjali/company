<?php

namespace Company\CustomerImport\Test\Unit\Model\Import\Profile;

use Company\CustomerImport\Model\Import\Profile\CsvProfile;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Api\Data\CustomerInterfaceFactory;
use Magento\Framework\File\Csv;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Api\Data\StoreInterface;
use Company\CustomerImport\Helper\DataHelper;
use PHPUnit\Framework\TestCase;

class CsvProfileTest extends TestCase
{
    private $csvParserMock;
    private $customerFactoryMock;
    private $customerRepositoryMock;
    private $storeManagerMock;
    private $storeMock;
    private $dataHelperMock;
    private $customerMock;

    protected function setUp(): void
    {
        $this->csvParserMock = $this->createMock(Csv::class);
        $this->customerFactoryMock = $this->createMock(CustomerInterfaceFactory::class);
        $this->customerRepositoryMock = $this->createMock(CustomerRepositoryInterface::class);
        $this->storeManagerMock = $this->createMock(StoreManagerInterface::class);
        $this->storeMock = $this->createMock(StoreInterface::class);
        $this->dataHelperMock = $this->createMock(DataHelper::class);
        $this->customerMock = $this->createMock(CustomerInterface::class);

        $this->storeManagerMock->method('getDefaultStoreView')->willReturn($this->storeMock);
        $this->storeMock->method('getWebsiteId')->willReturn(1);
        $this->dataHelperMock->method('getCustomerGroupIdByCode')->with('General')->willReturn(1);
    }

    public function testImport(): void
    {
        $data = [
            ['Firstname', 'Lastname', 'Email'],
            ['Emma', 'Waters', 'emma.waters@example.com'],
            ['Rami', 'Basara', 'rami.basara@example.com'],
        ];
        $this->csvParserMock->method('getData')->willReturn($data);

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

        $csvProfile = new CsvProfile(
            $this->csvParserMock,
            $this->customerFactoryMock,
            $this->customerRepositoryMock,
            $this->storeManagerMock,
            $this->dataHelperMock
        );

        $csvProfile->import('dummy/path/to/csv');
    }
}
