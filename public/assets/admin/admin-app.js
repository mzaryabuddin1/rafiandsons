window.AdminApp = (function ($) {
  'use strict';

  function csrfToken() {
    return $('meta[name="csrf-token"]').attr('content') || '';
  }

  function csrfTokenName() {
    return $('meta[name="csrf-name"]').attr('content') || 'csrf_test_name';
  }

  function csrfHeader() {
    var name = $('meta[name="csrf-header"]').attr('content') || 'X-CSRF-TOKEN';
    var headers = {};
    headers[name] = csrfToken();
    return headers;
  }

  function toast(type, message) {
    if (window.toastr) {
      toastr.options = {
        closeButton: true,
        progressBar: true,
        positionClass: 'toast-top-right',
        timeOut: 3500
      };
      toastr[type](message);
      return;
    }
    alert(message);
  }

  function withCsrf(data) {
    var tokenName = csrfTokenName();
    var token = csrfToken();

    if (typeof FormData !== 'undefined' && data instanceof FormData) {
      if (!data.has(tokenName)) {
        data.append(tokenName, token);
      }
      return data;
    }

    if (typeof data === 'string') {
      if (data.indexOf(tokenName + '=') === -1) {
        data += (data.length ? '&' : '') + encodeURIComponent(tokenName) + '=' + encodeURIComponent(token);
      }
      return data;
    }

    data = data || {};
    if (!data[tokenName]) {
      data[tokenName] = token;
    }
    return data;
  }

  function request(url, method, data, options) {
    options = options || {};
    method = (method || 'GET').toUpperCase();
    var isFormData = typeof FormData !== 'undefined' && data instanceof FormData;
    var ajaxOpts = {
      url: url,
      method: method,
      dataType: 'json',
      headers: csrfHeader()
    };

    if (method === 'GET') {
      ajaxOpts.data = data || {};
    } else {
      ajaxOpts.data = withCsrf(data);
      if (isFormData) {
        ajaxOpts.processData = false;
        ajaxOpts.contentType = false;
      }
    }

    return $.ajax(ajaxOpts).fail(function (xhr) {
      var msg = 'Request failed';
      if (xhr.responseJSON && xhr.responseJSON.message) {
        msg = xhr.responseJSON.message;
      } else if (xhr.status === 401) {
        msg = 'Session expired. Please login again.';
        window.location.href = (window.ADMIN_BASE || '/admin') + '/login';
      } else if (xhr.status === 403) {
        msg = 'You do not have permission for this action.';
      } else if (xhr.status === 419 || xhr.status === 403) {
        msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : msg;
      }
      if (!options.silent) {
        toast('error', msg);
      }
    });
  }

  function confirmDelete(callback, message) {
    message = message || 'Are you sure you want to delete this record?';
    if (window.swal) {
      swal({
        title: 'Are you sure?',
        text: message,
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ED5565',
        confirmButtonText: 'Yes, delete it',
        closeOnConfirm: true
      }, function (isConfirm) {
        if (isConfirm) {
          callback();
        }
      });
      return;
    }
    if (window.confirm(message)) {
      callback();
    }
  }

  function setButtonLoading($btn, loading) {
    if (!$btn || !$btn.length) return;
    if (loading) {
      $btn.data('original-html', $btn.html());
      $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Please wait...');
    } else {
      $btn.prop('disabled', false).html($btn.data('original-html') || $btn.html());
    }
  }

  /**
   * Server-side paginated table helper.
   * options: {
   *   url, $tbody, $pager, $info, $search, filters: fn()|object,
   *   perPage, debounceMs, emptyCols, emptyText,
   *   renderRow: function(row) -> html string,
   *   onLoaded: function(data)
   * }
   */
  function createDataTable(options) {
    var state = {
      page: 1,
      perPage: options.perPage || 20,
      total: 0,
      lastPage: 1,
      searchTimer: null,
      loading: false
    };

    function filterParams() {
      var extra = typeof options.filters === 'function' ? options.filters() : (options.filters || {});
      return $.extend({}, extra, {
        page: state.page,
        per_page: state.perPage,
        search: options.$search && options.$search.length ? $.trim(options.$search.val() || '') : (extra.search || '')
      });
    }

    function renderPager() {
      if (!options.$pager || !options.$pager.length) return;
      if (state.lastPage <= 1 && state.total <= state.perPage) {
        options.$pager.html('');
        return;
      }
      var html = '<ul class="pagination admin-pager mb-0">';
      html += '<li class="' + (state.page <= 1 ? 'disabled' : '') + '"><a href="#" data-page="' + (state.page - 1) + '">&laquo;</a></li>';
      var start = Math.max(1, state.page - 2);
      var end = Math.min(state.lastPage, start + 4);
      start = Math.max(1, end - 4);
      for (var p = start; p <= end; p++) {
        html += '<li class="' + (p === state.page ? 'active' : '') + '"><a href="#" data-page="' + p + '">' + p + '</a></li>';
      }
      html += '<li class="' + (state.page >= state.lastPage ? 'disabled' : '') + '"><a href="#" data-page="' + (state.page + 1) + '">&raquo;</a></li>';
      html += '</ul>';
      options.$pager.html(html);
    }

    function renderInfo() {
      if (!options.$info || !options.$info.length) return;
      if (!state.total) {
        options.$info.text('No records found');
        return;
      }
      var from = ((state.page - 1) * state.perPage) + 1;
      var to = Math.min(state.page * state.perPage, state.total);
      options.$info.text('Showing ' + from + '–' + to + ' of ' + state.total);
    }

    function load(resetPage) {
      if (resetPage) state.page = 1;
      if (state.loading) return;
      state.loading = true;
      var params = filterParams();
      return request(options.url, 'GET', params).done(function (res) {
        var data = res.data || {};
        var items = data.items || [];
        state.total = parseInt(data.total, 10) || items.length;
        state.page = parseInt(data.page, 10) || state.page;
        state.perPage = parseInt(data.per_page, 10) || state.perPage;
        state.lastPage = parseInt(data.last_page, 10) || Math.max(1, Math.ceil(state.total / state.perPage));

        var html = '';
        items.forEach(function (row) {
          html += options.renderRow(row) || '';
        });
        if (!html) {
          var cols = options.emptyCols || 8;
          html = '<tr><td colspan="' + cols + '" class="text-center text-muted">' + (options.emptyText || 'No records found') + '</td></tr>';
        }
        options.$tbody.html(html);
        renderPager();
        renderInfo();
        if (typeof options.onLoaded === 'function') {
          options.onLoaded(data);
        }
      }).always(function () {
        state.loading = false;
      });
    }

    function exportCsv() {
      var params = filterParams();
      delete params.page;
      delete params.per_page;
      params.export = 'csv';
      var qs = $.param(params);
      window.location.href = options.url + (options.url.indexOf('?') >= 0 ? '&' : '?') + qs;
    }

    if (options.$pager && options.$pager.length) {
      options.$pager.on('click', 'a[data-page]', function (e) {
        e.preventDefault();
        var $li = $(this).closest('li');
        if ($li.hasClass('disabled') || $li.hasClass('active')) return;
        var page = parseInt($(this).data('page'), 10);
        if (!page || page < 1 || page > state.lastPage) return;
        state.page = page;
        load(false);
      });
    }

    if (options.$search && options.$search.length) {
      options.$search.on('keyup input', function () {
        clearTimeout(state.searchTimer);
        state.searchTimer = setTimeout(function () {
          load(true);
        }, options.debounceMs || 350);
      });
    }

    return {
      load: load,
      reload: function () { return load(false); },
      exportCsv: exportCsv,
      state: state,
      filterParams: filterParams
    };
  }

  return {
    request: request,
    toast: toast,
    confirmDelete: confirmDelete,
    setButtonLoading: setButtonLoading,
    csrfToken: csrfToken,
    createDataTable: createDataTable
  };
})(jQuery);
