<?php
$siteName = $settings['site_name'] ?? 'Rafi & Sons';
$phone = $settings['contact_phone'] ?? '';
$email = $settings['contact_email'] ?? '';
$address = $settings['contact_address'] ?? '';
?>
<footer class="footer">
    <div class="container">
        <div class="footer-top">
            <div class="row align-items-center">
                <div class="col-lg-3">
                    <a href="<?= site_url('home') ?>" class="logo-footer">
                        <img src="<?= base_url('assets/images/logo.png') ?>" alt="<?= esc($siteName) ?>" width="72" height="72" style="height:56px;width:auto;">
                    </a>
                </div>
                <div class="col-lg-9">
                    <div class="widget widget-newsletter form-wrapper form-wrapper-inline">
                        <div class="newsletter-info mx-auto mr-lg-2 ml-lg-4">
                            <h4 class="widget-title">Shop with Installments</h4>
                            <p>Browse products, choose a plan, and submit your booking request.</p>
                        </div>
                        <a href="<?= site_url('shop') ?>" class="btn btn-primary btn-rounded btn-md ml-2">Shop Now<i class="d-icon-arrow-right"></i></a>
                    </div>
                </div>
            </div>
        </div>
        <div class="footer-middle">
            <div class="row">
                <div class="col-lg-3 col-md-6">
                    <div class="widget widget-info">
                        <h4 class="widget-title">Contact Info</h4>
                        <ul class="widget-body">
                            <?php if ($phone): ?><li><label>Phone:</label> <a href="tel:<?= esc($phone) ?>"><?= esc($phone) ?></a></li><?php endif; ?>
                            <?php if ($email): ?><li><label>Email:</label> <a href="mailto:<?= esc($email) ?>"><?= esc($email) ?></a></li><?php endif; ?>
                            <?php if ($address): ?><li><label>Address:</label> <a href="<?= site_url('contact') ?>"><?= esc($address) ?></a></li><?php endif; ?>
                            <li><a href="<?= site_url('contact') ?>">Contact Us</a></li>
                            <li><a href="<?= site_url('faq') ?>">FAQ</a></li>
                            <li><a href="<?= site_url('track-order') ?>">Track Order</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="widget ml-lg-4">
                        <h4 class="widget-title">Information</h4>
                        <ul class="widget-body">
                            <li><a href="<?= site_url('about') ?>">About Us</a></li>
                            <li><a href="<?= site_url('installment-terms') ?>">Installment Policy</a></li>
                            <li><a href="<?= site_url('return-policy') ?>">Return and Refund Policy</a></li>
                            <li><a href="<?= site_url('delivery-policy') ?>">Delivery Policy</a></li>
                            <li><a href="<?= site_url('payment-policy') ?>">Payment Policy</a></li>
                            <li><a href="<?= site_url('terms') ?>">Terms &amp; Conditions</a></li>
                            <li><a href="<?= site_url('privacy') ?>">Privacy Policy</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="widget ml-lg-4">
                        <h4 class="widget-title">Categories</h4>
                        <ul class="widget-body">
                            <?php foreach (array_slice($categoryTree ?? $categories, 0, 8) as $cat): ?>
                                <li><a href="<?= site_url('shop?category=' . urlencode($cat['slug'])) ?>"><?= esc($cat['name']) ?></a></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="widget">
                        <h4 class="widget-title">My Account</h4>
                        <ul class="widget-body">
                            <li><a href="<?= site_url('account/login') ?>">Sign In</a></li>
                            <li><a href="<?= site_url('account/register') ?>">Create Account</a></li>
                            <li><a href="<?= site_url('cart') ?>">View Cart</a></li>
                            <li><a href="<?= site_url('checkout') ?>">Checkout</a></li>
                            <li><a href="<?= site_url('account/orders') ?>">My Orders</a></li>
                            <li><a href="<?= site_url('shop') ?>">Shop</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <div class="footer-left">
                <p class="copyright">Rafi &amp; Sons &copy; <?= date('Y') ?>. All Rights Reserved.</p>
            </div>
            <div class="footer-right">
                <figure class="payment">
                    <img src="<?= base_url('theme/images/payment.png') ?>" alt="payment" width="159" height="29">
                </figure>
            </div>
        </div>
    </div>
</footer>
<a id="scroll-top" href="#top" title="Top" role="button" class="scroll-top"><i class="d-icon-arrow-up"></i></a>
