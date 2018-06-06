USE magento;

-- Remove tables
DROP TABLE IF EXISTS emarsys_settings;

-- Remove token
DELETE FROM core_config_data WHERE path='emartech/emarsys/connecttoken';

-- Remove extension from setup_module
DELETE FROM setup_module WHERE module='Emartech_Emarsys';

