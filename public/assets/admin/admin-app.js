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

  return {
    request: request,
    toast: toast,
    confirmDelete: confirmDelete,
    setButtonLoading: setButtonLoading,
    csrfToken: csrfToken
  };
})(jQuery);
