<?= $this->extend('store/layout') ?>
<?= $this->section('content') ?>
<main class="main">
    <nav class="breadcrumb-nav">
        <div class="container">
            <ul class="breadcrumb">
                <li><a href="<?= site_url('/') ?>"><i class="d-icon-home"></i></a></li>
                <li>Contact Us</li>
            </ul>
        </div>
    </nav>
    <div class="page-content pt-6 pb-10">
        <div class="container">
            <h2 class="title title-center mb-5">Contact Us</h2>
            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="page-content-body">
                        <?= $content['body'] ?? '<p>We would love to hear from you.</p>' ?>
                        <ul class="mt-4" style="list-style:none;padding:0;font-size:1.5rem;">
                            <?php if (! empty($settings['contact_phone'])): ?>
                                <li class="mb-2"><strong>Phone:</strong> <?= esc($settings['contact_phone']) ?></li>
                            <?php endif; ?>
                            <?php if (! empty($settings['contact_email'])): ?>
                                <li class="mb-2"><strong>Email:</strong> <?= esc($settings['contact_email']) ?></li>
                            <?php endif; ?>
                            <?php if (! empty($settings['contact_address'])): ?>
                                <li class="mb-2"><strong>Address:</strong> <?= esc($settings['contact_address']) ?></li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
                <div class="col-md-6">
                    <form class="form" onsubmit="event.preventDefault(); StoreApp.toast('Thanks! Please call or email us directly for now.');">
                        <label>Name</label>
                        <input type="text" class="form-control" required>
                        <label>Phone</label>
                        <input type="text" class="form-control" required>
                        <label>Message</label>
                        <textarea class="form-control" rows="5" required></textarea>
                        <button type="submit" class="btn btn-primary btn-rounded mt-2">Send Message</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>
<?= $this->endSection() ?>
