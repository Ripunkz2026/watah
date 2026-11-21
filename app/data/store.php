<?php
function appDatabase(): PDO
{
    static $pdo;
    if (!$pdo) {
        $pdo = require APP_ROOT . '/config/database.php';
        if (!$pdo instanceof PDO) throw new RuntimeException('Database service is unavailable.');
    }
    return $pdo;
}

function requestText(array $input, string $key): string
{
    return is_string($input[$key] ?? null) ? trim($input[$key]) : '';
}

function verifyCsrf(): void
{
    if (empty($_SESSION['csrf_token']) || !is_string($_POST['csrf_token'] ?? null) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        throw new InvalidArgumentException('Your session expired. Refresh the page and try again.');
    }
}

function moneyCents(string $value): int
{
    if (!preg_match('/^\d{1,7}(?:\.\d{1,2})?$/', $value)) throw new InvalidArgumentException('Enter a valid amount with at most two decimal places.');
    [$whole, $fraction] = array_pad(explode('.', $value), 2, '');
    return ((int)$whole * 100) + (int)str_pad($fraction, 2, '0');
}

function catalogProducts(): array
{
    $rows = appDatabase()->query('SELECT * FROM products ORDER BY id')->fetchAll();
    foreach ($rows as &$row) {
        foreach (['id','min_order','bottles_per_case','cases_per_pallet','total_bottles_per_pallet'] as $key) $row[$key] = (int)$row[$key];
        $row['inventory'] = $row['inventory'] === null ? null : (int)$row['inventory'];
        $row['price_per_pallet'] = (float)$row['price_per_pallet'];
        $row['features'] = json_decode($row['features'], true) ?: [];
        $row['image'] = preg_match('~^https?://~i', $row['image']) ? $row['image'] : assetUrl($row['image']);
    }
    return $rows;
}

function productCanOrder(array $product): bool
{
    return $product['status'] === 'in-stock' && ($product['inventory'] === null || (int)$product['inventory'] >= (int)$product['min_order']);
}

function deliveryStates(): array
{
    $rows = appDatabase()->query('SELECT * FROM shipping_states ORDER BY code')->fetchAll();
    $zips = appDatabase()->query('SELECT state_code, zip FROM shipping_zips ORDER BY zip')->fetchAll();
    foreach ($rows as &$row) {
        $row['min_order'] = (int)$row['min_order'];
        $row['zips'] = array_column(array_filter($zips, fn($zip) => $zip['state_code'] === $row['code']), 'zip');
    }
    return $rows;
}

function orderSelect(): string
{
    return 'SELECT o.*, o.id AS db_id, o.order_number AS id, o.company AS customer, o.created_at AS date FROM orders o';
}

function customerOrders(int $customerId): array
{
    $query = appDatabase()->prepare(orderSelect() . ' WHERE o.customer_id=? ORDER BY o.created_at DESC, o.id DESC');
    $query->execute([$customerId]);
    return $query->fetchAll();
}

function allOrders(): array
{
    return appDatabase()->query(orderSelect() . ' ORDER BY o.created_at DESC, o.id DESC')->fetchAll();
}

function findOrder(string $number, ?int $customerId = null): ?array
{
    $query = appDatabase()->prepare(orderSelect() . ' WHERE o.order_number=?' . ($customerId !== null ? ' AND o.customer_id=?' : ''));
    $query->execute($customerId !== null ? [$number, $customerId] : [$number]);
    $order = $query->fetch();
    if (!$order) return null;
    foreach (['items' => 'order_items', 'events' => 'order_events', 'quotes' => 'freight_quotes'] as $key => $table) {
        $query = appDatabase()->prepare('SELECT * FROM ' . $table . ' WHERE order_id=? ORDER BY id');
        $query->execute([$order['db_id']]);
        $order[$key] = $query->fetchAll();
    }
    return $order;
}

function createCustomerOrder(int $customerId, array $cart, array $input, string $token): string
{
    if (!preg_match('/^[a-f0-9]{64}$/', $token)) throw new InvalidArgumentException('Refresh checkout before submitting your order.');
    $pdo = appDatabase();
    $pdo->beginTransaction();
    try {
        // Serialize submissions from one customer and make retries idempotent.
        $query = $pdo->prepare('SELECT * FROM customers WHERE id=? FOR UPDATE');
        $query->execute([$customerId]);
        $customer = $query->fetch();
        if (!$customer) throw new InvalidArgumentException('Sign in again before ordering.');
        $query = $pdo->prepare('SELECT order_number FROM orders WHERE customer_id=? AND checkout_token=?');
        $query->execute([$customerId, $token]);
        if ($existing = $query->fetchColumn()) { $pdo->commit(); return $existing; }
        $values = [];
        foreach (['first_name'=>120,'last_name'=>120,'email'=>254,'street'=>255,'city'=>120,'state'=>2,'zip'=>5] as $key => $limit) {
            $values[$key] = requestText($input, $key);
            if ($values[$key] === '' || strlen($values[$key]) > $limit) throw new InvalidArgumentException('Complete all delivery fields using valid lengths.');
        }
        if (!filter_var($values['email'], FILTER_VALIDATE_EMAIL)) throw new InvalidArgumentException('Enter a valid email address.');
        if (!preg_match('/^[0-9]{5}$/', $values['zip'])) throw new InvalidArgumentException('Enter a valid 5-digit ZIP code.');
        $values['state'] = strtoupper($values['state']);
        $query = $pdo->prepare('SELECT * FROM shipping_states WHERE code=? FOR UPDATE');
        $query->execute([$values['state']]);
        $state = $query->fetch();
        if (!$state || !$state['enabled']) throw new InvalidArgumentException('This state is not currently eligible for delivery.');
        if ($state['restrict_zips']) {
            $query = $pdo->prepare('SELECT COUNT(*) FROM shipping_zips WHERE state_code=? AND zip=?');
            $query->execute([$values['state'], $values['zip']]);
            if (!$query->fetchColumn()) throw new InvalidArgumentException('This ZIP code is not currently eligible for delivery.');
        }
        if (!$cart) throw new InvalidArgumentException('Your cart is empty.');
        if (count($cart) > 100) throw new InvalidArgumentException('Too many different products in this order.');
        ksort($cart, SORT_NUMERIC);
        $items = []; $subtotal = 0; $pallets = 0;
        $query = $pdo->prepare('SELECT * FROM products WHERE id=? FOR UPDATE');
        foreach ($cart as $id => $quantity) {
            if (!is_numeric($id) || (int)$id < 1 || !is_int($quantity) || $quantity < 1 || $quantity > 999) throw new InvalidArgumentException('Pallet quantities must be between 1 and 999.');
            $query->execute([(int)$id]);
            $product = $query->fetch();
            if (!$product || $product['status'] !== 'in-stock') throw new InvalidArgumentException('An item in your cart is no longer available. Please review your cart.');
            if ($quantity < (int)$product['min_order']) throw new InvalidArgumentException($product['name'] . ' requires at least ' . $product['min_order'] . ' pallets.');
            if ($product['inventory'] !== null && $quantity > (int)$product['inventory']) throw new InvalidArgumentException('Not enough stock for ' . $product['name'] . '. Please update your cart.');
            $line = moneyCents($product['price_per_pallet']) * $quantity;
            $subtotal += $line; $pallets += $quantity;
            $items[] = [$product, $quantity, $line];
        }
        if ($pallets < (int)$state['min_order']) throw new InvalidArgumentException('Your delivery area requires at least ' . $state['min_order'] . ' pallets.');
        if ($subtotal > 90000000000) throw new InvalidArgumentException('Please contact us to arrange an order of this size.');
        $tax = intdiv($subtotal * 75 + 500, 1000); // Existing 7.5% estimate; integer cents.
        $number = 'WPE-' . strtoupper(bin2hex(random_bytes(6)));
        $query = $pdo->prepare('INSERT INTO orders (order_number, customer_id, checkout_token, customer_name, company, email, street, city, state, zip, subtotal, shipping, tax, total) VALUES (?,?,?,?,?,?,?,?,?,?,?,0,?,?)');
        $query->execute([$number,$customerId,$token,$values['first_name'].' '.$values['last_name'],$customer['company'],$values['email'],$values['street'],$values['city'],$values['state'],$values['zip'],$subtotal/100,$tax/100,($subtotal+$tax)/100]);
        $orderId = (int)$pdo->lastInsertId();
        $insert = $pdo->prepare('INSERT INTO order_items (order_id,product_id,product_name,sku,pallet_type,quantity,unit_price,line_total,inventory_reserved) VALUES (?,?,?,?,?,?,?,?,?)');
        $reserve = $pdo->prepare('UPDATE products SET inventory=inventory-? WHERE id=?');
        foreach ($items as [$product, $quantity, $line]) {
            $reserved = $product['inventory'] !== null;
            $insert->execute([$orderId,$product['id'],$product['name'],$product['sku'],$product['pallet_type'],$quantity,$product['price_per_pallet'],$line/100,(int)$reserved]);
            if ($reserved) $reserve->execute([$quantity,$product['id']]);
        }
        $pdo->prepare('INSERT INTO order_events (order_id,status,note) VALUES (?,?,?)')->execute([$orderId,'Pending freight quote','Order submitted. Delivery eligibility confirmed; freight review requested.']);
        $pdo->commit();
        return $number;
    } catch (Throwable $error) { if ($pdo->inTransaction()) $pdo->rollBack(); throw $error; }
}

function orderTransitions(): array
{
    return ['Pending freight quote'=>['Cancelled'], 'Awaiting payment'=>['Ready for shipment','Cancelled'], 'Ready for shipment'=>['Shipped','Cancelled'], 'Shipped'=>['Delivered'], 'Delivered'=>[], 'Cancelled'=>[]];
}

function updateOrderByAdmin(string $number, int $adminId, array $input): void
{
    $pdo = appDatabase();
    $pdo->beginTransaction();
    try {
        $query = $pdo->prepare('SELECT * FROM orders WHERE order_number=? FOR UPDATE');
        $query->execute([$number]);
        $order = $query->fetch();
        if (!$order) throw new InvalidArgumentException('Order not found.');
        if (requestText($input, 'expected_status') !== $order['status']) throw new InvalidArgumentException('This order changed. Refresh the page before updating it.');
        $note = requestText($input, 'note');
        if (strlen($note) > 500) throw new InvalidArgumentException('Notes must be 500 characters or fewer.');
        if (requestText($input, 'action') === 'quote') {
            if ($order['status'] !== 'Pending freight quote') throw new InvalidArgumentException('Freight has already been reviewed.');
            $carrier = requestText($input,'carrier'); $service = requestText($input,'service');
            if ($carrier === '' || strlen($carrier)>160 || $service === '' || strlen($service)>180) throw new InvalidArgumentException('Enter a carrier and delivery service.');
            $rate = moneyCents(requestText($input,'rate'));
            $pdo->prepare('INSERT INTO freight_quotes (order_id,admin_id,carrier,service,rate) VALUES (?,?,?,?,?)')->execute([$order['id'],$adminId,$carrier,$service,$rate/100]);
            $status = 'Awaiting payment';
            $total = moneyCents($order['subtotal']) + moneyCents($order['tax']) + $rate;
            $pdo->prepare('UPDATE orders SET shipping=?,total=? WHERE id=?')->execute([$rate/100,$total/100,$order['id']]);
            $note = 'Freight confirmed: ' . $carrier . ' / ' . $service . '.';
        } else {
            if (requestText($input,'action') !== 'status') throw new InvalidArgumentException('Unknown action.');
            $status = requestText($input,'status');
            if (!in_array($status, orderTransitions()[$order['status']] ?? [], true)) throw new InvalidArgumentException('That status transition is not allowed.');
            if ($status === 'Ready for shipment') {
                if (requestText($input,'payment_confirmed') !== '1') throw new InvalidArgumentException('Confirm that payment was received before preparing shipment.');
                $note = 'Payment receipt confirmed by administrator. ' . $note;
            }
            if ($status === 'Cancelled') {
                $query = $pdo->prepare('SELECT product_id,quantity FROM order_items WHERE order_id=? AND inventory_reserved=1 ORDER BY product_id');
                $query->execute([$order['id']]);
                $restore = $pdo->prepare('UPDATE products SET inventory=inventory+? WHERE id=? AND inventory IS NOT NULL');
                foreach ($query->fetchAll() as $item) $restore->execute([$item['quantity'],$item['product_id']]);
            }
        }
        $pdo->prepare('UPDATE orders SET status=? WHERE id=?')->execute([$status,$order['id']]);
        $pdo->prepare('INSERT INTO order_events (order_id,admin_id,status,note) VALUES (?,?,?,?)')->execute([$order['id'],$adminId,$status,substr($note,0,500)]);
        $pdo->commit();
    } catch (Throwable $error) { if ($pdo->inTransaction()) $pdo->rollBack(); throw $error; }
}

function updateCatalogProduct(int $id, array $input): void
{
    $exists = appDatabase()->prepare('SELECT id FROM products WHERE id=?');
    $exists->execute([$id]);
    if (!$exists->fetchColumn()) throw new InvalidArgumentException('Product not found.');
    $values = catalogStockValues($input);
    $query = appDatabase()->prepare('UPDATE products SET price_per_pallet=?,inventory=?,min_order=?,status=?,warehouse=? WHERE id=?');
    $query->execute([...$values,$id]);
}

function catalogStockValues(array $input): array
{
    $price = moneyCents(requestText($input,'price'));
    if ($price < 1) throw new InvalidArgumentException('Price must be greater than zero.');
    $inventory = requestText($input,'inventory');
    if ($inventory !== '' && !preg_match('/^\d{1,7}$/', $inventory)) throw new InvalidArgumentException('Inventory must be a whole number, or blank for unconfirmed stock.');
    $minimum = filter_var(requestText($input,'min_order'), FILTER_VALIDATE_INT, ['options'=>['min_range'=>1,'max_range'=>999]]);
    $status = requestText($input,'status'); $warehouse = requestText($input,'warehouse');
    if (!$minimum || !in_array($status,['in-stock','out-of-stock'],true) || $warehouse === '' || strlen($warehouse)>160) throw new InvalidArgumentException('Check the minimum quantity, status, and warehouse.');
    return [$price/100,$inventory === '' ? null : (int)$inventory,$minimum,$status,$warehouse];
}

function saveCatalogImageUpload(array $file): string
{
    $error = $file['error'] ?? null;
    if ($error === UPLOAD_ERR_INI_SIZE || $error === UPLOAD_ERR_FORM_SIZE) {
        throw new InvalidArgumentException('The product image is too large. Choose a file up to 2 MB.');
    }
    if ($error !== UPLOAD_ERR_OK || !is_string($file['tmp_name'] ?? null) || !is_uploaded_file($file['tmp_name'])) {
        throw new InvalidArgumentException('The image could not be uploaded. Please choose the file again.');
    }
    $temporaryPath = $file['tmp_name'];
    $size = filesize($temporaryPath);
    if ($size === false || $size < 1 || $size > 2 * 1024 * 1024) {
        throw new InvalidArgumentException('Choose a non-empty product image up to 2 MB.');
    }
    $extensions = ['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp','image/gif'=>'gif'];
    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($temporaryPath);
    $dimensions = @getimagesize($temporaryPath);
    if (!isset($extensions[$mime]) || !$dimensions || ($dimensions['mime'] ?? '') !== $mime) {
        throw new InvalidArgumentException('Choose a valid JPG, PNG, WebP, or GIF image.');
    }
    // Ignore the supplied filename and MIME type; serve only verified images as static files.
    $relativePath = 'images/products/uploads/' . bin2hex(random_bytes(16)) . '.' . $extensions[$mime];
    if (!move_uploaded_file($temporaryPath, APP_ROOT . '/assets/' . $relativePath)) {
        throw new RuntimeException('Could not store the uploaded product image.');
    }
    return $relativePath;
}

function createCatalogProduct(array $input, ?array $file = null): int
{
    $stock = catalogStockValues($input);
    $details = [];
    foreach (['name'=>180,'sku'=>80,'category'=>80,'bottle_size'=>80,'description'=>10000] as $key=>$limit) {
        $details[$key] = requestText($input, $key);
        if ($details[$key] === '' || strlen($details[$key]) > $limit) {
            throw new InvalidArgumentException('Enter a valid ' . str_replace('_', ' ', $key) . ' (up to ' . $limit . ' characters).');
        }
    }
    if (!preg_match('/^[A-Za-z0-9][A-Za-z0-9-]{0,79}$/', $details['sku'])) {
        throw new InvalidArgumentException('SKU must start with a letter or number and contain only letters, numbers, and hyphens.');
    }
    $palletType = requestText($input, 'pallet_type');
    if (!in_array($palletType, ['Full pallet','Half pallet'], true)) throw new InvalidArgumentException('Choose a valid pallet size.');
    foreach (['bottles_per_case','cases_per_pallet'] as $key) {
        $details[$key] = filter_var(requestText($input, $key), FILTER_VALIDATE_INT, ['options'=>['min_range'=>1,'max_range'=>9999]]);
        if (!$details[$key]) throw new InvalidArgumentException('Enter ' . str_replace('_', ' ', $key) . ' between 1 and 9999.');
    }
    foreach (['pallet_weight','pallet_dimensions'] as $key) {
        $details[$key] = requestText($input, $key) ?: 'To be confirmed';
        if (strlen($details[$key]) > 100) throw new InvalidArgumentException('Pallet weight and dimensions must be under 100 characters.');
    }
    $image = requestText($input, 'image');
    $hasUpload = $file !== null && ($file['error'] ?? null) !== UPLOAD_ERR_NO_FILE;
    $remoteImage = filter_var($image, FILTER_VALIDATE_URL) && in_array(strtolower(parse_url($image, PHP_URL_SCHEME) ?: ''), ['http','https'], true);
    $localImage = preg_match('~^images/(?:[A-Za-z0-9_-]+/)*[A-Za-z0-9_.-]+\.(?:jpg|jpeg|png|webp|gif)$~i', $image)
        && is_file(APP_ROOT . '/assets/' . $image);
    if (!$hasUpload && (strlen($image) > 500 || (!$remoteImage && !$localImage))) {
        throw new InvalidArgumentException('Choose a product image from your computer, or enter an HTTP/HTTPS image URL or existing image path.');
    }
    $featureText = requestText($input, 'features');
    if (strlen($featureText) > 10000) throw new InvalidArgumentException('Product features must be under 10000 characters.');
    $features = array_values(array_filter(array_map('trim', preg_split('/\R/', $featureText)), fn($line) => $line !== ''));
    $encodedFeatures = json_encode($features, JSON_THROW_ON_ERROR);
    $total = $details['bottles_per_case'] * $details['cases_per_pallet'];
    $query = appDatabase()->prepare('INSERT INTO products (price_per_pallet,inventory,min_order,status,warehouse,name,sku,slug,category,bottle_size,bottles_per_case,cases_per_pallet,total_bottles_per_pallet,pallet_weight,pallet_dimensions,description,features,image,pallet_type) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)');
    $uploadedImage = null;
    try {
        if ($hasUpload) $image = $uploadedImage = saveCatalogImageUpload($file);
        $query->execute([...$stock,$details['name'],$details['sku'],strtolower($details['sku']),$details['category'],$details['bottle_size'],$details['bottles_per_case'],$details['cases_per_pallet'],$total,$details['pallet_weight'],$details['pallet_dimensions'],$details['description'],$encodedFeatures,$image,$palletType]);
    } catch (Throwable $error) {
        if ($uploadedImage !== null) unlink(APP_ROOT . '/assets/' . $uploadedImage);
        if ($error instanceof PDOException && ($error->errorInfo[1] ?? null) === 1062) throw new InvalidArgumentException('A product with this SKU already exists. Choose a unique SKU.');
        throw $error;
    }
    return (int)appDatabase()->lastInsertId();
}

function updateDeliveryState(string $code, array $input): void
{
    $minimum = filter_var(requestText($input,'min_order'), FILTER_VALIDATE_INT, ['options'=>['min_range'=>1,'max_range'=>999]]);
    $zone = requestText($input,'zone');
    $zips = array_values(array_unique(preg_split('/[\s,]+/', requestText($input,'zips'), -1, PREG_SPLIT_NO_EMPTY)));
    $restricted = requestText($input,'restrict_zips') === '1';
    if (!$minimum || $zone === '' || strlen($zone)>80 || count($zips)>2000) throw new InvalidArgumentException('Check the zone, minimum order, and ZIP list.');
    foreach ($zips as $zip) if (!preg_match('/^[0-9]{5}$/', $zip)) throw new InvalidArgumentException('Each ZIP code must contain exactly five digits.');
    if ($restricted && !$zips) throw new InvalidArgumentException('List at least one ZIP code when ZIP restrictions are enabled.');
    $pdo = appDatabase(); $pdo->beginTransaction();
    try {
        $query = $pdo->prepare('SELECT code FROM shipping_states WHERE code=? FOR UPDATE'); $query->execute([$code]);
        if (!$query->fetchColumn()) throw new InvalidArgumentException('State not found.');
        $pdo->prepare('UPDATE shipping_states SET zone=?,min_order=?,enabled=?,restrict_zips=? WHERE code=?')->execute([$zone,$minimum,requestText($input,'enabled') === '1' ? 1 : 0,(int)$restricted,$code]);
        $pdo->prepare('DELETE FROM shipping_zips WHERE state_code=?')->execute([$code]);
        $insert = $pdo->prepare('INSERT INTO shipping_zips (state_code,zip) VALUES (?,?)');
        foreach ($zips as $zip) $insert->execute([$code,$zip]);
        $pdo->commit();
    } catch (Throwable $error) { if ($pdo->inTransaction()) $pdo->rollBack(); throw $error; }
}
