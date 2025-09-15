<?php
/**
 * Copyright ©2018 Itegration Ltd., Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Emartech\Emarsys\Model\ResourceModel\Api;

use Emartech\Emarsys\Helper\DataSource as DataSourceHelper;
use Magento\Customer\Model\ResourceModel\Attribute\CollectionFactory as CustomerAttributeCollectionFactory;
use Magento\Customer\Model\ResourceModel\Customer as CustomerResourceModel;
use Magento\Customer\Model\ResourceModel\Customer\Collection;
use Magento\Eav\Model\Entity\Attribute;
use Magento\Eav\Model\Entity\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Model\ResourceModel\Db\VersionControl\RelationComposite;
use Magento\Framework\Model\ResourceModel\Db\VersionControl\Snapshot;
use Magento\Framework\Model\ResourceModel\Iterator;
use Magento\Framework\Stdlib\DateTime;
use Magento\Framework\Validator\Factory;
use Magento\Store\Model\ResourceModel\Store\CollectionFactory as StoreCollectionFactory;
use Magento\Store\Model\StoreManagerInterface;

class Customer extends CustomerResourceModel
{
    public const CUSTOMER_ENTITY_TYPE_ID = 1;

    /**
     * @var array
     */
    private $attributeData = [];

    /**
     * @var string
     */
    private $mainTable = '';

    /**
     * @var CustomerAttributeCollectionFactory
     */
    private $customerAttributeCollectionFactory;

    /**
     * @var Iterator
     */
    private $iterator;
    /**
     * @var string
     */
    private $linkField = 'entity_id';

    /**
     * @var array
     */
    private $stores = [];

    /**
     * @var StoreCollectionFactory
     */
    private $storeCollectionFactory;

    /**
     * @var DataSourceHelper
     */
    private $dataSourceHelper;

    /**
     * Customer constructor.
     *
     * @param CustomerAttributeCollectionFactory $customerAttributeCollectionFactory
     * @param Iterator                           $iterator
     * @param Context                            $context
     * @param Snapshot                           $entitySnapshot
     * @param RelationComposite                  $entityRelationComposite
     * @param ScopeConfigInterface               $scopeConfig
     * @param Factory                            $validatorFactory
     * @param DateTime                           $dateTime
     * @param StoreManagerInterface              $storeManager
     * @param StoreCollectionFactory             $storeCollectionFactory
     * @param DataSourceHelper                   $dataSourceHelper
     * @param array                              $data
     */
    public function __construct(
        CustomerAttributeCollectionFactory $customerAttributeCollectionFactory,
        Iterator $iterator,
        Context $context,
        Snapshot $entitySnapshot,
        RelationComposite $entityRelationComposite,
        ScopeConfigInterface $scopeConfig,
        Factory $validatorFactory,
        DateTime $dateTime,
        StoreManagerInterface $storeManager,
        StoreCollectionFactory $storeCollectionFactory,
        DataSourceHelper $dataSourceHelper,
        $data = []
    ) {
        $this->customerAttributeCollectionFactory = $customerAttributeCollectionFactory;
        $this->iterator = $iterator;
        $this->storeCollectionFactory = $storeCollectionFactory;
        $this->dataSourceHelper = $dataSourceHelper;

        parent::__construct(
            $context,
            $entitySnapshot,
            $entityRelationComposite,
            $scopeConfig,
            $validatorFactory,
            $dateTime,
            $storeManager,
            $data
        );
    }

    /**
     * JoinSubscriptionStatus
     *
     * @param Collection $collection
     * @param int        $websiteId
     *
     * @return void
     */
    public function joinSubscriptionStatus(Collection $collection, int $websiteId): void
    {
        $storeIds = $this->getStoreIdsFromWebsite($websiteId);

        $subSelect = $this->_resource
            ->getConnection()->select()
            ->from(
                $this->getTable(
                    'newsletter_subscriber'
                ),
                ['subscriber_status']
            )
            ->where('customer_id = e.entity_id')
            ->where('store_id IN (?)', $storeIds)
            ->order('subscriber_id DESC')
            ->limit(1, 0);

        $collection->getSelect()->columns(
            [
                'accepts_marketing' => $subSelect,
            ]
        );
    }

    /**
     * HandleIds
     *
     * @param int      $page
     * @param int      $pageSize
     * @param int|null $websiteId
     *
     * @return array
     */
    public function handleIds(int $page, int $pageSize, ?int $websiteId = null): array
    {
        $customerTable = $this->getTable('customer_entity');

        $itemsCountQuery = $this->_resource
            ->getConnection()
            ->select()
            ->from(
                $customerTable,
                ['count' => 'count(' . $this->linkField . ')']
            );

        if ($websiteId) {
            $itemsCountQuery->where('website_id = ?', $websiteId);
        }

        $numberOfItems = $this->_resource->getConnection()->fetchOne($itemsCountQuery);

        $subFields['eid'] = $this->linkField;

        $subSelect = $this->_resource
            ->getConnection()
            ->select()
            ->from($customerTable, $subFields);

        if ($websiteId) {
            $subSelect->where('website_id = ?', $websiteId);
        }

        $subSelect
            ->order($this->linkField)
            ->limit($pageSize, $page);

        $fields = ['minId' => 'min(tmp.eid)', 'maxId' => 'max(tmp.eid)'];

        $idQuery = $this->_resource
            ->getConnection()
            ->select()
            ->from(['tmp' => $subSelect], $fields);

        $minMaxValues = $this->_resource->getConnection()->fetchRow($idQuery);

        $returnArray = [
            'numberOfItems' => (int) $numberOfItems,
            'minId'         => (int) $minMaxValues['minId'],
            'maxId'         => (int) $minMaxValues['maxId'],
        ];

        return $returnArray;
    }

    /**
     * GetAttributeData
     *
     * @param int      $minCustomerId
     * @param int      $maxCustomerId
     * @param string[] $attributeCodes
     * @param int|null $websiteId
     *
     * @return array
     */
    public function getAttributeData(
        int $minCustomerId,
        int $maxCustomerId,
        array $attributeCodes,
        ?int $websiteId = null
    ): array {
        $this->mainTable = $this->getEntityTable();
        $this->attributeData = [];

        $attributeMapper = [];
        $mainTableFields = [];
        $attributeTables = [];
        $sourceModels = [];

        $customerAttributeCollection = $this->customerAttributeCollectionFactory
            ->create()
            ->addFieldToFilter(
                'entity_type_id',
                ['eq' => self::CUSTOMER_ENTITY_TYPE_ID]
            )
            ->addFieldToFilter('attribute_code', ['in' => $attributeCodes]);

        $failedSources = [];

        /** @var Attribute $customerAttribute */
        foreach ($customerAttributeCollection as $customerAttribute) {
            if ($sourceModel = $customerAttribute->getSourceModel()) {
                try {
                    $sourceModels[$customerAttribute->getAttributeCode()] = $customerAttribute->getSource();
                } catch (\Exception $e) {
                    $failedSources[] = $customerAttribute->getSource();
                }
            }

            $attributeTable = $customerAttribute->getBackendTable();
            if ($this->mainTable === $attributeTable) {
                $mainTableFields[] = $customerAttribute->getAttributeCode();
            } else {
                if (!in_array($attributeTable, $attributeTables)) {
                    $attributeTables[] = $attributeTable;
                }
                $attributeMapper[$customerAttribute->getAttributeCode()] = (int) $customerAttribute->getId();
            }
        }

        $this
            ->getMainTableFieldItems(
                $mainTableFields,
                $minCustomerId,
                $maxCustomerId,
                $attributeMapper,
                $websiteId
            )
            ->getAttributeTableFieldItems(
                $attributeTables,
                $minCustomerId,
                $maxCustomerId,
                $attributeMapper,
                $websiteId
            );

        $attributeValues = $this->dataSourceHelper->getAllOptions(
            $sourceModels,
            [0]
        );

        return [
            'attribute_data'   => $this->attributeData,
            'attribute_values' => $attributeValues,
        ];
    }

    /**
     * GetStoreIdsFromWebsite
     *
     * @param int $websiteId
     *
     * @return int[]
     */
    private function getStoreIdsFromWebsite(int $websiteId): array
    {
        if (!array_key_exists($websiteId, $this->stores)) {
            $this->stores[$websiteId] = $this->storeCollectionFactory
                ->create()
                ->addFieldToFilter('website_id', $websiteId)
                ->getAllIds();
        }

        return $this->stores[$websiteId];
    }

    /**
     * GetMainTableFieldItems
     *
     * @param string[] $mainTableFields
     * @param int      $minCustomerId
     * @param int      $maxCustomerId
     * @param string[] $attributeMapper
     * @param int|null $websiteId
     *
     * @return Customer
     */
    private function getMainTableFieldItems(
        array $mainTableFields,
        int $minCustomerId,
        int $maxCustomerId,
        array $attributeMapper,
        ?int $websiteId = null
    ): Customer {
        if ($mainTableFields) {
            if (!in_array($this->linkField, $mainTableFields)) {
                $mainTableFields[] = $this->linkField;
            }
            $attributesQuery = $this->_resource
                ->getConnection()
                ->select()
                ->from(
                    $this->mainTable,
                    $mainTableFields
                )
                ->where($this->linkField . ' >= ?', $minCustomerId)
                ->where($this->linkField . ' <= ?', $maxCustomerId);

            if ($websiteId) {
                $attributesQuery->where('website_id = ?', $websiteId);
            }

            $this->iterator->walk(
                (string) $attributesQuery,
                [[$this, 'handleMainTableAttributeDataTable']],
                [
                    'fields'          => array_diff(
                        $mainTableFields,
                        [$this->linkField]
                    ),
                    'attributeMapper' => $attributeMapper,
                ],
                $this->_resource->getConnection()
            );
        }

        return $this;
    }

    /**
     * GetAttributeTableFieldItems
     *
     * @param array    $attributeTables
     * @param int      $minCustomerId
     * @param int      $maxCustomerId
     * @param array    $attributeMapper
     * @param int|null $websiteId
     *
     * @return Customer
     */
    private function getAttributeTableFieldItems(
        array $attributeTables,
        int $minCustomerId,
        int $maxCustomerId,
        array $attributeMapper,
        ?int $websiteId = null
    ): Customer {
        $attributeQueries = [];

        foreach ($attributeTables as $attributeTable) {
            $attributeQuery = $this->_resource
                ->getConnection()
                ->select()
                ->from(
                    ['at' => $attributeTable],
                    [
                        'attribute_id',
                        $this->linkField,
                        'value',
                    ]
                )->where(
                    'attribute_id IN (?)',
                    $attributeMapper
                )
                ->where('at.' . $this->linkField . ' >= ?', $minCustomerId)
                ->where('at.' . $this->linkField . ' <= ?', $maxCustomerId);

            if ($websiteId) {
                $attributeQuery->joinInner(
                    ['ce' => $this->_resource->getTableName('customer_entity')],
                    'at.' . $this->linkField . ' = ce.' . $this->linkField,
                    ['website_id']
                );

                $attributeQuery->where('website_id = ?', $websiteId);
            }

            $attributeQueries[] = $attributeQuery;
        }

        try {
            if (count($attributeQueries)) {
                $unionQuery = $this->_resource
                    ->getConnection()
                    ->select()
                    ->union($attributeQueries, 'UNION ALL');
                $this->iterator->walk(
                    (string) $unionQuery,
                    [[$this, 'handleAttributeDataTable']],
                    [
                        'attributeMapper' => $attributeMapper,
                    ],
                    $this->_resource->getConnection()
                );
            }
            return $this;
        } catch (\Exception $e) {
            return $this;
        }
    }

    /**
     * HandleMainTableAttributeDataTable
     *
     * @param array $args
     *
     * @return void
     */
    public function handleMainTableAttributeDataTable(array $args): void
    {
        $customerId = $args['row'][$this->linkField];

        foreach ($args['fields'] as $field) {
            $this->attributeData[$customerId][$field] = $args['row'][$field];
        }
    }

    /**
     * HandleAttributeDataTable
     *
     * @param array $args
     *
     * @return void
     */
    public function handleAttributeDataTable(array $args): void
    {
        $customerId = $args['row'][$this->linkField];
        $attributeCode = $this->findAttributeCodeById(
            $args['row']['attribute_id'],
            $args['attributeMapper']
        );

        if (!array_key_exists($customerId, $this->attributeData)) {
            $this->attributeData[$customerId] = [];
        }

        $this->attributeData[$customerId][$attributeCode] = $args['row']['value'];
    }

    /**
     * FindAttributeCodeById
     *
     * @param int   $attributeId
     * @param array $attributeMapper
     *
     * @return string
     */
    private function findAttributeCodeById(int $attributeId, array $attributeMapper): string
    {
        foreach ($attributeMapper as $attributeCode => $attributeCodeId) {
            if ($attributeId == $attributeCodeId) {
                return $attributeCode;
            }
        }

        return '';
    }
}
