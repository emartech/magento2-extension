# Marketing events

## Customer related events

### `customer_new_account_registered`

Triggered on new customer registration. [Example data](docs/MarketingEvents/customer_new_account_registered.json).

Structure:

```json
{
  "customer": {
    "password_hash": "",
    "rp_token": "",
    "rp_token_created_at": "2018-08-14 11:28:54",
    "deleteable": true,
    "failures_num": "0",
    "first_failure": null,
    "lock_expires": null,
    "id": 31,
    "group_id": 1,
    "created_at": "2018-08-14 11:28:54",
    "updated_at": "2018-08-14 11:28:54",
    "created_in": "Default Store View",
    "email": "",
    "firstname": "",
    "lastname": "",
    "store_id": 1,
    "website_id": 1,
    "addresses": [],
    "disable_auto_group_change": 0,
    "name": ""
  },
  "back_url": null,
  "store": {
    "store_id": "1",
    "code": "default",
    "website_id": "1",
    "group_id": "1",
    "name": "Default Store View",
    "sort_order": "0",
    "is_active": "1"
  }
}
```

### `customer_new_account_confirmation`

Triggered on new customer registration when email confirmation needed. [Example data](docs/MarketingEvents/customer_new_account_condirmation.json).

Structure:

```json
"store": {
    "code": "default",
    "name": "Default Store View",
    "group_id": "1",
    "store_id": "1",
    "is_active": "1",
    "sort_order": "0",
    "website_id": "1"
  },
  "back_url": null,
  "customer": {
    "id": "11",
    "dob": null,
    "email": "test.fest@test.com",
    "gender": null,
    "prefix": null,
    "suffix": null,
    "taxvat": null,
    "group_id": "1",
    "lastname": "fest",
    "rp_token": "35sZiMQynek52LRC4gCEAeUg8SxwDgPl",
    "store_id": "1",
    "firstname": "test",
    "is_active": "1",
    "created_at": "2010-12-01 10:45:45",
    "middlename": null,
    "updated_at": "2010-12-01 10:45:46",
    "website_id": "1",
    "extra_fields": [
      {
        "key": "emarsys_test_favorite_sport",
        "value": null,
        "text_value": null
      }
    ]
}
```

### `customer_new_account_registered_no_password`

Triggered on new customer registration after guest order. [Example data](docs/MarketingEvents/customer_new_account_registered_no_password.json).

Structure:

```json
{
  "customer": {
    "password_hash": null,
    "rp_token": "",
    "rp_token_created_at": "2018-08-14 11:28:54",
    "deleteable": true,
    "failures_num": "0",
    "first_failure": null,
    "lock_expires": null,
    "id": 32,
    "group_id": 1,
    "created_at": "2018-08-14 11:28:54",
    "updated_at": "2018-08-14 11:28:54",
    "created_in": "Default Store View",
    "email": "",
    "firstname": "",
    "lastname": "",
    "store_id": 1,
    "website_id": 1,
    "addresses": [],
    "disable_auto_group_change": 0,
    "name": ""
  },
  "back_url": null,
  "store": {
    "store_id": "1",
    "code": "default",
    "website_id": "1",
    "group_id": "1",
    "name": "Default Store View",
    "sort_order": "0",
    "is_active": "1"
  }
}
```

### `customer_password_reset_confirmation`

Triggered when customer clicks 'Forgot password' then 'Reset my password'. [Example data](docs/MarketingEvents/customer_password_reset_confirmation.json).

Structure:

```json
{
  "customer": {
    "password_hash": null,
    "rp_token": "",
    "rp_token_created_at": "2018-08-14 11:36:59",
    "deleteable": true,
    "failures_num": "3",
    "first_failure": "2018-08-14 08:59:26",
    "lock_expires": null,
    "id": 27,
    "group_id": 1,
    "default_billing": "3",
    "default_shipping": "2",
    "created_at": "2018-08-09 15:12:06",
    "updated_at": "2018-08-14 09:00:49",
    "created_in": "Default Store View",
    "email": "",
    "firstname": "",
    "lastname": "",
    "store_id": 1,
    "website_id": 1,
    "addresses": [],
    "disable_auto_group_change": 0,
    "name": ""
  },
  "0": {
    "store_id": "1",
    "code": "default",
    "website_id": "1",
    "group_id": "1",
    "name": "Default Store View",
    "sort_order": "0",
    "is_active": "1"
  }
}
```

### `customer_password_reset`

Triggered when customer changes password when logged in. [Example data](docs/MarketingEvents/customer_password_reset.json).

Structure:

```json
{
  "customer": {
    "password_hash": "",
    "rp_token": null,
    "rp_token_created_at": "2018-08-14 11:36:59",
    "deleteable": true,
    "failures_num": "3",
    "first_failure": "2018-08-14 08:59:26",
    "lock_expires": null,
    "id": 27,
    "group_id": 1,
    "default_billing": "3",
    "default_shipping": "2",
    "created_at": "2018-08-09 15:12:06",
    "updated_at": "2018-08-14 09:00:49",
    "created_in": "Default Store View",
    "email": "",
    "firstname": "",
    "lastname": "",
    "store_id": 1,
    "website_id": 1,
    "addresses": [],
    "disable_auto_group_change": 0,
    "name": ""
  },
  "0": {
    "store_id": "1",
    "code": "default",
    "website_id": "1",
    "group_id": "1",
    "name": "Default Store View",
    "sort_order": "0",
    "is_active": "1"
  }
}
```

### `customer_password_reminder`

Triggered when reset customer password change is initiated on the admin panel. [Example data](docs/MarketingEvents/customer_password_reminder.json).

Structure:

```json
{
  "customer": {
    "password_hash": null,
    "rp_token": "",
    "rp_token_created_at": "2018-08-14 11:36:59",
    "deleteable": true,
    "failures_num": "3",
    "first_failure": "2018-08-14 08:59:26",
    "lock_expires": null,
    "id": 27,
    "group_id": 1,
    "default_billing": "3",
    "default_shipping": "2",
    "created_at": "2018-08-09 15:12:06",
    "updated_at": "2018-08-14 09:00:49",
    "created_in": "Default Store View",
    "email": "",
    "firstname": "",
    "lastname": "",
    "store_id": 1,
    "website_id": 1,
    "addresses": [],
    "disable_auto_group_change": 0,
    "name": ""
  },
  "0": {
    "store_id": "1",
    "code": "default",
    "website_id": "1",
    "group_id": "1",
    "name": "Default Store View",
    "sort_order": "0",
    "is_active": "1"
  }
}
```

### `newsletter_send_confirmation_success_email`

Triggered on successful subscription. [Example data](docs/MarketingEvents/newsletter_send_confirmation_success_email.json).

Structure:

```json
{
  "subscriber": {
    "subscriber_confirm_code": "",
    "subscriber_status": 1,
    "store_id": "1",
    "customer_id": "",
    "subscriber_email": "",
    "change_status_at": "2018-08-14 12:18:19",
    "subscriber_id": ""
  }
}
```

### `newsletter_send_confirmation_request_email`

Triggered on subscription if confirmation is set to required. [Example data](docs/MarketingEvents/newsletter_send_confirmation_request_email.json).

Structure:

```json
{
  "subscriber": {
    "subscriber_confirm_code": "",
    "subscriber_status": 2,
    "store_id": "1",
    "customer_id": "",
    "subscriber_email": "",
    "change_status_at": "2018-08-14 12:18:19",
    "subscriber_id": ""
  }
}
```

### `newsletter_send_unsubscription_email`

Triggered on unsubscription. [Example data](docs/MarketingEvents/newsletter_send_unsubscription_email.json).

Structure:

```json
{
  "subscriber": {
    "subscriber_confirm_code": "",
    "subscriber_status": 3,
    "store_id": "1",
    "customer_id": "",
    "subscriber_email": "",
    "change_status_at": "2018-08-14 12:18:19",
    "subscriber_id": ""
  }
}
```

### `customer_email_and_password_changed`

Triggred when customer changes his email address & password in account settings. [Example data](docs/MarketingEvents/customer_email_and_password_changed.json)

```json
{
  "customer": {
    "password_hash": "",
    "rp_token": null,
    "rp_token_created_at": null,
    "deleteable": true,
    "failures_num": "",
    "first_failure": "",
    "lock_expires": null,
    "id": 3,
    "group_id": 1,
    "created_at": "2018-09-03 08:36:44",
    "updated_at": "2018-09-03 08:41:13",
    "created_in": "Default Store View",
    "email": "yolo@test-test.net",
    "firstname": "Yolo",
    "lastname": "Test",
    "store_id": 1,
    "website_id": 1,
    "addresses": [],
    "disable_auto_group_change": 0
  },
  "store": {
    "store_id": "1",
    "code": "default",
    "website_id": "1",
    "group_id": "1",
    "name": "Default Store View",
    "sort_order": "0",
    "is_active": "1"
  },
  "orig_customer_email": "yolo@test.net",
  "new_customer_email": "yolo@test-test.net"
}
```

### `customer_email_changed`

Triggred when customer changes his email address in account settings. [Example data](docs/MarketingEvents/customer_email_changed.json)

```json
{
  "customer": {
    "password_hash": "",
    "rp_token": null,
    "rp_token_created_at": null,
    "deleteable": true,
    "failures_num": "",
    "first_failure": "",
    "lock_expires": null,
    "id": 3,
    "group_id": 1,
    "created_at": "2018-09-03 08:36:44",
    "updated_at": "2018-09-03 08:40:27",
    "created_in": "Default Store View",
    "email": "yolo@test-test.net",
    "firstname": "Yolo",
    "lastname": "Test",
    "store_id": 1,
    "website_id": 1,
    "addresses": []
  },
  "store": {
    "store_id": "1",
    "code": "default",
    "website_id": "1",
    "group_id": "1",
    "name": "Default Store View",
    "sort_order": "0",
    "is_active": "1"
  },
  "orig_customer_email": "yolo@test.net",
  "new_customer_email": "yolo@test-test.net"
}
```

---

## Sales related events

Guest events have empty customer property.

### `sales_email_order_template` & `sales_email_order_guest_template`

Triggered on new order submission. [Example data](docs/MarketingEvents/sales_email_order_template.json).

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

### `sales_email_invoice_template` & `sales_email_invoice_guest_template`

Triggered on order invoicing. [Example data](docs/MarketingEvents/sales_email_invoice_template.json).

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

Triggered on order shipment. [Example data](docs/MarketingEvents/sales_email_shipment_guest_template.json).

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

### `sales_email_creditmemo_template` & `sales_email_creditmemo_guest_template`

Triggered on order refund. [Example data](docs/MarketingEvents/sales_email_creditmemo_guest_template.json).

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

Triggered on order comment. [Example data](docs/MarketingEvents/sales_email_order_comment_template.json).

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
