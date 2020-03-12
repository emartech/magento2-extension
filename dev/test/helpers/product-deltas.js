'use strict';

const { getTableName } = require('./get-table-name');

const insertProductDeltas = async (db, skus) => {
  const deltas = [];

  for (const sku of skus) {
    const productEntityRow = await db(getTableName('catalog_product_entity'))
      .select('entity_id')
      .where({ sku })
      .first();
    deltas.push({ sku, entity_id: productEntityRow.entity_id, row_id: productEntityRow.entity_id });
  }

  return db(getTableName('emarsys_product_delta')).insert(deltas);
};

module.exports = {
  insertProductDeltas
};
