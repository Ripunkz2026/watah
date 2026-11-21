# Dashboard database

The admin and customer dashboards share the existing `pure_life_demo` MySQL
database. Customers and admins remain separate account tables with hashed
passwords. Existing accounts are preserved.

Customer registration saves a delivery state and five-digit ZIP code. Account
and checkout pages check that location against the current delivery rules and
show availability and minimum order quantities. Customers outside the service
area can still register. Checkout validates the actual order destination.
Run the installer after updating to add the nullable `customers.state` and
`customers.zip` columns; older accounts keep an unknown location until supplied.

## Install or upgrade

Start MySQL in XAMPP, then run from the project directory:

```powershell
C:\xampp\php\php.exe database/migrate.php
```

The CLI-only installer creates missing tables, seeds the six existing products
and eight delivery states, and preserves existing records and admin edits when
run again. `schema.sql` contains table definitions; run the installer to create
the database and seed its initial catalog and delivery rules. No default account
or password is installed. Use existing administrator credentials, then choose
**Add administrator** from the admin dashboard to create more accounts.
Administrator creation requires an existing administrator account; public and
localhost first-account registration are disabled.

Connection settings come from `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, and
`DB_PASS`, with the existing XAMPP defaults. Database timestamps use UTC.

## Tables

| Table | Purpose |
| --- | --- |
| `customers`, `admins` | Separate customer and administrator identities |
| `products` | Catalog, prices, available inventory, availability, pallet details |
| `shipping_states`, `shipping_zips` | Enabled destinations, ZIP restrictions, minimum orders |
| `orders` | Customer ownership, delivery snapshot, amounts, current status, retry token |
| `order_items` | Purchased product names, SKUs, quantities, original prices, stock reservations |
| `freight_quotes` | Confirmed carrier, service, rate, and responsible administrator |
| `order_events` | Timestamped status history and customer-visible notes |
| `schema_migrations` | Initial seed tracking |

Orders reference customers; items reference orders and products; freight quotes
and order events reference orders and administrators. Historical order details
are snapshots and do not change when the customer profile or product price changes.

## Workflow

1. A signed-in customer submits checkout. Server-side validation checks current
   prices, stock, destination eligibility, and product/state minimums.
2. A transaction saves the order and items, reserves confirmed inventory, and
   starts status history. Repeated submissions with the same token reuse the
   existing order. The cart clears only after the save succeeds.
3. The order appears in the customer's account and the admin Orders page.
4. An admin opens the order and confirms a freight quote, changing its status to
   Awaiting payment. The customer sees the updated freight and total.
5. After independently receiving payment, an admin confirms receipt and moves
   the order to Ready for shipment, then Shipped and Delivered. Cancellation
   before shipping restores reserved stock once.

Admins can edit product prices, inventory, minimums, warehouse and availability
from Products, and enable/disable delivery states or update ZIP coverage from
Delivery areas. Saved rules apply to new orders; existing orders retain their
original delivery details.

Customer routes scope every order lookup to the signed-in customer's ID. Admin
routes verify the administrator account, and persistent form submissions require
a CSRF token. Database access uses prepared statements; status changes use row
locks and reject stale submissions.

## Integration checks

```powershell
C:\xampp\php\php.exe database/tests.php
```

The tests create an isolated database named `watah_test_<random>`, start a local
test HTTP server, and remove that database and its temporary sessions afterward.
They cover migration re-runs, ownership, pricing snapshots, validation, stock,
status history, CSRF, sign-in, checkout retries, admin edits, and customer updates.
The configured database and its accounts are not used by these tests.

## Current boundaries

Payments are recorded manually by administrators; no payment gateway or automatic
payment verification is installed. Freight is entered manually. The existing
7.5% estimated tax calculation is retained. Unknown inventory is explicitly
unconfirmed and is not decremented. Carts remain session-based. The contact form
still previews messages without sending them.
