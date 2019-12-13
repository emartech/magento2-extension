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
     * @param SourceItemCollectionFactory          $sourceItemCollectionFactory
     * @param InventoryApiResponseInterfaceFactory $inventoryApiResponseFactory
     * @param InventoryItemInterfaceFactory        $inventoryItemInterfaceFactory
     * @param InventoryItemItemInterfaceFactory    $inventoryItemItemFactory
     * @param Iterator                             $iterator
     */
    public function __construct(
        SourceItemCollectionFactory $sourceItemCollectionFactory,
        InventoryApiResponseInterfaceFactory $inventoryApiResponseFactory,
        InventoryItemInterfaceFactory $inventoryItemInterfaceFactory,
        InventoryItemItemInterfaceFactory $inventoryItemItemFactory,
        Iterator $iterator
    ) {
        $this->sourceItemCollectionFactory = $sourceItemCollectionFactory;
        $this->inventoryApiResponseFactory = $inventoryApiResponseFactory;
        $this->inventoryItemFactory = $inventoryItemInterfaceFactory;
        $this->inventoryItemItemFactory = $inventoryItemItemFactory;
        $this->iterator = $iterator;
    }

    /**
     * @param string[] $sku
     *
     * @return InventoryApiResponseInterface
     */
    public function getList($sku)
    {
        $this
            ->initCollection()
            ->filterSKUs($sku)
            ->parseInventoryItems();

        return $this->inventoryApiResponseFactory->create()
            ->setItems($this->handleItems());
    }

    /**
     * @return $this
     */
    private function parseInventoryItems()
    {
        $this->data = [];

        $this->iterator->walk(
            (string)$this->sourceItemCollection->getSelect(),
            [[$this, 'handleStockItemData']],
            [],
            $this->sourceItemCollection->getConnection()
        );

        return $this;
    }

    /**
     * @param array $args
     */
    public function handleStockItemData($args)
    {
        $sku = $args['row']['sku'];
        $sourceCode = $args['row']['source_code'];

        if (!array_key_exists($sku, $this->data)) {
            $this->data[$sku] = [];
        }
        if (!array_key_exists($sourceCode, $this->data[$sku])) {
            $this->data[$sku][$sourceCode] = [
                'quantity' => (float)$args['row']['quantity'],
                'status'   => (int)$args['row']['status'],
            ];
        }
    }

    /**
     * @return array
     */
    private function handleItems()
    {
        $returnArray = [];

        foreach ($this->data as $sku => $stockData) {
            $returnArray[] = $this->inventoryItemFactory->create()
                ->setSku($sku)
                ->setInventoryItems($this->handleInventoryItems($stockData));
        }

        return $returnArray;
    }

    /**
     * @param array $stockData
     *
     * @return array
     */
    private function handleInventoryItems($stockData)
    {
        $returnArray = [];

        foreach ($stockData as $sourceCode => $data) {
            $returnArray[] = $this->inventoryItemItemFactory->create()
                ->setQuantity($data['quantity'])
                ->setSourceCode($sourceCode)
                ->setStatus($data['status']);
        }

        return $returnArray;
    }

    /**
     * @return $this
     */
    private function initCollection()
    {
        $this->sourceItemCollection = $this->sourceItemCollectionFactory->create();

        return $this;
    }

    /**
     * @param string|string[] $sku
     *
     * @return $this
     */
    private function filterSKUs($sku)
    {
        if (!is_array($sku)) {
            $sku = [$sku];
        }

        $this->sourceItemCollection->addFieldToFilter('sku', ['in' => $sku]);

        return $this;
    }
}
