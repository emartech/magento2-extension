<?php

namespace Emartech\Emarsys\Model\Api;

use Emartech\Emarsys\Api\CustomersApiInterface;
use Emartech\Emarsys\Api\Data\ConfigInterface;
use Emartech\Emarsys\Api\Data\ConfigInterfaceFactory;
use Emartech\Emarsys\Api\Data\CustomersApiResponseInterface;
use Emartech\Emarsys\Api\Data\CustomersApiResponseInterfaceFactory;
use Emartech\Emarsys\Helper\Customer as CustomerHelper;
use Emartech\Emarsys\Helper\LinkField;
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
     * CustomersApi constructor.
     *
     * @param CustomersApiResponseInterfaceFactory $customersResponseFactory
     * @param ConfigInterfaceFactory               $configFactory
     * @param ConfigShare                          $configShare
     * @param LinkField                            $linkFieldHelper
     * @param CustomerHelper                       $customerHelper
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
     * @param int         $page
     * @param int         $pageSize
     * @param string|null $websiteId
     * @param string|null $storeId
     * @param bool|null   $onlyReg
     *
     * @return CustomersApiResponseInterface
     * @throws WebApiException
     */
    public function get($page, $pageSize, $websiteId = null, $storeId = null, $onlyReg = null)
    {
        /** @var ConfigInterface $config */
        $config = $this->configFactory->create();

        if (!array_key_exists($websiteId, $config->getAvailableWebsites())) {
            throw new WebApiException(__('Invalid Website'));
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

        return $this->customersResponseFactory->create()
            ->setCurrentPage($page)
            ->setLastPage($lastPageNumber)
            ->setPageSize($pageSize)
            ->setTotalCount($this->numberOfItems)
            ->setCustomers($this->handleCustomers());
    }

    /**
     * @param string|null $websiteId
     * @param bool        $onlyReg
     *
     * @return $this
     */
    private function handleWebsiteId($websiteId = null, $onlyReg = false)
    {
        if ($onlyReg || $this->configShare->isWebsiteScope()) {
            $this->websiteId = $websiteId;
        }
        return $this;
    }

    /**
     * @return $this
     */
    private function initCollection()
    {
        $this->customerHelper->initCollection($this->websiteId);

        return $this;
    }

    /**
     * @param int $page
     * @param int $pageSize
     *
     * @return $this
     */
    private function handleIds($page, $pageSize)
    {
        $page--;
        $page *= $pageSize;

        $data = $this->customerHelper->handleIds($page, $pageSize, $this->websiteId);

        $this->numberOfItems = $data['numberOfItems'];
        $this->minId = $data['minId'];
        $this->maxId = $data['maxId'];

        return $this;
    }

    /**
     * @return $this
     */
    private function handleAttributeData()
    {
        $this->customerHelper->getCustomersAttributeData(
            $this->minId,
            $this->maxId,
            $this->websiteId
        );

        return $this;
    }

    /**
     * @return $this
     */
    private function handleAddressesAttributeData()
    {
        $this->customerHelper->getCustomersAddressesAttributeData(
            $this->minId,
            $this->maxId,
            $this->websiteId
        );

        return $this;
    }

    /**
     * @return $this
     */
    protected function setWhere()
    {
        $this->customerHelper->setWhere($this->linkField, $this->minId, $this->maxId, $this->websiteId);

        return $this;
    }

    /**
     * @return $this
     */
    protected function setOrder()
    {
        $this->customerHelper->setOrder($this->linkField, DataCollection::SORT_ORDER_ASC);

        return $this;
    }

    /**
     * @return array
     */
    private function handleCustomers()
    {
        $customerArray = [];
        foreach ($this->customerHelper->getCustomerCollection() as $customer) {
            $customerArray[] = $this->customerHelper->buildCustomerObject($customer, $this->websiteId);
        }

        return $customerArray;
    }

    /**
     * @param int $websiteId
     *
     * @return $this
     */
    private function joinSubscriptionStatus($websiteId)
    {
        $this->customerHelper->joinSubscriptionStatus($websiteId);

        return $this;
    }
}
