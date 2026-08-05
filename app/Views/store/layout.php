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
    <link rel="stylesheet" type="text/css" href="<?= base_url('theme/vendor/fontawesome-free/css/all.min.css') ?>">
    <link rel="stylesheet" type="text/css" href="<?= base_url('theme/vendor/animate/animate.min.css') ?>">
    <link rel="stylesheet" type="text/css" href="<?= base_url('theme/vendor/magnific-popup/magnific-popup.min.css') ?>">
    <link rel="stylesheet" type="text/css" href="<?= base_url('theme/vendor/owl-carousel/owl.carousel.min.css') ?>">
    <link rel="stylesheet" type="text/css" href="<?= base_url('theme/vendor/sticky-icon/stickyicon.css') ?>">
    <link rel="stylesheet" type="text/css" href="<?= base_url('theme/css/' . ($cssFile ?? 'demo22.min.css')) ?>">
    <link rel="stylesheet" type="text/css" href="<?= base_url('assets/store/store.css') ?>">
</head>
<body class="home">
<div class="page-wrapper">
    <h1 class="d-none">Rafi &amp; Sons</h1>
    <?= $this->include('store/partials/header') ?>
    <?= $this->renderSection('content') ?>
    <?= $this->include('store/partials/footer') ?>
</div>

<script>
    window.STORE_BASE = '<?= rtrim(site_url(), '/') ?>';
    window.BASE_URL = '<?= rtrim(base_url(), '/') ?>/';
</script>
<script src="<?= base_url('theme/vendor/jquery/jquery.min.js') ?>"></script>
<script src="<?= base_url('theme/vendor/magnific-popup/jquery.magnific-popup.min.js') ?>"></script>
<script src="<?= base_url('theme/vendor/owl-carousel/owl.carousel.min.js') ?>"></script>
<script src="<?= base_url('theme/vendor/imagesloaded/imagesloaded.pkgd.min.js') ?>"></script>
<script src="<?= base_url('theme/vendor/elevatezoom/jquery.elevatezoom.min.js') ?>"></script>
<script src="<?= base_url('theme/js/main.min.js') ?>"></script>
<script src="<?= base_url('assets/store/store-app.js') ?>"></script>
<?= $this->renderSection('scripts') ?>
</body>
</html>
