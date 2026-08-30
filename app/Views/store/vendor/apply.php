<?= $this->extend('store/layout') ?>
<?= $this->section('content') ?>
<main class="main qb-main">
    <div class="page-content">
        <div class="container">
            <h1 class="qb-page-title">Become a Vendor</h1>
            <p class="qb-page-sub">Apply to sell with Rafi &amp; Sons. After you submit, we will review your application and email you.</p>

            <div class="qb-vendor-apply-actions">
                <a href="<?= site_url('vendor/login') ?>" class="qb-btn qb-btn-outline qb-btn-vendor-login">
                    <i class="fas fa-sign-in-alt"></i> Login as Vendor
                </a>
            </div>

            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <form id="vendor-apply-form" class="qb-form-card">
                        <h3>Vendor Application</h3>
                        <div class="row">
                            <div class="col-sm-6">
                                <label>Business Name *</label>
                                <input type="text" class="form-control" name="business_name" required>
                            </div>
                            <div class="col-sm-6">
                                <label>Contact Person *</label>
                                <input type="text" class="form-control" name="contact_name" required>
                            </div>
                            <div class="col-sm-6">
                                <label>Email *</label>
                                <input type="email" class="form-control" name="email" required>
                            </div>
                            <div class="col-sm-6">
                                <label>Phone *</label>
                                <input type="text" class="form-control" name="phone" required>
                            </div>
                            <div class="col-sm-6">
                                <label>Password *</label>
                                <input type="password" class="form-control" name="password" required minlength="6">
                            </div>
                            <div class="col-sm-6">
                                <label>Confirm Password *</label>
                                <input type="password" class="form-control" name="password_confirm" required minlength="6">
                            </div>
                            <div class="col-sm-6">
                                <label>CNIC</label>
                                <input type="text" class="form-control" name="cnic" placeholder="xxxxx-xxxxxxx-x">
                            </div>
                            <div class="col-sm-6">
                                <label>City</label>
                                <input type="text" class="form-control" name="city">
                            </div>
                            <div class="col-12">
                                <label>Address</label>
                                <input type="text" class="form-control" name="address">
                            </div>
                            <div class="col-12">
                                <label>About your business</label>
                                <textarea class="form-control" name="notes" rows="3" placeholder="Tell us what you sell"></textarea>
                            </div>
                        </div>
                        <button type="submit" class="qb-btn qb-btn-primary qb-btn-block mt-3" id="vendor-apply-btn">Submit Application</button>
                        <p class="qb-disclaimer mt-3">Already approved? <a href="<?= site_url('vendor/login') ?>">Login as Vendor</a> to open your vendor panel.</p>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$('#vendor-apply-form').on('submit', function (e) {
    e.preventDefault();
    var $btn = $('#vendor-apply-btn');
    var text = $btn.text();
    $btn.prop('disabled', true).text('Submitting...');
    StoreApp.request(STORE_BASE + '/vendor/apply', 'POST', $(this).serialize())
        .done(function (res) {
            StoreApp.toast(res.message);
            $('#vendor-apply-form')[0].reset();
        })
        .always(function () {
            $btn.prop('disabled', false).text(text);
        });
});
</script>
<?= $this->endSection() ?>
