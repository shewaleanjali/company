<?php

namespace Company\CustomerImport\Helper;

use Magento\Customer\Api\GroupRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\LocalizedException;

class DataHelper
{
    private $groupRepository;
    private $searchCriteriaBuilder;

    public function __construct(
        GroupRepositoryInterface $groupRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->groupRepository = $groupRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * Get customer group ID by group code.
     *
     * @param string $groupCode
     * @return int
     * @throws LocalizedException
     */
    public function getCustomerGroupIdByCode(string $groupCode): int
    {
        $searchCriteria = $this->searchCriteriaBuilder->addFilter('customer_group_code', $groupCode)->create();
        $groupList = $this->groupRepository->getList($searchCriteria)->getItems();

        if (empty($groupList)) {
            throw new LocalizedException(__('Customer group with code "%1" not found.', $groupCode));
        }

        $group = array_shift($groupList);
        return $group->getId();
    }
}
