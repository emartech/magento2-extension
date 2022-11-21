<?php
/**
 * Copyright ©2019 Itegration Ltd., Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @author: Perencz Tamás <tamas.perencz@itegraion.com>
 */

namespace Emartech\Emarsys\Model\Api;

use Emartech\Emarsys\Api\Data\InventoryApiResponseInterface;
use Emartech\Emarsys\Api\Data\InventoryApiResponseInterfaceFactory;
use Emartech\Emarsys\Api\Data\InventoryItemInterfaceFactory;
use Emartech\Emarsys\Api\Data\InventoryItemItemInterfaceFactory;
use Emartech\Emarsys\Api\InventoryApiInterface;
use Emartech\Emarsys\Helper\Inventory as InventoryHelper;
use Magento\Framework\Model\ResourceModel\Iterator;
use Magento\Inventory\Model\ResourceModel\SourceItem\Collection as SourceItemCollection;
use Magento\Inventory\Model\ResourceModel\SourceItem\CollectionFactory as SourceItemCollectionFactory;

/**
 * Class InventoryApi
 *
 * InventoryApi endpoint
 */
class InventoryApi implements InventoryApiInterface
{
    /**
     * @var SourceItemCollectionFactory
     */
    private $sourceItemCollectionFactory;

    /**
     * @var SourceItemCollection
     */
    private $sourceItemCollection;

    /**
     * @var InventoryApiResponseInterfaceFactory
     */
    private $inventoryApiResponseFactory;

    /**
     * @var InventoryItemInterfaceFactory
     */
    private $inventoryItemFactory;

    /**
     * @var InventoryItemItemInterfaceFactory
     */
    private $inventoryItemItemFactory;

    /**
     * @var Iterator
     */
    private $iterator;

    /**
     * @var array
     */
    private $data = [];

    /**
     * InventoryApi constructor.
     *
     * @param InventoryHelper                      $inventoryHelper
     * @param InventoryApiResponseInterfaceFactory $inventoryApiResponseFactory
     * @param InventoryItemInterfaceFactory        $inventoryItemInterfaceFactory
     * @param InventoryItemItemInterfaceFactory    $inventoryItemItemFactory
     * @param Iterator                             $iterator
     */
    public function __construct(
        InventoryHelper $inventoryHelper,
        InventoryApiResponseInterfaceFactory $inventoryApiResponseFactory,
        InventoryItemInterfaceFactory $inventoryItemInterfaceFactory,
        InventoryItemItemInterfaceFactory $inventoryItemItemFactory,
        Iterator $iterator
    ) {
        $this->sourceItemCollectionFactory = $inventoryHelper->getSourceItemCollectionFactory();
        $this->inventoryApiResponseFactory = $inventoryApiResponseFactory;
        $this->inventoryItemFactory = $inventoryItemInterfaceFactory;
        $this->inventoryItemItemFactory = $inventoryItemItemFactory;
        $this->iterator = $iterator;
    }

    /**
     * GetList
     *
     * @param string[] $sku
     *
     * @return InventoryApiResponseInterface
     */
    public function getList(array $sku): InventoryApiResponseInterface
    {
        /** @var InventoryApiResponseInterface $response */
        $response = $this->inventoryApiResponseFactory->create();

        if (null === $this->sourceItemCollectionFactory) {
            return $response->setItems([]);
        }

        $this
            ->initCollection()
            ->filterSKUs($sku)
            ->parseInventoryItems();

        return $response->setItems($this->handleItems());
    }

    /**
     * ParseInventoryItems
     *
     * @return InventoryApiInterface
     */
    private function parseInventoryItems(): InventoryApiInterface
    {
        $this->data = [];

        $this->iterator->walk(
            (string) $this->sourceItemCollection->getSelect(),
            [[$this, 'handleStockItemData']],
            [],
            $this->sourceItemCollection->getConnection()
        );

        return $this;
    }

    /**
     * HandleStockItemData
     *
     * @param array $args
     */
    public function handleStockItemData(array $args): void
    {
        $sku = $args['row']['sku'];
        $sourceCode = $args['row']['source_code'];

        if (!array_key_exists($sku, $this->data)) {
            $this->data[$sku] = [];
        }
        if (!array_key_exists($sourceCode, $this->data[$sku])) {
            $this->data[$sku][$sourceCode] = [
                'quantity'    => (float) $args['row']['quantity'],
                'is_in_stock' => (int) $args['row']['status'],
            ];
        }
    }

    /**
     * HandleItems
     *
     * @return array
     */
    private function handleItems(): array
    {
        $returnArray = [];

        foreach ($this->data as $sku => $stockData) {
            $returnArray[] = $this->inventoryItemFactory
                ->create()
                ->setSku($sku)
                ->setInventoryItems($this->handleInventoryItems($stockData));
        }

        return $returnArray;
    }

    /**
     * HandleInventoryItems
     *
     * @param array $stockData
     *
     * @return array
     */
    private function handleInventoryItems(array $stockData): array
    {
        $returnArray = [];

        foreach ($stockData as $sourceCode => $data) {
            $returnArray[] = $this->inventoryItemItemFactory
                ->create()
                ->setQuantity($data['quantity'])
                ->setSourceCode($sourceCode)
                ->setIsInStock($data['is_in_stock']);
        }

        return $returnArray;
    }

    /**
     * InitCollection
     *
     * @return InventoryApiInterface
     */
    private function initCollection(): InventoryApiInterface
    {
        $this->sourceItemCollection = $this->sourceItemCollectionFactory->create();

        return $this;
    }

    /**
     * FilterSKUs
     *
     * @param string|string[] $sku
     *
     * @return $this
     */
    private function filterSKUs($sku): InventoryApiInterface
    {
        if (!is_array($sku)) {
            $sku = [$sku];
        }

        $this->sourceItemCollection->addFieldToFilter('sku', ['in' => $sku]);

        return $this;
    }
}
