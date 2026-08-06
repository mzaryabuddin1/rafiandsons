<?= $this->extend('store/layout') ?>
<?= $this->section('content') ?>
<main class="main qb-main">
    <div class="page-content qb-account-page">
        <div class="container">
            <div class="qb-auth-card">
                <div class="qb-auth-head">
                    <h1>Forgot Password</h1>
                    <p>Enter your account email or phone. We will send a verification code to your registered email.</p>
                </div>

                <form id="forgot-password-form" class="qb-form-card">
                    <div id="forgot-step-login">
                        <div class="form-group">
                            <label>Email or Phone *</label>
                            <input type="text" class="form-control" name="login" required placeholder="you@email.com or 03xx-xxxxxxx">
                        </div>
                        <button type="button" class="qb-btn qb-btn-primary qb-btn-block" id="forgot-send-otp-btn">Send Verification Code</button>
                    </div>

                    <div id="forgot-step-reset" hidden>
                        <p class="qb-otp-sent-msg" id="forgot-otp-msg"></p>
                        <input type="hidden" name="email" id="forgot-email">
                        <div class="form-group">
                            <label>6-digit code *</label>
                            <input type="text" class="form-control qb-otp-input" name="otp" id="forgot-otp" maxlength="6" inputmode="numeric" pattern="\d{6}" placeholder="000000" autocomplete="one-time-code">
                        </div>
                        <div class="form-group">
                            <label>New Password *</label>
                            <input type="password" class="form-control" name="password" minlength="6" required placeholder="At least 6 characters">
                        </div>
                        <div class="form-group">
                            <label>Confirm New Password *</label>
                            <input type="password" class="form-control" name="password_confirm" minlength="6" required>
                        </div>
                        <button type="submit" class="qb-btn qb-btn-primary qb-btn-block" id="forgot-reset-btn">Reset Password</button>
                        <button type="button" class="qb-btn qb-btn-outline qb-btn-block mt-2" id="forgot-resend-btn">Resend Code</button>
                    </div>
                </form>

                <p class="qb-auth-switch">
                    Remember your password?
                    <a href="<?= site_url('account/login') ?>">Sign in</a>
                </p>
            </div>
        </div>
    </div>
</main>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
(function ($) {
    function showResetStep(message, email) {
        $('#forgot-email').val(email || '');
        $('#forgot-otp-msg').text(message || '');
        $('#forgot-step-login').attr('hidden', true);
        $('#forgot-step-reset').removeAttr('hidden');
        $('#forgot-otp').val('').focus();
    }

    $('#forgot-send-otp-btn').on('click', function () {
        sendOtp($(this), $('input[name="login"]').val());
    });

    $('#forgot-resend-btn').on('click', function () {
        sendOtp($(this), $('#forgot-email').val());
    });

    function sendOtp($btn, login) {
        var text = $btn.text();
        $btn.prop('disabled', true).text('Sending...');
        return StoreApp.request(STORE_BASE + '/account/forgot-password/send-otp', 'POST', {
            login: login
        }).done(function (res) {
            StoreApp.toast(res.message);
            showResetStep(res.message, res.data.email);
        }).always(function () {
            $btn.prop('disabled', false).text(text);
        });
    }

    $('#forgot-password-form').on('submit', function (e) {
        e.preventDefault();
        var $btn = $('#forgot-reset-btn');
        var text = $btn.text();
        $btn.prop('disabled', true).text('Updating...');
        StoreApp.request(STORE_BASE + '/account/forgot-password/reset', 'POST', {
            email: $('#forgot-email').val(),
            otp: $('#forgot-otp').val(),
            password: $('input[name="password"]').val(),
            password_confirm: $('input[name="password_confirm"]').val()
        }).done(function (res) {
            StoreApp.toast(res.message);
            window.location.href = res.data.redirect;
        }).always(function () {
            $btn.prop('disabled', false).text(text);
        });
    });
})(jQuery);
</script>
<?= $this->endSection() ?>
