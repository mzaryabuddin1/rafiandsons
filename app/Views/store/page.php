<?= $this->extend('store/layout') ?>
<?= $this->section('content') ?>
<main class="main qb-main">
    <nav class="qb-breadcrumb">
        <div class="container">
            <a href="<?= site_url('home') ?>">Home</a>
            <span>/</span>
            <span><?= esc($content['title'] ?? $pageTitle) ?></span>
        </div>
    </nav>
    <div class="page-content qb-cms-page">
        <div class="container qb-cms-wrap">
            <h1 class="qb-cms-title"><?= esc($content['title'] ?? $pageTitle) ?></h1>
            <div class="page-content-body">
                <?= $content['body'] ?? '<p>Content coming soon.</p>' ?>
            </div>
        </div>
    </div>
</main>
<?= $this->endSection() ?>
