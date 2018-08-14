# Marketing events

## Customer related events

TODO

## Sales related events

### `sales_email_order_template` & `sales_email_order_guest_template`

Triggered on new order submission. [Example data](MarketingEvents/sales_email_order_template.json).

Structure:
```json
{
  "customerName": "",
  "customerEmail": "",
  "is_guest": 1,
  "customer": {},
  "order": {
    "items": [],
    "status_histories": [],
    "extension_attributes": {},
    "addresses": {
      "shipping": {},
      "billing": {}
    },
    "shipping_method": "",
    "payment": {},
    "state": "new",
    "status": "pending",
    "store_name": "Website\nStore\nStore View",
    "entity_id": "",
    "id": "",
    "created_at": "",
    "updated_at": ""
  },
  "billing": {},
  "payment_html": "",
  "store": {},
  "formattedShippingAddress": "",
  "formattedBillinggAddress": ""
}
```

### `sales_email_invoice_guest_template` & `sales_email_invoice_guest_template`
Triggered on order invocing. [Example data](MarketingEvents/sales_email_invoice_template.json).

Structure:
```json
{
  "customerName": "",
  "customerEmail": "",
  "is_guest": 1,
  "customer": {},
  "order": {},
  "invoice": {
    "order_id": "",
    "store_id": "",
    "customer_id": "",
    "billing_address_id": "",
    "shipping_address_id": "",
    "global_currency_code": "USD",
    "base_currency_code": "USD",
    "store_currency_code": "USD",
    "order_currency_code": "USD",
    "store_to_base_rate": "0.0000",
    "store_to_order_rate": "0.0000",
    "base_to_global_rate": "1.0000",
    "base_to_order_rate": "1.0000",
    "discount_description": null,
    "items": [],
    "total_qty": 1,
    "subtotal": 22,
    "base_subtotal": 22,
    "subtotal_incl_tax": 22,
    "base_subtotal_incl_tax": 22,
    "grand_total": 27,
    "base_grand_total": 27,
    "discount_amount": -0,
    "base_discount_amount": -0,
    "shipping_amount": "5.0000",
    "base_shipping_amount": "5.0000",
    "shipping_incl_tax": "5.0000",
    "base_shipping_incl_tax": "5.0000",
    "shipping_tax_amount": "0.0000",
    "base_shipping_tax_amount": "0.0000",
    "shipping_discount_tax_compensation_amount": "0.0000",
    "base_shipping_discount_tax_compensation_amnt": "0.0000",
    "tax_amount": 0,
    "base_tax_amount": 0,
    "discount_tax_compensation_amount": 0,
    "base_discount_tax_compensation_amount": 0,
    "base_cost": 0,
    "can_void_flag": false,
    "state": 2,
    "increment_id": "000000008",
    "entity_id": "8",
    "id": "8",
    "created_at": "2018-08-14 09:07:13",
    "updated_at": "2018-08-14 09:07:13",
    "comments": [],
    "send_email": true
  },
  "comment": "",
  "billing": {},
  "payment_html": "",
  "store": {},
  "formattedShippingAddress": "",
  "formattedBillinggAddress": ""
}
```


### `sales_email_shipment_template` & `sales_email_shipment_guest_template`
Triggered on order shipment. [Example data](MarketingEvents/sales_email_shipment_guest_template.json).

Structure:
```json
{
  "customerName": "",
  "customerEmail": "",
  "is_guest": 1,
  "customer": {},
  "order": {},
  "shipment": {
    "order_id": "",
    "store_id": "",
    "customer_id": "",
    "billing_address_id": "",
    "shipping_address_id": "",
    "global_currency_code": "",
    "base_currency_code": "",
    "store_currency_code": "",
    "order_currency_code": "",
    "store_to_base_rate": "0.0000",
    "store_to_order_rate": "0.0000",
    "base_to_global_rate": "1.0000",
    "base_to_order_rate": "1.0000",
    "discount_description": null,
    "total_qty": 1,
    "packages": [],
    "increment_id": "000000006",
    "entity_id": "",
    "id": "",
    "created_at": "2018-08-14 09:05:18",
    "updated_at": "2018-08-14 09:05:18",
    "comments": [],
    "send_email": true
  },
  "comment": "",
  "billing": {},
  "payment_html": "",
  "store": {},
  "formattedShippingAddress": "",
  "formattedBillinggAddress": ""
}
```

### `sales_email_creditmemo_guest_template` & `sales_email_creditmemo_guest_template`
Triggered on order refund. [Example data](MarketingEvents/sales_email_creditmemo_guest_template.json).

Structure:
```json
{
  "customerName": "",
  "customerEmail": "",
  "is_guest": 1,
  "customer": {},
  "order": {},
  "creditmemo": {
    "order_id": "",
    "store_id": "",
    "customer_id": "",
    "billing_address_id": "",
    "shipping_address_id": "",
    "global_currency_code": "USD",
    "base_currency_code": "USD",
    "store_currency_code": "USD",
    "order_currency_code": "USD",
    "store_to_base_rate": "0.0000",
    "store_to_order_rate": "0.0000",
    "base_to_global_rate": "1.0000",
    "base_to_order_rate": "1.0000",
    "discount_amount": -0,
    "discount_description": null,
    "shipping_amount": 5,
    "base_discount_amount": -0,
    "base_shipping_incl_tax": 5,
    "shipping_tax_amount": 0,
    "base_shipping_tax_amount": 0,
    "shipping_discount_amount": "0.0000",
    "base_shipping_discount_amount": "0.0000",
    "items": [],
    "total_qty": 1,
    "base_shipping_amount": 5,
    "base_adjustment_positive": 0,
    "adjustment_positive": 0,
    "base_adjustment_negative": 0,
    "adjustment_negative": 0,
    "subtotal": 45,
    "base_subtotal": 45,
    "subtotal_incl_tax": 45,
    "base_subtotal_incl_tax": 45,
    "grand_total": 50,
    "base_grand_total": 50,
    "tax_amount": 0,
    "base_tax_amount": 0,
    "shipping_incl_tax": 5,
    "discount_tax_compensation_amount": 0,
    "base_discount_tax_compensation_amount": 0,
    "base_cost": 0,
    "adjustment": 0,
    "base_adjustment": 0,
    "state": 2,
    "do_transaction": false,
    "increment_id": "000000003",
    "entity_id": "3",
    "id": "3",
    "created_at": "2018-08-14 09:06:20",
    "updated_at": "2018-08-14 09:06:20",
    "comments": [],
    "send_email": true
  },
  "comment": "",
  "billing": {},
  "payment_html": "",
  "store": {},
  "formattedShippingAddress": "",
  "formattedBillinggAddress": ""
}
```

### `sales_email_order_comment_template` & `sales_email_order_comment_guest_template`
Triggered on order comment. [Example data](MarketingEvents/sales_email_comment_guest_template.json).

Structure:
```json
{
  "customerName": "",
  "customerEmail": "",
  "is_guest": 1,
  "customer": {},
  "order": {},
  "comment": "",
  "billing": {},
  "store": {},
  "formattedShippingAddress": "",
  "formattedBillinggAddress": ""
}
```