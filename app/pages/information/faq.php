<?php
require_once APP_ROOT . '/app/bootstrap.php';
$isExpressPage = true;
$isCustomerPage = true;
$pageTitle = 'Frequently asked questions';
$faqs = [
    ['question' => 'Can I order full and half pallets?', 'answer' => 'Yes. Water and sports drinks are available by the full or half pallet. Each product lists its cases, bottle size, and total bottle count. Individual bottles are not sold separately.'],
    ['question' => 'How do I check delivery to my business?', 'answer' => 'Use our delivery checker with your state and 5-digit ZIP code. It shows whether your destination is in our service area and the minimum order for your location.'],
    ['question' => 'Is freight included in the product price?', 'answer' => 'Product prices do not include freight. Delivery costs depend on your destination, order quantity, and site requirements. A final freight rate is reviewed before payment is collected.'],
    ['question' => 'Can I order just one pallet?', 'answer' => 'Product quantities start at one pallet, but your destination may require a larger order. Check your delivery area before placing an order.'],
    ['question' => 'What should I prepare for a pallet delivery?', 'answer' => 'Confirm site access, unloading equipment, and receiving hours. Tell our team if you need a liftgate or have special delivery requirements so these can be considered in your freight quote.'],
    ['question' => 'Who can help me with my order?', 'answer' => 'Contact our team with your order details and delivery destination. We can help with product quantities, delivery questions, and changes to an order.'],
];
require APP_ROOT . '/app/views/layouts/header.php';
?>
<main id="main-content" class="cx-page lp-wrap">
    <nav class="cx-breadcrumb" aria-label="Breadcrumb"><a href="<?php echo siteUrl('index.php'); ?>">Home</a><span aria-hidden="true">/</span><span>Frequently asked questions</span></nav>
    <header class="cx-heading"><div><p class="lp-eyebrow">GOOD TO KNOW</p><h1>A few answers.<br><span>A smoother restock.</span></h1><p class="cx-intro">From choosing your pallet to planning your delivery. Here&rsquo;s what to know before you stock up.</p></div><a class="lp-text-link" href="<?php echo siteUrl('contact.php'); ?>">Ask our team &rarr;</a></header>
    <div class="cx-columns"><section class="lp-faq-list" aria-label="Ordering questions"><?php foreach ($faqs as $index => $faq): ?><details <?php echo $index === 0 ? 'open' : ''; ?>><summary><?php echo htmlspecialchars($faq['question']); ?><span aria-hidden="true">+</span></summary><p><?php echo htmlspecialchars($faq['answer']); ?></p></details><?php endforeach; ?></section>
    <aside class="cx-panel cx-freight-note"><p class="lp-eyebrow">START WITH YOUR DESTINATION</p><h2>Do we deliver to you?</h2><p class="cx-intro cx-note">Find your delivery availability and order minimum in a few clicks.</p><div class="cx-help-strip"><a class="lp-text-link" href="<?php echo siteUrl('service-area.php'); ?>">Check delivery availability &rarr;</a></div></aside></div>
</main>
<?php require APP_ROOT . '/app/views/layouts/footer.php'; ?>