/**
 * Searchable Pakistan city picker.
 * Usage: wrap a text input with class qb-city-input and data-qb-city-select,
 * and provide cities via window.PAKISTAN_CITIES = [...].
 */
(function ($) {
  'use strict';

  function ensureCities() {
    return Array.isArray(window.PAKISTAN_CITIES) ? window.PAKISTAN_CITIES : [];
  }

  function filterCities(query) {
    var cities = ensureCities();
    var q = String(query || '').trim().toLowerCase();
    if (!q) {
      return cities.slice(0, 80);
    }
    return cities.filter(function (city) {
      return String(city).toLowerCase().indexOf(q) !== -1;
    }).slice(0, 80);
  }

  function closeAll($except) {
    $('.qb-city-picker.is-open').each(function () {
      if ($except && this === $except[0]) return;
      $(this).removeClass('is-open');
      $(this).find('.qb-city-dropdown').attr('hidden', true);
    });
  }

  function renderOptions($picker, cities, activeIndex) {
    var $list = $picker.find('.qb-city-dropdown');
    if (!cities.length) {
      $list.html('<li class="qb-city-empty">No city found</li>').removeAttr('hidden');
      $picker.addClass('is-open');
      return;
    }
    var html = cities.map(function (city, i) {
      var cls = i === activeIndex ? ' is-active' : '';
      return '<li class="qb-city-option' + cls + '" data-city="' + $('<div>').text(city).html() + '" role="option">' + $('<div>').text(city).html() + '</li>';
    }).join('');
    $list.html(html).removeAttr('hidden');
    $picker.addClass('is-open');
  }

  function initInput($input) {
    if ($input.data('qb-city-ready')) return;
    $input.data('qb-city-ready', true);

    var $picker = $('<div class="qb-city-picker"></div>');
    var $dropdown = $('<ul class="qb-city-dropdown" role="listbox" hidden></ul>');
    $input.addClass('qb-city-input').attr('autocomplete', 'off').attr('role', 'combobox').attr('aria-autocomplete', 'list');
    $input.wrap($picker);
    $picker = $input.parent();
    $picker.append($dropdown);

    var activeIndex = -1;
    var currentMatches = [];

    function refresh(open) {
      currentMatches = filterCities($input.val());
      activeIndex = currentMatches.length ? 0 : -1;
      if (open) {
        renderOptions($picker, currentMatches, activeIndex);
      }
    }

    function pick(city) {
      $input.val(city).trigger('change');
      closeAll();
    }

    $input.on('focus', function () {
      refresh(true);
    });

    $input.on('input', function () {
      refresh(true);
    });

    $input.on('keydown', function (e) {
      if (!$picker.hasClass('is-open')) {
        if (e.key === 'ArrowDown' || e.key === 'Enter') {
          refresh(true);
        }
        return;
      }
      if (e.key === 'ArrowDown') {
        e.preventDefault();
        if (!currentMatches.length) return;
        activeIndex = (activeIndex + 1) % currentMatches.length;
        renderOptions($picker, currentMatches, activeIndex);
      } else if (e.key === 'ArrowUp') {
        e.preventDefault();
        if (!currentMatches.length) return;
        activeIndex = (activeIndex - 1 + currentMatches.length) % currentMatches.length;
        renderOptions($picker, currentMatches, activeIndex);
      } else if (e.key === 'Enter') {
        if (activeIndex >= 0 && currentMatches[activeIndex]) {
          e.preventDefault();
          pick(currentMatches[activeIndex]);
        }
      } else if (e.key === 'Escape') {
        closeAll();
      }
    });

    $dropdown.on('mousedown', '.qb-city-option', function (e) {
      e.preventDefault();
      pick($(this).data('city'));
    });

    $input.on('blur', function () {
      setTimeout(function () {
        closeAll();
        // Snap to nearest valid city if typed value matches ignore-case
        var val = $.trim($input.val() || '');
        if (!val) return;
        var match = ensureCities().find(function (city) {
          return String(city).toLowerCase() === val.toLowerCase();
        });
        if (match) {
          $input.val(match);
        }
      }, 150);
    });
  }

  function boot() {
    $('input[data-qb-city-select]').each(function () {
      initInput($(this));
    });
  }

  $(document).on('click', function (e) {
    if (!$(e.target).closest('.qb-city-picker').length) {
      closeAll();
    }
  });

  $(boot);
  window.QbCitySelect = { init: boot };
})(jQuery);
