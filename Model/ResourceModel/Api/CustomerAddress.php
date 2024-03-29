<?php
/**
 * Copyright ©2019 Itegration Ltd., Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @author: Perencz Tamás <tamas.perencz@itegraion.com>
 */

namespace Emartech\Emarsys\Model\ResourceModel\Api;

use Emartech\Emarsys\Helper\DataSource as DataSourceHelper;
use Emartech\Emarsys\Model\ResourceModel\Api\Customer as CustomerResourceModel;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\ResourceModel\Address as CustomerAddressResourceModel;
use Magento\Customer\Model\ResourceModel\Address\Attribute\CollectionFactory
    as CustomerAddressAttributeCollectionFactory;
use Magento\Eav\Model\Entity\Attribute;
use Magento\Eav\Model\Entity\Context;
use Magento\Framework\Model\ResourceModel\Db\VersionControl\RelationComposite;
use Magento\Framework\Model\ResourceModel\Db\VersionControl\Snapshot;
use Magento\Framework\Model\ResourceModel\Iterator;
use Magento\Framework\Validator\Factory;

class CustomerAddress extends CustomerAddressResourceModel
{
    public const CUSTOMER_ADDRESS_ENTITY_TYPE_ID = 2;

    /**
     * @var array
     */
    private $attributeData = [];

    /**
     * @var string
     */
    private $mainTable = '';

    /**
     * @var CustomerAddressAttributeCollectionFactory
     */
    private $customerAddressAttributeCollectionFactory;

    /**
     * @var Iterator
     */
    private $iterator;
    /**
     * @var string
     */
    private $linkField = 'entity_id';

    /**
     * @var CustomerResourceModel
     */
    private $customerResourceModel;

    /**
     * @var DataSourceHelper
     */
    private $dataSourceHelper;

    /**
     * CustomerAddress constructor.
     *
     * @param CustomerAddressAttributeCollectionFactory $customerAddressAttributeCollectionFactory
     * @param Iterator                                  $iterator
     * @param Customer                                  $customerResourceModel
     * @param Context                                   $context
     * @param Snapshot                                  $entitySnapshot
     * @param RelationComposite                         $entityRelationComposite
     * @param Factory                                   $validatorFactory
     * @param CustomerRepositoryInterface               $customerRepository
     * @param DataSourceHelper                          $dataSourceHelper
     * @param array                                     $data
     */
    public function __construct(
        CustomerAddressAttributeCollectionFactory $customerAddressAttributeCollectionFactory,
        Iterator $iterator,
        CustomerResourceModel $customerResourceModel,
        Context $context,
        Snapshot $entitySnapshot,
        RelationComposite $entityRelationComposite,
        Factory $validatorFactory,
        CustomerRepositoryInterface $customerRepository,
        DataSourceHelper $dataSourceHelper,
        $data = []
    ) {
        $this->customerAddressAttributeCollectionFactory = $customerAddressAttributeCollectionFactory;
        $this->iterator = $iterator;
        $this->customerResourceModel = $customerResourceModel;
        $this->dataSourceHelper = $dataSourceHelper;

        parent::__construct(
            $context,
            $entitySnapshot,
            $entityRelationComposite,
            $validatorFactory,
            $customerRepository,
            $data
        );
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
        int $websiteId = null
    ): array {
        $this->mainTable = $this->getEntityTable();

        $this->attributeData = [];

        $attributeMapper = [];
        $mainTableFields = [];
        $attributeTables = [];
        $sourceModels = [];

        $customerAddressAttributeCollection = $this->customerAddressAttributeCollectionFactory
            ->create()
            ->addFieldToFilter('entity_type_id', ['eq' => self::CUSTOMER_ADDRESS_ENTITY_TYPE_ID])
            ->addFieldToFilter('attribute_code', ['in' => $attributeCodes]);

        $failedSources = [];

        /** @var Attribute $customerAddressAttribute */
        foreach ($customerAddressAttributeCollection as $customerAddressAttribute) {
            if ($sourceModel = $customerAddressAttribute->getSourceModel()) {
                try {
                    $sourceModels[$customerAddressAttribute->getAttributeCode()] =
                        $customerAddressAttribute->getSource();
                } catch (\Exception $e) {
                    $failedSources[] = $customerAddressAttribute->getSource();
                }
            }

            $attributeTable = $customerAddressAttribute->getBackendTable();
            if ($this->mainTable === $attributeTable) {
                $mainTableFields[] = $customerAddressAttribute->getAttributeCode();
            } else {
                if (!in_array($attributeTable, $attributeTables)) {
                    $attributeTables[] = $attributeTable;
                }
                $attributeMapper[$customerAddressAttribute->getAttributeCode()] =
                    (int) $customerAddressAttribute->getId();
            }
        }

        $this
            ->getMainTableFieldItems(
                $mainTableFields,
                $minCustomerId,
                $maxCustomerId,
                $attributeMapper,
                $websiteId
            )->getAttributeTableFieldItems(
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
     * GetMainTableFieldItems
     *
     * @param string[] $mainTableFields
     * @param int      $minCustomerId
     * @param int      $maxCustomerId
     * @param string[] $attributeMapper
     * @param int|null $websiteId
     *
     * @return CustomerAddress
     */
    private function getMainTableFieldItems(
        array $mainTableFields,
        int $minCustomerId,
        int $maxCustomerId,
        array $attributeMapper,
        int $websiteId = null
    ): CustomerAddress {
        if ($mainTableFields) {
            if (!in_array($this->linkField, $mainTableFields)) {
                $mainTableFields[] = $this->linkField;
            }
            if (!in_array('parent_id', $mainTableFields)) {
                $mainTableFields[] = 'parent_id';
            }

            $customerTable = $this->customerResourceModel->getEntityTable();
            $customerTableLinkField = $this->customerResourceModel->getLinkField();

            $attributesQuery = $this->_resource
                ->getConnection()
                ->select()
                ->from($this->mainTable, $mainTableFields)
                ->joinLeft(
                    ['customer_entity_table' => $customerTable],
                    $this->mainTable . '.parent_id = customer_entity_table.' . $customerTableLinkField,
                    ''
                )
                ->where(
                    '
                        customer_entity_table.default_shipping = ' . $this->mainTable . '.' . $this->linkField . '
                        OR
                        customer_entity_table.default_billing = ' . $this->mainTable . '.' . $this->linkField . '
                    '
                )
                ->where('parent_id' . ' >= ?', $minCustomerId)
                ->where('parent_id' . ' <= ?', $maxCustomerId);

            if ($websiteId) {
                $attributesQuery->where('customer_entity_table.website_id = ?', $websiteId);
            }

            $this->iterator->walk(
                (string) $attributesQuery,
                [[$this, 'handleMainTableAttributeDataTable']],
                [
                    'fields'          => array_diff($mainTableFields, [$this->linkField]),
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
     * @return $this
     */
    private function getAttributeTableFieldItems(
        array $attributeTables,
        int $minCustomerId,
        int $maxCustomerId,
        array $attributeMapper,
        int $websiteId = null
    ): CustomerAddress {
        $attributeQueries = [];

        $customerTable = $this->customerResourceModel->getEntityTable();
        $customerTableLinkField = $this->customerResourceModel->getLinkField();
        $customerAddressTable = $this->getEntityTable();

        foreach ($attributeTables as $attributeTable) {
            $attributeQuery = $this->_resource
                ->getConnection()
                ->select()
                ->from($attributeTable, ['attribute_id', $this->linkField, 'value'])
                ->joinLeft(
                    ['customer_address_entity_table' => $customerAddressTable],
                    $attributeTable . '.' . $this->linkField . ' = customer_address_entity_table.' . $this->linkField,
                    'parent_id as customer_id'
                )
                ->joinLeft(
                    ['customer_entity_table' => $customerTable],
                    'customer_address_entity_table.parent_id = customer_entity_table.' . $customerTableLinkField,
                    ''
                )
                ->where(
                    '
                        customer_entity_table.default_shipping = ' . $attributeTable . '.' . $this->linkField . '
                        OR
                        customer_entity_table.default_billing = ' . $attributeTable . '.' . $this->linkField . '
                    '
                )
                ->where('attribute_id IN (?)', $attributeMapper)
                ->where('customer_address_entity_table.parent_id >= ?', $minCustomerId)
                ->where('customer_address_entity_table.parent_id <= ?', $maxCustomerId);

            if ($websiteId) {
                $attributeQuery->where('customer_entity_table.website_id = ?', $websiteId);
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
        $addressId = $args['row'][$this->linkField];
        $customerId = $args['row']['parent_id'];

        foreach ($args['fields'] as $field) {
            $this->attributeData[$customerId][$addressId][$field] = $args['row'][$field];
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
        $addressId = $args['row'][$this->linkField];
        $customerId = $args['row']['customer_id'];
        $attributeCode = $this->findAttributeCodeById($args['row']['attribute_id'], $args['attributeMapper']);

        if (!array_key_exists($customerId, $this->attributeData)) {
            $this->attributeData[$customerId] = [];
        }
        if (!array_key_exists($addressId, $this->attributeData[$customerId])) {
            $this->attributeData[$customerId][$addressId] = [];
        }

        $this->attributeData[$customerId][$addressId][$attributeCode] = $args['row']['value'];
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
