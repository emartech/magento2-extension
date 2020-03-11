<?php
/**
 * Copyright ©2020 Itegration Ltd., Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @author: Perencz Tamás <tamas.perencz@itegraion.com>
 */

namespace Emartech\Emarsys\Model\Indexer;

use Emartech\Emarsys\Api\Data\ConfigInterface;
use Emartech\Emarsys\Helper\ConfigReader;
use Magento\Catalog\Model\Product as ProductModel;
use Magento\Catalog\Model\ResourceModel\Product\Collection as ProductCollection;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Indexer\ActionInterface as IndexerActionInterface;
use Magento\Framework\Mview\ActionInterface as MviewActionInterface;
use Magento\Framework\Mview\View as ViewModel;
use Magento\Framework\Mview\View\Collection as ViewCollection;
use Magento\Framework\Mview\View\CollectionFactory as ViewCollectionFactory;
use Psr\Log\LoggerInterface;

class DeltaIndexer implements IndexerActionInterface, MviewActionInterface
{
    const INDEXER_ID = 'emarsys_delta_check';

    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var ViewCollectionFactory
     */
    private $viewCollectionFactory;

    /**
     * @var ProductCollectionFactory
     */
    private $productCollectionFactory;

    /**
     * @var null|bool|ViewModel
     */
    private $viewModel = null;

    /**
     * @var ConfigReader
     */
    private $configReader;

    /**
     * DeltaIndexer constructor.
     *
     * @param ResourceConnection       $resourceConnection
     * @param ViewCollectionFactory    $viewCollectionFactory
     * @param ProductCollectionFactory $productCollectionFactory
     * @param ConfigReader             $configReader
     * @param LoggerInterface          $logger
     */
    public function __construct(
        ResourceConnection $resourceConnection,
        ViewCollectionFactory $viewCollectionFactory,
        ProductCollectionFactory $productCollectionFactory,
        ConfigReader $configReader,
        LoggerInterface $logger
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->viewCollectionFactory = $viewCollectionFactory;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->configReader = $configReader;
        $this->logger = $logger;
    }

    /**
     * @param int $id
     */
    public function executeRow($id)
    {
        $this->doExecute($id);
    }

    public function executeFull()
    {
        $viewModel = $this->getViewModel();
        if ($viewModel instanceof ViewModel) {
            $lastVersionId = (int)$viewModel->getState()->getVersionId();
            $ids = $viewModel->getChangelog()->getList(0, $lastVersionId);
            $this->doExecute($ids);
        }
    }

    /**
     * @param int[] $ids
     */
    public function executeList(array $ids)
    {
        $this->doExecute($ids);
    }

    /**
     * @param int[] $ids
     */
    public function execute($ids)
    {
        $this->doExecute($ids);
    }

    /**
     * @return bool|ViewModel
     */
    private function getViewModel()
    {
        if (null === $this->viewModel) {
            $this->viewModel = false;
            /** @var ViewCollection $viewCollection */
            $viewCollection = $this->viewCollectionFactory->create();
            /** @var ViewModel $viewModel */
            foreach ($viewCollection as $viewModel) {
                if ($viewModel->getId() === 'emarsys_delta_check') {
                    $this->viewModel = $viewModel;
                    break;
                }
            }
        }

        return $this->viewModel;
    }

    /**
     * @param int|int[] $ids
     *
     * @return bool
     */
    private function doExecute($ids)
    {
        if (!is_array($ids)) {
            $ids = [$ids];
        }

        if (!$ids) {
            return false;
        }

        /** @var ProductCollection $productCollection */
        $productCollection = $this->productCollectionFactory->create();
        $productCollection->addFieldToFilter('entity_id', $ids);

        $insertData = [];
        /** @var ProductModel $product */
        foreach ($productCollection as $product) {
            $canSave = false;

            foreach ($product->getWebsiteIds() as $websiteId) {
                if ($this->configReader->isEnabledForWebsite(
                    ConfigInterface::PRODUCT_DELTA_SYNC,
                    $websiteId
                )) {
                    $canSave = true;
                    break;
                }
            }

            if ($canSave) {
                $insertData[] = [
                    'sku'       => $product->getSku(),
                    'entity_id' => $product->getEntityId(),
                    'row_id'    => $product->getId(),
                ];
            }
        }

        if ($insertData) {
            $tableName = $this->resourceConnection
                ->getTableName('emarsys_product_delta');
            $this->resourceConnection->getConnection()
                ->insertMultiple($tableName, $insertData);
        }

        $viewModel = $this->getViewModel();
        if ($viewModel instanceof ViewModel) {
            $viewModel->getChangelog()
                ->clear($viewModel->getChangelog()->getVersion() + 1);
        }

        return true;
    }
}
