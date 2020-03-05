'use strict';

const insertProductDeltas = async (db, skus) => {
  const deltas = [];

  for (const sku of skus) {
    const productEntityRow = await db('catalog_product_entity')
      .select('entity_id')
      .where({ sku })
      .first();
    deltas.push({ sku, entity_id: productEntityRow.entity_id, row_id: productEntityRow.entity_id });
  }

  return db('emarsys_product_delta').insert(deltas);
};

module.exports = {
  insertProductDeltas
};
