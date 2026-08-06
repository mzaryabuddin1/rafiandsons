<?php
$auth = new \App\Libraries\StoreAuth();
$avatar = $auth->profileImageUrl($storeCustomer ?? null);
?>
<?= $this->extend('store/layout') ?>
<?= $this->section('content') ?>
<main class="main qb-main">
    <div class="page-content qb-account-page">
        <div class="container">
            <div class="row">
                <aside class="col-lg-3">
                    <div class="qb-account-sidebar">
                        <div class="qb-account-user">
                            <img src="<?= esc($avatar) ?>" alt="Profile" id="profile-avatar-preview" class="qb-account-avatar">
                            <strong><?= esc($customer['name'] ?? '') ?></strong>
                            <small><?= esc($customer['phone'] ?? '') ?></small>
                        </div>
                        <nav class="qb-account-nav">
                            <a href="<?= site_url('account/profile') ?>" class="active">Profile Settings</a>
                            <a href="<?= site_url('account/orders') ?>">My Orders</a>
                            <a href="#" id="account-logout-link">Sign Out</a>
                        </nav>
                    </div>
                </aside>
                <div class="col-lg-9">
                    <div class="qb-form-card qb-profile-card">
                        <h1 class="qb-page-title">Profile Settings</h1>
                        <p class="qb-page-sub">Update your personal details. These will auto-fill at checkout.</p>

                        <form id="profile-form" enctype="multipart/form-data">
                            <div class="qb-profile-photo">
                                <img src="<?= esc($avatar) ?>" alt="Profile" id="profile-photo-preview">
                                <div>
                                    <label class="qb-btn qb-btn-outline qb-btn-sm">
                                        Change Photo
                                        <input type="file" name="profile_image" id="profile_image" accept="image/jpeg,image/png,image/webp,image/gif" hidden>
                                    </label>
                                    <p class="qb-help-text">JPG, PNG or WebP. Max 2MB recommended.</p>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <label>Full Name *</label>
                                    <input type="text" class="form-control" name="name" required value="<?= esc($customer['name'] ?? '') ?>">
                                </div>
                                <div class="col-md-6">
                                    <label>Phone *</label>
                                    <input type="text" class="form-control" name="phone" required value="<?= esc($customer['phone'] ?? '') ?>">
                                </div>
                                <div class="col-md-6">
                                    <label>Email</label>
                                    <input type="email" class="form-control" name="email" value="<?= esc($customer['email'] ?? '') ?>">
                                </div>
                                <div class="col-md-6">
                                    <label>CNIC</label>
                                    <input type="text" class="form-control" name="cnic" value="<?= esc($customer['cnic'] ?? '') ?>" placeholder="xxxxx-xxxxxxx-x">
                                </div>
                                <div class="col-md-6">
                                    <label>City</label>
                                    <input type="text" class="form-control" name="city" value="<?= esc($customer['city'] ?? '') ?>">
                                </div>
                                <div class="col-md-6">
                                    <label>Address</label>
                                    <input type="text" class="form-control" name="address" value="<?= esc($customer['address'] ?? '') ?>">
                                </div>
                            </div>

                            <hr class="qb-profile-divider">

                            <h3>Change Password</h3>
                            <p class="qb-page-sub">Leave blank to keep your current password.</p>
                            <div class="row">
                                <div class="col-md-4">
                                    <label>Current Password</label>
                                    <input type="password" class="form-control" name="current_password" autocomplete="current-password">
                                </div>
                                <div class="col-md-4">
                                    <label>New Password</label>
                                    <input type="password" class="form-control" name="new_password" autocomplete="new-password">
                                </div>
                                <div class="col-md-4">
                                    <label>Confirm New Password</label>
                                    <input type="password" class="form-control" name="new_password_confirm" autocomplete="new-password">
                                </div>
                            </div>

                            <button type="submit" class="qb-btn qb-btn-primary mt-4" id="profile-save-btn">Save Changes</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$('#profile_image').on('change', function () {
    var file = this.files && this.files[0];
    if (!file) return;
    var reader = new FileReader();
    reader.onload = function (e) {
        $('#profile-photo-preview, #profile-avatar-preview').attr('src', e.target.result);
    };
    reader.readAsDataURL(file);
});

$('#profile-form').on('submit', function (e) {
    e.preventDefault();
    var $btn = $('#profile-save-btn');
    var text = $btn.text();
    $btn.prop('disabled', true).text('Saving...');
    var fd = new FormData(this);
    StoreApp.request(STORE_BASE + '/account/profile', 'POST', fd, true)
        .done(function (res) {
            StoreApp.toast(res.message);
            if (res.data && res.data.profile_image) {
                $('#profile-photo-preview, #profile-avatar-preview').attr('src', res.data.profile_image);
            }
        })
        .always(function () {
            $btn.prop('disabled', false).text(text);
        });
});

$('#account-logout-link').on('click', function (e) {
    e.preventDefault();
    StoreApp.request(STORE_BASE + '/account/logout', 'POST', {})
        .done(function (res) {
            window.location.href = res.data.redirect;
        });
});
</script>
<?= $this->endSection() ?>
