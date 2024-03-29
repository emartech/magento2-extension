<?php

namespace Emartech\Emarsys\Model\Api;

use Emartech\Emarsys\Api\CustomersApiInterface;
use Emartech\Emarsys\Api\Data\ConfigInterfaceFactory;
use Emartech\Emarsys\Api\Data\CustomersApiResponseInterface;
use Emartech\Emarsys\Api\Data\CustomersApiResponseInterfaceFactory;
use Emartech\Emarsys\Helper\Customer as CustomerHelper;
use Emartech\Emarsys\Helper\LinkField;
use Exception;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\Config\Share as ConfigShare;
use Magento\Framework\Data\Collection as DataCollection;
use Magento\Framework\Webapi\Exception as WebApiException;

class CustomersApi implements CustomersApiInterface
{
    /**
     * @var ConfigShare
     */
    private $configShare;

    /**
     * @var bool|int
     */
    private $websiteId = false;

    /**
     * @var CustomersApiResponseInterfaceFactory
     */
    private $customersResponseFactory;

    /**
     * @var ConfigInterfaceFactory
     */
    private $configFactory;

    /**
     * @var int
     */
    private $minId = 0;

    /**
     * @var int
     */
    private $maxId = 0;

    /**
     * @var int
     */
    private $numberOfItems = 0;

    /**
     * @var string
     */
    private $linkField;

    /**
     * @var LinkField
     */
    private $linkFieldHelper;

    /**
     * @var CustomerHelper
     */
    private $customerHelper;

    /**
     * @param CustomersApiResponseInterfaceFactory $customersResponseFactory
     * @param ConfigInterfaceFactory               $configFactory
     * @param ConfigShare                          $configShare
     * @param LinkField                            $linkFieldHelper
     * @param CustomerHelper                       $customerHelper
     *
     * @throws Exception
     */
    public function __construct(
        CustomersApiResponseInterfaceFactory $customersResponseFactory,
        ConfigInterfaceFactory $configFactory,
        ConfigShare $configShare,
        LinkField $linkFieldHelper,
        CustomerHelper $customerHelper
    ) {
        $this->configShare = $configShare;
        $this->customersResponseFactory = $customersResponseFactory;
        $this->configFactory = $configFactory;
        $this->linkFieldHelper = $linkFieldHelper;
        $this->linkField = $this->linkFieldHelper->getEntityLinkField(CustomerInterface::class);
        $this->customerHelper = $customerHelper;
    }

    /**
     * Get
     *
     * @param int         $page
     * @param int         $pageSize
     * @param string|null $websiteId
     * @param string|null $storeId
     * @param bool|null   $onlyReg
     *
     * @return CustomersApiResponseInterface
     * @throws WebApiException
     */
    public function get(
        int $page,
        int $pageSize,
        string $websiteId = null,
        string $storeId = null,
        bool $onlyReg = null
    ): CustomersApiResponseInterface {
        $config = $this->configFactory->create();

        if (!array_key_exists($websiteId, $config->getAvailableWebsites())) {
            throw new WebApiException(__('Invalid Website'));
        }
        if (null === $onlyReg) {
            $onlyReg = false;
        }

        $this
            ->handleWebsiteId($websiteId, $onlyReg)
            ->initCollection()
            ->handleIds($page, $pageSize)
            ->handleAttributeData()
            ->handleAddressesAttributeData()
            ->joinSubscriptionStatus($websiteId)
            ->setWhere()
            ->setOrder();

        $lastPageNumber = ceil($this->numberOfItems / $pageSize);

        return $this->customersResponseFactory
            ->create()
            ->setCurrentPage($page)
            ->setLastPage($lastPageNumber)
            ->setPageSize($pageSize)
            ->setTotalCount($this->numberOfItems)
            ->setCustomers($this->handleCustomers());
    }

    /**
     * HandleWebsiteId
     *
     * @param int|null $websiteId
     * @param bool     $onlyReg
     *
     * @return CustomersApi
     */
    private function handleWebsiteId(int $websiteId = null, bool $onlyReg = false): CustomersApi
    {
        if ($onlyReg || $this->configShare->isWebsiteScope()) {
            $this->websiteId = $websiteId;
        }

        return $this;
    }

    /**
     * InitCollection
     *
     * @return CustomersApi
     */
    private function initCollection(): CustomersApi
    {
        $this->customerHelper->initCollection($this->websiteId);

        return $this;
    }

    /**
     * HandleIds
     *
     * @param int $page
     * @param int $pageSize
     *
     * @return CustomersApi
     */
    private function handleIds(int $page, int $pageSize): CustomersApi
    {
        $page --;
        $page *= $pageSize;

        $data = $this->customerHelper->handleIds($page, $pageSize, $this->websiteId);

        $this->numberOfItems = $data['numberOfItems'];
        $this->minId = $data['minId'];
        $this->maxId = $data['maxId'];

        return $this;
    }

    /**
     * HandleAttributeData
     *
     * @return CustomersApi
     */
    private function handleAttributeData(): CustomersApi
    {
        $this->customerHelper->getCustomersAttributeData(
            $this->minId,
            $this->maxId,
            $this->websiteId
        );

        return $this;
    }

    /**
     * HandleAddressesAttributeData
     *
     * @return CustomersApi
     */
    private function handleAddressesAttributeData(): CustomersApi
    {
        $this->customerHelper->getCustomersAddressesAttributeData(
            $this->minId,
            $this->maxId,
            $this->websiteId
        );

        return $this;
    }

    /**
     * SetWhere
     *
     * @return CustomersApi
     */
    protected function setWhere(): CustomersApi
    {
        $this->customerHelper->setWhere($this->linkField, $this->minId, $this->maxId, $this->websiteId);

        return $this;
    }

    /**
     * SetOrder
     *
     * @return CustomersApi
     */
    protected function setOrder(): CustomersApi
    {
        $this->customerHelper->setOrder($this->linkField, DataCollection::SORT_ORDER_ASC);

        return $this;
    }

    /**
     * HandleCustomers
     *
     * @return array
     */
    private function handleCustomers(): array
    {
        $customerArray = [];
        foreach ($this->customerHelper->getCustomerCollection() as $customer) {
            $customerArray[] = $this->customerHelper->buildCustomerObject($customer, $this->websiteId);
        }

        return $customerArray;
    }

    /**
     * JoinSubscriptionStatus
     *
     * @param int|null $websiteId
     *
     * @return CustomersApi
     */
    private function joinSubscriptionStatus(int $websiteId = null): CustomersApi
    {
        $this->customerHelper->joinSubscriptionStatus($websiteId);

        return $this;
    }
}
