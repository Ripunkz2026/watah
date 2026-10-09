# Pure Life Wholesale Demo (Phase 1)

This repository is a PHP + Tailwind demo storefront for a wholesale bottled water business. It is designed for a client-facing mockup first, with MySQL-ready configuration and a clear path toward more advanced features in later phases.

## Project purpose

- Build a polished storefront for full-pallet bottled water sales
- Show approved service area restrictions for selected U.S. states
- Present product catalog and cart flow for client review
- Keep the implementation compatible with XAMPP + PHP + MySQL
- Lay the foundation for future checkout, admin, and payment features

## Stack

- PHP 8+
- XAMPP local server
- Tailwind CSS via CDN for frontend styling
- MySQL-ready PDO connection file
- PHP sessions for demo cart handling

## Phase structure

### Phase 1 (current)

- Home page
- Shop page
- Product detail page
- Service area checker
- Cart page
- Checkout flow with freight quote logic
- Order status page
- Customer login and account page
- Customer dashboard mockup
- FAQ and policy pages
- Admin dashboard and territory pages
- Admin login mockup

### Next phases

- real MySQL database schema
- product management admin
- order processing and freight quote approval
- Stripe payment integration
- real authentication and authorization
- deployment and production hardening

## Local setup

### Option 1: XAMPP (recommended for demo)

1. Place this project in your XAMPP document root, for example:
   `C:\xampp\htdocs\watah`
2. Start Apache and MySQL in XAMPP.
3. Open the browser and visit:
   `http://localhost/watah/index.php`
4. For admin demo pages, visit:
   `http://localhost/watah/admin/index.php`

### Option 2: PHP built-in server

Run the following from the project root:

```powershell
C:\xampp\php\php.exe -S localhost:8000 -t "C:/xampp/htdocs/watah"
```

Then open:

```text
http://localhost:8000/index.php
```

## MySQL notes

The project includes `db.php` with a PDO connection to MySQL using environment variables. For the current client demo, the storefront is primarily powered by mock product data and PHP sessions until the database schema is implemented.

Create a database and configure environment variables in your OS or php.ini if you want to connect later:

```bash
DB_HOST=127.0.0.1
DB_NAME=pure_life_demo
DB_USER=root
DB_PASS=
```

## Files to review

- `index.php` — storefront home page
- `shop.php` — catalog and product ordering
- `product.php` — product detail page
- `service-area.php` — territory validation demo
- `cart.php` — cart and pricing summary
- `checkout.php` — checkout and freight quote flow
- `order-status.php` — tracking and order status timeline
- `freight-quote.php` — freight quote request and approval mockup
- `account.php` — customer account and order history
- `admin/index.php` — administration mockup
- `admin/states.php` — state and ZIP eligibility view
- `admin/products.php` — product and inventory views
- `admin/orders.php` — order management view
- `db.php` — MySQL connection placeholder

## Verification

The project was verified using the local XAMPP PHP runtime and a live PHP development server.

### Syntax validation

```powershell
C:\xampp\php\php.exe -l "C:/xampp/htdocs/watah/index.php"
C:\xampp\php\php.exe -l "C:/xampp/htdocs/watah/shop.php"
C:\xampp\php\php.exe -l "C:/xampp/htdocs/watah/admin/index.php"
```

### Live server verification

```powershell
C:\xampp\php\php.exe -S localhost:8000 -t "C:/xampp/htdocs/watah"
Invoke-WebRequest -Uri "http://localhost:8000/index.php" -UseBasicParsing
```

Expected result: HTTP status `200` and page title `Pure Life Wholesale | Wholesale Pallet Store`.

## Important note

This is a presentation-first demo and is not production-ready. It is meant for client review and design validation before moving into stricter shipping, payment, and compliance workflows.
