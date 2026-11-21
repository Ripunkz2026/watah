# Water Pallets Express

A plain PHP storefront running on XAMPP. Existing page URLs remain unchanged,
including `http://localhost/watah/`, `shop.php`, and `admin-index.php`.

## Project layout

```text
watah/
├── index.php, shop.php, ...   Public entry files; keep these URLs stable
├── app/
│   ├── bootstrap.php         Starts the session and loads configuration/helpers
│   ├── auth/                 Authentication helpers and administrator guard
│   ├── data/products.php     Current product catalog
│   ├── pages/
│   │   ├── storefront/       Landing, shop, product, cart, checkout
│   │   ├── account/          Login, registration, logout, account, customer dashboard
│   │   ├── admin/            Administrator pages and authentication entry files
│   │   ├── delivery/         Service area, freight quote, order status
│   │   └── information/      Contact, FAQs, policies, terms
│   ├── support/helpers.php  URL, price, cart, and shipping helpers
│   └── views/layouts/       Shared headers and footers
├── assets/
│   ├── css/                 Landing, Express page, and authentication styles
│   ├── js/                  Shared, landing, and contact scripts
│   └── images/
│       └── products/        Catalog photography
├── config/
│   ├── app.php              Application name and base URL
│   ├── database.php         PDO connection using DB_* environment variables
│   └── shipping.php         Allowed states, minimum orders, ZIP restrictions
├── database/schema.sql     Dashboard database schema
└── README.md
```

## Run locally

1. Keep this directory at `C:\xampp\htdocs\watah`.
2. Start Apache and MySQL in the XAMPP control panel.
3. Run `C:\xampp\php\php.exe database/migrate.php` to create or upgrade the database.
   Existing accounts and records are preserved; initial catalog and delivery rules are seeded.
4. Open `http://localhost/watah/`.

Database configuration reads `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, and
`DB_PASS` from the environment, with the existing local XAMPP defaults as fallbacks.
If the application folder changes, update `APP_BASE_URL` in `config/app.php`.
No new Apache document-root setting or URL rewrite rule is needed.

## Where to make changes

- **Landing page:** `app/pages/storefront/index.php`, `assets/css/landing.css`,
  and `assets/js/landing.js`.
- **Product data:** admin Products for adding products and updating prices and inventory;
  `app/data/products.php` reads MySQL. Choose **Add product**, enter a unique SKU,
  product details, price, and stock, then save to make the product visible in the shop.
  Choose a JPG, PNG, WebP, or GIF image from your computer (up to 2 MB), or use an
  HTTP/HTTPS URL or an existing path such as `images/products/1.jpg` relative to `assets/`.
  Uploaded images are saved in `assets/images/products/uploads/`; this directory must
  be writable by PHP. Preserve its `.htaccess` rules when deploying on Apache.
  Total bottles per pallet is calculated from bottles per case
  and cases per pallet.
- **Navigation/footer:** `app/views/layouts/`.
- **Customer pages:** `assets/css/customer.css` shares the landing page's design
  tokens and components. Account and dashboard pages use
  `app/views/layouts/customer-account.php`; shop, cart, checkout, delivery, and
  help pages use the same Express header and footer.
- **Delivery rules:** admin Delivery areas; `config/shipping.php` reads saved rules.
- **Authentication:** `app/auth/` and `app/pages/account/`. Customers and admins
  use `login.php`; valid credentials open the matching dashboard. The old
  `admin-login.php` URL redirects to this shared page. If both account types
  have the same email and password, the administrator account takes precedence.

Root PHP files are small entry points. Edit their matching files under
`app/pages/` for page behavior and markup. Each entry point loads
`app/bootstrap.php` before the page; use `APP_ROOT` for filesystem includes.

Use `siteUrl('shop.php')` for page links and
`assetUrl('images/products/3.jpg')` for public assets. `assetUrl()` adds the
`/assets/` prefix. Old asset paths have moved; application references have
been updated to their new locations.

The `app/`, `config/`, and `database/` folders deny direct HTTP access through
Apache `.htaccess` files. PHP filesystem includes continue to work. Preserve
equivalent access rules if hosting on another server.

## Current scope

Accounts, products, delivery rules, orders, order items, freight quotes, and status
history are stored in MySQL. Checkout saves orders transactionally. Customers see
only their own orders; admins manage freight, fulfillment, stock, and delivery
rules. Cart contents remain in the session.

Payments and freight are handled manually; the contact form still previews
messages without sending them. Setup, table relationships, workflow, and isolated
integration tests are documented in [database/README.md](database/README.md).

## PHP syntax check (PowerShell)

```powershell
Get-ChildItem -Recurse -Filter *.php | ForEach-Object {
    & C:\xampp\php\php.exe -l $_.FullName
}
```
