
const {getTableName} = require("./get-table-name");

class ProductHelper {

    constructor(db, magentoEdition, magentoVersion) {
        this._db = db;
        this._magentoEdition = magentoEdition;
        this._magentoVersion = magentoVersion;
    }

    getEntityIdField() {
        return this._magentoEdition === 'Enterprise' ? 'row_id' : 'entity_id';
    }

    getEntityId(product) {
        return product[this.getEntityIdField()];
    }

    async getStoreIds() {
        let cacheId = 'store_ids';

        if (!global[cacheId]) {
            global[cacheId] = await this._db.select()
                .from(getTableName('store'))
                .orderBy('store_id', 'desc')
                .then((records) => {
                    let items = [];

                    records.forEach((record) => {
                        let _record = this.normalizeDbRecord(record);
                        items.push(_record.store_id);
                    })

                    return items;
                });
        }

        return global[cacheId];
    }

    async getCurrencyRate(currencyFrom, currencyTo) {
        let cacheId = 'currency_rate_' + currencyFrom + '_' + currencyTo;

        if (!global[cacheId]) {

            let currencyData = await this._db.select()
                .from(getTableName('directory_currency_rate'))
                .where('currency_from', currencyFrom)
                .where('currency_to', currencyTo)
                .limit(1)
                .first()
                .then((record) => {
                    return this.normalizeDbRecord(record);
                });

            global[cacheId] = currencyData ? currencyData.rate : 1;
        }

        return global[cacheId];
    }

    async getStoreCurrencyCode(storeId) {
        let cacheId = 'store_currency_code_' + storeId;

        if (!global[cacheId]) {
            let path = storeId > 0 ? 'currency/options/default' : 'currency/options/base';
            global[cacheId] = await this._db.select().from(getTableName('core_config_data'))
                .where('path', path)
                .limit(1)
                .first()
                .then((record) => {
                    return this.normalizeDbRecord(record).value;
                });
        }

        return global[cacheId];
    }

    async getProductIdBySku(sku) {
        return await this._db.select().from(getTableName('catalog_product_entity'))
            .where('sku', sku)
            .limit(1)
            .first()
            .then((record) => {
                let row = this.normalizeDbRecord(record);
                return row ? row.entity_id : null;
            });
    }

    async getProductsFromDb(page, limit) {
        return await this._db.select().from(getTableName('catalog_product_entity'))
            .limit(limit)
            .offset((page - 1) * limit)
            .then(async (records) => {
                let items = [];

                for (let record of records) {
                    let product = this.normalizeDbRecord(record);
                    product = await this._fillProductWithAttributes(product);
                    product = this._clearUnusedAttributes(product);
                    items.push(product);
                }

                return items;
            });
    }

    async getProductFromDb(id, idField = 'entity_id') {
        return await this._db.select().from(getTableName('catalog_product_entity'))
            .where(idField, id)
            .limit(1)
            .first()
            .then(async (record) => {
                let product = this.normalizeDbRecord(record);

                if (!product) {
                    return null;
                }

                product = await this._fillProductWithAttributes(product);
                return this._clearUnusedAttributes(product);
            });
    }

    async getProductAttributesFromDb() {
        if (global.productAttributes) {
            return global.productAttributes;
        }

        global.productAttributes = await this._db.select().from(getTableName('eav_attribute'))
            .columns(['attribute_id', 'attribute_code', 'backend_type', 'backend_table', 'frontend_label'])
            .where('entity_type_id', 4)
            .then((records) => {
                let items = [];

                records.forEach((record) => {
                    let _record = this.normalizeDbRecord(record);
                    items[_record.attribute_code] = _record;
                })

                return items;
            });

        return global.productAttributes;
    }

    async getProductAttributeValues(rowId, attributes, storeId = 0) {
        let eavAttributes = await this.getProductAttributesFromDb();
        let db = this._db;
        let result = {};

        for (let attributeCode of attributes) {
            result[attributeCode] = null;

            if (eavAttributes[attributeCode]) {
                let attribute = eavAttributes[attributeCode];

                if (attribute.backend_type !== 'static') {
                    let v = await db.select()
                        .from(getTableName('catalog_product_entity_' + attribute.backend_type))
                        .column('value')
                        .where(this.getEntityIdField(), rowId)
                        .where('attribute_id', attribute.attribute_id)
                        .whereIn('store_id', [storeId, 0])
                        .orderBy('store_id', 'desc')
                        .limit(1)
                        .first();

                    if (v && v.value) {
                        result[attributeCode] = v.value;
                    }
                }
            }
        }

        return result;
    }

    async getProductCountFromDb() {
        return await this._db.count('entity_id as count').from(getTableName('catalog_product_entity'))
            .first()
            .then((record) => {
                return this.normalizeDbRecord(record).count;
            });
    }

    _clearUnusedAttributes(product) {
        delete product.attribute_set_id;
        delete product.created_at;
        delete product.updated_at;
        delete product.has_options;
        delete product.required_options;
        delete product.created_in;
        delete product.updated_in;

        return product;
    }

    async _fillProductWithAttributes(product) {
        product.type = product.type_id;
        delete product.type_id;

        let stockItem = await this.getStockItemFromDb(product.entity_id);

        product.children_entity_ids = await this.getChildrenEntityIdsFromDb(product.entity_id);
        product.images = await this.getProductImagesFromDb(product);
        product.categories = await this.getProductCategoriesFromDb(product.entity_id, 'product_id');
        product.qty = stockItem.qty;
        product.is_in_stock = stockItem.is_in_stock;

        product.store_data = await this.getProductStoreDataFromDb(product);

        return product;
    }

    async getProductStoreDataFromDb(product) {
        let storeIds = [1, 0];
        let storesData = [];
        let baseCurrencyCode = await this.getStoreCurrencyCode(0);

        for (let storeId of storeIds) {
            let currencyCode = await this.getStoreCurrencyCode(storeId);
            let currencyRate = await this.getCurrencyRate(baseCurrencyCode, currencyCode);
            let storeData = await this.getProductAttributeValues(
                this.getEntityId(product),
                ['name', 'price', 'special_price', 'status', 'description', 'url_key'],
                storeId
            )

            let price = storeData.price
            let originalPrice = storeData.price

            if (storeData.special_price) {
                price = storeData.special_price
            }

            let result = {
                'name': storeData.name,
                'price': price,
                'display_price': price * currencyRate,
                'original_price': originalPrice,
                'original_display_price': originalPrice * currencyRate,
                'webshop_price': price,
                'display_webshop_price': price * currencyRate,
                'original_webshop_price': originalPrice,
                'original_display_webshop_price': originalPrice * currencyRate,
                'link': this.getBaseUrl(storeId) + storeData.url_key + '.html',
                'status': storeData.status,
                'description': storeData.description,
                'store_id': storeId,
                'currency_code': currencyCode,
                'extra_fields': [],
                'images' : {},
            }

            storesData.push(result)
        }

        return storesData
    }

    async getProductImagesFromDb(product) {
        let images = await this.getProductAttributeValues(this.getEntityId(product), ['image', 'small_image', 'thumbnail'], 0);
        let baseUrl = this.getProductBaseUrl();

        Object.keys(images).forEach(function (key, value) {
            if (images[key]) {
                images[key] = baseUrl + images[key];
            }
        });

        return images;
    }

    async getProductCategoriesFromDb(id, idField = 'entity_id') {

        return await this._db.select().from(getTableName('catalog_category_product'))
            .where(idField, id)
            .then(async (records) => {
                let items = [];

                for (let record of records) {
                    let _record = this.normalizeDbRecord(record);
                    let category = await this._db.select().from(getTableName('catalog_category_entity'))
                        .where('entity_id', _record.category_id)
                        .limit(1)
                        .first()
                        .then((record) => {
                            return this.normalizeDbRecord(record);
                        });

                    items.push(category.path);
                }

                return items;
            });
    }

    async getStockItemFromDb(entityId) {
        return await this._db.select().from(getTableName('cataloginventory_stock_item'))
            .where('product_id', entityId)
            .limit(1)
            .first()
            .then((record) => {
                return this.normalizeDbRecord(record);
            });
    }

    async getChildrenEntityIdsFromDb(entityId) {
        return await this._db.select().from(getTableName('catalog_product_relation'))
            .where('parent_id', entityId)
            .then((records) => {
                let items = [];

                records.forEach((record) => {
                    let _record = this.normalizeDbRecord(record);
                    items.push(_record.child_id);
                })

                return items;
            });
    }

    async getInventorySourceItemFromDb(sku) {
        return await this._db.select().from(getTableName('inventory_source_item'))
            .where('sku', sku)
            .limit(1)
            .first()
            .then((record) => {
                return this.normalizeDbRecord(record);
            });
    }

    async insertInventorySourceItemIntoDb(sku, sourceCode = 'default', qty = 100, status = 1) {
        return await this._db.insert({
            sku: sku,
            source_code: sourceCode,
            quantity: qty,
            status: status,
        }).into(getTableName('inventory_source_item'))
            .then((record) => {
                return this.normalizeDbRecord(record);
            });
    }

    static isPubInUrl(url) {
        return url.indexOf('/pub/') > -1;
    }

    static removePubFromUrl(url) {
        return url.replace('/pub/', '/');
    }

    getProductBaseUrl() {
        return global.magentoUrl
            ? this.getBaseUrl(0) + 'media/catalog/product'
            : this.getBaseUrl(0) + 'pub/media/catalog/product';
    }

    getBaseUrl(storeId) {
        if (storeId === 1) {
            return global.magentoUrl ? global.magentoUrl : 'http://magento-test.local/index.php/default/'
        }

        return global.magentoUrl ? global.magentoUrl : 'http://magento-test.local/'
    }

    normalizeDbRecord(record) {
        if (!record) {
            return record;
        }

        return JSON.parse(JSON.stringify(record));
    }

}

module.exports = {ProductHelper};
