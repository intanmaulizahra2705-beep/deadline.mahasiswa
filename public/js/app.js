/* Matcha Strawberry — interaksi & animasi */
(function () {
  'use strict';

  var root = document.documentElement;
  var safe = {
    get: function (k) { try { return localStorage.getItem(k); } catch (e) { return null; } },
    set: function (k, v) { try { localStorage.setItem(k, v); } catch (e) { /* abaikan */ } }
  };

  /* ---------- Tema gelap / terang ---------- */
  var saved = safe.get('theme');
  if (saved) root.setAttribute('data-theme', saved);
  function syncThemeIcon() {
    var dark = root.getAttribute('data-theme') === 'dark';
    document.querySelectorAll('[data-theme-toggle]').forEach(function (b) { b.textContent = dark ? '☀️' : '🌙'; });
  }
  document.addEventListener('click', function (e) {
    var t = e.target.closest('[data-theme-toggle]');
    if (!t) return;
    var next = root.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
    root.setAttribute('data-theme', next);
    safe.set('theme', next);
    syncThemeIcon();
  });
  syncThemeIcon();

  /* ---------- Sidebar mobile ---------- */
  var sb = document.querySelector('.sidebar'), scrim = document.querySelector('.scrim');
  function toggleSb(open) {
    if (!sb) return;
    sb.classList.toggle('open', open);
    if (scrim) scrim.classList.toggle('open', open);
  }
  document.addEventListener('click', function (e) {
    if (e.target.closest('[data-menu]')) toggleSb(!sb.classList.contains('open'));
    else if (e.target.closest('.scrim')) toggleSb(false);
  });

  /* ---------- Toast ---------- */
  function toast(type, msg) {
    var box = document.querySelector('.toasts');
    if (!box) { box = document.createElement('div'); box.className = 'toasts'; document.body.appendChild(box); }
    var el = document.createElement('div');
    el.className = 'toast ' + (type === 'ok' ? '' : type);
    el.textContent = msg;
    box.appendChild(el);
    setTimeout(function () { el.classList.add('out'); setTimeout(function () { el.remove(); }, 450); }, 3800);
  }
  window.toast = toast;
  var flash = document.querySelector('[data-toast]');
  if (flash) toast(flash.getAttribute('data-type'), flash.getAttribute('data-toast'));

  /* ---------- Reveal saat scroll ---------- */
  var reveals = document.querySelectorAll('.reveal');
  if ('IntersectionObserver' in window) {
    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (en) { if (en.isIntersecting) { en.target.classList.add('in'); io.unobserve(en.target); } });
    }, { threshold: .12 });
    reveals.forEach(function (el, i) { if (!el.style.getPropertyValue('--d')) el.style.setProperty('--d', Math.min(i % 6, 5) * 0.06 + 's'); io.observe(el); });
  } else {
    reveals.forEach(function (el) { el.classList.add('in'); });
  }

  /* ---------- Angka naik pelan-pelan ---------- */
  document.querySelectorAll('[data-count]').forEach(function (el) {
    var target = parseFloat(el.getAttribute('data-count')) || 0, dur = 900, t0 = null;
    function step(ts) {
      if (!t0) t0 = ts;
      var p = Math.min((ts - t0) / dur, 1), eased = 1 - Math.pow(1 - p, 3);
      el.textContent = Math.round(target * eased);
      if (p < 1) requestAnimationFrame(step);
    }
    requestAnimationFrame(step);
  });

  /* ---------- Progress bar & chart animasi ---------- */
  requestAnimationFrame(function () {
    setTimeout(function () {
      document.querySelectorAll('.bar > i[data-w]').forEach(function (i) { i.style.width = i.getAttribute('data-w') + '%'; });
      document.querySelectorAll('.week .b[data-h]').forEach(function (b) { b.style.height = b.getAttribute('data-h') + '%'; });
    }, 120);
  });

  /* ---------- Hitung mundur real-time ---------- */
  function fmt(ms) {
    var late = ms <= 0, s = Math.floor(Math.abs(ms) / 1000);
    var d = Math.floor(s / 86400), h = Math.floor(s % 86400 / 3600), m = Math.floor(s % 3600 / 60), sc = s % 60;
    var p = function (n) { return n < 10 ? '0' + n : n; };
    var txt = d > 0 ? d + 'h ' + p(h) + ':' + p(m) + ':' + p(sc) : p(h) + ':' + p(m) + ':' + p(sc);
    return (late ? 'Telat ' : '') + txt;
  }
  var cds = document.querySelectorAll('[data-deadline]');
  function tick() {
    var now = Date.now();
    cds.forEach(function (el) {
      var ms = new Date(el.getAttribute('data-deadline')).getTime() - now;
      el.textContent = fmt(ms);
      el.classList.toggle('late', ms <= 0);
    });
  }
  if (cds.length) { tick(); setInterval(tick, 1000); }

  /* ---------- Ubah status tugas tanpa reload ---------- */
  document.addEventListener('click', function (e) {
    var btn = e.target.closest('[data-status]');
    if (!btn) return;
    e.preventDefault();
    var url = btn.getAttribute('data-url'), status = btn.getAttribute('data-status');
    var token = document.querySelector('meta[name="csrf-token"]');
    btn.disabled = true;
    fetch(url, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': token ? token.content : '' },
      body: JSON.stringify({ status: status })
    }).then(function (r) { return r.json(); }).then(function (j) {
      toast('ok', j.pesan || 'Status diperbarui');
      var card = btn.closest('.task');
      if (card) {
        card.style.opacity = '.4';
        card.style.transform = 'scale(.98)';
      }
      setTimeout(function () { location.reload(); }, 650);
    }).catch(function () { btn.disabled = false; toast('err', 'Gagal mengubah status, coba lagi.'); });
  });

  /* ---------- Modal ---------- */
  document.addEventListener('click', function (e) {
    var o = e.target.closest('[data-open]');
    if (o) { var m = document.getElementById(o.getAttribute('data-open')); if (m) m.classList.add('open'); return; }
    if (e.target.closest('[data-close]') || e.target.classList.contains('modal')) {
      var mm = e.target.closest('.modal'); if (mm) mm.classList.remove('open');
    }
  });
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') document.querySelectorAll('.modal.open').forEach(function (m) { m.classList.remove('open'); });
  });
  var autoOpen = document.querySelector('.modal[data-auto-open]');
  if (autoOpen) autoOpen.classList.add('open');

  /* ---------- Salin kode kelas ---------- */
  document.addEventListener('click', function (e) {
    var c = e.target.closest('[data-copy]');
    if (!c) return;
    var v = c.getAttribute('data-copy');
    if (navigator.clipboard) navigator.clipboard.writeText(v).then(function () { toast('ok', 'Kode ' + v + ' disalin 📋'); });
  });

  /* ---------- Slider (nilai langsung) ---------- */
  document.querySelectorAll('input[type=range][data-out]').forEach(function (r) {
    var out = document.getElementById(r.getAttribute('data-out'));
    function up() { if (out) out.textContent = r.value; }
    r.addEventListener('input', up); up();
  });

  /* ---------- Simulator skor prioritas (halaman Algoritma) ---------- */
  var sim = document.getElementById('sim');
  if (sim) {
    var W = JSON.parse(sim.getAttribute('data-bobot')), WIN = +sim.getAttribute('data-jendela'), BMAX = +sim.getAttribute('data-bmaks');
    var BM = +sim.getAttribute('data-mendesak'), BS = +sim.getAttribute('data-sedang');
    var q = function (id) { return document.getElementById(id); };
    function calc() {
      var jam = +q('s-jam').value, bobot = +q('s-bobot').value, sulit = +q('s-sulit').value;
      var u = jam <= 0 ? 100 : Math.max(0, Math.min(100, 100 * (1 - jam / (WIN * 24))));
      var n = Math.min(bobot / BMAX, 1) * 100, k = sulit / 5 * 100;
      var skor = W.urgensi * u + W.nilai * n + W.kesulitan * k;
      var lv = skor >= BM ? ['mendesak', 'Mendesak'] : skor >= BS ? ['sedang', 'Sedang'] : ['aman', 'Aman'];
      q('o-u').textContent = u.toFixed(1); q('o-n').textContent = n.toFixed(1); q('o-k').textContent = k.toFixed(1);
      q('o-skor').textContent = skor.toFixed(1);
      var b = q('o-badge'); b.className = 'badge ' + lv[0]; b.textContent = lv[1];
      q('o-bar').style.width = skor + '%';
      q('v-jam').textContent = jam >= 24 ? Math.floor(jam / 24) + ' hari ' + (jam % 24) + ' jam' : jam + ' jam';
    }
    ['s-jam', 's-bobot', 's-sulit'].forEach(function (id) { q(id).addEventListener('input', calc); });
    calc();
  }

  /* ---------- Efek miring halus pada hero mock (landing) ---------- */
  var mock = document.querySelector('.hero-art .mock');
  if (mock && window.matchMedia('(pointer:fine)').matches) {
    document.addEventListener('mousemove', function (e) {
      var x = (e.clientX / window.innerWidth - .5) * 8, y = (e.clientY / window.innerHeight - .5) * -8;
      mock.style.transform = 'perspective(900px) rotateY(' + x + 'deg) rotateX(' + y + 'deg)';
    });
  }
})();
