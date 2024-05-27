<?php

namespace Company\CustomerImport\Test\Unit\Helper;

use Company\CustomerImport\Helper\DataHelper;
use Magento\Customer\Api\GroupRepositoryInterface;
use Magento\Customer\Api\Data\GroupInterface;
use Magento\Framework\Api\SearchCriteria;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchResultsInterface;
use Magento\Framework\Exception\LocalizedException;
use PHPUnit\Framework\TestCase;

class DataHelperTest extends TestCase
{
    private $groupRepositoryMock;
    private $searchCriteriaBuilderMock;
    private $searchCriteriaMock;
    private $searchResultsMock;
    private $groupMock;

    protected function setUp(): void
    {
        $this->groupRepositoryMock = $this->createMock(GroupRepositoryInterface::class);
        $this->searchCriteriaBuilderMock = $this->createMock(SearchCriteriaBuilder::class);
        $this->searchCriteriaMock = $this->createMock(SearchCriteria::class);
        $this->searchResultsMock = $this->createMock(SearchResultsInterface::class);
        $this->groupMock = $this->createMock(GroupInterface::class);
    }

    public function testGetCustomerGroupIdByCode(): void
    {
        $groupCode = 'General';
        $groupId = 1;

        $this->searchCriteriaBuilderMock->expects($this->once())
            ->method('addFilter')
            ->with('customer_group_code', $groupCode)
            ->willReturnSelf();
        $this->searchCriteriaBuilderMock->expects($this->once())
            ->method('create')
            ->willReturn($this->searchCriteriaMock);

        $this->groupRepositoryMock->expects($this->once())
            ->method('getList')
            ->with($this->searchCriteriaMock)
            ->willReturn($this->searchResultsMock);

        $this->searchResultsMock->expects($this->once())
            ->method('getItems')
            ->willReturn([$this->groupMock]);

        $this->groupMock->expects($this->once())
            ->method('getId')
            ->willReturn($groupId);

        $dataHelper = new DataHelper(
            $this->groupRepositoryMock,
            $this->searchCriteriaBuilderMock
        );

        $result = $dataHelper->getCustomerGroupIdByCode($groupCode);
        $this->assertEquals($groupId, $result);
    }

    public function testGetCustomerGroupIdByCodeThrowsException(): void
    {
        $groupCode = 'NonExistentGroup';

        $this->searchCriteriaBuilderMock->expects($this->once())
            ->method('addFilter')
            ->with('customer_group_code', $groupCode)
            ->willReturnSelf();
        $this->searchCriteriaBuilderMock->expects($this->once())
            ->method('create')
            ->willReturn($this->searchCriteriaMock);

        $this->groupRepositoryMock->expects($this->once())
            ->method('getList')
            ->with($this->searchCriteriaMock)
            ->willReturn($this->searchResultsMock);

        $this->searchResultsMock->expects($this->once())
            ->method('getItems')
            ->willReturn([]);

        $dataHelper = new DataHelper(
            $this->groupRepositoryMock,
            $this->searchCriteriaBuilderMock
        );

        $this->expectException(LocalizedException::class);
        $this->expectExceptionMessage('Customer group with code "NonExistentGroup" not found.');

        $dataHelper->getCustomerGroupIdByCode($groupCode);
    }
}
