<?= $this->extend('store/layout') ?>
<?= $this->section('content') ?>
<main class="main qb-main">
    <div class="page-content">
        <div class="container">
            <h1 class="qb-page-title">Vendor Login</h1>
            <p class="qb-page-sub">Sign in to view orders for your products.</p>
            <div class="row justify-content-center">
                <div class="col-md-6 col-lg-5">
                    <form id="vendor-login-form" class="qb-form-card">
                        <h3>Sign In</h3>
                        <label>Email *</label>
                        <input type="email" class="form-control" name="email" required>
                        <label>Password *</label>
                        <input type="password" class="form-control" name="password" required>
                        <button type="submit" class="qb-btn qb-btn-primary qb-btn-block mt-3" id="vendor-login-btn">Sign In</button>
                        <p class="qb-disclaimer mt-3">Want to sell with us? <a href="<?= site_url('vendor/apply') ?>">Become a Vendor</a></p>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$('#vendor-login-form').on('submit', function (e) {
    e.preventDefault();
    var $btn = $('#vendor-login-btn');
    var text = $btn.text();
    $btn.prop('disabled', true).text('Signing in...');
    StoreApp.request(STORE_BASE + '/vendor/login', 'POST', $(this).serialize())
        .done(function (res) {
            StoreApp.toast(res.message);
            window.location.href = res.data.redirect;
        })
        .always(function () {
            $btn.prop('disabled', false).text(text);
        });
});
</script>
<?= $this->endSection() ?>
