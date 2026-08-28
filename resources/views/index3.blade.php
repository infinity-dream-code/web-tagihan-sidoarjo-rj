<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
<title>Cek &amp; Bayar Tagihan | Sidoarjo Raudhatul Jannah</title>
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="icon" type="image/jpeg" href="{{ asset('icon-jannah.jpeg') }}">
<link rel="apple-touch-icon" href="{{ asset('icon-jannah.jpeg') }}">
<meta name="theme-color" content="#14532d">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<script>
(function(){
  try {
    if (localStorage.getItem('theme') === 'dark') document.documentElement.classList.add('dark');
  } catch (e) {}
})();
</script>
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
*{box-sizing:border-box;-webkit-tap-highlight-color:transparent}
:root{
--bg:#f3f6f4;--surface:#ffffff;--surface2:#f0f4f1;--border:#dce6df;--border2:#c5d4ca;
--text:#13261a;--text2:#3f5a48;--text3:#6b8173;
--accent:#15803d;--accent-h:#166534;--accent-soft:#ecf8ef;
--danger:#dc2626;
color-scheme:light;
}
html.dark{
--bg:#0c1610;--surface:#15241b;--surface2:#1c2e24;--border:#2a4033;--border2:#3d5a48;
--text:#eef6f0;--text2:#b7c9bc;--text3:#87a090;
--accent:#4ade80;--accent-h:#22c55e;--accent-soft:#14532d;
--danger:#f87171;
color-scheme:dark;
}
html,body{margin:0}
body{font-family:Inter,-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;background:var(--bg);color:var(--text);min-height:100vh;min-height:100dvh;transition:background .2s,color .2s}
.page-bg{position:fixed;inset:0;pointer-events:none;background:
  radial-gradient(1200px 420px at 50% -10%, rgba(21,128,61,.16), transparent 60%),
  radial-gradient(700px 280px at 100% 100%, rgba(21,128,61,.07), transparent 55%)}
html.dark .page-bg{background:
  radial-gradient(1200px 420px at 50% -10%, rgba(74,222,128,.12), transparent 60%),
  radial-gradient(700px 280px at 100% 100%, rgba(20,83,45,.35), transparent 55%)}
.wrap{position:relative;z-index:1;max-width:760px;margin:0 auto;padding:1.25rem 1rem 2.5rem;padding-left:max(1rem,env(safe-area-inset-left));padding-right:max(1rem,env(safe-area-inset-right));padding-bottom:max(2.5rem,env(safe-area-inset-bottom))}
.topbar{display:flex;justify-content:space-between;align-items:center;margin-bottom:1.25rem;gap:10px}
.brand-wrap{display:flex;align-items:center;gap:12px;min-width:0}
.brand-logo{height:52px;width:auto;max-width:220px;object-fit:contain;object-position:left center;flex-shrink:0;border-radius:8px;background:#fff}
.brand{font-size:11px;font-weight:600;letter-spacing:.04em;color:var(--text3);text-transform:uppercase;margin-bottom:3px}
h1{font-size:1.35rem;font-weight:700;color:var(--text);line-height:1.25;margin:0}
.theme-btn{display:flex;align-items:center;justify-content:center;gap:7px;min-width:44px;min-height:44px;padding:0 12px;border-radius:12px;border:1px solid var(--border2);background:var(--surface);font-size:13px;color:var(--text2);cursor:pointer;transition:background .15s,border .15s;flex-shrink:0}
.theme-btn:hover{background:var(--surface2)}
.theme-label{white-space:nowrap}
.card{background:var(--surface);border:1px solid var(--border);border-radius:18px;padding:1.35rem 1.2rem;margin-bottom:1rem;box-shadow:0 10px 28px rgba(19,38,26,.05)}
.section-title{font-size:11px;font-weight:700;letter-spacing:.08em;color:var(--text3);text-transform:uppercase;margin-bottom:1.15rem;padding-bottom:.8rem;border-bottom:1px solid var(--border)}
.field{margin-bottom:1rem}
.field label{display:block;font-size:13px;font-weight:600;color:var(--text2);margin-bottom:7px}
.field label em{color:var(--danger);font-style:normal}
.field input,.field select{width:100%;min-height:48px;padding:12px 14px;border-radius:12px;border:1px solid var(--border2);background-color:var(--surface2);color:var(--text);font-size:16px;outline:none;transition:border .15s,background .15s,box-shadow .15s}
.pw-wrap{position:relative}
.pw-wrap input{padding-right:48px}
.pw-toggle{position:absolute;right:6px;top:50%;transform:translateY(-50%);width:40px;height:40px;border:none;background:transparent;color:var(--text3);cursor:pointer;display:flex;align-items:center;justify-content:center;border-radius:10px}
.pw-toggle:hover{color:var(--text);background:var(--surface)}
.field select{appearance:none;-webkit-appearance:none;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='none' stroke='%236b8173' stroke-width='2' viewBox='0 0 24 24'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E");background-repeat:no-repeat;background-position:right 14px center;background-size:16px;padding-right:40px}
html.dark .field select{background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='none' stroke='%2387a090' stroke-width='2' viewBox='0 0 24 24'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E")}
.field input:focus,.field select:focus{border-color:var(--accent);background-color:var(--surface);box-shadow:0 0 0 4px rgba(21,128,61,.12)}
.field input::placeholder{color:var(--text3)}
.field select option{background:var(--surface);color:var(--text)}
.turnstile-area{display:flex;justify-content:center;padding:.35rem 0;overflow-x:auto;max-width:100%}
.submit-btn{width:100%;min-height:50px;padding:13px 16px;border-radius:12px;border:none;background:var(--accent);color:#fff;font-size:15px;font-weight:600;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px;margin-top:1.15rem;transition:background .15s,transform .15s}
.submit-btn:hover{background:var(--accent-h)}
.submit-btn:active{transform:scale(.99)}
html.dark .submit-btn{color:#052e16}
.divider{border:none;border-top:1px solid var(--border);margin:1.35rem 0}
.student-grid{display:grid;grid-template-columns:1fr 1fr;gap:.7rem;margin-bottom:1.15rem}
.sf{background:var(--surface2);border:1px solid var(--border);border-radius:12px;padding:11px 12px}
.sf label{display:block;font-size:11px;font-weight:600;letter-spacing:.04em;text-transform:uppercase;color:var(--text3);margin-bottom:4px}
.sf p{font-size:14px;font-weight:650;color:var(--text);margin:0;word-break:break-word;line-height:1.35}
.tbl-bar{display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:.75rem;flex-wrap:wrap;gap:.65rem}
.tbl-title{font-size:15px;font-weight:700;color:var(--text);line-height:1.35}
.tbl-controls{display:flex;align-items:center;gap:8px;flex-wrap:wrap}
.tbl-controls select{min-height:38px;padding:7px 30px 7px 10px;border-radius:10px;border:1px solid var(--border2);background-color:var(--surface2);color:var(--text);font-size:13px;outline:none;cursor:pointer;appearance:none;-webkit-appearance:none;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' fill='none' stroke='%236b8173' stroke-width='2' viewBox='0 0 24 24'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E");background-repeat:no-repeat;background-position:right 8px center}
html.dark .tbl-controls select{background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' fill='none' stroke='%2387a090' stroke-width='2' viewBox='0 0 24 24'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E")}
.btn-showall{min-height:38px;padding:7px 12px;border-radius:10px;border:1px solid var(--border2);background:var(--surface2);color:var(--text2);font-size:13px;cursor:pointer;transition:background .15s}
.btn-showall:hover{background:var(--border)}
.tbl-info{font-size:12px;color:var(--text3)}
.tbl-wrap{overflow-x:auto;-webkit-overflow-scrolling:touch;border-radius:12px;border:1px solid var(--border);background:var(--surface)}
table{width:100%;border-collapse:collapse;font-size:13px}
thead tr{background:var(--surface2)}
th{padding:11px 12px;text-align:left;font-size:11px;font-weight:700;letter-spacing:.05em;color:var(--text3);text-transform:uppercase;border-bottom:1px solid var(--border);white-space:nowrap}
td{padding:11px 12px;border-bottom:1px solid var(--border);color:var(--text);vertical-align:middle}
tbody tr:last-child td{border-bottom:none}
tbody tr:hover{background:var(--surface2)}
.card-list{display:none;flex-direction:column;gap:10px}
.bill-card{background:var(--surface2);border:1px solid var(--border);border-radius:14px;padding:13px 14px}
.bill-card-top{display:flex;justify-content:space-between;align-items:center;gap:8px;margin-bottom:8px}
.bill-card h3{margin:0 0 6px;font-size:15px;font-weight:650;line-height:1.35;color:var(--text)}
.bill-amount{margin:0 0 8px;font-size:17px;font-weight:700;color:var(--accent)}
.bill-meta{display:flex;flex-wrap:wrap;gap:6px 10px;font-size:12px;color:var(--text3)}
.badge{display:inline-flex;align-items:center;padding:3px 9px;border-radius:100px;font-size:11px;font-weight:700}
.badge-unpaid{background:#fef2f2;color:#b91c1c}
html.dark .badge-unpaid{background:#450a0a;color:#fca5a5}
.badge-paid{background:#ecf8ef;color:#15803d}
html.dark .badge-paid{background:#052e16;color:#86efac}
.btn-detail{min-height:34px;padding:6px 12px;border-radius:9px;border:1px solid #bbf7d0;color:#15803d;background:transparent;font-size:12px;font-weight:600;cursor:pointer;transition:background .15s}
.btn-detail:hover{background:#ecf8ef}
html.dark .btn-detail{border-color:#14532d;color:#86efac}
html.dark .btn-detail:hover{background:#14532d}
.pagination{display:flex;gap:6px;justify-content:center;margin-top:.9rem;flex-wrap:wrap}
.pg-btn{min-width:38px;min-height:38px;padding:6px 10px;border-radius:10px;border:1px solid var(--border2);background:var(--surface2);color:var(--text2);font-size:13px;cursor:pointer;transition:background .15s}
.pg-btn:hover:not(.pg-active){background:var(--border)}
.pg-active{background:var(--accent);color:#fff;border-color:var(--accent)}
html.dark .pg-active{color:#052e16}
.error-box{margin-top:.25rem;background:#fef2f2;border:1px solid #fecaca;border-radius:14px;padding:1rem 1.15rem}
.error-box p{color:#b91c1c;font-size:14px;margin:0}
html.dark .error-box{background:#450a0a;border-color:#7f1d1d}
html.dark .error-box p{color:#fca5a5}
.modal-bg{display:none;position:fixed;inset:0;background:rgba(12,22,16,.5);z-index:100;align-items:center;justify-content:center;padding:1rem;padding-bottom:max(1rem,env(safe-area-inset-bottom))}
.modal-bg.open{display:flex}
.modal-box{background:var(--surface);border:1px solid var(--border2);border-radius:18px;width:100%;max-width:520px;max-height:88vh;overflow-y:auto}
.modal-head{display:flex;justify-content:space-between;align-items:center;padding:1rem 1.15rem;border-bottom:1px solid var(--border);position:sticky;top:0;background:var(--surface)}
.modal-head h3{font-size:16px;font-weight:700;color:var(--text);margin:0}
.modal-x{background:none;border:none;cursor:pointer;color:var(--text3);width:40px;height:40px;border-radius:10px;display:flex;align-items:center;justify-content:center}
.modal-x:hover{background:var(--surface2);color:var(--text)}
.modal-body{padding:1.15rem}
.modal-row{display:flex;justify-content:space-between;align-items:flex-start;gap:12px;padding:10px 0;border-bottom:1px solid var(--border)}
.modal-row:last-child{border-bottom:none}
.modal-row-lbl{font-size:13px;color:var(--text2)}
.modal-row-val{font-size:13px;font-weight:650;color:var(--text);text-align:right}
.modal-foot{padding:.9rem 1.15rem;border-top:1px solid var(--border)}
.btn-close-full{width:100%;min-height:44px;padding:10px;border-radius:12px;border:1px solid var(--border2);background:transparent;color:var(--text2);font-size:14px;font-weight:600;cursor:pointer}
.btn-close-full:hover{background:var(--surface2)}
.chk{width:16px;height:16px;accent-color:var(--accent);cursor:pointer}
.select-all-mobile{display:none;align-items:center;gap:8px;font-size:13px;font-weight:600;color:var(--text2);margin-bottom:.65rem}
.pay-note{font-size:13px;color:var(--danger);margin:.95rem 0 .7rem}
.pay-summary{display:none;justify-content:space-between;align-items:center;gap:10px;padding:12px 14px;border-radius:12px;background:var(--accent-soft);border:1px solid var(--border);margin-bottom:.75rem;font-size:13px;color:var(--text2)}
.pay-summary.open{display:flex}
.pay-summary b{color:var(--accent)}
.pay-btn{width:100%;min-height:50px;padding:13px 16px;border-radius:12px;border:none;background:#15803d;color:#fff;font-size:15px;font-weight:600;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px;transition:background .15s}
.pay-btn:hover{background:#166534}
html.dark .pay-btn{background:#16a34a;color:#052e16}
.pay-btn:disabled{opacity:.6;cursor:not-allowed}
.bill-check{display:flex;align-items:center;gap:8px;font-size:12px;font-weight:600;color:var(--text2)}
.modal-box.pay-modal{max-width:640px}
.pay-list{border:1px solid var(--border);border-radius:12px;overflow:hidden;margin:.85rem 0}
.pay-item{display:flex;justify-content:space-between;gap:12px;padding:11px 12px;border-bottom:1px solid var(--border);font-size:13px}
.pay-item:last-child{border-bottom:none}
.pay-item span{color:var(--text2)}
.pay-item b{color:var(--text);text-align:right;white-space:nowrap}
.pay-total{display:flex;justify-content:space-between;align-items:center;padding:12px 14px;border-radius:12px;background:var(--accent-soft);margin-bottom:1rem;font-size:14px;font-weight:700}
.pay-actions{display:flex;gap:10px}
.btn-ghost{flex:1;min-height:46px;border-radius:12px;border:1px solid var(--border2);background:transparent;color:var(--text2);font-size:14px;font-weight:600;cursor:pointer}
.btn-ghost:hover{background:var(--surface2)}
.btn-pay-confirm{flex:1;min-height:46px;border-radius:12px;border:none;background:var(--accent);color:#fff;font-size:14px;font-weight:600;cursor:pointer}
.btn-pay-confirm:hover{background:var(--accent-h)}
.btn-pay-confirm:disabled{opacity:.65;cursor:not-allowed}
html.dark .btn-pay-confirm{color:#052e16}
.va-box{text-align:center;padding:.35rem 0 .2rem}
.va-box h4{margin:0 0 .55rem;font-size:15px;font-weight:700}
.va-number{font-family:ui-monospace,SFMono-Regular,Menlo,monospace;font-size:1.35rem;font-weight:700;letter-spacing:.04em;color:var(--accent);word-break:break-all;margin:.2rem 0 .7rem}
.va-meta{font-size:13px;color:var(--text2);margin:0 0 .35rem}
.va-help{font-size:12px;color:var(--text3);line-height:1.5;margin:.85rem 0 0}
.pay-input{width:118px;min-height:38px;padding:7px 10px;border-radius:10px;border:1px solid var(--border2);background:var(--surface2);color:var(--text);font-size:13px;text-align:right}
.pay-input:disabled{opacity:.65;cursor:not-allowed}
.pay-input:focus{border-color:var(--accent);outline:none;box-shadow:0 0 0 4px rgba(21,128,61,.12);background:var(--surface)}
.badge-cicil{background:#ecf8ef;color:#15803d}
html.dark .badge-cicil{background:#052e16;color:#86efac}
.badge-no-cicil{background:#fff7ed;color:#c2410c}
html.dark .badge-no-cicil{background:#431407;color:#fdba74}
.bill-pay-row{display:flex;justify-content:space-between;align-items:center;gap:10px;margin-top:10px}
.footer{text-align:center;font-size:12px;color:var(--text3);margin-top:1.5rem;padding-bottom:.5rem}
.scroll-top{position:fixed;bottom:max(1.15rem,env(safe-area-inset-bottom));right:max(1rem,env(safe-area-inset-right));width:44px;height:44px;border-radius:50%;border:1px solid var(--border2);background:var(--surface);color:var(--text2);display:flex;align-items:center;justify-content:center;cursor:pointer;box-shadow:0 8px 20px rgba(19,38,26,.12);z-index:20}
.scroll-top:hover{background:var(--surface2)}
.empty-note{text-align:center;padding:1.5rem 1rem;color:var(--text3);font-size:13px}
@media(max-width:720px){
.tbl-wrap{display:none}
.card-list{display:flex}
.select-all-mobile{display:flex}
.pay-actions{flex-direction:column}
}
@media(max-width:520px){
.wrap{padding-top:1rem}
.brand-logo{height:42px;max-width:170px}
h1{font-size:1.15rem}
.theme-label{display:none}
.theme-btn{padding:0;width:44px}
.card{padding:1.1rem .95rem;border-radius:16px}
.student-grid{gap:.55rem}
.sf{padding:10px}
.sf p{font-size:13px}
.tbl-title{font-size:14px;width:100%}
.modal-bg{align-items:flex-end;padding:0}
.modal-box{max-width:none;border-radius:18px 18px 0 0;max-height:90vh}
}
</style>
</head>
<body>
<div class="page-bg"></div>
<div class="wrap">
  <div class="topbar">
    <div class="brand-wrap">
      <img src="{{ asset('icon-jannah.jpeg') }}" alt="Sidoarjo Raudhatul Jannah" class="brand-logo">
      <div>
        <div class="brand">Sidoarjo Raudhatul Jannah</div>
        <h1>Cek &amp; bayar tagihan</h1>
      </div>
    </div>
    <button class="theme-btn" onclick="toggleTheme()" id="themeBtn" type="button" aria-label="Ubah tema">
      <svg id="themeIcon" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
      <span class="theme-label" id="themeLabel">Mode gelap</span>
    </button>
  </div>

  <div class="card">
    <form method="POST" action="/" id="billForm">
      @csrf
      <div class="section-title">Informasi akun</div>
      <div class="field">
        <label>Nomor virtual account <em>*</em></label>
        <input type="text" name="no_cust" id="noCust" inputmode="numeric" autocomplete="username" placeholder="797766xxx" value="{{ old('no_cust', $va ?? '') }}" required>
      </div>
      <div class="field">
        <label>Password <em>*</em></label>
        <div class="pw-wrap">
          <input type="password" name="password" id="password" autocomplete="current-password" placeholder="Masukkan password" required>
          <button type="button" class="pw-toggle" id="togglePassword" aria-label="Tampilkan password">
            <svg id="iconEye" width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
            <svg id="iconEyeOff" width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" hidden><path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.477 0-8.268-2.943-9.542-7a10.05 10.05 0 012.223-3.444M6.18 6.18A9.966 9.966 0 0112 5c4.477 0 8.268 2.943 9.542 7a9.974 9.974 0 01-4.245 5.253M3 3l18 18"/></svg>
          </button>
        </div>
      </div>
      <div class="field">
        <label>Tahun akademik <em>*</em></label>
        <select id="academic_year" name="academic_year">
          <option value="">Memuat data...</option>
        </select>
      </div>
      <div class="field">
        <label>Verifikasi keamanan <em>*</em></label>
        <div class="turnstile-area">
          <div id="turnstile-widget"></div>
          <input type="hidden" name="cf_turnstile_response" id="cfToken">
        </div>
      </div>
      <button type="submit" name="submit" class="submit-btn">
        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        Cek tagihan
      </button>
    </form>

    @if(session('error'))
    <script>
    document.addEventListener('DOMContentLoaded', function () {
      var dark = document.documentElement.classList.contains('dark');
      Swal.fire({
        icon: 'error',
        title: 'Gagal',
        text: @json(session('error')),
        confirmButtonColor: '#15803d',
        background: dark ? '#15241b' : '#fff',
        color: dark ? '#eef6f0' : '#13261a'
      });
    });
    </script>
    @endif
    @if(session('success'))
    <script>
    document.addEventListener('DOMContentLoaded', function () {
      var dark = document.documentElement.classList.contains('dark');
      Swal.fire({
        icon: 'success',
        title: 'Berhasil',
        text: @json(session('success')),
        timer: 2000,
        showConfirmButton: false,
        background: dark ? '#15241b' : '#fff',
        color: dark ? '#eef6f0' : '#13261a'
      });
    });
    </script>
    @endif
  </div>

  @if(isset($result))
    @if($result['status'])
      <div class="card" id="resultSection">
        <div class="section-title">Data siswa</div>
        <div class="student-grid">
          <div class="sf"><label>Nama</label><p>{{ $result['data']['nama'] ?? '-' }}</p></div>
          <div class="sf"><label>Kelas</label><p>{{ $result['data']['kelas'] ?? '-' }}</p></div>
          <div class="sf"><label>Angkatan</label><p>{{ $academic_year ?: '-' }}</p></div>
          <div class="sf"><label>Saldo VA</label><p>Rp {{ number_format($result['data']['saldo'] ?? 0, 0, ',', '.') }}</p></div>
          <div class="sf"><label>NOVA</label><p>{{ $result['data']['va_number'] ?? '-' }}</p></div>
          <div class="sf"><label>Jenjang</label><p>{{ $result['data']['jenjang'] ?? '-' }}</p></div>
        </div>

        <div class="divider"></div>

        <div class="tbl-bar">
          <div class="tbl-title">Tagihan aktif — {{ $academic_year ?: '-' }}</div>
          <div class="tbl-controls">
            <select id="tagihanPerPage" onchange="changeTagihanPerPage()" aria-label="Jumlah data">
              <option value="10">10</option>
              <option value="25">25</option>
              <option value="50">50</option>
              <option value="100">100</option>
            </select>
            <button class="btn-showall" type="button" onclick="showAllTagihan()">Tampilkan semua</button>
          </div>
        </div>
        <div id="tagihanInfo" class="tbl-info" style="margin-bottom:.75rem"></div>
        <div class="tbl-wrap">
          <table>
            <thead>
              <tr>
                <th><input type="checkbox" class="chk" id="selectAll" onclick="toggleSelectAll(this)" aria-label="Pilih semua"></th>
                <th>No</th>
                <th>Nama tagihan</th>
                <th>Nominal</th>
                <th>Sudah dibayar</th>
                <th>Dapat dicicil</th>
                <th>Bayar</th>
                <th>Detail</th>
              </tr>
            </thead>
            <tbody id="tagihanTableBody">
              @forelse($result['data']['tagihan'] as $i => $tagih)
              @php
                $bolehCicil = (int)($tagih['is_installment'] ?? $tagih['isINSTALLMENT'] ?? 0) === 1;
                $sudahBayar = (int)($tagih['sudah_dibayar'] ?? 0);
                $totalTagih = (int)($tagih['total_tagihan'] ?? 0);
                $sisaTagih = (int)($tagih['sisa_tagihan'] ?? max(0, $totalTagih - $sudahBayar));
              @endphp
              <tr data-index="{{ $i }}">
                <td>
                  <input type="checkbox" class="chk tagihan-checkbox" value="{{ $tagih['AA'] ?? '' }}" data-index="{{ $i }}" {{ $sisaTagih <= 0 ? 'disabled' : '' }} aria-label="Pilih tagihan">
                </td>
                <td>{{ $i+1 }}</td>
                <td>{{ ucwords(str_replace('_', ' ', strtolower($tagih['nama_tagihan']))) }}</td>
                <td>Rp {{ number_format($totalTagih, 0, ',', '.') }}</td>
                <td>Rp {{ number_format($sudahBayar, 0, ',', '.') }}</td>
                <td>
                  @if($bolehCicil)
                    <span class="badge badge-cicil">Ya</span>
                  @else
                    <span class="badge badge-no-cicil">Tidak</span>
                  @endif
                </td>
                <td>
                  <input type="number" class="pay-input bayar-input" data-index="{{ $i }}" min="1" max="{{ $sisaTagih }}" value="0" disabled inputmode="numeric" aria-label="Nominal bayar">
                </td>
                <td><button type="button" class="btn-detail" onclick='showDetailModal(@json($tagih))'>Lihat</button></td>
              </tr>
              @empty
              <tr><td colspan="8" class="empty-note">Tidak ada data tersedia</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
        <label class="select-all-mobile">
          <input type="checkbox" class="chk" id="selectAllMobile" onclick="toggleSelectAll(this)"> Pilih semua
        </label>
        <div class="card-list" id="tagihanCardList">
          @forelse($result['data']['tagihan'] as $i => $tagih)
          @php
            $bolehCicil = (int)($tagih['is_installment'] ?? $tagih['isINSTALLMENT'] ?? 0) === 1;
            $sudahBayar = (int)($tagih['sudah_dibayar'] ?? 0);
            $totalTagih = (int)($tagih['total_tagihan'] ?? 0);
            $sisaTagih = (int)($tagih['sisa_tagihan'] ?? max(0, $totalTagih - $sudahBayar));
          @endphp
          <article class="bill-card" data-index="{{ $i }}">
            <div class="bill-card-top">
              <label class="bill-check">
                <input type="checkbox" class="chk tagihan-checkbox" value="{{ $tagih['AA'] ?? '' }}" data-index="{{ $i }}" {{ $sisaTagih <= 0 ? 'disabled' : '' }}> Pilih
              </label>
              @if($bolehCicil)
                <span class="badge badge-cicil">Bisa dicicil</span>
              @else
                <span class="badge badge-no-cicil">Tidak dicicil</span>
              @endif
              <button type="button" class="btn-detail" onclick='showDetailModal(@json($tagih))'>Detail</button>
            </div>
            <h3>{{ ucwords(str_replace('_', ' ', strtolower($tagih['nama_tagihan']))) }}</h3>
            <p class="bill-amount">Rp {{ number_format($totalTagih, 0, ',', '.') }}</p>
            <div class="bill-meta">
              <span>Sudah dibayar Rp {{ number_format($sudahBayar, 0, ',', '.') }}</span>
              <span>Sisa Rp {{ number_format($sisaTagih, 0, ',', '.') }}</span>
            </div>
            <div class="bill-pay-row">
              <span class="bill-meta">Bayar</span>
              <input type="number" class="pay-input bayar-input" data-index="{{ $i }}" min="1" max="{{ $sisaTagih }}" value="0" disabled inputmode="numeric" aria-label="Nominal bayar">
            </div>
          </article>
          @empty
          <div class="empty-note">Tidak ada data tersedia</div>
          @endforelse
        </div>
        <div id="tagihanPagination" class="pagination"></div>

        @if(!empty($result['data']['tagihan']))
        <p class="pay-note">*Pilih tagihan yang akan dibayar. Yang tidak bisa dicicil harus dibayar sesuai sisa. Yang bisa dicicil, nominal bayar tidak boleh melebihi sisa tagihan.</p>
        <div class="pay-summary" id="paySummary">
          <span id="paySummaryText">0 tagihan dipilih</span>
          <b id="paySummaryTotal">Rp 0</b>
        </div>
        <button type="button" class="pay-btn" id="btnBayar" onclick="showPaymentModal()">
          <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
          Bayar tagihan
        </button>
        @endif

        <div class="divider"></div>

        <div class="tbl-bar">
          <div class="tbl-title">Tagihan lunas — {{ $academic_year ?: '-' }}</div>
          <div class="tbl-controls">
            <select id="lunasPerPage" onchange="changeLunasPerPage()" aria-label="Jumlah data">
              <option value="10">10</option>
              <option value="25">25</option>
              <option value="50">50</option>
              <option value="100">100</option>
            </select>
            <button class="btn-showall" type="button" onclick="showAllLunas()">Tampilkan semua</button>
          </div>
        </div>
        <div id="lunasInfo" class="tbl-info" style="margin-bottom:.75rem"></div>
        <div class="tbl-wrap">
          <table>
            <thead>
              <tr>
                <th>No</th>
                <th>Urutan</th>
                <th>Nama tagihan</th>
                <th>Nominal</th>
                <th>Tgl bayar</th>
                <th>Detail</th>
              </tr>
            </thead>
            <tbody id="lunasTableBody">
              @forelse($result['data']['tagihan_lunas'] ?? [] as $i => $tagih)
              <tr data-index="{{ $i }}">
                <td>{{ $i+1 }}</td>
                <td>{{ $tagih['FURUTAN'] ?? '-' }}</td>
                <td>{{ ucwords(str_replace('_', ' ', strtolower($tagih['nama_tagihan']))) }}</td>
                <td>Rp {{ number_format($tagih['total_tagihan'], 0, ',', '.') }}</td>
                <td>{{ !empty($tagih['PAIDDT']) ? \Carbon\Carbon::parse($tagih['PAIDDT'])->format('Y-m-d') : '-' }}</td>
                <td><button type="button" class="btn-detail" onclick='showDetailModal(@json($tagih))'>Lihat</button></td>
              </tr>
              @empty
              <tr><td colspan="6" class="empty-note">Tidak ada tagihan lunas</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
        <div class="card-list" id="lunasCardList">
          @forelse($result['data']['tagihan_lunas'] ?? [] as $i => $tagih)
          <article class="bill-card" data-index="{{ $i }}">
            <div class="bill-card-top">
              <span class="badge badge-paid">Lunas</span>
              <button type="button" class="btn-detail" onclick='showDetailModal(@json($tagih))'>Detail</button>
            </div>
            <h3>{{ ucwords(str_replace('_', ' ', strtolower($tagih['nama_tagihan']))) }}</h3>
            <p class="bill-amount">Rp {{ number_format($tagih['total_tagihan'], 0, ',', '.') }}</p>
            <div class="bill-meta">
              <span>Urutan {{ $tagih['FURUTAN'] ?? '-' }}</span>
              <span>Bayar {{ !empty($tagih['PAIDDT']) ? \Carbon\Carbon::parse($tagih['PAIDDT'])->format('d M Y') : '-' }}</span>
            </div>
          </article>
          @empty
          <div class="empty-note">Tidak ada tagihan lunas</div>
          @endforelse
        </div>
        <div id="lunasPagination" class="pagination"></div>
      </div>
      <script>setTimeout(()=>{document.getElementById('resultSection').scrollIntoView({behavior:'smooth',block:'start'})},100);</script>
    @else
      <div class="error-box">
        <p>{{ $result['message'] ?? 'Data tidak ditemukan' }}</p>
      </div>
    @endif
  @endif

  <div class="footer">© {{ date('Y') }} Sidoarjo Raudhatul Jannah. All rights reserved.</div>
</div>

<div id="detailModal" class="modal-bg">
  <div class="modal-box">
    <div class="modal-head">
      <h3>Detail tagihan</h3>
      <button class="modal-x" onclick="closeDetailModal()" aria-label="Tutup" type="button">
        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
      </button>
    </div>
    <div class="modal-body">
      <div class="modal-row"><span class="modal-row-lbl">Nama tagihan</span><span class="modal-row-val" id="mNama"></span></div>
      <div class="modal-row"><span class="modal-row-lbl">Tahun akademik</span><span class="modal-row-val" id="mTahun"></span></div>
      <div id="mDetailTable"></div>
    </div>
    <div class="modal-foot">
      <button class="btn-close-full" onclick="closeDetailModal()" type="button">Tutup</button>
    </div>
  </div>
</div>

<div id="paymentModal" class="modal-bg">
  <div class="modal-box pay-modal">
    <div class="modal-head">
      <h3>Konfirmasi pembayaran</h3>
      <button class="modal-x" onclick="closePaymentModal()" aria-label="Tutup" type="button">
        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
      </button>
    </div>
    <div class="modal-body" id="paymentBody"></div>
    <div class="modal-foot" id="paymentFoot"></div>
  </div>
</div>

<button class="scroll-top" onclick="window.scrollTo({top:0,behavior:'smooth'})" aria-label="Scroll ke atas" type="button">
  <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
</button>

<script>
let currentTheme = (typeof localStorage !== 'undefined' && localStorage.getItem('theme') === 'dark') ? 'dark' : 'light';
let turnstileToken = null;
let tagihanPage = 1, tagihanPerPageVal = 10, tagihanAll = false;
let lunasPage = 1, lunasPerPageVal = 10, lunasAll = false;
const preselectedYear = @json(old('academic_year', $academic_year ?? 'all'));

const ICON_MOON = '<path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>';
const ICON_SUN = '<path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>';

function syncThemeUI(theme) {
  currentTheme = theme;
  const lbl = document.getElementById('themeLabel');
  const icon = document.getElementById('themeIcon');
  if (theme === 'dark') {
    lbl.textContent = 'Mode terang';
    icon.innerHTML = ICON_SUN;
  } else {
    lbl.textContent = 'Mode gelap';
    icon.innerHTML = ICON_MOON;
  }
}

function applyTheme(theme, resetWidget) {
  document.documentElement.classList.toggle('dark', theme === 'dark');
  localStorage.setItem('theme', theme);
  syncThemeUI(theme);
  if (resetWidget) resetTurnstile();
}

function toggleTheme() {
  applyTheme(currentTheme === 'light' ? 'dark' : 'light', true);
}

function initTurnstile() {
  const w = document.getElementById('turnstile-widget');
  w.innerHTML = '';
  turnstileToken = null;
  document.getElementById('cfToken').value = '';
  if (typeof turnstile === 'undefined') { setTimeout(initTurnstile, 150); return; }
  turnstile.render('#turnstile-widget', {
    sitekey: @json(config('services.turnstile.site_key')),
    theme: currentTheme === 'dark' ? 'dark' : 'light',
    size: window.innerWidth < 420 ? 'compact' : 'normal',
    retry: 'auto',
    callback: t => { turnstileToken = t; document.getElementById('cfToken').value = t; },
    'error-callback': () => { turnstileToken = null; document.getElementById('cfToken').value = ''; },
    'expired-callback': () => { turnstileToken = null; document.getElementById('cfToken').value = ''; }
  });
}

function resetTurnstile() {
  if (typeof turnstile !== 'undefined') { document.getElementById('turnstile-widget').innerHTML = ''; setTimeout(initTurnstile, 100); }
}

document.addEventListener('DOMContentLoaded', () => {
  syncThemeUI(currentTheme);
  document.documentElement.classList.toggle('dark', currentTheme === 'dark');

  const togglePw = document.getElementById('togglePassword');
  if (togglePw) {
    togglePw.addEventListener('click', () => {
      const field = document.getElementById('password');
      const eye = document.getElementById('iconEye');
      const eyeOff = document.getElementById('iconEyeOff');
      const show = field.getAttribute('type') === 'password';
      field.setAttribute('type', show ? 'text' : 'password');
      togglePw.setAttribute('aria-label', show ? 'Sembunyikan password' : 'Tampilkan password');
      if (eye) eye.hidden = show;
      if (eyeOff) eyeOff.hidden = !show;
    });
  }

  const s = document.getElementById('academic_year');
  if (s) {
    fetch("/list-tahun-akademik")
      .then(r => r.json())
      .then(data => {
        s.innerHTML = '';
        const def = document.createElement('option');
        def.value = 'all'; def.textContent = 'Semua tahun akademik';
        s.appendChild(def);
        if (data.status && data.data.length) {
          data.data.forEach(item => {
            const o = document.createElement('option');
            o.value = item.thn_aka; o.textContent = item.thn_aka;
            s.appendChild(o);
          });
        }
        const match = Array.from(s.options).find(o => o.value === preselectedYear);
        s.value = match ? preselectedYear : 'all';
      })
      .catch(() => { s.innerHTML = '<option>Gagal memuat data</option>'; });
  }
  initTagihanPagination();
  initLunasPagination();
});

window.onload = initTurnstile;

function getRows(id) {
  const el = document.getElementById(id);
  return el ? Array.from(el.querySelectorAll('tr[data-index]')) : [];
}

function syncCards(tableBodyId, rows) {
  const cardList = document.getElementById(tableBodyId.replace('TableBody', 'CardList'));
  if (!cardList) return;
  const vis = {};
  rows.forEach(r => { vis[r.dataset.index] = r.style.display; });
  cardList.querySelectorAll('[data-index]').forEach(c => {
    c.style.display = Object.prototype.hasOwnProperty.call(vis, c.dataset.index) ? vis[c.dataset.index] : 'none';
  });
}

function paginateRows(rows, page, perPage, showAll, infoId, paginationFn, tableBodyId) {
  const total = rows.length;
  rows.forEach(r => r.style.display = 'none');
  if (showAll) { rows.forEach(r => r.style.display = ''); }
  else {
    const start = (page - 1) * perPage;
    for (let i = start; i < Math.min(start + perPage, total); i++) rows[i].style.display = '';
  }
  if (tableBodyId) syncCards(tableBodyId, rows);
  const s2 = showAll ? 1 : (page - 1) * perPage + 1;
  const e2 = showAll ? total : Math.min(page * perPage, total);
  const inf = document.getElementById(infoId);
  if (inf) inf.textContent = `Menampilkan ${s2} – ${e2} dari ${total} data`;
  paginationFn(showAll ? 1 : Math.ceil(total / perPage), total);
}

function renderPagination(id, totalPages, totalRows, showAll, getCurrent, goTo) {
  const el = document.getElementById(id);
  if (!el) return;
  if (showAll || totalRows === 0) { el.innerHTML = ''; return; }
  const cur = getCurrent();
  let h = '';
  if (cur > 1) h += `<button class="pg-btn" type="button" onclick="${goTo}(${cur-1})">‹</button>`;
  const max = 5;
  let sp = Math.max(1, cur - Math.floor(max/2));
  let ep = Math.min(totalPages, sp + max - 1);
  if (ep - sp < max - 1) sp = Math.max(1, ep - max + 1);
  if (sp > 1) { h += `<button class="pg-btn" type="button" onclick="${goTo}(1)">1</button>`; if (sp > 2) h += `<span style="padding:5px 4px;color:var(--text3)">…</span>`; }
  for (let i = sp; i <= ep; i++) h += `<button class="pg-btn${i === cur ? ' pg-active' : ''}" type="button" onclick="${goTo}(${i})">${i}</button>`;
  if (ep < totalPages) { if (ep < totalPages - 1) h += `<span style="padding:5px 4px;color:var(--text3)">…</span>`; h += `<button class="pg-btn" type="button" onclick="${goTo}(${totalPages})">${totalPages}</button>`; }
  if (cur < totalPages) h += `<button class="pg-btn" type="button" onclick="${goTo}(${cur+1})">›</button>`;
  el.innerHTML = h;
}

function initTagihanPagination() {
  const rows = getRows('tagihanTableBody');
  if (rows.length) paginateRows(rows, tagihanPage, tagihanPerPageVal, tagihanAll, 'tagihanInfo', (tp, tr) => renderPagination('tagihanPagination', tp, tr, tagihanAll, () => tagihanPage, 'goToTagihanPage'), 'tagihanTableBody');
}

function initLunasPagination() {
  const rows = getRows('lunasTableBody');
  if (rows.length) paginateRows(rows, lunasPage, lunasPerPageVal, lunasAll, 'lunasInfo', (tp, tr) => renderPagination('lunasPagination', tp, tr, lunasAll, () => lunasPage, 'goToLunasPage'), 'lunasTableBody');
}

function goToTagihanPage(p) { tagihanPage = p; paginateRows(getRows('tagihanTableBody'), tagihanPage, tagihanPerPageVal, tagihanAll, 'tagihanInfo', (tp, tr) => renderPagination('tagihanPagination', tp, tr, tagihanAll, () => tagihanPage, 'goToTagihanPage'), 'tagihanTableBody'); }
function goToLunasPage(p) { lunasPage = p; paginateRows(getRows('lunasTableBody'), lunasPage, lunasPerPageVal, lunasAll, 'lunasInfo', (tp, tr) => renderPagination('lunasPagination', tp, tr, lunasAll, () => lunasPage, 'goToLunasPage'), 'lunasTableBody'); }

function changeTagihanPerPage() { tagihanPerPageVal = parseInt(document.getElementById('tagihanPerPage').value); tagihanPage = 1; tagihanAll = false; initTagihanPagination(); }
function changeLunasPerPage() { lunasPerPageVal = parseInt(document.getElementById('lunasPerPage').value); lunasPage = 1; lunasAll = false; initLunasPagination(); }
function showAllTagihan() { tagihanAll = true; initTagihanPagination(); }
function showAllLunas() { lunasAll = true; initLunasPagination(); }

function showDetailModal(tagihan) {
  document.getElementById('mNama').textContent = tagihan.nama_tagihan ? tagihan.nama_tagihan.toLowerCase().replace(/_/g,' ').replace(/\b\w/g, l => l.toUpperCase()) : '-';
  document.getElementById('mTahun').textContent = tagihan.tahun_akademik_tagihan || '-';
  let t = '<table style="width:100%;border-collapse:collapse;margin-top:.75rem"><thead><tr style="background:var(--surface2)"><th style="padding:9px 12px;text-align:left;font-size:11px;font-weight:700;letter-spacing:.05em;color:var(--text3);text-transform:uppercase;border-bottom:1px solid var(--border)">Komponen</th><th style="padding:9px 12px;text-align:right;font-size:11px;font-weight:700;letter-spacing:.05em;color:var(--text3);text-transform:uppercase;border-bottom:1px solid var(--border)">Nominal</th></tr></thead><tbody>';
  if (Array.isArray(tagihan.detail) && tagihan.detail.length) {
    tagihan.detail.forEach(d => { t += `<tr><td style="padding:9px 12px;border-bottom:1px solid var(--border);font-size:13px;color:var(--text)">${d.akun_detail||'-'}</td><td style="padding:9px 12px;border-bottom:1px solid var(--border);text-align:right;font-size:13px;color:var(--text);font-weight:650">Rp ${parseInt(d.nominal_detail||0).toLocaleString('id-ID')}</td></tr>`; });
  } else { t += `<tr><td colspan="2" style="padding:1.5rem;text-align:center;color:var(--text3);font-size:13px">Tidak ada rincian</td></tr>`; }
  t += '</tbody></table>';
  document.getElementById('mDetailTable').innerHTML = t;
  document.getElementById('detailModal').classList.add('open');
  document.body.style.overflow = 'hidden';
}

function closeDetailModal() {
  document.getElementById('detailModal').classList.remove('open');
  document.body.style.overflow = '';
}

window.addEventListener('click', e => {
  if (e.target.id === 'detailModal') closeDetailModal();
  if (e.target.id === 'paymentModal') closePaymentModal();
});

document.getElementById('billForm').addEventListener('submit', e => {
  if (!turnstileToken) { e.preventDefault(); alert('Selesaikan verifikasi keamanan terlebih dahulu!'); }
});

const tagihanAktif = @json(isset($result['data']['tagihan']) ? $result['data']['tagihan'] : []);
const siswaBayar = {
  id: @json($result['data']['id'] ?? null),
  nama: @json($result['data']['nama'] ?? ''),
  kelas: @json($result['data']['kelas'] ?? ''),
  no_cust: @json($result['data']['no_cust'] ?? ''),
  num2nd: @json($result['data']['num2nd'] ?? ''),
  va_number: @json($result['data']['va_number'] ?? ''),
  tahun_akademik: @json($academic_year ?? ''),
  jenjang: @json($result['data']['jenjang'] ?? ''),
  saldo: @json($result['data']['saldo'] ?? 0)
};
const generateVaUrl = @json(route('generate-va'));
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

function formatRp(n) {
  return 'Rp ' + (parseInt(n, 10) || 0).toLocaleString('id-ID');
}

function esc(s) {
  return String(s ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
}

function billName(name) {
  return esc((name || '-').toLowerCase().replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase()));
}

function isCicil(item) {
  return String(item?.is_installment ?? item?.isINSTALLMENT ?? item?.isinstallable ?? 0) === '1';
}

function sudahBayarOf(item) {
  return parseInt(item?.sudah_dibayar, 10) || 0;
}

function sisaTagihan(item) {
  if (item?.sisa_tagihan != null && item.sisa_tagihan !== '') {
    return Math.max(0, parseInt(item.sisa_tagihan, 10) || 0);
  }
  return Math.max(0, (parseInt(item?.total_tagihan, 10) || 0) - sudahBayarOf(item));
}

function setBayarForIndex(idx, value, enabled) {
  document.querySelectorAll('.bayar-input[data-index="' + idx + '"]').forEach(inp => {
    const item = tagihanAktif[idx];
    const max = sisaTagihan(item);
    const cicil = isCicil(item);
    inp.max = max;
    if (enabled) {
      inp.disabled = !cicil;
      inp.readOnly = !cicil;
      inp.value = Math.min(max, Math.max(0, value));
    } else {
      inp.disabled = true;
      inp.readOnly = true;
      inp.value = 0;
    }
  });
}

function getBayarAmount(idx) {
  const inp = document.querySelector('.bayar-input[data-index="' + idx + '"]');
  const item = tagihanAktif[idx];
  const max = sisaTagihan(item);
  let n = inp ? (parseInt(String(inp.value).replace(/\D/g, ''), 10) || 0) : 0;
  if (n < 0) n = 0;
  if (n > max) n = max;
  if (item && !isCicil(item) && n > 0) n = max;
  return n;
}

function clampBayarInput(inp) {
  const idx = inp.dataset.index;
  const item = tagihanAktif[idx];
  const max = sisaTagihan(item);
  let n = parseInt(String(inp.value).replace(/\D/g, ''), 10) || 0;
  if (n < 1) n = 1;
  if (n > max) n = max;
  if (!isCicil(item)) n = max;
  document.querySelectorAll('.bayar-input[data-index="' + idx + '"]').forEach(el => { el.value = n; });
  updatePaySummary();
}

function getSelectedTagihan() {
  const seen = new Set();
  const selected = [];
  document.querySelectorAll('.tagihan-checkbox:checked').forEach(cb => {
    const idx = String(cb.dataset.index);
    if (seen.has(idx) || cb.disabled) return;
    seen.add(idx);
    const item = tagihanAktif[idx];
    if (!item) return;
    selected.push({ ...item, _idx: idx, bayar: getBayarAmount(idx) });
  });
  return selected;
}

function syncCheckboxPair(source) {
  document.querySelectorAll('.tagihan-checkbox[data-index="' + source.dataset.index + '"]').forEach(cb => {
    cb.checked = source.checked;
  });
  const idx = source.dataset.index;
  const item = tagihanAktif[idx];
  setBayarForIndex(idx, source.checked ? sisaTagihan(item) : 0, source.checked);
  const all = Array.from(document.querySelectorAll('#tagihanTableBody .tagihan-checkbox')).filter(cb => !cb.disabled);
  const checked = all.filter(cb => cb.checked);
  const allOn = all.length > 0 && all.length === checked.length;
  ['selectAll', 'selectAllMobile'].forEach(id => {
    const el = document.getElementById(id);
    if (el) el.checked = allOn;
  });
}

function toggleSelectAll(master) {
  document.querySelectorAll('.tagihan-checkbox').forEach(cb => {
    if (cb.disabled) return;
    cb.checked = master.checked;
    const idx = cb.dataset.index;
    const item = tagihanAktif[idx];
    setBayarForIndex(idx, master.checked ? sisaTagihan(item) : 0, master.checked);
  });
  ['selectAll', 'selectAllMobile'].forEach(id => {
    const el = document.getElementById(id);
    if (el) el.checked = master.checked;
  });
  updatePaySummary();
}

function updatePaySummary() {
  const selected = getSelectedTagihan();
  const total = selected.reduce((s, i) => s + (parseInt(i.bayar, 10) || 0), 0);
  const box = document.getElementById('paySummary');
  const text = document.getElementById('paySummaryText');
  const tot = document.getElementById('paySummaryTotal');
  if (!box) return;
  if (!selected.length) {
    box.classList.remove('open');
    return;
  }
  box.classList.add('open');
  text.textContent = selected.length + ' tagihan dipilih';
  tot.textContent = formatRp(total);
}

document.addEventListener('change', e => {
  if (e.target.classList.contains('tagihan-checkbox')) {
    syncCheckboxPair(e.target);
    updatePaySummary();
  }
});

document.addEventListener('input', e => {
  if (e.target.classList.contains('bayar-input')) clampBayarInput(e.target);
});

function swalTheme() {
  const dark = document.documentElement.classList.contains('dark');
  return {
    confirmButtonColor: '#15803d',
    background: dark ? '#15241b' : '#fff',
    color: dark ? '#eef6f0' : '#13261a'
  };
}

function showPaymentModal() {
  const selected = getSelectedTagihan();
  if (!selected.length) {
    Swal.fire({ icon: 'warning', title: 'Belum ada tagihan', text: 'Pilih minimal satu tagihan untuk dibayar.', ...swalTheme() });
    return;
  }

  const missingAa = selected.some(i => !i.AA);
  if (missingAa || !siswaBayar.id) {
    Swal.fire({ icon: 'error', title: 'Data tidak lengkap', text: 'Data tagihan tidak bisa diproses. Silakan cek ulang tagihan.', ...swalTheme() });
    return;
  }

  for (const i of selected) {
    const sisa = sisaTagihan(i);
    const bayar = parseInt(i.bayar, 10) || 0;
    if (bayar <= 0) {
      Swal.fire({ icon: 'warning', title: 'Nominal belum diisi', text: 'Isi nominal bayar untuk setiap tagihan yang dipilih.', ...swalTheme() });
      return;
    }
    if (bayar > sisa) {
      Swal.fire({ icon: 'warning', title: 'Nominal melebihi sisa', text: 'Nominal bayar tidak boleh lebih dari sisa tagihan.', ...swalTheme() });
      return;
    }
    if (!isCicil(i) && bayar !== sisa) {
      Swal.fire({ icon: 'warning', title: 'Tidak bisa dicicil', text: 'Tagihan yang tidak bisa dicicil harus dibayar sesuai sisa tagihan.', ...swalTheme() });
      return;
    }
  }

  const total = selected.reduce((s, i) => s + (parseInt(i.bayar, 10) || 0), 0);
  let list = '';
  selected.forEach(i => {
    const cicilLabel = isCicil(i) ? 'Bisa dicicil' : 'Tidak dicicil';
    const sisa = sisaTagihan(i);
    const input = isCicil(i)
      ? `<input type="number" class="pay-input bayar-input modal-bayar-input" data-index="${i._idx}" min="1" max="${sisa}" value="${i.bayar}" inputmode="numeric">`
      : `<b>${formatRp(i.bayar)}</b>`;
    list += `<div class="pay-item"><span>${billName(i.nama_tagihan)}<br><small>${esc(i.tahun_akademik_tagihan || siswaBayar.tahun_akademik || '-')} · ${cicilLabel} · sisa ${formatRp(sisa)}</small></span>${input}</div>`;
  });

  document.getElementById('paymentBody').innerHTML = `
    <div class="student-grid" style="margin-bottom:.9rem">
      <div class="sf"><label>Nama</label><p>${esc(siswaBayar.nama) || '-'}</p></div>
      <div class="sf"><label>Kelas</label><p>${esc(siswaBayar.kelas) || '-'}</p></div>
      <div class="sf"><label>NOVA</label><p>${esc(siswaBayar.va_number || siswaBayar.no_cust) || '-'}</p></div>
      <div class="sf"><label>Item</label><p>${selected.length} tagihan</p></div>
    </div>
    <div class="pay-list">${list}</div>
    <div class="pay-total"><span>Total pembayaran</span><span id="modalPayTotal">${formatRp(total)}</span></div>
    <div id="vaResult"></div>`;

  document.getElementById('paymentFoot').innerHTML = `
    <div class="pay-actions" id="payActions">
      <button type="button" class="btn-ghost" onclick="closePaymentModal()">Batal</button>
      <button type="button" class="btn-pay-confirm" id="btnBuatVa" onclick="prosesPembayaran()">Buat nomor VA</button>
    </div>`;

  document.querySelectorAll('.modal-bayar-input').forEach(inp => {
    inp.addEventListener('input', () => {
      clampBayarInput(inp);
      const tot = getSelectedTagihan().reduce((s, i) => s + (parseInt(i.bayar, 10) || 0), 0);
      const el = document.getElementById('modalPayTotal');
      if (el) el.textContent = formatRp(tot);
    });
  });

  document.getElementById('paymentModal').classList.add('open');
  document.body.style.overflow = 'hidden';
}

function closePaymentModal() {
  document.getElementById('paymentModal').classList.remove('open');
  document.body.style.overflow = '';
}

async function prosesPembayaran() {
  const selected = getSelectedTagihan();
  const btn = document.getElementById('btnBuatVa');
  if (!selected.length) {
    Swal.fire({ icon: 'warning', title: 'Belum ada tagihan', text: 'Pilih minimal satu tagihan untuk dibayar.', ...swalTheme() });
    return;
  }

  for (const i of selected) {
    const sisa = sisaTagihan(i);
    const bayar = parseInt(i.bayar, 10) || 0;
    if (bayar <= 0 || bayar > sisa || (!isCicil(i) && bayar !== sisa)) {
      Swal.fire({ icon: 'warning', title: 'Nominal tidak valid', text: 'Periksa nominal bayar. Yang tidak bisa dicicil harus lunas sisa tagihan, yang bisa dicicil tidak boleh melebihi sisa.', ...swalTheme() });
      return;
    }
  }

  const items = selected.map(i => ({ AA: i.AA, amount: parseInt(i.bayar, 10) }));
  const ids = items.map(i => i.AA);
  const amounts = items.map(i => i.amount);
  const total = amounts.reduce((s, n) => s + n, 0);
  const nocust = siswaBayar.no_cust || siswaBayar.num2nd || '';

  if (btn) { btn.disabled = true; btn.textContent = 'Memproses...'; }

  try {
    const res = await fetch(generateVaUrl, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': csrfToken
      },
      body: JSON.stringify({
        custid: siswaBayar.id,
        nocust: nocust,
        namacust: siswaBayar.nama,
        array_tagihan: ids.join(','),
        billam: amounts.join(','),
        total: total,
        items: items
      })
    });

    const data = await res.json();
    const va = data?.data?.va_number || data?.va_number || nocust;

    if (data?.status && va) {
      document.getElementById('vaResult').innerHTML = `
        <div class="va-box">
          <h4>Nomor Virtual Account</h4>
          <div class="va-number" id="vaNumberText">${esc(va)}</div>
          <button type="button" class="btn-copy" onclick="copyVa()">Salin nomor VA</button>
          <p class="va-meta">Total: <b>${formatRp(total)}</b></p>
          <p class="va-help">Bayar ke nomor VA di atas. Setiap pembayaran berikutnya akan menambah baris baru di data VA.</p>
        </div>`;
      const actions = document.getElementById('payActions');
      if (actions) {
        actions.innerHTML = `<button type="button" class="btn-ghost" onclick="closePaymentModal()">Tutup</button>`;
      }
    } else {
      Swal.fire({ icon: 'error', title: 'Gagal membuat VA', text: data?.message || 'Silakan coba lagi.', ...swalTheme() });
      if (btn) { btn.disabled = false; btn.textContent = 'Buat nomor VA'; }
    }
  } catch (err) {
    Swal.fire({ icon: 'error', title: 'Terjadi kesalahan', text: 'Gagal memproses pembayaran. Coba beberapa saat lagi.', ...swalTheme() });
    if (btn) { btn.disabled = false; btn.textContent = 'Buat nomor VA'; }
  }
}

function copyVa() {
  const va = (document.getElementById('vaNumberText')?.textContent || '').trim();
  if (!va) return;
  const done = () => Swal.fire({ icon: 'success', title: 'Tersalin', text: 'Nomor VA sudah disalin.', timer: 1400, showConfirmButton: false, ...swalTheme() });
  if (navigator.clipboard && navigator.clipboard.writeText) {
    navigator.clipboard.writeText(va).then(done).catch(() => fallbackCopy(va, done));
  } else {
    fallbackCopy(va, done);
  }
}

function fallbackCopy(va, done) {
  const t = document.createElement('textarea');
  t.value = va;
  document.body.appendChild(t);
  t.select();
  document.execCommand('copy');
  t.remove();
  done();
}
</script>
</body>
</html>
