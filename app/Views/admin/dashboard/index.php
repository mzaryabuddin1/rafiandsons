<?= $this->extend('admin/layout') ?>

<?= $this->section('styles') ?>
<link href="<?= base_url('admintheme/css/plugins/morris/morris-0.4.3.min.css') ?>" rel="stylesheet">
<link href="<?= base_url('assets/admin/dashboard.css') ?>" rel="stylesheet">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row wrapper border-bottom white-bg page-heading dashboard-heading">
    <div class="col-lg-10">
        <h2>Dashboard</h2>
        <ol class="breadcrumb">
            <li class="active"><strong>Welcome back, <?= esc($authUser['name'] ?? 'Admin') ?></strong></li>
        </ol>
        <p class="text-muted m-b-none">Installment orders, catalog, and customer overview at a glance.</p>
    </div>
    <div class="col-lg-2 text-right dashboard-heading-actions">
        <a href="<?= site_url('admin/orders') ?>" class="btn btn-primary btn-sm"><i class="fa fa-shopping-cart"></i> View Orders</a>
    </div>
</div>

<div class="row dashboard-widgets">
    <div class="col-lg-3 col-md-6">
        <div class="ibox dashboard-stat">
            <div class="ibox-content">
                <div class="row">
                    <div class="col-8">
                        <h5 class="text-muted m-b-xs">Active Products</h5>
                        <h1 class="no-margins" id="stat-products">-</h1>
                        <small>In catalog</small>
                    </div>
                    <div class="col-4 text-right">
                        <span class="dashboard-icon bg-navy"><i class="fa fa-cube"></i></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="ibox dashboard-stat">
            <div class="ibox-content">
                <div class="row">
                    <div class="col-8">
                        <h5 class="text-muted m-b-xs">Total Orders</h5>
                        <h1 class="no-margins" id="stat-orders">-</h1>
                        <div class="stat-percent font-bold" id="stat-orders-change-wrap">
                            <span id="stat-orders-change">-</span> <small>this month</small>
                        </div>
                    </div>
                    <div class="col-4 text-right">
                        <span class="dashboard-icon bg-primary"><i class="fa fa-shopping-cart"></i></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="ibox dashboard-stat">
            <div class="ibox-content">
                <div class="row">
                    <div class="col-8">
                        <h5 class="text-muted m-b-xs">Customers</h5>
                        <h1 class="no-margins" id="stat-customers">-</h1>
                        <small>Registered</small>
                    </div>
                    <div class="col-4 text-right">
                        <span class="dashboard-icon bg-info"><i class="fa fa-users"></i></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="ibox dashboard-stat">
            <div class="ibox-content">
                <div class="row">
                    <div class="col-8">
                        <h5 class="text-muted m-b-xs">Total Revenue</h5>
                        <h1 class="no-margins dashboard-revenue" id="stat-revenue">-</h1>
                        <div class="stat-percent font-bold" id="stat-revenue-change-wrap">
                            <span id="stat-revenue-change">-</span> <small>vs last month</small>
                        </div>
                    </div>
                    <div class="col-4 text-right">
                        <span class="dashboard-icon bg-success"><i class="fa fa-money"></i></span>
                    </div>
                </div>
                <span id="spark-revenue" class="dashboard-sparkline"></span>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="ibox">
            <div class="ibox-title">
                <h5><i class="fa fa-line-chart"></i> Orders Overview</h5>
                <div class="ibox-tools">
                    <span class="label label-primary">Last 6 months</span>
                </div>
            </div>
            <div class="ibox-content">
                <div class="row">
                    <div class="col-lg-9">
                        <div class="flot-chart dashboard-flot">
                            <div class="flot-chart-content" id="flot-orders-chart"></div>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <ul class="stat-list">
                            <li>
                                <h2 class="no-margins" id="side-new-orders">-</h2>
                                <small>New orders</small>
                                <div class="stat-percent text-navy" id="side-new-percent">-</div>
                                <div class="progress progress-mini">
                                    <div class="progress-bar" id="bar-new-orders"></div>
                                </div>
                            </li>
                            <li>
                                <h2 class="no-margins" id="side-approved">-</h2>
                                <small>Approved</small>
                                <div class="stat-percent text-navy" id="side-approved-percent">-</div>
                                <div class="progress progress-mini">
                                    <div class="progress-bar progress-bar-success" id="bar-approved"></div>
                                </div>
                            </li>
                            <li>
                                <h2 class="no-margins" id="side-pending">-</h2>
                                <small>Pending action</small>
                                <div class="stat-percent text-warning" id="side-pending-percent">-</div>
                                <div class="progress progress-mini">
                                    <div class="progress-bar progress-bar-warning" id="bar-pending"></div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="ibox">
            <div class="ibox-title">
                <h5><i class="fa fa-pie-chart"></i> Order Status</h5>
            </div>
            <div class="ibox-content">
                <div id="morris-donut-chart" class="dashboard-donut"></div>
                <div id="status-empty" class="text-center text-muted p-md" style="display:none;">
                    <i class="fa fa-inbox fa-3x m-b-sm"></i>
                    <p>No orders yet. Status breakdown will appear here.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-4">
        <div class="ibox">
            <div class="ibox-title">
                <h5><i class="fa fa-bar-chart"></i> Monthly Revenue</h5>
            </div>
            <div class="ibox-content">
                <canvas id="revenueBarChart" height="140"></canvas>
            </div>
        </div>
        <div class="ibox">
            <div class="ibox-title"><h5>Quick Stats</h5></div>
            <div class="ibox-content">
                <div class="row text-center">
                    <div class="col-6">
                        <div class="dashboard-mini-stat">
                            <span class="chart" id="chart-approval" data-percent="0">0%</span>
                            <small>Approval rate</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="dashboard-mini-stat">
                            <h2 class="no-margins text-primary" id="stat-categories">-</h2>
                            <small>Categories</small>
                        </div>
                    </div>
                </div>
                <hr>
                <ul class="list-group clear-list m-t">
                    <li class="list-group-item fist-item">
                        <span class="float-right badge badge-primary" id="stat-new-badge">-</span>
                        New bookings
                    </li>
                    <li class="list-group-item">
                        <span class="float-right badge badge-success" id="stat-completed-badge">-</span>
                        Completed
                    </li>
                    <li class="list-group-item">
                        <span class="float-right badge badge-danger" id="stat-cancelled-badge">-</span>
                        Cancelled
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="ibox">
            <div class="ibox-title">
                <h5><i class="fa fa-list"></i> Recent Installment Orders</h5>
                <div class="ibox-tools">
                    <a href="<?= site_url('admin/orders') ?>" class="btn btn-xs btn-white">View all</a>
                </div>
            </div>
            <div class="ibox-content">
                <div class="table-responsive">
                    <table class="table table-hover table-striped" id="recent-orders-table">
                        <thead>
                        <tr>
                            <th>Order #</th>
                            <th>Customer</th>
                            <th>Phone</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('admintheme/js/plugins/flot/jquery.flot.js') ?>"></script>
<script src="<?= base_url('admintheme/js/plugins/flot/jquery.flot.tooltip.min.js') ?>"></script>
<script src="<?= base_url('admintheme/js/plugins/flot/jquery.flot.spline.js') ?>"></script>
<script src="<?= base_url('admintheme/js/plugins/flot/jquery.flot.resize.js') ?>"></script>
<script src="<?= base_url('admintheme/js/plugins/flot/jquery.flot.time.js') ?>"></script>
<script src="<?= base_url('admintheme/js/plugins/chartJs/Chart.min.js') ?>"></script>
<script src="<?= base_url('admintheme/js/plugins/morris/raphael-2.1.0.min.js') ?>"></script>
<script src="<?= base_url('admintheme/js/plugins/morris/morris.js') ?>"></script>
<script src="<?= base_url('admintheme/js/plugins/sparkline/jquery.sparkline.min.js') ?>"></script>
<script src="<?= base_url('admintheme/js/plugins/easypiechart/jquery.easypiechart.js') ?>"></script>
<script src="<?= base_url('assets/admin/dashboard.js') ?>"></script>
<?= $this->endSection() ?>
