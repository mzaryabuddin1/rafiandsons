<?= $this->extend('store/layout') ?>
<?= $this->section('content') ?>
<main class="main qb-main">
    <div class="page-content qb-account-page">
        <div class="container">
            <div class="qb-auth-card">
                <div class="qb-auth-head">
                    <h1>Create Account</h1>
                    <p>We will send a 6-digit verification code to your email.</p>
                </div>

                <form id="account-register-form" class="qb-form-card">
                    <?php if (! empty($redirect)): ?>
                        <input type="hidden" name="redirect" value="<?= esc($redirect) ?>">
                    <?php endif; ?>
                    <div id="register-step-details">
                        <div class="form-group">
                            <label>Full Name *</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        <div class="form-group">
                            <label>Phone *</label>
                            <input type="text" class="form-control" name="phone" required placeholder="03xx-xxxxxxx">
                        </div>
                        <div class="form-group">
                            <label>Email *</label>
                            <input type="email" class="form-control" name="email" required placeholder="For OTP verification">
                        </div>
                        <div class="form-group">
                            <label>Password *</label>
                            <input type="password" class="form-control" name="password" required minlength="6" placeholder="At least 6 characters">
                        </div>
                        <div class="form-group">
                            <label>Confirm Password *</label>
                            <input type="password" class="form-control" name="password_confirm" required minlength="6">
                        </div>
                        <button type="button" class="qb-btn qb-btn-primary qb-btn-block" id="register-send-otp-btn">Send Verification Code</button>
                    </div>

                    <div id="register-step-otp" hidden>
                        <p class="qb-otp-sent-msg" id="register-otp-msg"></p>
                        <input type="hidden" name="verify_email" id="verify-email">
                        <div class="form-group">
                            <label>Enter 6-digit code *</label>
                            <input type="text" class="form-control qb-otp-input" name="otp" id="register-otp" maxlength="6" inputmode="numeric" pattern="\d{6}" placeholder="000000" autocomplete="one-time-code">
                        </div>
                        <button type="submit" class="qb-btn qb-btn-primary qb-btn-block" id="register-verify-btn">Verify &amp; Create Account</button>
                        <button type="button" class="qb-btn qb-btn-outline qb-btn-block mt-2" id="register-resend-btn">Resend Code</button>
                        <button type="button" class="qb-btn qb-btn-link qb-btn-block mt-2" id="register-back-btn">Change details</button>
                    </div>
                </form>

                <p class="qb-auth-switch">
                    Already have an account?
                    <a href="<?= site_url('account/login' . (! empty($redirect) ? '?redirect=' . urlencode($redirect) : '')) ?>">Sign in</a>
                </p>
            </div>
        </div>
    </div>
</main>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
(function ($) {
    var otpEmail = '';

    function showOtpStep(message, email) {
        otpEmail = email || '';
        $('#verify-email').val(otpEmail);
        $('#register-otp-msg').text(message || ('Verification code sent to ' + otpEmail));
        $('#register-step-details').attr('hidden', true);
        $('#register-step-otp').removeAttr('hidden');
        $('#register-otp').val('').focus();
    }

    function sendOtp($btn) {
        var text = $btn.text();
        $btn.prop('disabled', true).text('Sending...');
        return StoreApp.request(STORE_BASE + '/account/register/send-otp', 'POST', $('#account-register-form').serialize())
            .done(function (res) {
                StoreApp.toast(res.message);
                showOtpStep(res.message, res.data.email);
            })
            .always(function () {
                $btn.prop('disabled', false).text(text);
            });
    }

    $('#register-send-otp-btn, #register-resend-btn').on('click', function () {
        sendOtp($(this));
    });

    $('#register-back-btn').on('click', function () {
        $('#register-step-otp').attr('hidden', true);
        $('#register-step-details').removeAttr('hidden');
    });

    $('#account-register-form').on('submit', function (e) {
        e.preventDefault();
        var $btn = $('#register-verify-btn');
        var text = $btn.text();
        $btn.prop('disabled', true).text('Verifying...');
        StoreApp.request(STORE_BASE + '/account/register/verify-otp', 'POST', {
            email: $('#verify-email').val(),
            otp: $('#register-otp').val(),
            redirect: $('input[name="redirect"]').val() || ''
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
