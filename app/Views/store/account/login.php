<?= $this->extend('store/layout') ?>
<?= $this->section('content') ?>
<main class="main qb-main">
    <div class="page-content qb-account-page">
        <div class="container">
            <div class="qb-auth-card">
                <div class="qb-auth-head">
                    <h1>Sign In</h1>
                    <p>Welcome back. Sign in for faster checkout and order tracking.</p>
                </div>

                <form id="account-login-form" class="qb-form-card">
                    <?php if (! empty($redirect)): ?>
                        <input type="hidden" name="redirect" value="<?= esc($redirect) ?>">
                    <?php endif; ?>
                    <div class="form-group">
                        <label>Email or Phone *</label>
                        <input type="text" class="form-control" name="login" required placeholder="you@email.com or 03xx-xxxxxxx">
                    </div>
                    <div class="form-group">
                        <label>Password *</label>
                        <input type="password" class="form-control" name="password" required placeholder="Your password">
                    </div>
                    <div class="qb-auth-forgot">
                        <a href="<?= site_url('account/forgot-password') ?>">Forgot password?</a>
                    </div>
                    <button type="submit" class="qb-btn qb-btn-primary qb-btn-block" id="login-btn">Sign In</button>
                </form>

                <p class="qb-auth-switch">
                    Don't have an account?
                    <a href="<?= site_url('account/register' . (! empty($redirect) ? '?redirect=' . urlencode($redirect) : '')) ?>">Create one</a>
                </p>
            </div>
        </div>
    </div>
</main>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$('#account-login-form').on('submit', function (e) {
    e.preventDefault();
    var $btn = $('#login-btn');
    var text = $btn.text();
    $btn.prop('disabled', true).text('Signing in...');
    StoreApp.request(STORE_BASE + '/account/login', 'POST', $(this).serialize())
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
