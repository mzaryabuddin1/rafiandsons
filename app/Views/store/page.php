<?= $this->extend('store/layout') ?>
<?= $this->section('content') ?>
<main class="main">
    <nav class="breadcrumb-nav">
        <div class="container">
            <ul class="breadcrumb">
                <li><a href="<?= site_url('/') ?>"><i class="d-icon-home"></i></a></li>
                <li><?= esc($content['title'] ?? $pageTitle) ?></li>
            </ul>
        </div>
    </nav>
    <div class="page-content pt-6 pb-10">
        <div class="container" style="max-width:900px;">
            <h2 class="title title-center mb-5"><?= esc($content['title'] ?? $pageTitle) ?></h2>
            <div class="page-content-body">
                <?= $content['body'] ?? '<p>Content coming soon.</p>' ?>
            </div>
        </div>
    </div>
</main>
<?= $this->endSection() ?>
