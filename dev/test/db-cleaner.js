'use strict';

module.exports = class DbCleaner {
  static create(db) {
    return new DbCleaner(db);
  }

  constructor(db) {
    this._db = db;
  }

  async clearCustomers() {
    await this._db.raw('DELETE FROM customer_entity');
  }

  async clearOrders() {
    await this._db.raw('SET FOREIGN_KEY_CHECKS=0');

    await this._db.raw('TRUNCATE TABLE sales_bestsellers_aggregated_daily');
    await this._db.raw('TRUNCATE TABLE sales_bestsellers_aggregated_monthly');
    await this._db.raw('TRUNCATE TABLE sales_bestsellers_aggregated_yearly');

    await this._db.raw('TRUNCATE TABLE sales_creditmemo');
    await this._db.raw('TRUNCATE TABLE sales_creditmemo_comment');
    await this._db.raw('TRUNCATE TABLE sales_creditmemo_grid');
    await this._db.raw('TRUNCATE TABLE sales_creditmemo_item');
    await this._db.raw('TRUNCATE TABLE sales_invoice');
    await this._db.raw('TRUNCATE TABLE sales_invoiced_aggregated');
    await this._db.raw('TRUNCATE TABLE sales_invoiced_aggregated_order');
    await this._db.raw('TRUNCATE TABLE sales_invoice_comment');
    await this._db.raw('TRUNCATE TABLE sales_invoice_grid');
    await this._db.raw('TRUNCATE TABLE sales_invoice_item');
    await this._db.raw('TRUNCATE TABLE sales_order');
    await this._db.raw('TRUNCATE TABLE sales_order_address');
    await this._db.raw('TRUNCATE TABLE sales_order_aggregated_created');
    await this._db.raw('TRUNCATE TABLE sales_order_aggregated_updated');
    await this._db.raw('TRUNCATE TABLE sales_order_grid');
    await this._db.raw('TRUNCATE TABLE sales_order_item');
    await this._db.raw('TRUNCATE TABLE sales_order_payment');
    await this._db.raw('TRUNCATE TABLE sales_order_status_history');
    await this._db.raw('TRUNCATE TABLE sales_order_tax');
    await this._db.raw('TRUNCATE TABLE sales_order_tax_item');
    await this._db.raw('TRUNCATE TABLE sales_payment_transaction');
    await this._db.raw('TRUNCATE TABLE sales_refunded_aggregated');
    await this._db.raw('TRUNCATE TABLE sales_refunded_aggregated_order');
    await this._db.raw('TRUNCATE TABLE sales_shipment');
    await this._db.raw('TRUNCATE TABLE sales_shipment_comment');
    await this._db.raw('TRUNCATE TABLE sales_shipment_grid');
    await this._db.raw('TRUNCATE TABLE sales_shipment_item');
    await this._db.raw('TRUNCATE TABLE sales_shipment_track');
    await this._db.raw('TRUNCATE TABLE sales_shipping_aggregated');
    await this._db.raw('TRUNCATE TABLE sales_shipping_aggregated_order');

    await this._db.raw('TRUNCATE TABLE quote');
    await this._db.raw('TRUNCATE TABLE quote_address');
    await this._db.raw('TRUNCATE TABLE quote_address_item');
    await this._db.raw('TRUNCATE TABLE quote_id_mask');
    await this._db.raw('TRUNCATE TABLE quote_item');
    await this._db.raw('TRUNCATE TABLE quote_item_option');
    await this._db.raw('TRUNCATE TABLE quote_payment');
    await this._db.raw('TRUNCATE TABLE quote_shipping_rate');

    await this._db.raw('TRUNCATE TABLE sequence_invoice_1');
    await this._db.raw('TRUNCATE TABLE sequence_order_1');
    await this._db.raw('TRUNCATE TABLE sequence_shipment_1');
    await this._db.raw('TRUNCATE TABLE sequence_creditmemo_1');

    await this._db.raw('SET FOREIGN_KEY_CHECKS=1');
  }

  async tearDown() {
    await this.clearCustomers();
    await this.clearOrders();
  }

  async resetEmarsysEventsData() {
    await this._db.truncate('emarsys_events_data');
  }
};
