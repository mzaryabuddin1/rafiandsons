(function ($) {
    'use strict';

    var morrisChart = null;
    var revenueChart = null;

    var statusColors = {
        new: '#1ab394',
        under_review: '#f8ac59',
        customer_contacted: '#23c6c8',
        approved: '#1c84c6',
        rejected: '#ed5565',
        processing: '#f8ac59',
        completed: '#1ab394',
        cancelled: '#ed5565'
    };

    function formatMoney(amount) {
        var n = Number(amount) || 0;
        if (n >= 1000000) {
            return 'PKR ' + (n / 1000000).toFixed(1) + 'M';
        }
        if (n >= 1000) {
            return 'PKR ' + Math.round(n).toLocaleString();
        }
        return 'PKR ' + Math.round(n);
    }

    function formatChange(value) {
        var v = Number(value) || 0;
        var icon = v >= 0 ? 'fa-level-up' : 'fa-level-down';
        var cls = v >= 0 ? 'text-change-up' : 'text-change-down';
        var sign = v > 0 ? '+' : '';
        return '<span class="' + cls + '">' + sign + v + '% <i class="fa ' + icon + '"></i></span>';
    }

    function statusBadge(status, label) {
        var cls = 'status-badge-' + (status || 'new');
        return '<span class="badge ' + cls + '">' + (label || status) + '</span>';
    }

    function pct(part, total) {
        if (!total) return 0;
        return Math.round((part / total) * 100);
    }

    function renderFlotChart(monthly) {
        var labels = monthly.labels || [];
        var orders = monthly.orders || [];
        var data = [];

        for (var i = 0; i < labels.length; i++) {
            data.push([i, orders[i] || 0]);
        }

        var options = {
            series: {
                lines: { show: true, fill: true, fillColor: { colors: [{ opacity: 0.25 }, { opacity: 0.05 }] } },
                points: { show: true, radius: 4, fillColor: '#1ab394' }
            },
            colors: ['#1ab394'],
            grid: { hoverable: true, borderWidth: 0, color: '#eee' },
            tooltip: true,
            tooltipOpts: {
                content: function (label, xval, yval) {
                    var month = labels[xval] || '';
                    return month + ': <strong>' + yval + ' orders</strong>';
                }
            },
            xaxis: {
                ticks: labels.map(function (lbl, idx) { return [idx, lbl]; }),
                tickColor: '#f1f1f1'
            },
            yaxis: { min: 0, tickColor: '#f1f1f1', tickDecimals: 0 }
        };

        $.plot($('#flot-orders-chart'), [{ data: data, label: 'Orders' }], options);
    }

    function renderMorrisDonut(statusChart) {
        $('#morris-donut-chart').empty();

        if (!statusChart || !statusChart.length) {
            $('#morris-donut-chart').hide();
            $('#status-empty').show();
            return;
        }

        $('#status-empty').hide();
        $('#morris-donut-chart').show();

        var data = statusChart.map(function (row) {
            return {
                label: row.label,
                value: row.value
            };
        });

        var colors = statusChart.map(function (row) {
            return statusColors[row.status] || '#676a6c';
        });

        morrisChart = Morris.Donut({
            element: 'morris-donut-chart',
            data: data,
            colors: colors,
            resize: true,
            formatter: function (y) { return y + ' orders'; }
        });
    }

    function renderRevenueChart(monthly) {
        var ctx = document.getElementById('revenueBarChart');
        if (!ctx) return;

        if (revenueChart) {
            revenueChart.destroy();
        }

        revenueChart = new Chart(ctx.getContext('2d'), {
            type: 'bar',
            data: {
                labels: monthly.labels || [],
                datasets: [{
                    label: 'Revenue (PKR)',
                    backgroundColor: 'rgba(26,179,148,0.7)',
                    borderColor: 'rgba(26,179,148,1)',
                    borderWidth: 1,
                    data: monthly.revenue || []
                }]
            },
            options: {
                responsive: true,
                legend: { display: false },
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true,
                            callback: function (value) {
                                if (value >= 1000000) return (value / 1000000) + 'M';
                                if (value >= 1000) return (value / 1000) + 'K';
                                return value;
                            }
                        }
                    }]
                },
                tooltips: {
                    callbacks: {
                        label: function (item) {
                            return 'PKR ' + Number(item.yLabel).toLocaleString();
                        }
                    }
                }
            }
        });
    }

    function renderSparkline(revenue) {
        var values = revenue && revenue.length ? revenue : [0, 0, 0, 0, 0, 0];
        $('#spark-revenue').sparkline(values, {
            type: 'line',
            width: '100%',
            height: '40',
            lineColor: '#1ab394',
            fillColor: 'rgba(26,179,148,0.15)',
            spotColor: '#1ab394',
            minSpotColor: '#1ab394',
            maxSpotColor: '#1ab394',
            highlightSpotColor: '#1ab394',
            highlightLineColor: '#1ab394'
        });
    }

    function renderApprovalChart(rate) {
        var $el = $('#chart-approval');
        $el.attr('data-percent', rate).text(rate + '%');
        $el.easyPieChart({
            barColor: '#1ab394',
            trackColor: '#f1f1f1',
            scaleColor: false,
            lineCap: 'round',
            lineWidth: 6,
            size: 90,
            animate: 1000
        });
    }

    function loadDashboard() {
        AdminApp.request(ADMIN_BASE + '/api/dashboard/stats', 'GET').done(function (res) {
            var c = res.data.counts || {};
            var monthly = res.data.monthly || {};
            var totalOrders = c.orders || 0;

            $('#stat-products').text(c.products || 0);
            $('#stat-orders').text(totalOrders);
            $('#stat-customers').text(c.customers || 0);
            $('#stat-categories').text(c.categories || 0);
            $('#stat-revenue').text(formatMoney(c.total_revenue || 0));

            $('#stat-orders-change').html(formatChange(c.orders_change || 0));
            $('#stat-revenue-change').html(formatChange(c.revenue_change || 0));

            $('#side-new-orders').text(c.new_orders || 0);
            $('#side-approved').text(c.approved || 0);
            $('#side-pending').text(c.pending || 0);

            var newPct = pct(c.new_orders || 0, totalOrders);
            var approvedPct = pct(c.approved || 0, totalOrders);
            var pendingPct = pct(c.pending || 0, totalOrders);

            $('#side-new-percent').text(newPct + '%');
            $('#side-approved-percent').text(approvedPct + '%');
            $('#side-pending-percent').text(pendingPct + '%');
            $('#bar-new-orders').css('width', newPct + '%');
            $('#bar-approved').css('width', approvedPct + '%');
            $('#bar-pending').css('width', pendingPct + '%');

            $('#stat-new-badge').text(c.new_orders || 0);
            $('#stat-completed-badge').text(c.completed || 0);
            $('#stat-cancelled-badge').text(c.cancelled || 0);

            renderFlotChart(monthly);
            renderMorrisDonut(res.data.status_chart || []);
            renderRevenueChart(monthly);
            renderSparkline(monthly.revenue || []);
            renderApprovalChart(c.approval_rate || 0);

            var rows = '';
            (res.data.recent_orders || []).forEach(function (o) {
                rows += '<tr>' +
                    '<td><strong>' + (o.order_number || '-') + '</strong></td>' +
                    '<td>' + (o.customer_name || '-') + '</td>' +
                    '<td>' + (o.customer_phone || '-') + '</td>' +
                    '<td>' + formatMoney(o.total_payable || 0) + '</td>' +
                    '<td>' + statusBadge(o.status, o.status_label) + '</td>' +
                    '<td><small>' + (o.created_at || '') + '</small></td>' +
                    '</tr>';
            });
            if (!rows) {
                rows = '<tr><td colspan="6" class="text-center text-muted p-lg"><i class="fa fa-inbox fa-2x m-b-sm"></i><br>No orders yet</td></tr>';
            }
            $('#recent-orders-table tbody').html(rows);
        });
    }

    $(loadDashboard);
})(jQuery);
