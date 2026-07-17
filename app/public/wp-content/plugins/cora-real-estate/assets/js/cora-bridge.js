/**
 * Cora Data Bridge v1.0
 * Injected automatically into all Lovable-served pages.
 * Wires data-cora-* attributes to Cora's backend REST API.
 * Requires: window.CORA_API_URL, window.CORA_NONCE (injected by cora-real-estate.php)
 */
(function () {
  'use strict';

  var CORA_BRIDGE_VERSION = '1.0.0';

  // ── Utilities ──────────────────────────────────────────────────────────────

  function api(endpoint, opts) {
    var base = (window.CORA_API_URL || '').replace(/\/$/, '');
    var url  = base + '/' + endpoint.replace(/^\//, '');
    var headers = { 'Content-Type': 'application/json' };
    if (window.CORA_NONCE) headers['X-WP-Nonce'] = window.CORA_NONCE;
    return fetch(url, Object.assign({ headers: headers }, opts || {}))
      .then(function (r) { return r.json(); });
  }

  function qs(el, sel) { return el.querySelector(sel); }
  function qsa(el, sel) { return Array.prototype.slice.call(el.querySelectorAll(sel)); }

  // ── Property Listings ──────────────────────────────────────────────────────

  function renderPropertyCard(prop) {
    return '<div class="cora-property-card" data-id="' + prop.id + '">' +
      (prop.image ? '<img src="' + prop.image + '" alt="' + (prop.title || '') + '" style="width:100%;aspect-ratio:16/9;object-fit:cover;border-radius:8px 8px 0 0;">' : '') +
      '<div style="padding:16px">' +
        '<div style="font-size:18px;font-weight:700;margin-bottom:4px">' + (prop.title || 'Untitled') + '</div>' +
        '<div style="font-size:13px;color:#6b7280;margin-bottom:8px">' + (prop.address || '') + '</div>' +
        '<div style="display:flex;gap:16px;font-size:12px;color:#374151">' +
          (prop.price ? '<span style="font-weight:700;font-size:16px">₹' + prop.price + '</span>' : '') +
          (prop.bedrooms ? '<span>' + prop.bedrooms + ' Beds</span>' : '') +
          (prop.bathrooms ? '<span>' + prop.bathrooms + ' Baths</span>' : '') +
          (prop.area ? '<span>' + prop.area + ' sq.ft</span>' : '') +
        '</div>' +
      '</div>' +
    '</div>';
  }

  function initPropertyGrid(el) {
    var limit = el.getAttribute('data-cora-limit') || 12;
    var type  = el.getAttribute('data-cora-type') || '';
    var endpoint = 'properties?per_page=' + limit + (type ? '&type=' + type : '');
    el.innerHTML = '<div style="text-align:center;padding:40px;color:#9ca3af;font-size:13px">Loading properties…</div>';
    api(endpoint)
      .then(function (data) {
        var items = Array.isArray(data) ? data : (data.data || []);
        if (!items.length) {
          el.innerHTML = '<div style="text-align:center;padding:40px;color:#9ca3af">No properties found.</div>';
          return;
        }
        var cols = el.getAttribute('data-cora-cols') || '3';
        el.style.display = 'grid';
        el.style.gridTemplateColumns = 'repeat(' + cols + ', 1fr)';
        el.style.gap = '24px';
        el.innerHTML = items.map(renderPropertyCard).join('');
      })
      .catch(function () {
        el.innerHTML = '<div style="text-align:center;padding:40px;color:#ef4444">Could not load properties.</div>';
      });
  }

  // ── Lead Capture Form ──────────────────────────────────────────────────────

  function initLeadForm(formEl) {
    formEl.addEventListener('submit', function (e) {
      e.preventDefault();
      var btn = qs(formEl, '[type=submit]');
      var data = {};
      qsa(formEl, '[name]').forEach(function (inp) { data[inp.name] = inp.value; });

      if (btn) { btn.disabled = true; btn.textContent = 'Sending…'; }

      api('leads', {
        method: 'POST',
        body: JSON.stringify(data)
      }).then(function (res) {
        if (res && (res.success || res.id)) {
          formEl.innerHTML = '<div style="text-align:center;padding:32px;color:#16a34a;font-weight:600">✓ Thank you! We\'ll be in touch soon.</div>';
        } else {
          if (btn) { btn.disabled = false; btn.textContent = 'Submit'; }
          showBridgeError(formEl, 'Submission failed. Please try again.');
        }
      }).catch(function () {
        if (btn) { btn.disabled = false; btn.textContent = 'Submit'; }
        showBridgeError(formEl, 'Network error. Please try again.');
      });
    });
  }

  // ── Agent Bio / Profile ────────────────────────────────────────────────────

  function initAgentCard(el) {
    var agentId = el.getAttribute('data-cora-id') || '';
    var endpoint = agentId ? 'agents/' + agentId : 'agents?per_page=1';
    api(endpoint).then(function (data) {
      var agent = Array.isArray(data) ? data[0] : data;
      if (!agent) return;
      var existing = qs(el, '[data-cora-field]');
      if (existing) {
        qsa(el, '[data-cora-field]').forEach(function (f) {
          var field = f.getAttribute('data-cora-field');
          if (agent[field] !== undefined) f.textContent = agent[field];
        });
      } else {
        el.innerHTML =
          '<div style="display:flex;align-items:center;gap:16px;padding:16px">' +
            (agent.avatar ? '<img src="' + agent.avatar + '" style="width:64px;height:64px;border-radius:50%;object-fit:cover">' : '') +
            '<div><div style="font-weight:700">' + (agent.name || '') + '</div>' +
            '<div style="font-size:12px;color:#6b7280">' + (agent.designation || 'Agent') + '</div></div>' +
          '</div>';
      }
    });
  }

  // ── Search Bar ─────────────────────────────────────────────────────────────

  function initSearchBar(el) {
    var input = qs(el, 'input[type=text],input[type=search]') || el;
    var target = el.getAttribute('data-cora-target');
    var targetEl = target ? document.querySelector(target) : null;

    input.addEventListener('input', debounce(function () {
      var q = input.value.trim();
      if (!q || !targetEl) return;
      api('properties?search=' + encodeURIComponent(q)).then(function (data) {
        var items = Array.isArray(data) ? data : (data.data || []);
        targetEl.style.display = 'grid';
        targetEl.innerHTML = items.length
          ? items.map(renderPropertyCard).join('')
          : '<div style="grid-column:1/-1;text-align:center;padding:32px;color:#9ca3af">No results for "' + q + '"</div>';
      });
    }, 400));
  }

  // ── Testimonials ───────────────────────────────────────────────────────────

  function initTestimonials(el) {
    var limit = el.getAttribute('data-cora-limit') || 6;
    api('testimonials?per_page=' + limit).then(function (data) {
      var items = Array.isArray(data) ? data : (data.data || []);
      if (!items.length) return;
      el.innerHTML = items.map(function (t) {
        return '<div style="padding:24px;background:#f9fafb;border-radius:12px">' +
          '<div style="font-size:32px;color:#d1d5db;line-height:1">&ldquo;</div>' +
          '<div style="font-size:14px;color:#374151;margin:8px 0 16px">' + (t.content || t.review || '') + '</div>' +
          '<div style="font-size:12px;font-weight:700">' + (t.author || t.name || 'Customer') + '</div>' +
        '</div>';
      }).join('');
    });
  }

  // ── Generic Field Injection ────────────────────────────────────────────────

  function initFieldInjector(el) {
    var src    = el.getAttribute('data-cora-inject');
    var field  = el.getAttribute('data-cora-field');
    var id     = el.getAttribute('data-cora-id') || '';
    var endpoint = src + (id ? '/' + id : '?per_page=1');
    api(endpoint).then(function (data) {
      var item = Array.isArray(data) ? data[0] : data;
      if (!item) return;
      if (field && item[field] !== undefined) {
        el.textContent = item[field];
      }
    });
  }

  // ── Booking / Contact Button ───────────────────────────────────────────────

  function initBookingButton(el) {
    el.addEventListener('click', function (e) {
      e.preventDefault();
      var propertyId = el.getAttribute('data-cora-property') || '';
      var payload = { type: 'booking_request', property_id: propertyId, timestamp: new Date().toISOString() };
      api('bookings', { method: 'POST', body: JSON.stringify(payload) })
        .then(function (res) {
          if (res && (res.success || res.id)) {
            el.textContent = '✓ Request Sent!';
            el.style.background = '#16a34a';
          }
        });
    });
  }

  // ── Helpers ────────────────────────────────────────────────────────────────

  function showBridgeError(parent, msg) {
    var div = document.createElement('div');
    div.style.cssText = 'color:#ef4444;font-size:12px;margin-top:8px;text-align:center';
    div.textContent = msg;
    parent.appendChild(div);
    setTimeout(function () { div.remove(); }, 5000);
  }

  function debounce(fn, ms) {
    var t;
    return function () {
      clearTimeout(t);
      t = setTimeout(fn, ms);
    };
  }

  // ── Auto-fill hidden nonce inputs ──────────────────────────────────────────

  function initNonceInputs() {
    qsa(document, '#cora-nonce,[name="cora_nonce"]').forEach(function (el) {
      el.value = window.CORA_NONCE || '';
    });
  }

  // ── Mount ──────────────────────────────────────────────────────────────────

  function mount() {
    initNonceInputs();

    qsa(document, '[data-cora-inject="properties"]').forEach(initPropertyGrid);
    qsa(document, '[data-cora-inject="testimonials"]').forEach(initTestimonials);
    qsa(document, '[data-cora-inject="agent"]').forEach(initAgentCard);

    qsa(document, 'form[data-cora-inject="lead-form"],form[data-cora-inject="leads"]').forEach(initLeadForm);

    qsa(document, '[data-cora-search="true"]').forEach(initSearchBar);
    qsa(document, '[data-cora-inject="booking-button"],[data-cora-inject="booking"]').forEach(initBookingButton);

    // Generic field injectors — anything with data-cora-inject + data-cora-field on a non-container element
    qsa(document, '[data-cora-inject][data-cora-field]').forEach(function (el) {
      var inject = el.getAttribute('data-cora-inject');
      if (!['properties','testimonials','agent','lead-form','leads','booking','booking-button'].includes(inject)) {
        initFieldInjector(el);
      }
    });

    window.CORA_BRIDGE_MOUNTED = true;
    window.dispatchEvent(new CustomEvent('cora:bridge:ready', { detail: { version: CORA_BRIDGE_VERSION } }));
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', mount);
  } else {
    mount();
  }

  // Re-run on React/SPA navigation (history pushState)
  var _origPush = history.pushState;
  history.pushState = function () {
    _origPush.apply(history, arguments);
    setTimeout(mount, 300);
  };
  window.addEventListener('popstate', function () { setTimeout(mount, 300); });

})();
