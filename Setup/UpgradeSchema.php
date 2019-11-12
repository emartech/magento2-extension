<?php

namespace Emartech\Emarsys\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    public function upgrade(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.5.3') < 0) {
            $tableName = $setup->getTable('emarsys_events_data');

            if ($setup->getConnection()->isTableExists($tableName) == true) {
                $setup->getConnection()->modifyColumn($tableName, 'event_data', 'mediumblob');
            }
        }

        $setup->endSetup();
    }
}
