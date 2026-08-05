<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0">
    <meta name="csrf-token" content="<?= csrf_hash() ?>">
    <meta name="csrf-header" content="<?= config('Security')->headerName ?>">
    <meta name="csrf-name" content="<?= csrf_token() ?>">
    <title><?= esc($pageTitle ?? 'Rafi & Sons') ?> | Rafi &amp; Sons</title>
    <meta name="description" content="<?= esc($metaDescription ?? 'Shop quality products on easy installment plans at Rafi & Sons.') ?>">
    <link rel="icon" type="image/png" href="<?= base_url('theme/images/icons/favicon.png') ?>">
    <script>
        WebFontConfig = { google: { families: ['Poppins:400,500,600,700,800'] } };
        (function (d) {
            var wf = d.createElement('script'), s = d.scripts[0];
            wf.src = '<?= base_url('theme/js/webfont.js') ?>';
            wf.async = true;
            s.parentNode.insertBefore(wf, s);
        })(document);
    </script>
    <link rel="stylesheet" type="text/css" href="<?= base_url('assets/riode-vendor/fontawesome-free/css/all.min.css') ?>">
    <link rel="stylesheet" type="text/css" href="<?= base_url('assets/riode-vendor/animate/animate.min.css') ?>">
    <link rel="stylesheet" type="text/css" href="<?= base_url('assets/riode-vendor/magnific-popup/magnific-popup.min.css') ?>">
    <link rel="stylesheet" type="text/css" href="<?= base_url('assets/riode-vendor/owl-carousel/owl.carousel.min.css') ?>">
    <link rel="stylesheet" type="text/css" href="<?= base_url('assets/riode-vendor/sticky-icon/stickyicon.css') ?>">
    <link rel="stylesheet" type="text/css" href="<?= base_url('theme/css/' . ($cssFile ?? 'demo22.min.css')) ?>">
    <link rel="stylesheet" type="text/css" href="<?= base_url('assets/store/store.css') ?>">
    <link rel="stylesheet" type="text/css" href="<?= base_url('assets/store/qist-style.css') ?>">
</head>
<body class="<?= esc($bodyClass ?? 'store-qist') ?>">
<div class="page-wrapper">
    <h1 class="d-none">Rafi &amp; Sons</h1>
    <?= $this->include('store/partials/header') ?>
    <?= $this->renderSection('content') ?>
    <?= $this->include('store/partials/footer') ?>
</div>

<div class="qb-qv-overlay" id="qb-qv-overlay" hidden></div>
<div class="qb-qv-modal" id="qb-qv-modal" hidden role="dialog" aria-modal="true" aria-labelledby="qb-qv-title">
    <button type="button" class="qb-qv-close" id="qb-qv-close" aria-label="Close">&times;</button>
    <div class="qb-qv-grid">
        <div class="qb-qv-media">
            <img src="" alt="" id="qb-qv-image">
        </div>
            <div class="qb-qv-info">
            <h2 id="qb-qv-title"></h2>
            <div class="qb-qv-meta" id="qb-qv-meta"></div>
            <div class="qb-qv-payment-wrap" id="qb-qv-payment-wrap" hidden>
                <h3 class="qb-installment-title">Choose payment method</h3>
                <div class="qb-payment-tabs qb-qv-payment-tabs" id="qb-qv-payment-tabs"></div>
            </div>
            <div class="qb-product-price-lg qb-qv-cash-block" id="qb-qv-price"></div>
            <p class="qb-product-desc" id="qb-qv-desc"></p>
            <div class="qb-installment-block" id="qb-qv-installment" hidden>
                <h3 class="qb-installment-title">Select installment plan</h3>
                <p class="qb-installment-sub">Advance amount will be added to cart.</p>
                <div class="qb-plan-cards" id="qb-qv-plans"></div>
            </div>
            <div class="qb-buy-row" id="qb-qv-buy-row">
                <input class="qb-qty" type="number" min="1" max="10" value="1" id="qb-qv-qty">
                <button type="button" class="qb-btn qb-btn-primary" id="qb-qv-add">Add to Cart</button>
                <a href="#" class="qb-btn qb-btn-outline" id="qb-qv-details">View Details</a>
            </div>
        </div>
    </div>
</div>

<script>
    window.STORE_BASE = '<?= rtrim(site_url(), '/') ?>';
    window.BASE_URL = '<?= rtrim(base_url(), '/') ?>/';
</script>
<script src="<?= base_url('assets/riode-vendor/jquery/jquery.min.js') ?>"></script>
<script src="<?= base_url('assets/riode-vendor/magnific-popup/jquery.magnific-popup.min.js') ?>"></script>
<script src="<?= base_url('assets/riode-vendor/owl-carousel/owl.carousel.min.js') ?>"></script>
<script src="<?= base_url('assets/riode-vendor/imagesloaded/imagesloaded.pkgd.min.js') ?>"></script>
<script src="<?= base_url('assets/riode-vendor/elevatezoom/jquery.elevatezoom.min.js') ?>"></script>
<script src="<?= base_url('theme/js/main.min.js') ?>"></script>
<script src="<?= base_url('assets/store/store-app.js') ?>"></script>
<?= $this->renderSection('scripts') ?>
</body>
</html>
