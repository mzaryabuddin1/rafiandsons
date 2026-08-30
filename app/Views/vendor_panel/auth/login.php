<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= csrf_hash() ?>">
    <meta name="csrf-header" content="<?= config('Security')->headerName ?>">
    <meta name="csrf-name" content="<?= csrf_token() ?>">
    <title><?= esc($pageTitle ?? 'Vendor Login') ?> | Rafi &amp; Sons</title>
    <link rel="icon" type="image/png" href="<?= base_url('assets/images/favicon.png') ?>">
    <link href="<?= base_url('admintheme/css/bootstrap.min.css') ?>" rel="stylesheet">
    <link href="<?= base_url('admintheme/font-awesome/css/font-awesome.css') ?>" rel="stylesheet">
    <link href="<?= base_url('admintheme/css/plugins/toastr/toastr.min.css') ?>" rel="stylesheet">
    <link href="<?= base_url('admintheme/css/animate.css') ?>" rel="stylesheet">
    <link href="<?= base_url('admintheme/css/style.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/admin/admin-brand.css') ?>" rel="stylesheet">
</head>
<body class="gray-bg">
<div class="middle-box text-center loginscreen animated fadeInDown">
    <div>
        <div>
            <img src="<?= base_url('assets/admin/rafi-and-sons-logo.png') ?>" alt="Rafi &amp; Sons" class="admin-login-logo">
        </div>
        <h3>Vendor Panel</h3>
        <p>Sign in to view your dashboard and orders.</p>
        <form class="m-t" role="form" id="vendor-login-form">
            <div class="form-group">
                <input type="email" name="email" class="form-control" placeholder="Email" required>
            </div>
            <div class="form-group">
                <input type="password" name="password" class="form-control" placeholder="Password" required>
            </div>
            <button type="submit" class="btn btn-primary block full-width m-b" id="login-btn">Login as Vendor</button>
        </form>
        <p class="m-t">
            <a href="<?= site_url('vendor/apply') ?>">Become a Vendor</a>
            &nbsp;·&nbsp;
            <a href="<?= site_url('home') ?>">Back to Store</a>
        </p>
        <p class="m-t"><small>Separate vendor admin panel</small></p>
    </div>
</div>

<script>
    window.ADMIN_BASE = '<?= rtrim(site_url('vendor'), '/') ?>';
    window.VENDOR_BASE = '<?= rtrim(site_url('vendor'), '/') ?>';
</script>
<script src="<?= base_url('admintheme/js/jquery-3.1.1.min.js') ?>"></script>
<script src="<?= base_url('admintheme/js/popper.min.js') ?>"></script>
<script src="<?= base_url('admintheme/js/bootstrap.js') ?>"></script>
<script src="<?= base_url('admintheme/js/plugins/toastr/toastr.min.js') ?>"></script>
<script src="<?= base_url('assets/admin/admin-app.js') ?>"></script>
<script>
$('#vendor-login-form').on('submit', function (e) {
    e.preventDefault();
    var $btn = $('#login-btn');
    AdminApp.setButtonLoading($btn, true);
    AdminApp.request(VENDOR_BASE + '/login', 'POST', {
        email: $('input[name="email"]').val(),
        password: $('input[name="password"]').val()
    }).done(function (res) {
        AdminApp.toast('success', res.message);
        window.location.href = res.data.redirect;
    }).always(function () {
        AdminApp.setButtonLoading($btn, false);
    });
});
</script>
</body>
</html>
