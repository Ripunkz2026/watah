        <footer class="border-t border-slate-200 bg-white">
            <div class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
                <div class="grid gap-10 md:grid-cols-4">
                    <div>
                        <div class="mb-4 flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-600 text-lg font-extrabold text-white">P</div>
                            <div>
                                <div class="text-base font-bold text-slate-900">Pure Life</div>
                                <div class="text-[10px] uppercase tracking-[0.18em] text-slate-500">Wholesale</div>
                            </div>
                        </div>
                        <p class="text-sm leading-6 text-slate-600">Full-pallet bottled water delivery for businesses, hospitality groups, and institutions across approved U.S. service areas.</p>
                    </div>

                    <div>
                        <h3 class="mb-4 text-sm font-semibold uppercase tracking-[0.12em] text-slate-500">Company</h3>
                        <ul class="space-y-3 text-sm text-slate-600">
                            <li><a href="<?php echo siteUrl('shop.php'); ?>" class="hover:text-blue-700">Shop</a></li>
                            <li><a href="<?php echo siteUrl('faq.php'); ?>" class="hover:text-blue-700">FAQs</a></li>
                            <li><a href="<?php echo siteUrl('contact.php'); ?>" class="hover:text-blue-700">Contact</a></li>
                        </ul>
                    </div>

                    <div>
                        <h3 class="mb-4 text-sm font-semibold uppercase tracking-[0.12em] text-slate-500">Policies</h3>
                        <ul class="space-y-3 text-sm text-slate-600">
                            <li><a href="<?php echo siteUrl('shipping-policy.php'); ?>" class="hover:text-blue-700">Shipping & Delivery</a></li>
                            <li><a href="<?php echo siteUrl('refund-policy.php'); ?>" class="hover:text-blue-700">Refunds & Cancellations</a></li>
                            <li><a href="<?php echo siteUrl('privacy-policy.php'); ?>" class="hover:text-blue-700">Privacy Policy</a></li>
                            <li><a href="<?php echo siteUrl('terms.php'); ?>" class="hover:text-blue-700">Terms</a></li>
                        </ul>
                    </div>

                    <div>
                        <h3 class="mb-4 text-sm font-semibold uppercase tracking-[0.12em] text-slate-500">Service Area</h3>
                        <ul class="space-y-3 text-sm text-slate-600">
                            <li>Approved states</li>
                            <li>Freight quote required</li>
                            <li>Minimum order rules</li>
                            <li><a href="<?php echo siteUrl('service-area.php'); ?>" class="font-medium text-blue-700">Check eligibility</a></li>
                        </ul>
                    </div>
                </div>

                <div class="mt-10 flex flex-col items-center justify-between border-t border-slate-200 pt-8 text-sm text-slate-500 md:flex-row">
                    <p>© <?php echo date('Y'); ?> Pure Life Wholesale. All rights reserved.</p>
                    <p>Demo storefront for client presentation only.</p>
                </div>
            </div>
        </footer>

        <script src="<?php echo assetUrl('js/main.js'); ?>"></script>
    </body>
</html>
