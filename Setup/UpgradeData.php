<?php
/**
 * Copyright ©2020 Itegration Ltd., Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @author: Perencz Tamás <tamas.perencz@itegraion.com>
 */

namespace Emartech\Emarsys\Setup;

use Emartech\Emarsys\Model\Indexer\DeltaIndexer;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\Indexer\IndexerRegistry;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;

class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var IndexerRegistry
     */
    private $indexerRegistry;

    /**
     * @var TypeListInterface
     */
    private $cacheTypeList;

    /**
     * UpgradeData constructor.
     *
     * @param IndexerRegistry $indexerRegistry
     */
    public function __construct(
        IndexerRegistry $indexerRegistry,
        TypeListInterface $cacheTypeList
    ) {
        $this->indexerRegistry = $indexerRegistry;
        $this->cacheTypeList = $cacheTypeList;
    }


    public function upgrade(
        ModuleDataSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.9.3', '<')) {
            $this->setIndexerMode();
        }

        $setup->endSetup();
    }

    private function setIndexerMode()
    {
        $this->indexerRegistry->get(DeltaIndexer::INDEXER_ID)
            ->setScheduled(true);

        $types = [
            'config', 'layout', 'block_html', 'collections', 'reflection',
            'db_ddl', 'eav', 'config_integration', 'config_integration_api',
            'full_page', 'translate', 'config_webservice',
        ];
        foreach ($types as $type) {
            $this->cacheTypeList->cleanType($type);
        }
    }
}