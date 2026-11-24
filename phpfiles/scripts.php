<?php
header('Content-Type: application/javascript; charset=utf-8');
header('Cache-Control: no-store');
?>
/* AtClam PHP UI - browser behavior. Mirrors the Flutter screens' interactions. */
(function () {
  'use strict';

  var $ = function (sel, root) { return (root || document).querySelector(sel); };
  var $$ = function (sel, root) { return Array.prototype.slice.call((root || document).querySelectorAll(sel)); };

  function notify(message, isError) {
    var bar = $('#snackbar');
    if (!bar) {
      bar = document.createElement('div');
      bar.className = 'snackbar';
      bar.id = 'snackbar';
      document.body.appendChild(bar);
    }
    bar.textContent = message;
    bar.classList.toggle('error', !!isError);
    bar.classList.add('show');
    clearTimeout(bar._timer);
    bar._timer = setTimeout(function () { bar.classList.remove('show'); }, 4000);
  }
  window.atclamNotify = notify;
  var initialSnack = $('#snackbar');
  if (initialSnack) {
    clearTimeout(initialSnack._timer);
    initialSnack._timer = setTimeout(function () { initialSnack.classList.remove('show'); }, 4000);
  }

  /* ---------- API proxy (keeps the session token on the server) ---------- */
  function apiFetch(path, method, body) {
    method = method || 'GET';
    var init = {
      method: method,
      credentials: 'same-origin',
      headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
    };
    if (body !== undefined && body !== null && method !== 'GET') {
      init.headers['Content-Type'] = 'application/json';
      init.body = JSON.stringify(body);
    }
    var url = 'api.php?path=' + encodeURIComponent(path);
    if (method !== 'GET') url += '&method=' + encodeURIComponent(method);
    return fetch(url, init).then(function (res) {
      return res.text().then(function (text) {
        var data = null;
        try { data = text ? JSON.parse(text) : null; } catch (e) { data = { detail: text }; }
        if (!res.ok) {
          var msg = data && (data.detail || data.error || data.message) || ('Request failed (' + res.status + ')');
          if (Array.isArray(msg)) {
            msg = msg.map(function (x) { return x && x.msg ? x.msg : x; }).join('\n');
          }
          throw new Error(String(msg).replace(/^Value error,\s*/, ''));
        }
        return data;
      });
    });
  }
  window.atclamApi = apiFetch;

  /* ---------- Dialogs ---------- */
  function showDialog(el) {
    if (!el) return;
    if (typeof el.showModal === 'function') {
      if (!el.open) el.showModal();
    } else {
      el.setAttribute('open', '');
    }
  }
  function closeDialog(id) {
    var el = typeof id === 'string' ? document.getElementById(id) : id;
    if (!el) return;
    if (typeof el.close === 'function' && el.open) el.close();
    else el.removeAttribute('open');
  }
  window.openDialog = function (target) {
    if (!target) return;
    // Supports a plain dialog id, "#dialogId", and "#dialogId?field=value".
    var raw = String(target).replace(/^#/, '');
    var parts = raw.split('?');
    var id = parts[0];
    var el = document.getElementById(id);
    if (!el) { notify('Dialog not found: ' + id, true); return; }
    if (parts[1]) {
      new URLSearchParams(parts[1]).forEach(function (value, key) {
        var input = el.querySelector('[name="' + key + '"]');
        if (input) input.value = value;
        var text = el.querySelector('[data-dialog-text="' + key + '"]');
        if (text) text.textContent = value;
        if (el.dataset.formAction) el.querySelector('form').action = el.dataset.formAction.replace('{id}', value);
      });
    }
    showDialog(el);
  };
  window.closeDialog = closeDialog;
  $$('[data-close-dialog]').forEach(function (btn) {
    btn.addEventListener('click', function () { closeDialog(btn.getAttribute('data-close-dialog')); });
  });
  $$('.flutter-dialog, .notify-dialog').forEach(function (dlg) {
    dlg.addEventListener('click', function (e) {
      if (e.target === dlg) closeDialog(dlg.id);
    });
  });

  /* ---------- Password visibility ---------- */
  $$('.pass-toggle').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var input = document.getElementById(btn.dataset.target);
      if (!input) return;
      input.type = input.type === 'password' ? 'text' : 'password';
      btn.setAttribute('aria-label', input.type === 'password' ? 'Show password' : 'Hide password');
      btn.classList.toggle('visible', input.type === 'text');
    });
  });

  /* ---------- Account menu, drawer, server address ---------- */
  var accountBtn = $('#accountBtn');
  var accountMenu = $('#accountMenu');
  if (accountBtn && accountMenu) {
    accountBtn.addEventListener('click', function (e) {
      e.stopPropagation();
      accountMenu.classList.toggle('open');
    });
    document.addEventListener('click', function (e) {
      if (!accountMenu.contains(e.target) && e.target !== accountBtn) accountMenu.classList.remove('open');
    });
    $$('[data-menu]', accountMenu).forEach(function (item) {
      item.addEventListener('click', function () {
        var action = item.getAttribute('data-menu');
        accountMenu.classList.remove('open');
        if (action === 'password') location.href = 'change_password.php';
        else if (action === 'photos') location.href = 'face_registration.php?view=photos';
        else if (action === 'server') showDialog($('#serverDialog'));
        else if (action === 'logout') location.href = 'index.php?action=logout';
      });
    });
  }
  var drawerToggle = $('#drawerToggle');
  var sidebar = $('#sidebar');
  if (drawerToggle && sidebar) {
    drawerToggle.addEventListener('click', function (e) {
      e.stopPropagation();
      sidebar.classList.toggle('open');
    });
    document.addEventListener('click', function (e) {
      if (window.innerWidth <= 819 && sidebar.classList.contains('open') &&
          !sidebar.contains(e.target) && !drawerToggle.contains(e.target)) {
        sidebar.classList.remove('open');
      }
    });
  }
  window.saveBackendUrl = function () {
    var input = $('[name="backend_url"]');
    if (!input || !input.value.trim()) { notify('Enter a backend URL', true); return; }
    document.cookie = 'atclam_backend=' + encodeURIComponent(input.value.trim()) + '; path=/; max-age=' + (60 * 60 * 24 * 365) + '; SameSite=Lax';
    closeDialog('serverDialog');
    notify('Server address saved. Reloading…');
    setTimeout(function () { location.reload(); }, 500);
  };

  /* ---------- Copy class codes ---------- */
  function copyText(text) {
    if (navigator.clipboard && navigator.clipboard.writeText) return navigator.clipboard.writeText(text);
    return new Promise(function (resolve, reject) {
      var ta = document.createElement('textarea');
      ta.value = text;
      ta.style.position = 'fixed';
      ta.style.opacity = '0';
      document.body.appendChild(ta);
      ta.select();
      try { document.execCommand('copy'); resolve(); } catch (e) { reject(e); }
      document.body.removeChild(ta);
    });
  }
  $$('.copy-code').forEach(function (btn) {
    btn.addEventListener('click', function (e) {
      e.preventDefault();
      e.stopPropagation();
      copyText(btn.dataset.code || btn.textContent.trim()).then(function () {
        notify('Class code copied');
      }).catch(function () { notify('Could not copy the code', true); });
    });
  });

  /* ---------- Notification bell ---------- */
  var notifyDialog = $('#notifyDialog');
  var bellBtn = $('#bellBtn');
  function renderNotifications(data) {
    var list = $('#notifyList');
    if (!list) return;
    var items = (data && data.items) || [];
    var badge = $('#notifyBadge');
    var unread = Number((data && data.unread) || 0);
    if (badge) {
      badge.textContent = unread;
      badge.hidden = unread === 0;
    }
    if (!items.length) {
      list.innerHTML = '<div class="empty-state">No notifications yet.</div>';
      return;
    }
    list.innerHTML = '';
    items.forEach(function (item) {
      var btn = document.createElement('button');
      btn.type = 'button';
      btn.className = 'notification-item';
      btn.dataset.id = item.id;
      if (item.class_id) btn.dataset.classId = item.class_id;
      var iconName = item.kind === 'excuse' ? 'mail' : 'campaign';
      btn.innerHTML = '<svg class="icon" width="22" height="22" viewBox="0 0 24 24" fill="currentColor" style="color:' +
        (item.read ? '#8FAE99' : '#F5C518') + '"><path d="M20 2H4a2 2 0 0 0-2 2v18l4-4h14a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2z"/></svg>' +
        '<span><strong></strong><small></small></span>';
      $('strong', btn).textContent = item.title || '';
      $('small', btn).textContent = (item.body || '') + ' · ' + (item.time || '');
      list.appendChild(btn);
    });
  }
  function refreshNotifications(silent) {
    return apiFetch('/notifications').then(function (data) {
      renderNotifications(data);
    }).catch(function (e) { if (!silent) notify(e.message, true); });
  }
  if (bellBtn && notifyDialog) {
    bellBtn.addEventListener('click', function () {
      showDialog(notifyDialog);
      refreshNotifications(true);
    });
    if (window.location.href.indexOf('login.php') === -1) {
      refreshNotifications(true);
      setInterval(function () { refreshNotifications(true); }, 20000);
    }
    var markAll = $('#markAllRead');
    if (markAll) {
      markAll.addEventListener('click', function () {
        apiFetch('/notifications/read', 'POST', { all: true }).then(function () {
          renderNotifications({ items: [], unread: 0 });
          notify('All notifications marked as read');
        }).catch(function (e) { notify(e.message, true); });
      });
    }
    document.addEventListener('click', function (e) {
      var item = e.target.closest('.notification-item');
      if (!item || !notifyDialog.contains(item)) return;
      var id = item.dataset.id;
      apiFetch('/notifications/read', 'POST', { id: Number(id) }).then(function () {
        item.style.opacity = '0.55';
        closeDialog(notifyDialog);
        refreshNotifications(true);
        var classId = item.dataset.classId;
        if (classId) location.href = 'class.php?id=' + encodeURIComponent(classId);
      }).catch(function (err) { notify(err.message, true); });
    });
  }

  /* ---------- Photo previews ---------- */
  $$('.photo-input').forEach(function (input) {
    input.addEventListener('change', function () {
      var target = document.getElementById(input.dataset.preview);
      if (!target || !input.files || !input.files[0]) return;
      var reader = new FileReader();
      reader.onload = function () {
        if (target.tagName === 'IMG') target.src = reader.result;
        else target.style.backgroundImage = 'url("' + reader.result + '")';
        target.hidden = false;
        target.classList.remove('empty');
        var row = input.closest('.face-pick-row');
        if (row) {
          var label = row.querySelector('.selected-label');
          if (label) label.textContent = ' selected';
          var lead = row.querySelector('.circle-avatar');
          if (lead) lead.innerHTML = '<svg class="icon" width="20" height="20" viewBox="0 0 24 24" fill="currentColor" style="color:#A8E063"><path d="m9 16.2-3.5-3.5L4 14.2 9 19 20 8l-1.4-1.4L9 16.2z"/></svg>';
        }
      };
      reader.readAsDataURL(input.files[0]);
      if (input.dataset.autoSubmit !== undefined && input.form) {
        if (input.form.requestSubmit) input.form.requestSubmit();
        else input.form.submit();
      }
    });
  });

  /* ---------- Live camera tab ---------- */
  var livePanel = $('#livePanel');
  if (livePanel) {
    var classId = livePanel.dataset.classId;
    var liveState = $('#liveState');
    var liveRoster = $('#liveRoster');
    var startBtn = $('#startSessionBtn');
    var stopBtn = $('#stopSessionBtn');
    function renderSession(data) {
      if (liveState) {
        var active = !!(data && data.active);
        liveState.textContent = active ? 'CLASS RUNNING' : 'NO CLASS RUNNING';
        liveState.className = active ? 'lime-pill' : 'gold-pill';
        if (startBtn) startBtn.disabled = active || !classId;
        if (stopBtn) stopBtn.disabled = !active;
        var select = $('#liveClassSelect');
        if (select) select.disabled = active;
        var modelState = $('#modelState');
        if (modelState) modelState.textContent = data && data.model_ready ? 'Model ready' : 'Model loading…';
      }
      if (liveRoster) {
        var roster = (data && data.roster) || [];
        liveRoster.innerHTML = '';
        if (!roster.length) {
          liveRoster.innerHTML = '<div class="empty-state">No students enrolled yet.</div>';
        } else {
          roster.forEach(function (s) {
            var row = document.createElement('div');
            row.className = 'roster-row';
            var state = (s.attendance_status || s.status || 'absent');
            row.innerHTML = '<span class="avatar small"></span><span class="roster-name"></span>';
            $('.avatar', row).textContent = String(s.name || s.student_name || '?').charAt(0).toUpperCase();
            $('.roster-name', row).textContent = s.name || s.student_name || s.email || '';
            var trailing = statusChip(state);
            if (s.time) trailing.textContent += ' ' + s.time;
            row.appendChild(trailing);
            liveRoster.appendChild(row);
          });
        }
      }
    }
    function statusChip(status) {
      var chip = document.createElement('span');
      chip.className = 'status-chip status-' + String(status).toLowerCase();
      chip.textContent = status ? status.charAt(0).toUpperCase() + status.slice(1) : 'Absent';
      return chip;
    }
    function refreshSession(silent) {
      return apiFetch('/attendance/session?class_id=' + encodeURIComponent(classId)).then(function (data) {
        renderSession(data);
      }).catch(function (e) { if (!silent) notify(e.message, true); });
    }
    var classSelect = $('#liveClassSelect');
    if (classSelect) classSelect.addEventListener('change', function () {
      classId = classSelect.value;
      livePanel.dataset.classId = classId;
      refreshSession(true);
    });
    if (startBtn) startBtn.addEventListener('click', function () {
      apiFetch('/attendance/session/start', 'POST', { class_id: String(classId) }).then(function () {
        notify('Class started'); refreshSession(true);
      }).catch(function (e) { notify(e.message, true); });
    });
    if (stopBtn) stopBtn.addEventListener('click', function () {
      apiFetch('/attendance/session/end', 'POST', {}).then(function () {
        notify('Class ended. Attendance saved.'); refreshSession(true);
      }).catch(function (e) { notify(e.message, true); });
    });
    refreshSession(true);
    setInterval(function () { refreshSession(true); }, 2000);
  }

  /* ---------- Scanner camera + face frames ---------- */
  var scanPanel = $('#scanPanel');
  if (scanPanel) {
    var classIdScan = scanPanel.dataset.classId;
    var video = $('#scannerVideo');
    var canvas = $('#scannerCanvas') || document.createElement('canvas');
    if (!canvas.id) { canvas.id = 'scannerCanvas'; canvas.hidden = true; scanPanel.appendChild(canvas); }
    var overlay = $('#faceOverlay');
    var scanRoster = $('#scanRoster');
    var scanStatus = $('#scanStatus');
    var scanning = false;
    var stream = null;
    var busy = false;

    function drawFaces(faces, roster) {
      if (!overlay) return;
      overlay.innerHTML = '';
      (faces || []).forEach(function (face) {
        var box = face.box || [];
        if (box.length < 4) return;
        var state = face.state || 'checking';
        var rect = document.createElement('div');
        rect.className = 'face-box face-' + state;
        var label = document.createElement('span');
        var name = face.name || face.student_name || (face.student_id ? ('ID ' + face.student_id) : state);
        label.textContent = name + (state === 'blink' ? ' · blink' : '');
        rect.style.left = (Number(box[0]) * 100) + '%';
        rect.style.top = (Number(box[1]) * 100) + '%';
        rect.style.width = ((Number(box[2]) - Number(box[0])) * 100) + '%';
        rect.style.height = ((Number(box[3]) - Number(box[1])) * 100) + '%';
        rect.appendChild(label);
        overlay.appendChild(rect);
      });
      var detected = $('#detectedFaces');
      if (detected) {
        detected.innerHTML = '';
        (faces || []).forEach(function (face) {
          var chip = document.createElement('span');
          var state = face.state || 'checking';
          chip.className = 'status-chip status-' + state;
          chip.textContent = face.name || face.student_name || state;
          detected.appendChild(chip);
        });
        var count = $('#detectedCount');
        if (count) count.textContent = (faces || []).length;
      }
      if (scanRoster) {
        scanRoster.innerHTML = '';
        (roster || []).forEach(function (s) {
          var row = document.createElement('div');
          row.className = 'roster-row';
          var status = s.attendance_status || s.status || 'absent';
          row.innerHTML = '<span class="avatar small"></span><span class="roster-name"></span>';
          $('.avatar', row).textContent = String(s.name || s.student_name || '?').charAt(0).toUpperCase();
          $('.roster-name', row).textContent = s.name || s.student_name || '';
          var trailing = statusChip(status);
          if (s.time) trailing.textContent += ' ' + s.time;
          row.appendChild(trailing);
          scanRoster.appendChild(row);
        });
      }
    }

    function sendFrame() {
      if (busy || !scanning || video.readyState < 2) return;
      busy = true;
      try {
        var w = 640;
        var h = Math.round((video.videoHeight / (video.videoWidth || 1)) * w) || 480;
        canvas.width = w;
        canvas.height = h;
        canvas.getContext('2d').drawImage(video, 0, 0, w, h);
        var dataUri = canvas.toDataURL('image/jpeg', 0.82);
        apiFetch('/attendance/frame', 'POST', { image: dataUri }).then(function (data) {
          drawFaces(data && data.faces, data && data.roster);
          if (scanStatus) {
            scanStatus.textContent = data && data.model_ready ? 'Face model ready' : 'Face model loading…';
          }
        }).catch(function () { /* keep scanning; transient frame errors are expected */ });
      } finally {
        busy = false;
      }
    }

    function startCamera() {
      if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
        notify('Camera is not available in this browser', true);
        return;
      }
      navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' }, audio: false }).then(function (s) {
        stream = s;
        video.srcObject = s;
        return video.play();
      }).then(function () {
        scanning = true;
        if (scanStatus) scanStatus.textContent = 'Camera running';
        timer = setInterval(sendFrame, 550);
      }).catch(function (e) { notify('Camera permission denied or unavailable', true); });
    }
    function stopCamera() {
      scanning = false;
      clearInterval(timer);
      if (stream) stream.getTracks().forEach(function (t) { t.stop(); });
      stream = null;
      video.srcObject = null;
      if (scanStatus) scanStatus.textContent = 'Camera stopped';
    }
    var timer = null;
    var startScanBtn = $('#startScanBtn');
    var stopScanBtn = $('#stopScanBtn');
    var resetScanBtn = $('#resetScanBtn');
    var endClassBtn = $('#endClassBtn');
    var startClassBtn = $('#startClassBtn');
    if (startScanBtn) startScanBtn.addEventListener('click', startCamera);
    if (stopScanBtn) stopScanBtn.addEventListener('click', stopCamera);
    if (resetScanBtn) resetScanBtn.addEventListener('click', function () {
      apiFetch('/attendance/frame/reset', 'POST', {}).then(function () {
        if (overlay) overlay.innerHTML = '';
        notify('Tracking reset');
      }).catch(function (e) { notify(e.message, true); });
    });
    if (startClassBtn) startClassBtn.addEventListener('click', function () {
      apiFetch('/attendance/session/start', 'POST', { class_id: String(classIdScan) }).then(function () {
        notify('Class started'); location.reload();
      }).catch(function (e) { notify(e.message, true); });
    });
    if (endClassBtn) endClassBtn.addEventListener('click', function () {
      stopCamera();
      apiFetch('/attendance/session/end', 'POST', {}).then(function () {
        notify('Class ended. Attendance saved.'); location.reload();
      }).catch(function (e) { notify(e.message, true); });
    });
    window.addEventListener('beforeunload', function () { if (stream) stopCamera(); });
    if (scanPanel.dataset.active === '1') startCamera();
  }

  /* ---------- Fetch-driven dialogs (e.g. delete confirmation) ---------- */
  $$('[data-load-dialog]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      fetch(btn.getAttribute('data-load-dialog'), { credentials: 'same-origin', headers: { 'X-Dialog': '1' } })
        .then(function (r) { return r.text(); })
        .then(function (html) {
          var holder = $('#fragmentDialog');
          if (!holder) {
            holder = document.createElement('dialog');
            holder.id = 'fragmentDialog';
            holder.className = 'flutter-dialog';
            document.body.appendChild(holder);
          }
          holder.innerHTML = html;
          showDialog(holder);
        });
    });
  });
})();
