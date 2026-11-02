/* Roster & kartu "Kuliah hari ini" — penanda hari ini, sedang berlangsung, dan berikutnya */
(function () {
  'use strict';

  var HARI = [null, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
  var KEY = 'roster_kelas';

  function lsGet(k) { try { return localStorage.getItem(k); } catch (e) { return null; } }
  function lsSet(k, v) { try { localStorage.setItem(k, v); } catch (e) { /* abaikan */ } }
  function menit(s) { var p = String(s).split('.'); return (+p[0]) * 60 + (+p[1] || 0); }
  function readJson(id) {
    var n = document.getElementById(id);
    if (!n) return null;
    try { return JSON.parse(n.textContent); } catch (e) { return null; }
  }
  function mk(tag, cls, text) {
    var n = document.createElement(tag);
    if (cls) n.className = cls;
    if (text !== undefined && text !== null) n.textContent = text;
    return n;
  }
  function byJam(a, b) { return menit(a.mulai) - menit(b.mulai); }

  /* Kuliah yang sedang berlangsung, atau yang paling dekat berikutnya */
  function cariBerikut(items, now) {
    var dow = now.getDay(), m = now.getHours() * 60 + now.getMinutes();
    for (var off = 0; off <= 7; off++) {
      var d = (dow + off) % 7;
      if (d === 0) continue;
      var nama = HARI[d];
      var list = items.filter(function (i) { return i.hari === nama; }).sort(byJam);
      for (var k = 0; k < list.length; k++) {
        var it = list[k], s = menit(it.mulai), e = menit(it.selesai);
        if (off === 0) {
          if (m >= s && m < e) return { item: it, sedang: true, label: 'Hari ini' };
          if (s > m) return { item: it, sedang: false, label: 'Hari ini' };
        } else {
          return { item: it, sedang: false, label: off === 1 ? 'Besok' : nama };
        }
      }
    }
    return null;
  }

  /* ---------- Halaman Roster ---------- */
  function initRoster() {
    var page = document.getElementById('rosterPage');
    if (!page) return;

    var kelas = page.getAttribute('data-kelas');
    var ada = page.getAttribute('data-param') === '1';
    var valid = readJson('rosterKelasList') || [];
    var saved = lsGet(KEY);

    if (!ada && saved && saved !== kelas && valid.indexOf(saved) > -1) {
      try {
        var u = new URL(window.location.href);
        u.searchParams.set('kelas', saved);
        window.location.replace(u.toString());
        return;
      } catch (e) { /* lanjut tanpa pindah */ }
    }
    lsSet(KEY, kelas);

    var items = readJson('rosterItems') || [];
    var box = document.getElementById('rNext');

    function segarkan() {
      var now = new Date();
      var hariIni = HARI[now.getDay()] || '';
      var m = now.getHours() * 60 + now.getMinutes();

      document.querySelectorAll('[data-hari]').forEach(function (n) {
        n.classList.toggle('today', n.getAttribute('data-hari') === hariIni);
      });
      document.querySelectorAll('[data-slot]').forEach(function (n) {
        var on = n.getAttribute('data-h') === hariIni &&
                 m >= menit(n.getAttribute('data-mulai')) && m < menit(n.getAttribute('data-selesai'));
        n.classList.toggle('now', on);
      });

      if (!box) return;
      var r = cariBerikut(items, now);
      if (!r) { box.hidden = true; return; }
      box.hidden = false;
      box.classList.toggle('live', r.sedang);
      document.getElementById('rNextBadge').textContent = r.sedang ? 'Sedang berlangsung' : 'Berikutnya';
      document.getElementById('rNextMk').textContent = r.item.mk;
      document.getElementById('rNextInfo').textContent =
        r.label + ' · ' + r.item.mulai + '–' + r.item.selesai + ' · ' + r.item.ruang + ' · ' + r.item.dosen;
    }
    segarkan();
    setInterval(segarkan, 60000);
  }

  /* ---------- Kartu "Kuliah hari ini" di dashboard ---------- */
  function initKartu() {
    var card = document.getElementById('kuliahHariIni');
    if (!card) return;

    var all = readJson('rosterAll') || {};
    var names = Object.keys(all);
    var select = document.getElementById('kuliahSelect');
    var list = document.getElementById('kuliahList');
    var link = document.getElementById('kuliahLink');
    var base = card.getAttribute('data-url') || '#';
    if (!select || !list) return;

    var kosong = mk('option', null, '— pilih kelas —');
    kosong.value = '';
    select.appendChild(kosong);
    names.forEach(function (n) {
      var o = mk('option', null, 'Kelas ' + n);
      o.value = n;
      select.appendChild(o);
    });
    var saved = lsGet(KEY);
    if (saved && all[saved]) select.value = saved;

    function baris(it, sedang) {
      var row = mk('div', 'kh-item rc-' + it.warna + (sedang ? ' now' : ''));
      row.appendChild(mk('span', 'kh-jam', it.mulai + '–' + it.selesai));
      var isi = mk('div', 'grow');
      isi.style.minWidth = '0';
      isi.appendChild(mk('b', null, it.mk));
      isi.appendChild(mk('div', 'xs', it.ruang + ' · ' + it.dosen));
      row.appendChild(isi);
      return row;
    }

    function render() {
      var k = select.value;
      list.textContent = '';
      if (!k || !all[k]) {
        list.appendChild(mk('p', 'muted small', 'Pilih kelasmu untuk melihat jadwal kuliah hari ini.'));
        if (link) link.hidden = true;
        return;
      }
      lsSet(KEY, k);
      if (link) { link.hidden = false; link.setAttribute('href', base + '?kelas=' + encodeURIComponent(k)); }

      var now = new Date();
      var hariIni = HARI[now.getDay()] || '';
      var m = now.getHours() * 60 + now.getMinutes();
      var hariIniList = all[k].filter(function (i) { return i.hari === hariIni; }).sort(byJam);

      if (hariIniList.length) {
        hariIniList.forEach(function (it) {
          list.appendChild(baris(it, m >= menit(it.mulai) && m < menit(it.selesai)));
        });
      } else {
        list.appendChild(mk('p', 'muted small', hariIni ? 'Tidak ada kuliah hari ini. 🎉' : 'Hari Minggu, tidak ada kuliah. 🎉'));
        var r = cariBerikut(all[k], now);
        if (r) {
          list.appendChild(mk('div', 'xs muted', 'Kuliah berikutnya · ' + r.label));
          list.appendChild(baris(r.item, false));
        }
      }
    }
    select.addEventListener('change', render);
    render();
  }

  function start() { initRoster(); initKartu(); }
  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', start);
  else start();
})();
