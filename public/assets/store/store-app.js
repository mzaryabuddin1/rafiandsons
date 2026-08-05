window.StoreApp = (function ($) {
  'use strict';

  function csrfToken() {
    return $('meta[name="csrf-token"]').attr('content') || '';
  }

  function csrfName() {
    return $('meta[name="csrf-name"]').attr('content') || 'csrf_test_name';
  }

  function csrfHeader() {
    var h = {};
    h[$('meta[name="csrf-header"]').attr('content') || 'X-CSRF-TOKEN'] = csrfToken();
    return h;
  }

  function withCsrf(data) {
    if (typeof data === 'string') {
      return data + (data ? '&' : '') + encodeURIComponent(csrfName()) + '=' + encodeURIComponent(csrfToken());
    }
    data = data || {};
    data[csrfName()] = csrfToken();
    return data;
  }

  function toast(message) {
    var $el = $('#store-toast');
    if (!$el.length) {
      $el = $('<div id="store-toast" class="toast-store"></div>').appendTo('body');
    }
    $el.text(message).addClass('show');
    setTimeout(function () { $el.removeClass('show'); }, 2800);
  }

  function request(url, method, data) {
    method = (method || 'GET').toUpperCase();
    return $.ajax({
      url: url,
      method: method,
      dataType: 'json',
      headers: csrfHeader(),
      data: method === 'GET' ? (data || {}) : withCsrf(data)
    }).fail(function (xhr) {
      var msg = (xhr.responseJSON && xhr.responseJSON.message) || 'Request failed';
      toast(msg);
    });
  }

  function updateCartBadge(count, subtotal) {
    if (typeof count !== 'undefined') {
      $('#header-cart-count').text(count);
    }
    if (typeof subtotal !== 'undefined') {
      $('#header-cart-subtotal').text('PKR ' + Number(subtotal).toLocaleString());
    }
  }

  $(document).on('click', '.js-add-cart', function (e) {
    e.preventDefault();
    var $btn = $(this);
    var productId = $btn.data('product-id');
    var qty = $btn.data('qty') || $('#product-qty').val() || 1;
    var planId = $btn.data('plan-id') || $('input[name="installment_plan_id"]:checked').val() || '';

    request((window.STORE_BASE || '') + '/cart/add', 'POST', {
      product_id: productId,
      qty: qty,
      plan_id: planId
    }).done(function (res) {
      toast(res.message);
      if (res.data) {
        updateCartBadge(res.data.count, res.data.subtotal);
      }
    });
  });

  return {
    request: request,
    toast: toast,
    updateCartBadge: updateCartBadge
  };
})(jQuery);
