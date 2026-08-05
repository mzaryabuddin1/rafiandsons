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

  function openQuickView(slug) {
    var $modal = $('#qb-qv-modal');
    var $overlay = $('#qb-qv-overlay');
    if (!$modal.length) {
      window.location.href = (window.STORE_BASE || '') + '/product/' + slug;
      return;
    }

    $modal.find('#qb-qv-title').text('Loading...');
    $modal.find('#qb-qv-plans').empty();
    $modal.add($overlay).removeAttr('hidden');
    $('body').addClass('qb-qv-open');

    request((window.STORE_BASE || '') + '/product/' + encodeURIComponent(slug) + '/quick', 'GET')
      .done(function (res) {
        var p = res.data || {};
        $('#qb-qv-title').text(p.name || '');
        $('#qb-qv-meta').text((p.sku ? 'SKU: ' + p.sku + ' · ' : '') + (p.stock || ''));
        $('#qb-qv-price').text(money(p.price));
        $('#qb-qv-desc').text(p.description || '');
        $('#qb-qv-image').attr('src', p.image || '').attr('alt', p.name || '');
        $('#qb-qv-details').attr('href', p.url || '#');
        $('#qb-qv-add').data('product-id', p.id);
        $('#qb-qv-qty').val(1);

        if (p.min_advance) {
          $('#qb-qv-advance').text('Rs. ' + Number(p.min_advance).toLocaleString() + ' Advance').removeAttr('hidden');
        } else {
          $('#qb-qv-advance').attr('hidden', true);
        }

        var plansHtml = '';
        (p.plans || []).forEach(function (plan, i) {
          plansHtml += '<label class="qb-plan' + (i === 0 ? ' is-active' : '') + '">'
            + '<input type="radio" name="qv_installment_plan_id" value="' + plan.id + '"' + (i === 0 ? ' checked' : '') + '>'
            + '<span class="qb-plan-body">'
            + '<strong>' + (plan.name || '') + '</strong>'
            + '<span>Down: ' + money(plan.down_payment) + '</span>'
            + '<span>Monthly: ' + money(plan.monthly_installment) + ' × ' + plan.months + '</span>'
            + '</span></label>';
        });
        $('#qb-qv-plans').html(plansHtml || '<p class="text-muted">No installment plans.</p>');
      })
      .fail(function () {
        closeQuickView();
      });
  }

  $(document).on('click', '.js-add-cart', function (e) {
    e.preventDefault();
    e.stopPropagation();
    var $btn = $(this);
    var productId = $btn.data('product-id');
    var qty = $btn.data('qty') || $('#product-qty').val() || $('#qb-qv-qty').val() || 1;
    var planId = $btn.data('plan-id')
      || $('input[name="installment_plan_id"]:checked').val()
      || $('input[name="qv_installment_plan_id"]:checked').val()
      || '';

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

  $(document).on('click', '.js-quick-view', function (e) {
    e.preventDefault();
    e.stopPropagation();
    openQuickView($(this).data('slug'));
  });

  $(document).on('click', '#qb-qv-close, #qb-qv-overlay', function () {
    closeQuickView();
  });

  $(document).on('keydown', function (e) {
    if (e.key === 'Escape') {
      closeQuickView();
    }
  });

  $(document).on('change', 'input[name="qv_installment_plan_id"]', function () {
    $('#qb-qv-plans .qb-plan').removeClass('is-active');
    $(this).closest('.qb-plan').addClass('is-active');
  });

  $(document).on('click', '#qb-qv-add', function (e) {
    e.preventDefault();
    var productId = $(this).data('product-id');
    if (!productId) {
      return;
    }
    request((window.STORE_BASE || '') + '/cart/add', 'POST', {
      product_id: productId,
      qty: $('#qb-qv-qty').val() || 1,
      plan_id: $('input[name="qv_installment_plan_id"]:checked').val() || ''
    }).done(function (res) {
      toast(res.message);
      if (res.data) {
        updateCartBadge(res.data.count, res.data.subtotal);
      }
      closeQuickView();
    });
  });

  // Prevent Riode theme from hijacking .btn-quickview / product-action links
  $(document).off('click', '.btn-quickview');

  function initProductSearch() {
    var $form = $('#qb-search-form');
    var $input = $('#qb-search-input');
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
        var advance = item.min_advance ? 'Rs. ' + Number(item.min_advance).toLocaleString() + ' Advance' : '';
        var $li = $('<li role="option"></li>');
        var $a = $('<a></a>').attr('href', item.url);
        $a.append(
          $('<img>').attr('src', item.image).attr('alt', item.name),
          $('<div class="qb-search-result-body"></div>').append(
            $('<strong></strong>').text(item.name),
            $('<span></span>').text(item.category_name || ''),
            advance ? $('<em></em>').text(item.price_label + ' · ' + advance) : $('<em></em>').text(item.price_label)
          )
        );
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
      clearTimeout(timer);
      timer = setTimeout(fetchSuggestions, 280);
    });

    $input.on('focus', function () {
      if (($input.val() || '').trim().length >= 2) {
        fetchSuggestions();
      }
    });

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
