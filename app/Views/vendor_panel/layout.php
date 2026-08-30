<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="<?= csrf_hash() ?>">
    <meta name="csrf-header" content="<?= config('Security')->headerName ?>">
    <meta name="csrf-name" content="<?= csrf_token() ?>">
    <title><?= esc($pageTitle ?? 'Vendor') ?> | Vendor Panel</title>
    <link rel="icon" type="image/png" href="<?= base_url('assets/images/favicon.png') ?>">
    <link href="<?= base_url('admintheme/css/bootstrap.min.css') ?>" rel="stylesheet">
    <link href="<?= base_url('admintheme/font-awesome/css/font-awesome.css') ?>" rel="stylesheet">
    <link href="<?= base_url('admintheme/css/plugins/toastr/toastr.min.css') ?>" rel="stylesheet">
    <link href="<?= base_url('admintheme/css/animate.css') ?>" rel="stylesheet">
    <link href="<?= base_url('admintheme/css/style.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/admin/admin-brand.css') ?>" rel="stylesheet">
    <?= $this->renderSection('styles') ?>
</head>
<body>
<div id="wrapper">
    <?= $this->include('vendor_panel/partials/sidebar') ?>
    <div id="page-wrapper" class="gray-bg dashbard-1">
        <?= $this->include('vendor_panel/partials/header') ?>
        <div class="wrapper wrapper-content">
            <?= $this->renderSection('content') ?>
        </div>
        <?= $this->include('admin/partials/footer') ?>
    </div>
</div>

<script>
    window.ADMIN_BASE = '<?= rtrim(site_url('vendor'), '/') ?>';
    window.VENDOR_BASE = '<?= rtrim(site_url('vendor'), '/') ?>';
    window.BASE_URL = '<?= rtrim(base_url(), '/') ?>/';
</script>
<script src="<?= base_url('admintheme/js/jquery-3.1.1.min.js') ?>"></script>
<script src="<?= base_url('admintheme/js/popper.min.js') ?>"></script>
<script src="<?= base_url('admintheme/js/bootstrap.js') ?>"></script>
<script src="<?= base_url('admintheme/js/plugins/metisMenu/jquery.metisMenu.js') ?>"></script>
<script src="<?= base_url('admintheme/js/plugins/slimscroll/jquery.slimscroll.min.js') ?>"></script>
<script src="<?= base_url('admintheme/js/inspinia.js') ?>"></script>
<script src="<?= base_url('admintheme/js/plugins/pace/pace.min.js') ?>"></script>
<script src="<?= base_url('admintheme/js/plugins/toastr/toastr.min.js') ?>"></script>
<script src="<?= base_url('assets/admin/admin-app.js') ?>"></script>
<script>
    $(document).on('click', '.vendor-logout-btn', function (e) {
        e.preventDefault();
        AdminApp.request(VENDOR_BASE + '/logout', 'POST').done(function (res) {
            window.location.href = (res.data && res.data.redirect) || (VENDOR_BASE + '/login');
        });
    });
</script>
<?= $this->renderSection('scripts') ?>
</body>
</html>
