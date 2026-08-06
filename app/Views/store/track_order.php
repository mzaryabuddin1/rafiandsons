<?= $this->extend('store/layout') ?>
<?= $this->section('content') ?>
<main class="main qb-main">
    <div class="page-content qb-track-page">
        <div class="container">
            <div class="qb-track-wrap">
                <div class="qb-auth-head">
                    <h1>Track Your Order</h1>
                    <p>Enter your <strong>order number</strong> or <strong>phone number</strong> to check the current status.</p>
                </div>

                <form id="track-order-form" class="qb-form-card qb-track-form">
                    <div class="form-group">
                        <label>Order Number</label>
                        <input type="text" class="form-control" name="order_number" id="track-order-number"
                               placeholder="e.g. RS-260805-6697C3"
                               value="<?= esc($prefillOrder ?? '') ?>">
                    </div>
                    <div class="qb-track-or">or</div>
                    <div class="form-group">
                        <label>Phone Number</label>
                        <input type="text" class="form-control" name="phone" id="track-phone"
                               placeholder="Phone used on the order">
                    </div>
                    <button type="submit" class="qb-btn qb-btn-primary qb-btn-block" id="track-btn">Track Order</button>
                </form>

                <div id="track-result" class="qb-track-result" hidden></div>
            </div>
        </div>
    </div>
</main>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
(function ($) {
    function money(n) {
        return 'PKR ' + Number(n || 0).toLocaleString();
    }

    function renderOrderCard(data) {
        var o = data.order || {};
        var items = data.items || [];

        var itemsHtml = '';
        items.forEach(function (item) {
            itemsHtml += '<div class="qb-track-item">'
                + '<div><strong>' + $('<div>').text(item.name).html() + '</strong> × ' + item.qty
                + (item.plan ? '<br><small>' + $('<div>').text(item.plan).html() + '</small>' : '')
                + '</div>'
                + '<div class="text-right"><span class="qb-track-pay-tag">' + item.payment + '</span><br><strong>' + money(item.due_now) + '</strong></div>'
                + '</div>';
        });

        return ''
            + '<div class="qb-form-card qb-track-card">'
            + '<div class="qb-track-status-hero qb-track-status-hero--' + (o.status || 'processing') + '">'
            + '<small>Current Status</small>'
            + '<strong>' + $('<div>').text(o.status_label || 'Processing').html() + '</strong>'
            + '</div>'
            + '<div class="qb-track-card-head">'
            + '<div><small>Order Number</small><strong>' + $('<div>').text(o.order_number).html() + '</strong></div>'
            + '</div>'
            + '<div class="qb-track-meta">'
            + '<div><span>Customer</span><strong>' + $('<div>').text(o.customer_name || '').html() + '</strong></div>'
            + '<div><span>Phone</span><strong>' + $('<div>').text(o.customer_phone || '').html() + '</strong></div>'
            + '<div><span>Placed on</span><strong>' + $('<div>').text(o.created_at || '').html() + '</strong></div>'
            + '<div><span>Payment</span><strong>' + $('<div>').text(o.payment_type || '').html() + '</strong></div>'
            + '</div>'
            + '<h3>Items</h3>'
            + '<div class="qb-track-items">' + itemsHtml + '</div>'
            + '<div class="qb-track-totals">'
            + '<div><span>Due now</span><strong>' + money(o.subtotal) + '</strong></div>'
            + '<div><span>Total order value</span><strong>' + money(o.total_payable) + '</strong></div>'
            + '</div>'
            + '</div>';
    }

    function renderResult(data) {
        var orders = data.orders || [];
        if (!orders.length) {
            $('#track-result').html('<div class="qb-form-card"><p class="text-muted mb-0">No orders found.</p></div>').removeAttr('hidden');
            return;
        }

        var html = '';
        if (orders.length > 1) {
            html += '<p class="qb-track-count">' + orders.length + ' orders found for this phone</p>';
        }
        orders.forEach(function (entry) {
            html += renderOrderCard(entry);
        });

        $('#track-result').html(html).removeAttr('hidden');
    }

    $('#track-order-form').on('submit', function (e) {
        e.preventDefault();
        var orderNo = ($('#track-order-number').val() || '').trim();
        var phone = ($('#track-phone').val() || '').trim();
        if (!orderNo && !phone) {
            StoreApp.toast('Enter an order number or phone number.');
            return;
        }

        var $btn = $('#track-btn');
        var text = $btn.text();
        $btn.prop('disabled', true).text('Tracking...');
        $('#track-result').attr('hidden', true).empty();

        StoreApp.request(STORE_BASE + '/track-order/lookup', 'POST', $(this).serialize())
            .done(function (res) {
                StoreApp.toast(res.message);
                renderResult(res.data || {});
            })
            .always(function () {
                $btn.prop('disabled', false).text(text);
            });
    });
})(jQuery);
</script>
<?= $this->endSection() ?>
