'use strict';

const createSource = async function(magentoApi) {
  return await magentoApi.post({
    path: '/index.php/rest/V1/inventory/sources',
    payload: {
      source: {
        source_code: 'custom_source',
        name: 'Custom',
        enabled: true,
        country_id: 'HU',
        postcode: '1111'
      }
    }
  });
};

const addStockForProductInInventorySource = async function(magentoApi) {
  return await magentoApi.post({
    path: '/index.php/rest/V1/inventory/source-items',
    payload: {
      sourceItems: [
        {
          sku: skuWithMultipleSources,
          source_code: inventorySourceCode,
          quantity: 99,
          status: 1
        }
      ]
    }
  });
};

const inventorySourceCode = 'custom_source';
const skuWithMultipleSources = '24-WB04';
const skuWithDefaultSource = '24-WB07';

describe('Product inventory API', function() {

  before(async function() {
    if (this.magentoVersion >= '2.3.0') {
      await createSource(this.magentoApi);
      try {
        await addStockForProductInInventorySource(this.magentoApi);
      } catch (error) {
        console.log(error.response.data.errors);
      }
    }
  });

  it('should return product stock for all inventory sources', async function() {
    if (this.magentoVersion >= '2.3.0') {
      const { items } = await this.magentoApi.execute('inventory', 'getForProducts', {
        sku: [skuWithMultipleSources, skuWithDefaultSource]
      });

      const productWithMultipleInventoryItems = items[0].inventory_items;
      const productWithDefaultInventoryItem = items[1].inventory_items;

      expect(productWithMultipleInventoryItems).to.eql([
        { source_code: inventorySourceCode, quantity: 99, is_in_stock: true },
        { source_code: 'default', quantity: 100, is_in_stock: true }
      ]);

      expect(productWithDefaultInventoryItem).to.eql([{ source_code: 'default', quantity: 100, is_in_stock: true }]);
    } else {
      console.log('Magento version is not compatible with Multi Source Inventory.');
    }
  });
});
