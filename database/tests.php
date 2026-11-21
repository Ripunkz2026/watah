<?php
// Integration tests use a freshly created database, never the configured store.
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
$testName = 'watah_test_' . bin2hex(random_bytes(6));
putenv('DB_NAME=' . $testName);
$runtime = __DIR__ . '/.test-' . substr($testName, 11);
mkdir($runtime, 0700);
ini_set('session.save_path', $runtime);
$server = null; $pdo = null; $checks = 0; $uploadedTestFiles = [];
function check(bool $condition, string $message): void {
    global $checks;
    if (!$condition) throw new RuntimeException('FAILED: ' . $message);
    $checks++;
}
function rejects(callable $action, string $message): void {
    try { $action(); } catch (InvalidArgumentException $error) { check(true,$message); return; }
    throw new RuntimeException('FAILED: ' . $message);
}
function hiddenValue(string $html, string $name): string {
    preg_match('/name="' . preg_quote($name,'/') . '" value="([^"]*)"/', $html, $match);
    return html_entity_decode($match[1] ?? '', ENT_QUOTES);
}
function browserRequest($client, string $url, ?array $post = null): array {
    curl_setopt_array($client, [CURLOPT_URL=>$url,CURLOPT_RETURNTRANSFER=>true,CURLOPT_FOLLOWLOCATION=>false,CURLOPT_TIMEOUT=>10,CURLOPT_HEADER=>true]);
    if ($post !== null) {
        $multipart = (bool)array_filter($post, fn($value) => $value instanceof CURLFile);
        curl_setopt_array($client,[CURLOPT_POST=>true,CURLOPT_POSTFIELDS=>$multipart ? $post : http_build_query($post)]);
    }
    else curl_setopt($client,CURLOPT_HTTPGET,true);
    $response = curl_exec($client);
    if ($response === false) throw new RuntimeException(curl_error($client));
    $header = substr($response,0,curl_getinfo($client,CURLINFO_HEADER_SIZE));
    $body = substr($response,curl_getinfo($client,CURLINFO_HEADER_SIZE));
    preg_match('/^Location:\s*(.+)$/mi',$header,$match);
    check(!preg_match('/(?:Fatal error:|Warning:|Notice:)/',$body), 'No PHP errors in ' . $url);
    return ['status'=>curl_getinfo($client,CURLINFO_HTTP_CODE),'body'=>$body,'location'=>trim($match[1]??'')];
}
function newBrowser() { $client=curl_init(); curl_setopt($client,CURLOPT_COOKIEFILE,''); return $client; }
try {
    $migration = proc_open([PHP_BINARY,__DIR__.'/migrate.php'],[0=>['pipe','r'],1=>['pipe','w'],2=>['pipe','w']],$pipes);
    fclose($pipes[0]); $output=stream_get_contents($pipes[1]); $error=stream_get_contents($pipes[2]); fclose($pipes[1]); fclose($pipes[2]);
    check(proc_close($migration)===0,'Migration: '.$error);
    require dirname(__DIR__) . '/app/bootstrap.php';
    session_write_close();
    $pdo=appDatabase();
    check(count(catalogProducts())===6,'Catalog seeded');
    check(count(deliveryStates())===8,'Delivery states seeded');
    $password=bin2hex(random_bytes(12));
    $hash=password_hash($password,PASSWORD_DEFAULT);
    $insert=$pdo->prepare('INSERT INTO customers(name,company,email,password_hash) VALUES (?,?,?,?)');
    $insert->execute(['Test Customer','Test Business','customer@example.test',$hash]); $customer=(int)$pdo->lastInsertId();
    $insert->execute(['Other Customer','Other Business','other@example.test',$hash]); $other=(int)$pdo->lastInsertId();
    $pdo->prepare('INSERT INTO admins(name,company,email,password_hash) VALUES (?,?,?,?)')->execute(['Test Admin','Test Business','admin@example.test',$hash]); $admin=(int)$pdo->lastInsertId();
    $input=['first_name'=>'Test','last_name'=>'Customer','email'=>'customer@example.test','street'=>'123 Test Street','city'=>'Houston','state'=>'TX','zip'=>'77002'];
    $pdo->exec('UPDATE products SET inventory=10 WHERE id=6');
    $token=bin2hex(random_bytes(32));
    $number=createCustomerOrder($customer,[6=>2],$input,$token);
    $order=findOrder($number,$customer);
    check($order!==null && count($order['items'])===1,'Order with item persisted');
    check($order['subtotal']==='999.98' && $order['tax']==='75.00' && $order['total']==='1074.98','Server-calculated totals');
    check((int)$pdo->query('SELECT inventory FROM products WHERE id=6')->fetchColumn()===8,'Inventory reserved');
    check(findOrder($number,$other)===null,'Other customer cannot read order');
    check(createCustomerOrder($customer,[],$input,$token)===$number && count(customerOrders($customer))===1,'Retry does not duplicate order');
    rejects(fn()=>createCustomerOrder($customer,[6=>1],$input,bin2hex(random_bytes(32))),'Delivery minimum enforced');
    rejects(fn()=>createCustomerOrder($customer,[6=>9],$input,bin2hex(random_bytes(32))),'Stock limit enforced');
    rejects(fn()=>createCustomerOrder($customer,[6=>2],array_replace($input,['zip'=>'99999']),bin2hex(random_bytes(32))),'ZIP restriction enforced');
    rejects(fn()=>createCustomerOrder($customer,[6=>2],array_replace($input,['email'=>'invalid']),bin2hex(random_bytes(32))),'Email validation');
    rejects(fn()=>createCustomerOrder($customer,[6=>-1],$input,bin2hex(random_bytes(32))),'Negative quantity rejected');
    check(count(allOrders())===1 && (int)$pdo->query('SELECT inventory FROM products WHERE id=6')->fetchColumn()===8,'Failed orders roll back');
    updateCatalogProduct(6,['price'=>'550.00','inventory'=>'8','min_order'=>'1','status'=>'in-stock','warehouse'=>'Test warehouse']);
    check(findOrder($number,$customer)['items'][0]['unit_price']==='499.99','Historical order price stays fixed');
    updateOrderByAdmin($number,$admin,['action'=>'quote','expected_status'=>'Pending freight quote','carrier'=>'Test Carrier','service'=>'Dock delivery','rate'=>'125.50']);
    $order=findOrder($number,$customer);
    check($order['status']==='Awaiting payment' && $order['total']==='1200.48' && count($order['quotes'])===1,'Freight updates total and customer status');
    rejects(fn()=>updateOrderByAdmin($number,$admin,['action'=>'quote','expected_status'=>'Pending freight quote','carrier'=>'Duplicate','service'=>'Dock','rate'=>'1']),'Stale quote rejected');
    rejects(fn()=>updateOrderByAdmin($number,$admin,['action'=>'status','expected_status'=>'Awaiting payment','status'=>'Delivered']),'Status cannot skip payment');
    rejects(fn()=>updateOrderByAdmin($number,$admin,['action'=>'status','expected_status'=>'Awaiting payment','status'=>'Ready for shipment']),'Payment confirmation required');
    updateOrderByAdmin($number,$admin,['action'=>'status','expected_status'=>'Awaiting payment','status'=>'Ready for shipment','payment_confirmed'=>'1']);
    updateOrderByAdmin($number,$admin,['action'=>'status','expected_status'=>'Ready for shipment','status'=>'Shipped']);
    updateOrderByAdmin($number,$admin,['action'=>'status','expected_status'=>'Shipped','status'=>'Delivered']);
    check(count(findOrder($number,$customer)['events'])===5,'Status history persisted');
    $cancel=createCustomerOrder($customer,[6=>2],$input,bin2hex(random_bytes(32)));
    updateOrderByAdmin($cancel,$admin,['action'=>'status','expected_status'=>'Pending freight quote','status'=>'Cancelled']);
    check((int)$pdo->query('SELECT inventory FROM products WHERE id=6')->fetchColumn()===8,'Cancellation restores stock');
    rejects(fn()=>updateOrderByAdmin($cancel,$admin,['action'=>'status','expected_status'=>'Cancelled','status'=>'Cancelled']),'Repeated cancellation rejected');
    updateDeliveryState('TX',['zone'=>'Test zone','min_order'=>'3','enabled'=>'1','restrict_zips'=>'1','zips'=>'77002, 75201']);
    rejects(fn()=>createCustomerOrder($customer,[6=>2],$input,bin2hex(random_bytes(32))),'Updated delivery minimum used');
    updateDeliveryState('TX',['zone'=>'South','min_order'=>'2','enabled'=>'0','restrict_zips'=>'1','zips'=>'77002']);
    rejects(fn()=>createCustomerOrder($customer,[6=>2],$input,bin2hex(random_bytes(32))),'Disabled state rejected');
    updateDeliveryState('TX',['zone'=>'South','min_order'=>'2','enabled'=>'1','restrict_zips'=>'1','zips'=>'77002']);
    rejects(fn()=>updateDeliveryState('TX',['zone'=>'South','min_order'=>'2','enabled'=>'1','restrict_zips'=>'1','zips'=>'bad']),'Malformed ZIP rule rejected');
    $_SESSION['csrf_token']='test-token'; $_POST=['csrf_token'=>'wrong']; rejects(fn()=>verifyCsrf(),'CSRF rejection');
    // Simulate the previous customer schema to exercise the existing-store upgrade.
    $pdo->exec('ALTER TABLE customers DROP COLUMN state, DROP COLUMN zip');
    $migration=proc_open([PHP_BINARY,__DIR__.'/migrate.php'],[0=>['pipe','r'],1=>['pipe','w'],2=>['pipe','w']],$pipes);
    fclose($pipes[0]); stream_get_contents($pipes[1]); stream_get_contents($pipes[2]); fclose($pipes[1]); fclose($pipes[2]);
    check(proc_close($migration)===0,'Migration can be repeated');
    $legacyLocation=$pdo->query('SELECT state,zip FROM customers ORDER BY id LIMIT 1')->fetch();
    check($legacyLocation===['state'=>null,'zip'=>null],'Upgrade preserves old accounts with unknown delivery location');
    check((string)$pdo->query('SELECT price_per_pallet FROM products WHERE id=6')->fetchColumn()==='550.00','Repeat migration preserves product edits');
    check((int)$pdo->query('SELECT COUNT(*) FROM customers')->fetchColumn()===2 && count(allOrders())===2,'Repeat migration preserves accounts and orders');
    $socket=stream_socket_server('tcp://127.0.0.1:0',$errno,$errstr); if (!$socket) throw new RuntimeException($errstr);
    $address=stream_socket_get_name($socket,false); fclose($socket);
    $base='http://'.$address;
    $server=proc_open([PHP_BINARY,'-d','session.save_path='.$runtime,'-S',$address,'-t',APP_ROOT,__DIR__.'/test-router.php'],[0=>['pipe','r'],1=>['file',$runtime.'/server.log','a'],2=>['file',$runtime.'/server.log','a']],$serverPipes);
    fclose($serverPipes[0]);
    for($i=0;$i<50;$i++){ $ready=@fsockopen('127.0.0.1',(int)substr(strrchr($address,':'),1),$errno,$errstr,.1); if($ready){fclose($ready);break;} usleep(100000); }
    $guest=newBrowser(); $customerBrowser=newBrowser(); $otherBrowser=newBrowser(); $adminBrowser=newBrowser();
    foreach(['account.php','dashboard.php','order-status.php?order='.$number,'freight-quote.php?order='.$number,'admin-index.php','admin-orders.php','admin-order.php?order='.$number] as $path){$response=browserRequest($guest,$base.'/watah/'.$path);check($response['status']===302,'Unauthenticated guard: '.$path);}
    $response=browserRequest($guest,$base.'/watah/admin-login.php');
    check($response['status']===302 && $response['location']==='/watah/login.php','Old admin login forwards to unified login');
    $response=browserRequest($guest,$base.'/watah/admin-login.php',['email'=>'admin@example.test','password'=>$password]);
    check($response['status']===307 && $response['location']==='/watah/login.php','Old login preserves POST when redirecting');
    $response=browserRequest($guest,$base.'/watah/login.php');
    check(str_contains($response['body'],'customer or administrator account') && !str_contains($response['body'],'aria-label="Account type"'),'One login form for both roles');
    check(!str_contains($response['body'],'admin-register.php'),'Public login has no admin registration link');
    $loginToken=hiddenValue($response['body'],'csrf_token');
    foreach (['customer@example.test','admin@example.test'] as $email) {
        $response=browserRequest($guest,$base.'/watah/login.php',['email'=>$email,'password'=>$password,'csrf_token'=>'wrong']);
        check($response['status']===200 && str_contains($response['body'],'session expired'),'Login rejects invalid CSRF for '.$email);
        $response=browserRequest($guest,$base.'/watah/login.php',['email'=>$email,'password'=>'wrong-password','csrf_token'=>$loginToken]);
        check($response['status']===200 && str_contains($response['body'],'password is incorrect'),'Login rejects wrong password for '.$email);
    }
    $sharedAdminPassword=bin2hex(random_bytes(12));
    $adminInsert=$pdo->prepare('INSERT INTO admins(name,company,email,password_hash) VALUES (?,?,?,?)');
    $adminInsert->execute(['Shared Email Admin','Test','customer@example.test',password_hash($sharedAdminPassword,PASSWORD_DEFAULT)]);
    $adminInsert->execute(['Username Admin','Test','admin',$hash]);
    foreach([[$customerBrowser,'customer@example.test','account.php'],[$otherBrowser,'other@example.test','account.php'],[$adminBrowser,'admin@example.test','admin-index.php']] as [$browser,$email,$destination]){
        $response=browserRequest($browser,$base.'/watah/login.php');
        $response=browserRequest($browser,$base.'/watah/login.php',['email'=>strtoupper($email),'password'=>$password,'csrf_token'=>hiddenValue($response['body'],'csrf_token')]);
        check($response['status']===302 && $response['location']==='/watah/'.$destination,'Unified sign-in routes '.$email);
        $response=browserRequest($browser,$base.'/watah/login.php');
        check($response['status']===302 && $response['location']==='/watah/'.$destination,'Signed-in visit routes '.$email);
    }
    foreach ([['admin',$password],['customer@example.test',$sharedAdminPassword]] as [$email,$loginPassword]) {
        $browser=newBrowser();
        $response=browserRequest($browser,$base.'/watah/login.php');
        $response=browserRequest($browser,$base.'/watah/login.php',['email'=>$email,'password'=>$loginPassword,'csrf_token'=>hiddenValue($response['body'],'csrf_token')]);
        check($response['status']===302 && $response['location']==='/watah/admin-index.php','Admin username and shared-email password supported: '.$email);
        curl_close($browser);
    }
    $checkoutBrowser=newBrowser();
    $response=browserRequest($checkoutBrowser,$base.'/watah/checkout.php');
    check($response['status']===302 && $response['location']==='/watah/login.php','Checkout requires unified login');
    $response=browserRequest($checkoutBrowser,$base.'/watah/login.php');
    $response=browserRequest($checkoutBrowser,$base.'/watah/login.php',['email'=>'customer@example.test','password'=>$password,'csrf_token'=>hiddenValue($response['body'],'csrf_token')]);
    check($response['status']===302 && $response['location']==='/watah/checkout.php','Customer returns to checkout after unified login');
    curl_close($checkoutBrowser);
    $adminCount=(int)$pdo->query('SELECT COUNT(*) FROM admins')->fetchColumn();
    $newAdmin=['name'=>'New Administrator','company'=>'Test Business','email'=>'new-admin@example.test','password'=>$password,'password_confirmation'=>$password,'terms'=>'1'];
    foreach ([$guest,$customerBrowser] as $browser) {
        $response=browserRequest($browser,$base.'/watah/admin-register.php');
        check($response['status']===302 && $response['location']==='/watah/login.php','Non-admin cannot open admin registration');
        $response=browserRequest($browser,$base.'/watah/admin-register.php',$newAdmin);
        check($response['status']===302 && (int)$pdo->query('SELECT COUNT(*) FROM admins')->fetchColumn()===$adminCount,'Non-admin cannot create an admin');
    }
    $response=browserRequest($adminBrowser,$base.'/watah/admin-index.php');
    check(str_contains($response['body'],'admin-register.php'),'Dashboard provides Add administrator');
    $response=browserRequest($adminBrowser,$base.'/watah/admin-register.php');
    check($response['status']===200 && str_contains($response['body'],'aria-label="Administration"'),'Admin creation uses dashboard navigation');
    $newAdmin['csrf_token']=hiddenValue($response['body'],'csrf_token');
    $response=browserRequest($adminBrowser,$base.'/watah/admin-register.php',array_replace($newAdmin,['csrf_token'=>'wrong']));
    check(str_contains($response['body'],'session expired') && (int)$pdo->query('SELECT COUNT(*) FROM admins')->fetchColumn()===$adminCount,'Admin creation requires CSRF');
    $response=browserRequest($adminBrowser,$base.'/watah/admin-register.php',$newAdmin);
    check($response['status']===302 && $response['location']==='/watah/admin-index.php' && (int)$pdo->query('SELECT COUNT(*) FROM admins')->fetchColumn()===$adminCount+1,'Admin creates account and returns to dashboard');
    $response=browserRequest($adminBrowser,$base.'/watah/admin-index.php');
    check(str_contains($response['body'],'Administrator account created.') && str_contains($response['body'],'Test Admin'),'Success message preserves creating admin session');
    $newAdminBrowser=newBrowser();
    $response=browserRequest($newAdminBrowser,$base.'/watah/login.php');
    $response=browserRequest($newAdminBrowser,$base.'/watah/login.php',['email'=>$newAdmin['email'],'password'=>$password,'csrf_token'=>hiddenValue($response['body'],'csrf_token')]);
    check($response['status']===302 && $response['location']==='/watah/admin-index.php','New admin can sign in');
    $response=browserRequest($newAdminBrowser,$base.'/watah/admin-register.php');
    $newAdmin['csrf_token']=hiddenValue($response['body'],'csrf_token');
    $pdo->prepare('DELETE FROM admins WHERE email=?')->execute([$newAdmin['email']]);
    $response=browserRequest($newAdminBrowser,$base.'/watah/admin-register.php',array_replace($newAdmin,['email'=>'revoked-admin@example.test']));
    check($response['status']===302 && $response['location']==='/watah/login.php' && (int)$pdo->query('SELECT COUNT(*) FROM admins')->fetchColumn()===$adminCount,'Deleted admin session cannot create accounts');
    curl_close($newAdminBrowser);
    $registrationBrowser=newBrowser();
    $response=browserRequest($registrationBrowser,$base.'/watah/register.php');
    check(str_contains($response['body'],'name="state"') && str_contains($response['body'],'name="zip"') && str_contains($response['body'],'value="NY"'),'Registration includes state and ZIP, including unserved states');
    $registration=['name'=>'Delivery Customer','company'=>'Test Business','email'=>'delivery@example.test','password'=>$password,'password_confirmation'=>$password,'terms'=>'1','state'=>'TX','zip'=>'77002','csrf_token'=>hiddenValue($response['body'],'csrf_token')];
    $customerCount=(int)$pdo->query('SELECT COUNT(*) FROM customers')->fetchColumn();
    foreach ([['zip'=>''],['zip'=>'1234'],['zip'=>'123456'],['zip'=>'abcde'],['zip'=>['77002']],['state'=>''],['state'=>'ZZ'],['csrf_token'=>'wrong']] as $invalid) {
        $response=browserRequest($registrationBrowser,$base.'/watah/register.php',array_replace($registration,$invalid));
        check($response['status']===200 && str_contains($response['body'],'role="alert"') && (int)$pdo->query('SELECT COUNT(*) FROM customers')->fetchColumn()===$customerCount,'Registration rejects invalid location or CSRF');
    }
    $deliveryBrowsers=[];
    foreach ([['TX','77002',true],['TX','99999',false],['NY','10001',false],['MA','02108',false]] as [$state,$zip,$available]) {
        $browser=newBrowser();
        $response=browserRequest($browser,$base.'/watah/register.php');
        $details=array_replace($registration,['email'=>'delivery-'.$zip.'@example.test','state'=>strtolower($state),'zip'=>$zip,'csrf_token'=>hiddenValue($response['body'],'csrf_token')]);
        $response=browserRequest($browser,$base.'/watah/register.php',$details);
        check($response['status']===302 && $response['location']==='/watah/login.php','Customers can register inside or outside service area: '.$zip);
        $locationQuery=$pdo->prepare('SELECT state,zip FROM customers WHERE email=?');
        $locationQuery->execute([$details['email']]);
        check($locationQuery->fetch()===['state'=>$state,'zip'=>$zip],'Location saved, including leading zero: '.$zip);
        $response=browserRequest($browser,$base.'/watah/login.php');
        $response=browserRequest($browser,$base.'/watah/login.php',['email'=>$details['email'],'password'=>$password,'csrf_token'=>hiddenValue($response['body'],'csrf_token')]);
        check($response['status']===302 && $response['location']==='/watah/account.php','Registered customer can sign in: '.$zip);
        $response=browserRequest($browser,$base.'/watah/account.php');
        $message=($available ? 'Delivery is available' : 'Delivery is not currently available').' for '.$state.' '.$zip;
        check(str_contains($response['body'],$message),'Dashboard shows actual delivery eligibility: '.$zip);
        $response=browserRequest($browser,$base.'/watah/checkout.php');
        check(str_contains($response['body'],$message),'Checkout also shows saved delivery eligibility: '.$zip);
        $deliveryBrowsers[$zip]=$browser;
    }
    browserRequest($deliveryBrowsers['77002'],$base.'/watah/shop.php',['add_to_cart'=>'1','product_id'=>'6','quantity'=>'2']);
    $response=browserRequest($deliveryBrowsers['77002'],$base.'/watah/checkout.php');
    check(str_contains($response['body'],'value="TX" selected') && str_contains($response['body'],'value="77002"'),'Checkout prefills saved state and ZIP');
    updateDeliveryState('TX',['zone'=>'South','min_order'=>'3','enabled'=>'0','restrict_zips'=>'1','zips'=>'77002']);
    $response=browserRequest($deliveryBrowsers['77002'],$base.'/watah/account.php');
    check(str_contains($response['body'],'Delivery is not currently available for TX 77002'),'Dashboard reflects disabled delivery state immediately');
    updateDeliveryState('TX',['zone'=>'South','min_order'=>'3','enabled'=>'1','restrict_zips'=>'0','zips'=>'']);
    $response=browserRequest($deliveryBrowsers['99999'],$base.'/watah/account.php');
    check(str_contains($response['body'],'Delivery is available for TX 99999') && str_contains($response['body'],'Minimum order: 3 pallets'),'Dashboard honors unrestricted state and updated minimum');
    updateDeliveryState('TX',['zone'=>'South','min_order'=>'2','enabled'=>'1','restrict_zips'=>'1','zips'=>'77002']);
    foreach ($deliveryBrowsers as $browser) curl_close($browser);
    curl_close($registrationBrowser);
    $response=browserRequest($adminBrowser,$base.'/watah/admin-register.php');
    check(!str_contains($response['body'],'name="zip"') && !str_contains($response['body'],'name="state"'),'Admin creation does not require a delivery location');
    $response=browserRequest($customerBrowser,$base.'/watah/account.php'); check(str_contains($response['body'],$number) && str_contains($response['body'],'Delivered'),'Customer dashboard shows real order');
    check(str_contains($response['body'],'No delivery ZIP code is saved'),'Legacy accounts show unknown location instead of false availability');
    $response=browserRequest($otherBrowser,$base.'/watah/order-status.php?order='.$number); check($response['status']===404 && !str_contains($response['body'],'123 Test Street'),'HTTP ownership enforced');
    $response=browserRequest($customerBrowser,$base.'/watah/admin-order.php?order='.$number); check($response['status']===302,'Customer cannot access admin controls');
    foreach(['admin-index.php','admin-products.php?edit=6','admin-states.php?edit=TX','admin-orders.php','admin-order.php?order='.$number] as $path){$response=browserRequest($adminBrowser,$base.'/watah/'.$path);check($response['status']===200,'Admin database page renders: '.$path);}
    $response=browserRequest($adminBrowser,$base.'/watah/admin-orders.php?export=csv'); check(str_contains($response['body'],$number) && !str_contains($response['body'],'Sample'),'Real order CSV export');
    $response=browserRequest($adminBrowser,$base.'/watah/admin-products.php?edit=6'); $csrf=hiddenValue($response['body'],'csrf_token');
    $response=browserRequest($adminBrowser,$base.'/watah/admin-products.php?edit=6',['csrf_token'=>'wrong','product_id'=>'6','price'=>'1.00','inventory'=>'8','min_order'=>'1','status'=>'in-stock','warehouse'=>'Test']);
    check(str_contains($response['body'],'session expired') && $pdo->query('SELECT price_per_pallet FROM products WHERE id=6')->fetchColumn()==='550.00','Admin POST rejects CSRF');
    $response=browserRequest($adminBrowser,$base.'/watah/admin-products.php?edit=6',['csrf_token'=>$csrf,'product_id'=>'6','price'=>'560.00','inventory'=>'8','min_order'=>'1','status'=>'in-stock','warehouse'=>'Test warehouse']);
    check($response['status']===302,'Admin product update persisted');
    $response=browserRequest($customerBrowser,$base.'/watah/shop.php'); check(str_contains($response['body'],'$560.00'),'Updated price visible in shop');
    $newProduct = ['action'=>'create','name'=>'Test Spring Water','sku'=>'TEST-SPRING-24','category'=>'Spring Water','price'=>'345.67','inventory'=>'12','min_order'=>'1','status'=>'in-stock','warehouse'=>'Test warehouse','bottle_size'=>'500 ml','bottles_per_case'=>'24','cases_per_pallet'=>'30','pallet_type'=>'Half pallet','image'=>'https://example.com/water.jpg','description'=>'Fresh spring water for testing.','features'=>"Recyclable bottles\nSpring water"];
    $beforeCount = count(catalogProducts());
    foreach ([$guest,$customerBrowser] as $browser) {
        $response=browserRequest($browser,$base.'/watah/admin-products.php?action=create');
        check($response['status']===302,'Only admins can open product creation');
        $response=browserRequest($browser,$base.'/watah/admin-products.php',$newProduct);
        check($response['status']===302 && count(catalogProducts())===$beforeCount,'Only admins can create products');
    }
    $response=browserRequest($adminBrowser,$base.'/watah/admin-products.php?action=create');
    check(str_contains($response['body'],'Add a new product') && str_contains($response['body'],'name="description"'),'Creation form renders');
    $newProduct['csrf_token']=hiddenValue($response['body'],'csrf_token');
    $response=browserRequest($adminBrowser,$base.'/watah/admin-products.php',array_replace($newProduct,['csrf_token'=>'wrong']));
    check(str_contains($response['body'],'session expired') && count(catalogProducts())===$beforeCount,'Creation requires valid CSRF');
    foreach (['name'=>'','sku'=>'invalid sku','price'=>'-5','inventory'=>'1.5','min_order'=>'0','bottles_per_case'=>'0','cases_per_pallet'=>'10000','pallet_type'=>'Other','status'=>'Other','description'=>'','image'=>'javascript:alert(1)'] as $field=>$invalid) {
        $response=browserRequest($adminBrowser,$base.'/watah/admin-products.php',array_replace($newProduct,[$field=>$invalid]));
        check(str_contains($response['body'],'role="alert"') && str_contains($response['body'],'value="TEST-SPRING-24"') === ($field !== 'sku') && count(catalogProducts())===$beforeCount,'Invalid creation rejected and form retained: '.$field);
    }
    $response=browserRequest($adminBrowser,$base.'/watah/admin-products.php',$newProduct);
    check($response['status']===302 && str_contains($response['location'],'created=1'),'Admin creates product and redirects');
    parse_str(parse_url($response['location'],PHP_URL_QUERY),$createdParams); $createdId=(int)$createdParams['edit'];
    $created=current(array_filter(catalogProducts(),fn($product)=>$product['id']===$createdId));
    check(count(catalogProducts())===$beforeCount+1 && $created['total_bottles_per_pallet']===720 && $created['features']===['Recyclable bottles','Spring water'] && $created['image']==='https://example.com/water.jpg','Product details, totals, features, and URL persist');
    $response=browserRequest($adminBrowser,$base.$response['location']);
    check(str_contains($response['body'],'Product added.') && str_contains($response['body'],'Edit Test Spring Water'),'Creation success links to editable product');
    $response=browserRequest($guest,$base.'/watah/shop.php');
    check(str_contains($response['body'],'Test Spring Water') && str_contains($response['body'],'$345.67'),'New product visible in shop');
    $response=browserRequest($guest,$base.'/watah/product.php?id='.$createdId);
    check($response['status']===200 && str_contains($response['body'],'Fresh spring water for testing.') && str_contains($response['body'],'720'),'New product detail renders');
    $response=browserRequest($adminBrowser,$base.'/watah/admin-products.php',$newProduct);
    check(str_contains($response['body'],'SKU already exists') && count(catalogProducts())===$beforeCount+1,'Duplicate submission cannot create duplicate SKU');
    $localProduct=array_replace($newProduct,['sku'=>'TEST-LOCAL','image'=>'images/products/1.jpg','inventory'=>'','status'=>'out-of-stock']);
    $localId=createCatalogProduct($localProduct);
    $local=current(array_filter(catalogProducts(),fn($product)=>$product['id']===$localId));
    check($local['inventory']===null && $local['image']===assetUrl('images/products/1.jpg') && !productCanOrder($local),'Local image and unconfirmed stock supported; unavailable product cannot be ordered');
    $response=browserRequest($adminBrowser,$base.'/watah/admin-products.php?action=create');
    check(str_contains($response['body'],'enctype="multipart/form-data"') && str_contains($response['body'],'name="product_image" type="file"'),'Product form offers computer file picker');
    $uploadDirectory=APP_ROOT.'/assets/images/products/uploads/';
    $filesBefore=glob($uploadDirectory.'*') ?: [];
    $uploadProduct=array_replace($newProduct,['sku'=>'TEST-UPLOAD','image'=>'','product_image'=>new CURLFile(APP_ROOT.'/assets/images/products/1.jpg','application/octet-stream','photo.php.jpg')]);
    foreach ([$guest,$customerBrowser] as $browser) {
        $response=browserRequest($browser,$base.'/watah/admin-products.php?action=create',$uploadProduct);
        check($response['status']===302 && (glob($uploadDirectory.'*') ?: [])===$filesBefore,'Unauthorized upload stores no files');
    }
    $response=browserRequest($adminBrowser,$base.'/watah/admin-products.php?action=create',array_replace($uploadProduct,['csrf_token'=>'wrong']));
    check(str_contains($response['body'],'session expired') && (glob($uploadDirectory.'*') ?: [])===$filesBefore,'Upload requires valid CSRF token');
    $response=browserRequest($adminBrowser,$base.'/watah/admin-products.php?action=create',array_replace($uploadProduct,['name'=>'']));
    check(str_contains($response['body'],'role="alert"') && str_contains($response['body'],'Please choose your file again') && (glob($uploadDirectory.'*') ?: [])===$filesBefore,'Invalid product leaves no uploaded file and explains re-selection');
    file_put_contents($runtime.'/fake.jpg','<?php echo "Not an image";');
    $response=browserRequest($adminBrowser,$base.'/watah/admin-products.php?action=create',array_replace($uploadProduct,['product_image'=>new CURLFile($runtime.'/fake.jpg','image/jpeg','fake.jpg')]));
    check(str_contains($response['body'],'Choose a valid JPG') && (glob($uploadDirectory.'*') ?: [])===$filesBefore,'Forged image extension and MIME type rejected');
    file_put_contents($runtime.'/large.jpg',str_repeat('x',2*1024*1024+1));
    $response=browserRequest($adminBrowser,$base.'/watah/admin-products.php?action=create',array_replace($uploadProduct,['product_image'=>new CURLFile($runtime.'/large.jpg','image/jpeg','large.jpg')]));
    check(str_contains($response['body'],'2 MB') && str_contains($response['body'],'role="alert"') && (glob($uploadDirectory.'*') ?: [])===$filesBefore,'Oversized upload rejected');
    $response=browserRequest($adminBrowser,$base.'/watah/admin-products.php?action=create',$uploadProduct);
    $uploadedTestFiles=array_values(array_diff(glob($uploadDirectory.'*') ?: [],$filesBefore));
    check($response['status']===302 && count($uploadedTestFiles)===1,'Multipart image upload creates product and one image file');
    parse_str(parse_url($response['location'],PHP_URL_QUERY),$uploadParams); $uploadId=(int)$uploadParams['edit'];
    $imageQuery=$pdo->prepare('SELECT image FROM products WHERE id=?'); $imageQuery->execute([$uploadId]); $uploadedPath=$imageQuery->fetchColumn();
    check((bool)preg_match('~^images/products/uploads/[a-f0-9]{32}\.jpg$~',$uploadedPath) && hash_file('sha256',APP_ROOT.'/assets/'.$uploadedPath)===hash_file('sha256',APP_ROOT.'/assets/images/products/1.jpg'),'Image stored with generated filename and verified extension');
    $response=browserRequest($guest,$base.'/watah/product.php?id='.$uploadId);
    check(str_contains($response['body'],assetUrl($uploadedPath)),'Uploaded image appears on product detail page');
    $response=browserRequest($adminBrowser,$base.'/watah/admin-products.php?action=create',$uploadProduct);
    check(str_contains($response['body'],'SKU already exists') && count(glob($uploadDirectory.'*') ?: [])===count($filesBefore)+1,'Duplicate product cleans up its unused upload');
    browserRequest($customerBrowser,$base.'/watah/shop.php',['add_to_cart'=>'1','product_id'=>'6','quantity'=>'2']);
    $response=browserRequest($customerBrowser,$base.'/watah/checkout.php'); $csrf=hiddenValue($response['body'],'csrf_token'); $checkoutToken=hiddenValue($response['body'],'checkout_token');
    $post=array_merge($input,['csrf_token'=>$csrf,'checkout_token'=>$checkoutToken]);
    $response=browserRequest($customerBrowser,$base.'/watah/checkout.php',$post);
    check($response['status']===302 && str_contains($response['location'],'order-status.php?order='),'HTTP checkout saves order');
    parse_str(parse_url($response['location'],PHP_URL_QUERY),$params); $httpOrder=$params['order'];
    $response=browserRequest($customerBrowser,$base.'/watah/checkout.php',$post); check($response['status']===302 && str_contains($response['location'],$httpOrder),'HTTP double-submit is idempotent');
    $response=browserRequest($customerBrowser,$base.'/watah/cart.php'); check(str_contains($response['body'],'An empty cart'),'Cart cleared after saved order');
    $response=browserRequest($adminBrowser,$base.'/watah/admin-orders.php'); check(str_contains($response['body'],$httpOrder),'Customer checkout visible to admin');
    $response=browserRequest($adminBrowser,$base.'/watah/admin-order.php?order='.$httpOrder); $csrf=hiddenValue($response['body'],'csrf_token');
    $response=browserRequest($adminBrowser,$base.'/watah/admin-order.php?order='.$httpOrder,['csrf_token'=>$csrf,'action'=>'quote','expected_status'=>'Pending freight quote','carrier'=>'HTTP Carrier','service'=>'Liftgate','rate'=>'200.00']);
    check($response['status']===302,'HTTP freight update');
    $response=browserRequest($customerBrowser,$base.'/watah/freight-quote.php?order='.$httpOrder); check(str_contains($response['body'],'HTTP Carrier') && str_contains($response['body'],'Awaiting payment'),'Freight update visible to customer');
    $response=browserRequest($adminBrowser,$base.'/watah/admin-states.php?edit=TX'); $csrf=hiddenValue($response['body'],'csrf_token');
    $response=browserRequest($adminBrowser,$base.'/watah/admin-states.php?edit=TX',['csrf_token'=>$csrf,'code'=>'TX','zone'=>'South','min_order'=>'4','enabled'=>'1','restrict_zips'=>'1','zips'=>'77002']);
    check($response['status']===302,'HTTP delivery update');
    $response=browserRequest($guest,$base.'/watah/service-area.php?state=TX&zip=77002'); check(str_contains($response['body'],'4 pallets'),'Delivery checker uses saved rules');
    updateCatalogProduct(6,['price'=>'560.00','inventory'=>'0','min_order'=>'1','status'=>'in-stock','warehouse'=>'Test warehouse']);
    $response=browserRequest($guest,$base.'/watah/product.php?id=6'); check(str_contains($response['body'],'disabled') && str_contains($response['body'],'Currently unavailable'),'Zero stock disables product ordering');
    $response=browserRequest($guest,$base.'/watah/shop.php'); check(str_contains($response['body'],'disabled') && str_contains($response['body'],'Unavailable'),'Zero stock reflected in catalog');
    $response=browserRequest($guest,$base.'/watah/shop.php',['add_to_cart'=>'1','product_id'=>'6','quantity'=>'2']);
    $response=browserRequest($guest,$base.'/watah/cart.php'); check(str_contains($response['body'],'An empty cart'),'Unavailable product cannot be added');
    updateCatalogProduct(6,['price'=>'560.00','inventory'=>'10','min_order'=>'7','status'=>'in-stock','warehouse'=>'Test warehouse']);
    $response=browserRequest($guest,$base.'/watah/product.php?id=6'); check(str_contains($response['body'],'value="7"') && str_contains($response['body'],'Product minimum: 7'),'Quantity options follow product minimum');
    echo "Passed $checks database and HTTP integration checks.\n";
} finally {
    if (is_resource($server)) { proc_terminate($server); proc_close($server); }
    foreach ($uploadedTestFiles as $file) if (is_file($file)) unlink($file);
    if ($pdo && preg_match('/^watah_test_[a-f0-9]+$/',$testName)) $pdo->exec('DROP DATABASE `' . $testName . '`');
    foreach (glob($runtime.'/*') ?: [] as $file) if (is_file($file)) unlink($file);
    if (is_dir($runtime)) rmdir($runtime);
}
