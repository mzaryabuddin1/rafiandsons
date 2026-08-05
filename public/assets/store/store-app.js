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

  function money(n) {
    return 'PKR ' + Number(n || 0).toLocaleString();
  }

  function closeQuickView() {
    $('#qb-qv-modal, #qb-qv-overlay').attr('hidden', true);
    $('body').removeClass('qb-qv-open');
  }

  function detectPaymentType() {
    var pt = $('input[name="payment_type"]:checked').val()
      || $('input[name="qv_payment_type"]:checked').val()
      || $('input[name="qv_payment_type"][type="hidden"]').val()
      || $('input[name="payment_type"][type="hidden"]').val();
    if (pt) {
      return pt;
    }
    if ($('input[name="installment_plan_id"]:checked, input[name="qv_installment_plan_id"]:checked').val()) {
      return 'installment';
    }
    return 'cash';
  }

  function addToCart(productId, qty, paymentType, planId) {
    return request((window.STORE_BASE || '') + '/cart/add', 'POST', {
      product_id: productId,
      qty: qty || 1,
      payment_type: paymentType || 'cash',
      plan_id: planId || ''
    }).done(function (res) {
      toast(res.message);
      if (res.data) {
        updateCartBadge(res.data.count, res.data.subtotal);
      }
    });
  }

  function syncQvPaymentUI() {
    var mode = $('input[name="qv_payment_type"]:checked').val()
      || $('input[name="qv_payment_type"][type="hidden"]').val()
      || 'cash';
    if (mode === 'cash') {
      $('#qb-qv-price').removeAttr('hidden');
      $('#qb-qv-installment').attr('hidden', true);
    } else {
      $('#qb-qv-price').attr('hidden', true);
      $('#qb-qv-installment').removeAttr('hidden');
    }
    $('.qb-qv-payment-tabs .qb-payment-tab').removeClass('is-active');
    $('.qb-qv-payment-tabs input[value="' + mode + '"]').closest('.qb-payment-tab').addClass('is-active');
  }

  function openQuickView(slug) {
    var $modal = $('#qb-qv-modal');
    var $overlay = $('#qb-qv-overlay');
    if (!$modal.length) {
      window.location.href = (window.STORE_BASE || '') + '/product/' + slug;
      return;
    }

    $('#qb-qv-title').text('Loading...');
    $('#qb-qv-plans').empty();
    $modal.add($overlay).removeAttr('hidden');
    $('body').addClass('qb-qv-open');

    request((window.STORE_BASE || '') + '/product/' + encodeURIComponent(slug) + '/quick', 'GET')
      .done(function (res) {
        var p = res.data || {};
        var cashOk = Number(p.cash_available) === 1;
        var instOk = Number(p.installment_available) === 1 && (p.plans || []).length > 0;
        var both = cashOk && instOk;
        var compare = p.compare_price && Number(p.compare_price) > Number(p.price)
          ? '<span class="qb-price-compare">' + money(p.compare_price) + '</span> ' : '';

        $('#qb-qv-title').text(p.name || '');
        $('#qb-qv-meta').text((p.sku ? 'SKU: ' + p.sku + ' · ' : '') + (p.stock || ''));
        $('#qb-qv-price').html('<span class="qb-cash-price-label">Cash Price</span> ' + compare + money(p.price));
        $('#qb-qv-desc').text(p.description || '');
        $('#qb-qv-image').attr('src', p.image || '').attr('alt', p.name || '');
        $('#qb-qv-details').attr('href', p.url || '#');
        $('#qb-qv-add').data('product-id', p.id);
        $('#qb-qv-qty').val(1);

        $('input[name="qv_payment_type"][type="hidden"]').remove();

        if (both) {
          var tabsHtml = ''
            + '<label class="qb-payment-tab is-active">'
            + '<input type="radio" name="qv_payment_type" value="cash" checked>'
            + '<span class="qb-payment-tab-label">Cash</span>'
            + '<span class="qb-payment-tab-price">' + money(p.price) + '</span>'
            + '<span class="qb-payment-tab-note">Full price</span>'
            + '</label>'
            + '<label class="qb-payment-tab">'
            + '<input type="radio" name="qv_payment_type" value="installment">'
            + '<span class="qb-payment-tab-label">Installment</span>'
            + '<span class="qb-payment-tab-price">From Rs. ' + Number(p.min_advance || 0).toLocaleString() + '</span>'
            + '<span class="qb-payment-tab-note">Pay advance first</span>'
            + '</label>';
          $('#qb-qv-payment-tabs').html(tabsHtml);
          $('#qb-qv-payment-wrap').removeAttr('hidden');
        } else if (instOk && !cashOk) {
          $('#qb-qv-payment-wrap').attr('hidden', true);
          $('#qb-qv-buy-row').prepend('<input type="hidden" name="qv_payment_type" value="installment">');
        } else {
          $('#qb-qv-payment-wrap').attr('hidden', true);
          $('#qb-qv-buy-row').prepend('<input type="hidden" name="qv_payment_type" value="cash">');
        }

        var plansHtml = '';
        (p.plans || []).forEach(function (plan, i) {
          plansHtml += '<label class="qb-plan-card' + (i === 0 ? ' is-active' : '') + '">'
            + '<input type="radio" name="qv_installment_plan_id" value="' + plan.id + '"' + (i === 0 ? ' checked' : '') + '>'
            + '<div class="qb-plan-card-inner">'
            + '<div class="qb-plan-card-top"><strong>' + (plan.name || '') + '</strong><span class="qb-plan-card-check"></span></div>'
            + '<div class="qb-plan-card-monthly">Rs. ' + Number(plan.monthly_installment || 0).toLocaleString()
            + ' <span>× ' + plan.months + ' months</span></div>'
            + '<div class="qb-plan-card-advance">Rs. ' + Number(plan.down_payment || 0).toLocaleString() + ' <em>Advance</em></div>'
            + '</div></label>';
        });
        $('#qb-qv-plans').html(plansHtml);

        if (instOk && !cashOk) {
          $('#qb-qv-price').attr('hidden', true);
          $('#qb-qv-installment').removeAttr('hidden');
        } else if (cashOk && !instOk) {
          $('#qb-qv-price').removeAttr('hidden');
          $('#qb-qv-installment').attr('hidden', true);
        } else {
          syncQvPaymentUI();
        }
      })
      .fail(function () {
        closeQuickView();
      });
  }

  $(document).on('change', 'input[name="qv_payment_type"]', syncQvPaymentUI);

  $(document).on('change', 'input[name="qv_installment_plan_id"]', function () {
    $('#qb-qv-plans .qb-plan-card').removeClass('is-active');
    $(this).closest('.qb-plan-card').addClass('is-active');
  });

  $(document).on('click', '.js-add-cart', function (e) {
    e.preventDefault();
    e.stopPropagation();
    var $btn = $(this);
    var productId = $btn.data('product-id');
    var slug = $btn.data('slug');
    var openQv = Number($btn.data('open-qv')) === 1
      || (Number($btn.data('cash-available')) === 1 && Number($btn.data('installment-available')) === 1);

    if (openQv && slug) {
      openQuickView(slug);
      return;
    }

    var qty = $btn.data('qty') || $('#product-qty').val() || $('#qb-qv-qty').val() || 1;
    var paymentType = detectPaymentType();
    if (Number($btn.data('installment-available')) === 1 && Number($btn.data('cash-available')) !== 1) {
      paymentType = 'installment';
    } else if (Number($btn.data('cash-available')) === 1 && Number($btn.data('installment-available')) !== 1) {
      paymentType = 'cash';
    }
    var planId = paymentType === 'installment'
      ? ($('input[name="installment_plan_id"]:checked').val()
        || $('input[name="qv_installment_plan_id"]:checked').val()
        || '')
      : '';

    addToCart(productId, qty, paymentType, planId);
  });

  $(document).on('click', '.js-quick-view', function (e) {
    e.preventDefault();
    e.stopPropagation();
    openQuickView($(this).data('slug'));
  });

  $(document).on('click', '#qb-qv-close, #qb-qv-overlay', closeQuickView);

  $(document).on('keydown', function (e) {
    if (e.key === 'Escape') {
      closeQuickView();
    }
  });

  $(document).on('click', '#qb-qv-add', function (e) {
    e.preventDefault();
    var productId = $(this).data('product-id');
    if (!productId) {
      return;
    }
    var paymentType = detectPaymentType();
    var planId = paymentType === 'installment'
      ? ($('input[name="qv_installment_plan_id"]:checked').val() || '')
      : '';

    addToCart(productId, $('#qb-qv-qty').val() || 1, paymentType, planId).done(function () {
      closeQuickView();
    });
  });

  $(document).on('click', '.js-search-quick-view', function (e) {
    e.preventDefault();
    e.stopPropagation();
    $('#qb-search-dropdown').attr('hidden', 'hidden');
    $('#qb-search-input').attr('aria-expanded', 'false');
    openQuickView($(this).data('slug'));
  });

  $(document).off('click', '.btn-quickview');

  function initProductSearch() {
    var $form = $('#qb-search-form');
    var $input = $('#qb-search-input');
    var $clear = $('#qb-search-clear');
    var $dropdown = $('#qb-search-dropdown');
    var $results = $('#qb-search-results');
    var $viewAll = $('#qb-search-view-all');
    var $category = $('#qb-search-category');

    if (!$form.length || !$input.length) {
      return;
    }

    var timer = null;
    var xhr = null;
    var activeIndex = -1;

    function shopSearchUrl(query) {
      var params = new URLSearchParams();
      if (query) {
        params.set('q', query);
      }
      var cat = ($category.val() || '').trim();
      if (cat) {
        params.set('category', cat);
      }
      var qs = params.toString();
      return (window.STORE_BASE || '') + '/shop' + (qs ? '?' + qs : '');
    }

    function closeDropdown() {
      $dropdown.attr('hidden', 'hidden');
      $input.attr('aria-expanded', 'false');
      activeIndex = -1;
    }

    function openDropdown() {
      $dropdown.removeAttr('hidden');
      $input.attr('aria-expanded', 'true');
    }

    function toggleClear() {
      var hasVal = ($input.val() || '').trim().length > 0;
      if ($clear.length) {
        if (hasVal) {
          $clear.removeAttr('hidden');
        } else {
          $clear.attr('hidden', true);
        }
      }
    }

    function renderItems(items, total, query) {
      $results.empty();
      activeIndex = -1;

      if (!items.length) {
        $results.html('<li class="qb-search-empty">No products found for "' + $('<div>').text(query).html() + '"</li>');
        $viewAll.attr('hidden', 'hidden');
        openDropdown();
        return;
      }

      items.forEach(function (item) {
        var cashOk = Number(item.cash_available) === 1;
        var instOk = Number(item.installment_available) === 1;
        var both = cashOk && instOk;
        var priceText = item.price_label;
        if (both && item.min_advance) {
          priceText = item.price_label + ' · From Rs. ' + Number(item.min_advance).toLocaleString() + ' adv.';
        } else if (instOk && !cashOk && item.min_advance) {
          priceText = 'From Rs. ' + Number(item.min_advance).toLocaleString() + ' Advance';
        }

        var $li = $('<li role="option"></li>');
        var $a = $('<a></a>').attr('href', item.url);
        var $body = $('<div class="qb-search-result-body"></div>');
        $body.append($('<strong></strong>').text(item.name));
        $body.append($('<span></span>').text(item.category_name || ''));
        $body.append($('<em></em>').text(priceText));

        if (both) {
          var $tags = $('<div class="qb-search-result-tags"></div>');
          $tags.append('<span class="qb-search-result-tag qb-search-result-tag--cash">Cash</span>');
          $tags.append('<span class="qb-search-result-tag qb-search-result-tag--inst">Installment</span>');
          $body.append($tags);
          var $actions = $('<div class="qb-search-result-actions"></div>');
          $actions.append(
            $('<button type="button" class="js-search-quick-view">Choose option</button>')
              .attr('data-slug', item.slug)
          );
          $body.append($actions);
        }

        $a.append($('<img>').attr('src', item.image).attr('alt', item.name), $body);
        $li.append($a);
        $results.append($li);
      });

      $viewAll
        .removeAttr('hidden')
        .attr('href', shopSearchUrl(query))
        .text(total > items.length ? 'View all ' + total + ' results' : 'View all results');

      openDropdown();
    }

    function fetchSuggestions() {
      var query = ($input.val() || '').trim();
      toggleClear();
      if (query.length < 2) {
        closeDropdown();
        return;
      }

      if (xhr) {
        xhr.abort();
      }

      $results.html('<li class="qb-search-loading">Searching products...</li>');
      openDropdown();

      xhr = $.ajax({
        url: (window.STORE_BASE || '') + '/search/suggest',
        method: 'GET',
        dataType: 'json',
        data: {
          q: query,
          category: ($category.val() || '').trim()
        }
      }).done(function (res) {
        var data = (res && res.data) || {};
        renderItems(data.items || [], data.total || 0, query);
      }).fail(function () {
        closeDropdown();
      }).always(function () {
        xhr = null;
      });
    }

    $input.on('input', function () {
      toggleClear();
      clearTimeout(timer);
      timer = setTimeout(fetchSuggestions, 280);
    });

    $input.on('focus', function () {
      toggleClear();
      if (($input.val() || '').trim().length >= 2) {
        fetchSuggestions();
      }
    });

    if ($clear.length) {
      $clear.on('click', function () {
        $input.val('').focus();
        toggleClear();
        closeDropdown();
      });
    }

    toggleClear();

    $input.on('keydown', function (e) {
      var $items = $results.find('li[role="option"] a');
      if (!$items.length || $dropdown.is('[hidden]')) {
        return;
      }

      if (e.key === 'ArrowDown') {
        e.preventDefault();
        activeIndex = Math.min(activeIndex + 1, $items.length - 1);
      } else if (e.key === 'ArrowUp') {
        e.preventDefault();
        activeIndex = Math.max(activeIndex - 1, 0);
      } else if (e.key === 'Enter' && activeIndex >= 0) {
        e.preventDefault();
        window.location.href = $items.eq(activeIndex).attr('href');
        return;
      } else if (e.key === 'Escape') {
        closeDropdown();
        return;
      } else {
        return;
      }

      $results.find('li[role="option"]').removeClass('is-active');
      if (activeIndex >= 0) {
        $items.eq(activeIndex).closest('li').addClass('is-active');
      }
    });

    $form.on('submit', function (e) {
      var query = ($input.val() || '').trim();
      if (!query) {
        e.preventDefault();
        $input.focus();
        return;
      }
      closeDropdown();
    });

    $(document).on('click', function (e) {
      if (!$(e.target).closest('.qb-search-form').length) {
        closeDropdown();
      }
    });
  }

  initProductSearch();

  return {
    request: request,
    toast: toast,
    updateCartBadge: updateCartBadge,
    openQuickView: openQuickView,
    closeQuickView: closeQuickView
  };
})(jQuery);
