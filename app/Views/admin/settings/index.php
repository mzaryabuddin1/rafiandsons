<?= $this->extend('admin/layout') ?>
<?= $this->section('content') ?>
<div class="row wrapper border-bottom white-bg page-heading" style="margin:-15px -15px 20px;padding:15px;"><div class="col-lg-12"><h2>Settings</h2></div></div>
<div class="ibox"><div class="ibox-title"><h5>Website & Contact Settings</h5></div>
<div class="ibox-content">
<form id="settings-form">
<div class="row">
<div class="col-md-6"><div class="form-group"><label>Site Name</label><input class="form-control" name="site_name" id="site_name"></div></div>
<div class="col-md-6"><div class="form-group"><label>Order Notify Email</label><input class="form-control" name="order_notify_email" id="order_notify_email"></div></div>
</div>
<div class="row">
<div class="col-md-4"><div class="form-group"><label>Contact Email</label><input class="form-control" name="contact_email" id="contact_email"></div></div>
<div class="col-md-4"><div class="form-group"><label>Contact Phone</label><input class="form-control" name="contact_phone" id="contact_phone"></div></div>
<div class="col-md-4"><div class="form-group"><label>WhatsApp</label><input class="form-control" name="whatsapp_number" id="whatsapp_number"></div></div>
</div>
<div class="form-group"><label>Address</label><input class="form-control" name="contact_address" id="contact_address"></div>
<hr>
<h4>Social Links</h4>
<div class="row">
<div class="col-md-4"><div class="form-group"><label>Facebook</label><input class="form-control" name="facebook_url" id="facebook_url"></div></div>
<div class="col-md-4"><div class="form-group"><label>Instagram</label><input class="form-control" name="instagram_url" id="instagram_url"></div></div>
<div class="col-md-4"><div class="form-group"><label>YouTube</label><input class="form-control" name="youtube_url" id="youtube_url"></div></div>
</div>
<hr>
<h4>SMTP (for order emails)</h4>
<div class="row">
<div class="col-md-4"><div class="form-group"><label>SMTP Host</label><input class="form-control" name="smtp_host" id="smtp_host"></div></div>
<div class="col-md-4"><div class="form-group"><label>SMTP User</label><input class="form-control" name="smtp_user" id="smtp_user"></div></div>
<div class="col-md-4"><div class="form-group"><label>SMTP Pass</label><input type="password" class="form-control" name="smtp_pass" id="smtp_pass"></div></div>
</div>
<div class="row">
<div class="col-md-4"><div class="form-group"><label>SMTP Port</label><input class="form-control" name="smtp_port" id="smtp_port"></div></div>
<div class="col-md-4"><div class="form-group"><label>From Email</label><input class="form-control" name="smtp_from_email" id="smtp_from_email"></div></div>
<div class="col-md-4"><div class="form-group"><label>From Name</label><input class="form-control" name="smtp_from_name" id="smtp_from_name"></div></div>
</div>
<?php if (!empty($canUpdate)): ?><button type="submit" class="btn btn-primary" id="save-btn">Save Settings</button><?php endif; ?>
</form>
</div></div>
<?= $this->endSection() ?>
<?= $this->section('scripts') ?>
<script>
function loadSettings(){AdminApp.request(ADMIN_BASE+'/api/settings','GET').done(function(res){var d=res.data||{};Object.keys(d).forEach(function(k){if($('#'+k).length)$('#'+k).val(d[k]);});});}
$('#settings-form').on('submit',function(e){e.preventDefault();var $btn=$('#save-btn');AdminApp.setButtonLoading($btn,true);AdminApp.request(ADMIN_BASE+'/api/settings','POST',$(this).serialize()).done(function(res){AdminApp.toast('success',res.message);}).always(function(){AdminApp.setButtonLoading($btn,false);});});
$(loadSettings);
</script>
<?= $this->endSection() ?>
