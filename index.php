<!DOCTYPE html>
<!--
LRS · ระบบเอกสารใบลาราชการ (สพฐ.) · Enterprise Single Page Application Engine
PHP + SQLite Enterprise Edition (พร้อมระบบอนุมัติแบบป๊อปอัป, กระดิ่งแจ้งเตือน & ลายเซ็นดิจิทัล)
-->
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>ระบบเอกสารใบลาราชการ · สพฐ.</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700;800&family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<link rel="manifest" href="manifest.json">
<meta name="theme-color" content="#0a84ff">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="default">
<meta name="apple-mobile-web-app-title" content="ใบลาราชการ">
<meta name="description" content="ระบบยื่น–อนุมัติ–พิมพ์เอกสารใบลาสำหรับข้าราชการครูและบุคลากรทางการศึกษา สพฐ.">
<style>
:root{
  --accent:#0a84ff; --accent2:#5e5ce6; --accent-d:#0060df;
  --grad:linear-gradient(135deg,#0a84ff,#5e5ce6);
  --ink:#1d1d1f; --ink2:#3a3a3c; --muted:#86868b; --line:#e3e3e8; --line2:#ececf1;
  --bg:#f5f5f7; --bg2:#ffffff; --glass:rgba(255,255,255,.78);
  --shadow:0 10px 40px rgba(0,0,0,.10); --shadow-sm:0 2px 10px rgba(0,0,0,.06);
  --radius:16px; --sidebar:264px;
  --ok:#34c759; --warn:#ff9f0a; --danger:#ff3b30; --info:#0a84ff; --purple:#af52de; --pink:#ff2d55;
}
[data-theme="macgraphite"]{--accent:#48484a;--accent2:#8e8e93;--accent-d:#333;--grad:linear-gradient(135deg,#5a5a5f,#8e8e93)}
[data-theme="macgreen"]{--accent:#34c759;--accent2:#30b0c7;--accent-d:#248a3d;--grad:linear-gradient(135deg,#34c759,#30b0c7)}
[data-theme="macpink"]{--accent:#ff2d55;--accent2:#ff375f;--accent-d:#d70036;--grad:linear-gradient(135deg,#ff2d55,#ff9f0a)}
[data-theme="macorange"]{--accent:#ff9500;--accent2:#ff375f;--accent-d:#c93400;--grad:linear-gradient(135deg,#ff9500,#ff2d55)}
[data-theme="macpurple"]{--accent:#af52de;--accent2:#5e5ce6;--accent-d:#8944ab;--grad:linear-gradient(135deg,#af52de,#5e5ce6)}
[data-theme="macteal"]{--accent:#30b0c7;--accent2:#0a84ff;--accent-d:#1d7f91;--grad:linear-gradient(135deg,#30b0c7,#0a84ff)}

/* Dark Mode */
[data-dark="1"]{
  --ink:#f5f5f7; --ink2:#d1d1d6; --muted:#8e8e93; --line:#38383a; --line2:#2c2c2e;
  --bg:#1c1c1e; --bg2:#2c2c2e; --glass:rgba(30,30,32,.88);
  --shadow:0 10px 40px rgba(0,0,0,.40); --shadow-sm:0 2px 10px rgba(0,0,0,.30);
}
[data-dark="1"] .sidebar{background:rgba(28,28,30,.9)}
[data-dark="1"] .navbar{background:rgba(28,28,30,.88)}
[data-dark="1"] .panel,[data-dark="1"] .card{background:#2c2c2e;border-color:#38383a}
[data-dark="1"] .input,[data-dark="1"] .select,[data-dark="1"] .textarea{background:#1c1c1e;border-color:#48484a;color:#f5f5f7}
[data-dark="1"] .btn-ghost{color:#ebebf5;border-color:#48484a}
[data-dark="1"] .btn-outline{color:var(--accent);border-color:var(--accent)}
[data-dark="1"] .lf-win{background:#2c2c2e}
[data-dark="1"] .lf-formpanel{background:#2c2c2e}
[data-dark="1"] .lf-input-wrap input{background:#1c1c1e;border-color:#48484a;color:#f5f5f7}
[data-dark="1"] .modal-card{background:#2c2c2e;border-color:#38383a}
[data-dark="1"] .sb-clock{background:rgba(255,255,255,.06)}
[data-dark="1"] .sb-user{background:rgba(255,255,255,.06)}
[data-dark="1"] .table td,[data-dark="1"] .table th{border-color:#38383a}
[data-dark="1"] .notify-popover{background:#2c2c2e;border-color:#38383a}


*{box-sizing:border-box;-webkit-tap-highlight-color:transparent}
html,body{margin:0;padding:0;max-width:100%;overflow-x:hidden}
body{font-family:'Sarabun','Kanit',system-ui,sans-serif;background:var(--bg);color:var(--ink);font-size:14px;line-height:1.55}
h1,h2,h3,h4,h5,.font-display,.hero-title,.kpi-val,.nav-title{font-family:'Kanit','Sarabun',system-ui,sans-serif}
a{color:var(--accent);text-decoration:none}
button{font-family:inherit}
::-webkit-scrollbar{width:8px;height:8px}::-webkit-scrollbar-thumb{background:#c7c7cc;border-radius:9px}

/* Boot loader */
#boot-loader{position:fixed;inset:0;z-index:400;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:18px;background:linear-gradient(160deg,#f5f5f7,#e9e9ef)}
.boot-ring{position:relative;width:84px;height:84px;display:flex;align-items:center;justify-content:center;color:var(--accent);font-size:34px}
.boot-ring::before{content:"";position:absolute;inset:0;border-radius:50%;border:3px solid rgba(10,132,255,.16);border-top-color:var(--accent);animation:spin 1s linear infinite}
.boot-txt{font-family:'Kanit';font-weight:600;color:var(--ink2);font-size:15px}
@keyframes spin{to{transform:rotate(360deg)}}

/* Shell Layout */
.shell{display:flex;align-items:stretch;min-height:100vh;width:100%}
.sidebar{flex:0 0 var(--sidebar);width:var(--sidebar);position:sticky;top:0;height:100vh;overflow-y:auto;background:var(--glass);backdrop-filter:blur(22px);-webkit-backdrop-filter:blur(22px);border-right:1px solid var(--line);z-index:60}
.main-area{flex:1 1 auto;min-width:0;display:flex;flex-direction:column}

/* Sidebar Content */
.sb-head{padding:18px 16px 12px;border-bottom:1px solid var(--line2)}
.sb-logo{display:flex;gap:11px;align-items:center}
.sb-logo-ic{width:42px;height:42px;border-radius:12px;background:var(--grad);display:flex;align-items:center;justify-content:center;color:#fff;font-size:21px;box-shadow:0 6px 16px rgba(10,132,255,.28)}
.sb-logo-img{width:42px;height:42px;border-radius:12px;object-fit:cover;box-shadow:var(--shadow-sm)}
.sb-name{font-family:'Kanit';font-weight:700;font-size:14px;line-height:1.2}
.sb-org{font-size:11px;color:var(--muted)}
.sb-clock{margin-top:12px;background:rgba(255,255,255,.6);border:1px solid var(--line2);border-radius:12px;padding:9px 12px;text-align:center}
.sb-clock-t{font-family:'Kanit';font-weight:700;font-size:19px;font-variant-numeric:tabular-nums;color:var(--accent)}
.sb-clock-d{font-size:11px;color:var(--muted);margin-top:1px}
.sb-user{margin-top:11px;display:flex;gap:10px;align-items:center;background:rgba(255,255,255,.55);border:1px solid var(--line2);border-radius:12px;padding:9px 11px}
.sb-av{width:38px;height:38px;border-radius:50%;background:var(--grad);color:#fff;display:flex;align-items:center;justify-content:center;font-family:'Kanit';font-weight:700;font-size:15px;flex:0 0 38px;overflow:hidden}
.sb-av img{width:100%;height:100%;object-fit:cover}
.sb-uinfo{min-width:0}
.sb-uname{font-weight:600;font-size:12.5px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.sb-nav{padding:12px 10px}
.sb-cat{font-size:10.5px;font-weight:700;letter-spacing:.06em;color:var(--muted);text-transform:uppercase;margin:14px 8px 6px}
.sb-cat:first-child{margin-top:2px}
.sb-link{display:flex;gap:11px;align-items:center;padding:10px 12px;border-radius:11px;color:var(--ink2);font-weight:500;font-size:13.5px;position:relative;transition:.15s;cursor:pointer}
.sb-link i{font-size:17px;width:20px;text-align:center}
.sb-link:hover{background:rgba(10,132,255,.07)}
.sb-link.active{background:var(--grad);color:#fff;box-shadow:0 6px 16px rgba(10,132,255,.26)}
.sb-link.is-logout{color:var(--danger)}

/* Navbar */
.navbar{position:sticky;top:0;z-index:55;display:flex;align-items:center;gap:12px;padding:11px 20px;background:var(--glass);backdrop-filter:blur(22px);-webkit-backdrop-filter:blur(22px);border-bottom:1px solid var(--line)}
.nav-burger{display:none;width:38px;height:38px;border:0;border-radius:10px;background:rgba(0,0,0,.05);color:var(--ink);font-size:18px;cursor:pointer}
.nav-pageic{width:40px;height:40px;border-radius:11px;background:var(--grad);color:#fff;display:flex;align-items:center;justify-content:center;font-size:19px;flex:0 0 40px}
.nav-title{font-family:'Kanit';font-weight:700;font-size:15px;line-height:1.15}
.nav-sub{font-size:11.5px;color:var(--muted)}
.nav-right{margin-left:auto;display:flex;align-items:center;gap:10px}

/* Theme Picker Dots */
.theme-picker{display:flex;align-items:center;gap:5px;padding:5px 9px;background:rgba(0,0,0,.04);border-radius:99px}
.theme-dot{width:16px;height:16px;border-radius:50%;cursor:pointer;border:2px solid transparent;transition:.15s}
.theme-dot:hover,.theme-dot.active{transform:scale(1.25);border-color:#fff;box-shadow:0 0 0 2px var(--accent)}

/* Notification Bell Dropdown */
.nav-notify-wrap{position:relative}
.nav-bell{width:36px;height:36px;border-radius:50%;border:0;background:rgba(0,0,0,.05);color:var(--ink);font-size:16px;display:flex;align-items:center;justify-content:center;cursor:pointer;position:relative;transition:.15s}
.nav-bell:hover{background:rgba(0,0,0,.09)}
.nav-badge{position:absolute;top:-2px;right:-2px;background:var(--danger);color:#fff;font-size:10px;font-weight:700;padding:2px 6px;border-radius:99px;line-height:1}
.notify-popover{position:absolute;top:44px;right:0;width:330px;max-height:420px;background:var(--glass);backdrop-filter:blur(22px);border:1px solid var(--line);border-radius:18px;box-shadow:var(--shadow);z-index:200;display:flex;flex-direction:column;overflow:hidden;animation:tin .2s ease-out}
.notify-header{padding:12px 16px;border-bottom:1px solid var(--line2);display:flex;justify-content:space-between;align-items:center;font-weight:700;font-size:13px}
.notify-list{overflow-y:auto;max-height:340px}
.notify-item{padding:11px 15px;border-bottom:1px solid var(--line2);cursor:pointer;transition:.15s;display:flex;gap:10px;align-items:flex-start}
.notify-item:hover{background:rgba(10,132,255,.05)}
.notify-item.unread{background:rgba(10,132,255,.08)}
.notify-item-title{font-weight:600;font-size:12.5px}
.notify-item-sub{font-size:11.5px;color:var(--muted);margin-top:2px}

.nav-pill{display:flex;align-items:center;gap:6px;background:rgba(52,199,89,.14);color:#248a3d;border-radius:99px;padding:5px 11px;font-size:11.5px;font-weight:600}
.nav-pill .dot{width:7px;height:7px;border-radius:50%;background:var(--ok);animation:pulse 2s infinite}
@keyframes pulse{0%,100%{opacity:1}50%{opacity:.4}}
.nav-prof{display:flex;align-items:center;gap:8px;background:rgba(0,0,0,.05);border:0;border-radius:99px;padding:5px 12px 5px 6px;cursor:pointer}
.nav-prof-av{width:30px;height:30px;border-radius:50%;background:var(--grad);color:#fff;display:flex;align-items:center;justify-content:center;font-family:'Kanit';font-weight:700;font-size:13px;overflow:hidden}
.nav-prof-av img{width:100%;height:100%;object-fit:cover}
.nav-prof-nm{font-size:12.5px;font-weight:600;max-width:130px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}

/* Page wrap & Hero */
.page-wrap{padding:22px;max-width:1180px;margin:0 auto;width:100%}
.hero{position:relative;overflow:hidden;background:var(--grad);color:#fff;border-radius:var(--radius);padding:22px 24px;box-shadow:0 14px 40px rgba(10,132,255,.28);margin-bottom:18px}
.hero::before{content:"";position:absolute;top:-40%;right:-10%;width:280px;height:280px;border-radius:50%;background:radial-gradient(circle,rgba(255,255,255,.22),transparent 62%);pointer-events:none}
.hero>*{position:relative;z-index:1}
.hero-pill{display:inline-flex;align-items:center;gap:6px;background:rgba(255,255,255,.2);border-radius:99px;padding:4px 12px;font-size:11.5px;font-weight:600;margin-bottom:8px}
.hero-title{font-weight:800;font-size:23px;margin:0}
.hero-sub{opacity:.9;font-size:13px;margin-top:3px}
.hero-kpis{display:grid;grid-template-columns:repeat(auto-fit,minmax(120px,1fr));gap:11px;margin-top:16px}
.hk{background:rgba(255,255,255,.16);border:1px solid rgba(255,255,255,.22);border-radius:13px;padding:11px 13px;backdrop-filter:blur(6px)}
.hk-v{font-family:'Kanit';font-weight:800;font-size:22px;line-height:1;font-variant-numeric:tabular-nums}
.hk-l{font-size:11px;opacity:.9;margin-top:3px}

/* Quota Grid & Progress Bars */
.quota-card-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:12px;margin-bottom:18px}
.qc{background:var(--bg2);border:1px solid var(--line);border-radius:15px;padding:14px 16px;box-shadow:var(--shadow-sm)}
.qc-top{display:flex;justify-content:space-between;align-items:center;margin-bottom:8px}
.qc-ttl{font-weight:700;font-size:13px;display:flex;align-items:center;gap:6px}
.qc-val{font-family:'Kanit';font-weight:800;font-size:18px;color:var(--accent)}
.qc-bar-bg{height:7px;background:#e3e3e8;border-radius:99px;overflow:hidden}
.qc-bar-fill{height:100%;border-radius:99px;background:var(--grad);transition:width .4s ease}
.qc-sub{font-size:11px;color:var(--muted);margin-top:6px;display:flex;justify-content:space-between}

/* Stat Cards & Grid */
.grid-stats{display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:13px;margin-bottom:18px}
.stat{background:var(--bg2);border:1px solid var(--line);border-radius:15px;padding:15px 16px;box-shadow:var(--shadow-sm);position:relative;overflow:hidden;transition:.2s;min-width:0}
.stat:hover{transform:translateY(-3px);box-shadow:var(--shadow)}
.stat-ic{width:44px;height:44px;border-radius:13px;display:flex;align-items:center;justify-content:center;color:#fff;font-size:20px;margin-bottom:10px}
.stat-v{font-family:'Kanit';font-weight:800;font-size:26px;line-height:1;font-variant-numeric:tabular-nums}
.stat-l{font-size:12.5px;color:var(--muted);margin-top:4px}
.g-blue{background:linear-gradient(135deg,#0a84ff,#5e5ce6)}
.g-green{background:linear-gradient(135deg,#34c759,#30b0c7)}
.g-orange{background:linear-gradient(135deg,#ff9f0a,#ff375f)}
.g-red{background:linear-gradient(135deg,#ff3b30,#ff375f)}
.g-purple{background:linear-gradient(135deg,#af52de,#5e5ce6)}
.g-teal{background:linear-gradient(135deg,#30b0c7,#0a84ff)}
.g-gray{background:linear-gradient(135deg,#8e8e93,#636366)}

.panel{background:var(--bg2);border:1px solid var(--line);border-radius:var(--radius);box-shadow:var(--shadow-sm);padding:18px;margin-bottom:16px}
.panel-h{display:flex;align-items:center;gap:9px;margin-bottom:14px}
.panel-h .ttl{font-family:'Kanit';font-weight:700;font-size:15px}
.panel-h .ic{width:32px;height:32px;border-radius:9px;background:rgba(10,132,255,.1);color:var(--accent);display:flex;align-items:center;justify-content:center;font-size:16px}
.btn{display:inline-flex;align-items:center;justify-content:center;gap:7px;border:1px solid var(--line);background:var(--bg2);color:var(--ink);border-radius:11px;padding:9px 15px;font-weight:600;font-size:13px;cursor:pointer;transition:.15s;white-space:nowrap}
.btn:hover{transform:translateY(-1px);box-shadow:var(--shadow-sm)}
.btn-primary{background:var(--grad);color:#fff;border:0;box-shadow:0 6px 16px rgba(10,132,255,.3)}
.btn-ghost{background:rgba(0,0,0,.04)}
.btn-ok{background:linear-gradient(135deg,#34c759,#248a3d);color:#fff;border:0}
.btn-danger{background:linear-gradient(135deg,#ff3b30,#d70036);color:#fff;border:0}
.btn-sm{padding:6px 11px;font-size:12px;border-radius:99px}

.badge{display:inline-flex;align-items:center;gap:5px;padding:4px 10px;border-radius:99px;font-size:11.5px;font-weight:600}
.b-draft{background:#ececf1;color:#636366}
.b-submitted{background:rgba(255,159,10,.15);color:#c26a00}
.b-reviewed{background:rgba(10,132,255,.14);color:#0060df}
.b-approved{background:rgba(52,199,89,.16);color:#248a3d}
.b-rejected{background:rgba(255,59,48,.14);color:#c9271f}
.b-cancelled{background:#f2e6e6;color:#8e8e93}
.role-chip{display:inline-flex;align-items:center;gap:5px;padding:3px 9px;border-radius:99px;font-size:11px;font-weight:600}
.role-teacher{background:rgba(52,199,89,.16);color:#248a3d}
.role-clerk{background:rgba(48,176,199,.16);color:#1d7f91}
.role-director{background:rgba(175,82,222,.16);color:#8944ab}
.role-admin{background:rgba(255,159,10,.18);color:#c26a00}
.type-chip{display:inline-flex;align-items:center;gap:5px;padding:3px 9px;border-radius:8px;font-size:11px;font-weight:600;background:rgba(10,132,255,.1);color:var(--accent)}

.toolbar{display:flex;flex-wrap:wrap;gap:10px;align-items:center;margin-bottom:16px}
.search{flex:1 1 240px;min-width:0;display:flex;align-items:center;gap:8px;background:var(--bg2);border:1px solid var(--line);border-radius:12px;padding:9px 13px}
.search i{color:var(--muted);font-size:16px}
.search input{flex:1;border:0;outline:0;background:0;font-family:inherit;font-size:13.5px;min-width:0}

.form-grid{display:grid;gap:16px;margin-bottom:16px}
.fg-2{grid-template-columns:1fr 1fr}
.fg-3{grid-template-columns:1fr 1fr 1fr}
.field{display:flex;flex-direction:column;gap:7px;margin-bottom:16px}
.form-grid .field{margin-bottom:0}
.field label{font-weight:600;font-size:13px;color:var(--ink2);line-height:1.45;display:inline-flex;align-items:center;gap:5px;margin:0}
.field label i{font-size:15px;margin-right:2px}
.field .req{color:var(--danger);font-weight:700}
.field-hint{font-size:12px;color:var(--muted);margin-top:2px;line-height:1.4}
.input,.textarea,.select{border:1px solid var(--line);border-radius:12px;padding:10.5px 14px;font-family:inherit;font-size:13.5px;color:var(--ink);background:var(--bg2);outline:0;transition:border-color .18s ease,box-shadow .18s ease,background-color .18s ease;width:100%;box-shadow:0 1px 3px rgba(0,0,0,.03)}
.input:focus,.textarea:focus,.select:focus{border-color:var(--accent);box-shadow:0 0 0 4px rgba(10,132,255,.14);background:var(--bg2)}
.textarea{resize:vertical;min-height:85px}
.radio-cards{display:grid;grid-template-columns:repeat(auto-fill,minmax(150px,1fr));gap:10px}
.radio-card{border:1.5px solid var(--line);border-radius:13px;padding:13px;cursor:pointer;display:flex;gap:11px;align-items:center;transition:.15s;background:var(--bg2)}
.radio-card:hover{border-color:var(--accent)}
.radio-card.selected{border-color:var(--accent);background:rgba(10,132,255,.05);box-shadow:0 0 0 3px rgba(10,132,255,.15)}
.radio-card .rc-ic{width:40px;height:40px;border-radius:11px;background:var(--grad);color:#fff;display:flex;align-items:center;justify-content:center;font-size:19px;flex:0 0 40px}
.radio-card .rc-t{font-weight:700;font-size:13px;font-family:'Kanit'}
.radio-card .rc-s{font-size:11px;color:var(--muted)}
.wiz-nav{display:flex;align-items:center;gap:10px;margin-top:22px;padding-top:16px;border-top:1px solid var(--line2)}

#toast-host{position:fixed;top:16px;right:16px;z-index:350;display:flex;flex-direction:column;gap:9px;pointer-events:none}
.toast{pointer-events:auto;min-width:260px;max-width:380px;background:var(--glass);backdrop-filter:blur(18px);border:1px solid rgba(255,255,255,.6);border-radius:13px;box-shadow:0 12px 34px rgba(0,0,0,.16);padding:12px 15px;display:flex;gap:11px;align-items:center;border-left:4px solid var(--accent);animation:tin .28s cubic-bezier(.2,.9,.3,1.05)}
.toast.success{border-left-color:var(--ok)}.toast.error{border-left-color:var(--danger)}
.toast .ti{font-size:20px}.toast.success .ti{color:var(--ok)}.toast.error .ti{color:var(--danger)}
.toast .tm{font-size:13px;font-weight:500}
@keyframes tin{from{opacity:0;transform:translateX(30px)}to{opacity:1;transform:none}}

#spin-host{display:none;position:fixed;inset:0;z-index:300;align-items:center;justify-content:center;background:rgba(245,245,247,.6);backdrop-filter:blur(8px)}
#spin-host.on{display:flex}
.spin-card{background:var(--glass);border:1px solid rgba(255,255,255,.6);border-radius:22px;padding:28px 34px;text-align:center;box-shadow:0 24px 60px rgba(0,0,0,.18);min-width:220px}
.spin-orbit{position:relative;width:64px;height:64px;margin:0 auto 14px;display:flex;align-items:center;justify-content:center;color:var(--accent);font-size:26px}
.spin-orbit::before{content:"";position:absolute;inset:0;border-radius:50%;border:3px solid rgba(10,132,255,.16);border-top-color:var(--accent);animation:spin 1.1s linear infinite}

.tbl-wrap{overflow-x:auto;border-radius:12px;border:1px solid var(--line)}
.tbl{width:100%;border-collapse:collapse;font-size:13px}
.tbl th{background:#fafafd;text-align:left;padding:11px 13px;font-weight:600;color:var(--muted);font-size:11.5px;text-transform:uppercase;letter-spacing:.03em;white-space:nowrap;border-bottom:1px solid var(--line)}
.tbl td{padding:11px 13px;border-bottom:1px solid var(--line2);vertical-align:middle}
.tbl tr:last-child td{border-bottom:0}
.tbl tr.clk{cursor:pointer}
.tbl tr.clk:hover td{background:rgba(10,132,255,.04)}

/* Login Screen */
.lf-stage{min-height:100vh;display:flex;flex-direction:column;align-items:center;justify-content:center;padding:24px;position:relative;overflow:hidden;background:linear-gradient(155deg,#e8eefb,#f5f0fb 55%,#fbeef4)}
.lf-win{width:100%;max-width:960px;display:grid;grid-template-columns:1.05fr 1fr;background:var(--glass);backdrop-filter:blur(24px);border:1px solid rgba(255,255,255,.6);border-radius:24px;box-shadow:0 40px 90px rgba(0,0,0,.16);overflow:hidden;position:relative;z-index:1}
.lf-brandpanel{background:linear-gradient(150deg,#0a84ff,#5e5ce6);color:#fff;padding:38px 34px;display:flex;flex-direction:column;position:relative;overflow:hidden}
.lf-logo{width:56px;height:56px;border-radius:16px;background:rgba(255,255,255,.18);display:flex;align-items:center;justify-content:center;font-size:28px;margin-bottom:14px}
.lf-title{font-family:'Kanit';font-weight:800;font-size:26px;line-height:1.2}
.lf-sub{opacity:.9;font-size:13px;margin-top:6px;line-height:1.7}
.lf-stats{display:flex;gap:16px;margin-top:auto;padding-top:20px}
.lf-stat-v{font-family:'Kanit';font-weight:800;font-size:22px}
.lf-stat-l{opacity:.85;font-size:11px}
.lf-formpanel{padding:38px 34px;display:flex;flex-direction:column;justify-content:center}
.lf-h{font-family:'Kanit';font-weight:800;font-size:24px}
.lf-hs{font-size:13px;color:var(--muted);margin-top:4px;margin-bottom:20px}
.lf-form{display:flex;flex-direction:column;gap:14px}
.lf-input-wrap{position:relative;display:flex;align-items:center;border:1px solid var(--line);border-radius:12px;background:var(--bg2);transition:.15s}
.lf-icon{position:absolute;left:13px;color:var(--muted);font-size:17px}
.lf-input-wrap input{flex:1;border:0;outline:0;background:0;padding:12px 44px;font-family:inherit;font-size:14px}
.lf-submit{margin-top:4px;width:100%;display:block;background:var(--grad);color:#fff;border:0;border-radius:13px;height:50px;font-family:'Kanit';font-weight:700;font-size:15px;cursor:pointer;box-shadow:0 8px 22px rgba(10,132,255,.3)}
.lf-demo{margin-top:20px;border:1px dashed rgba(10,132,255,.3);border-radius:14px;padding:12px;background:rgba(255,255,255,.4)}
.lf-demo-h{display:flex;align-items:center;gap:6px;font-size:12px;font-weight:600;color:var(--ink2);margin-bottom:10px}
.lf-demo-h .tag{margin-left:auto;background:linear-gradient(135deg,#ff9f0a,#ff375f);color:#fff;padding:2px 8px;border-radius:99px;font-size:10px}
.lf-demo-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:8px}
.lf-demo-card{border:1px solid var(--line);border-radius:11px;padding:9px 5px;text-align:center;cursor:pointer;background:var(--bg2);transition:.15s}
.lf-demo-card:hover{transform:translateY(-2px);box-shadow:var(--shadow-sm)}
.lf-demo-ic{width:34px;height:34px;border-radius:10px;margin:0 auto 5px;display:flex;align-items:center;justify-content:center;color:#fff;font-size:15px}
.lf-demo-card[data-role="admin"] .lf-demo-ic{background:linear-gradient(135deg,#ff9f0a,#ff9500)}
.lf-demo-card[data-role="director"] .lf-demo-ic{background:linear-gradient(135deg,#af52de,#5e5ce6)}
.lf-demo-card[data-role="clerk"] .lf-demo-ic{background:linear-gradient(135deg,#30b0c7,#0a84ff)}
.lf-demo-card[data-role="teacher"] .lf-demo-ic{background:linear-gradient(135deg,#34c759,#30b0c7)}
.lf-demo-role{font-size:10.5px;font-weight:700}
.lf-demo-user{font-size:9.5px;color:var(--muted);font-family:monospace}

.mono{font-family:monospace}
.muted{color:var(--muted)}
.mt16{margin-top:16px}

/* Modal System */
.modal-overlay{position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,.5);display:flex;align-items:center;justify-content:center;z-index:300;backdrop-filter:blur(4px)}
.modal-card{width:95%;max-width:580px;background:var(--bg2);border-radius:20px;box-shadow:0 24px 60px rgba(0,0,0,.25);padding:24px;max-height:90vh;overflow-y:auto;position:relative}
.modal-head{display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;padding-bottom:12px;border-bottom:1px solid var(--line2)}
.modal-ttl{font-family:'Kanit';font-weight:700;font-size:17px;margin:0;display:flex;align-items:center;gap:8px}

/* PRINT STYLES FOR ALL 5 OFFICIAL THAI FORMS (A4 Standard) */
#print-area{display:none}
.pr-doc{font-family:'Sarabun','TH Sarabun New',serif;color:#000;font-size:16px;line-height:1.65}
.pr-page{position:relative;width:210mm;min-height:297mm;margin:0 auto 10mm;padding:15mm 20mm;background:#fff;box-sizing:border-box;box-shadow:0 8px 30px rgba(0,0,0,.12)}
.pr-title{font-family:'Sarabun',serif;font-weight:700;font-size:20px;text-align:center;margin-bottom:12px}
.pr-header-right{text-align:right;margin-bottom:14px}
.pr-line-dots{display:inline-block;border-bottom:1px dotted #000;padding:0 6px;text-align:center;font-weight:600}
.pr-prow{display:flex;flex-wrap:wrap;align-items:baseline;gap:0 6px;margin:5px 0;font-size:15.5px}
.pr-chk{display:inline-block;width:15px;height:15px;border:1px solid #000;vertical-align:-2px;text-align:center;line-height:13px;font-size:12px;margin:0 4px}
.pr-chk.on::after{content:"✓";font-weight:700}
.pr-sign-block{margin-top:14px;text-align:center;margin-left:auto;width:55%}
.pr-sig-img{max-height:46px;vertical-align:middle;margin:-8px 0}
.pr-bottom-grid{display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-top:16px;border-top:1px solid #ddd;padding-top:14px}
.pr-stat-tbl{width:100%;border-collapse:collapse;margin-top:8px;font-size:14px}
.pr-stat-tbl th,.pr-stat-tbl td{border:1px solid #000;padding:4px 3px;text-align:center;height:26px}
.pr-stat-tbl th{font-weight:700;background:#f9f9f9}

@media print{
  @page{size:A4 portrait;margin:0}
  body.printing #app-root,body.printing #modal-host,body.printing #spin-host,body.printing #boot-loader,body.printing #toast-host{display:none!important}
  body.printing #print-area{display:block!important}
  .pr-page{margin:0;box-shadow:none;width:auto;min-height:auto;padding:15mm 18mm;page-break-after:always}
  .pr-bottom-grid,.pr-stat-tbl{page-break-inside:avoid}
  *{-webkit-print-color-adjust:exact;print-color-adjust:exact}
}

/* Sidebar Backdrop for Mobile */
.sidebar-backdrop { position: fixed; inset: 0; background: rgba(0,0,0,0.5); backdrop-filter: blur(4px); -webkit-backdrop-filter: blur(4px); z-index: 290; opacity: 0; pointer-events: none; transition: opacity 0.25s ease; }
.sidebar-backdrop.open { opacity: 1; pointer-events: auto; }

.lf-mobile-header { display: none; }

/* Default Desktop: Hide Mobile Elements */
.mobile-bottom-bar, .mobile-fab { display: none !important; }
.desktop-only { display: flex; }

@media(max-width:1024px){
  html, body, .shell, .main-area { max-width: 100vw !important; overflow-x: hidden !important; }
  .sidebar{position:fixed;left:-300px;width:280px;flex:none;transition:left .25s;z-index:300}
  .sidebar.open{left:0}
  .nav-burger{display:flex}
  
  /* Mobile & Tablet Header Clean Up */
  .theme-picker, .nav-pill, .nav-prof, .nav-prof-nm, .nav-sub, .desktop-only { display: none !important; }
  .navbar { padding: 8px 12px; position: sticky; top: 0; z-index: 100; gap: 8px; justify-content: space-between; width: 100%; max-width: 100vw; }
  .nav-title { font-size: 14px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 160px; }
  .nav-pageic { width: 34px; height: 34px; font-size: 16px; flex: 0 0 34px; border-radius: 9px; }
  .nav-burger { width: 36px; height: 36px; font-size: 18px; border-radius: 9px; }
  .nav-right { gap: 6px; }
}

@media(max-width:768px){
  body{font-size:15px;padding-bottom:70px}
  .shell{flex-direction:row}
  .sidebar{width:280px;z-index:300}

  .page-wrap{padding:12px 10px 80px;max-width:100%}
  .hero{padding:18px 16px;border-radius:14px;margin-bottom:14px}
  .hero-title{font-size:20px}
  .hero-sub{font-size:12px}
  .hero-kpis{grid-template-columns:1fr 1fr;gap:8px}
  .quota-card-grid{grid-template-columns:1fr 1fr;gap:8px}
  .grid-stats{grid-template-columns:1fr 1fr;gap:8px}
  .panel{padding:14px 12px;border-radius:14px;margin-bottom:12px}
  .form-grid.fg-2,.form-grid.fg-3,.form-grid.fg-4{grid-template-columns:1fr}
  
  /* Mobile Login Page Improvements */
  .lf-stage { padding: 16px 12px; background: linear-gradient(155deg,#e8eefb,#f5f0fb 55%,#fbeef4); }
  .lf-win { grid-template-columns: 1fr; max-width: 440px; border-radius: 22px; box-shadow: 0 20px 50px rgba(0,0,0,0.12); }
  .lf-brandpanel { display: none; }
  .lf-formpanel { padding: 26px 20px; }
  .lf-mobile-header { display: block !important; margin-bottom: 22px; text-align: center; }
  .lf-mobile-header .lf-logo { margin: 0 auto 10px; width: 52px; height: 52px; border-radius: 16px; background: var(--grad); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 28px; box-shadow: 0 8px 20px rgba(10,132,255,0.3); }
  .lf-mobile-header .lf-title { font-family: 'Kanit'; font-weight: 800; font-size: 21px; color: var(--ink); margin: 0; }
  .lf-mobile-header .lf-sub { font-size: 12.5px; color: var(--muted); margin-top: 3px; }
  .lf-demo-grid { grid-template-columns: 1fr 1fr; gap: 8px; }
  .lf-demo-card { padding: 9px 6px; }
  .lf-input-wrap input { font-size: 16px !important; min-height: 48px; }

  /* Large touch-friendly input elements (Prevents iOS Auto-Zoom) */
  .input,.select,.textarea{font-size:15px!important;min-height:44px;padding:10px 14px;border-radius:12px}
  .field label{font-size:13px;font-weight:600;margin:0}
  .field-hint{font-size:11.5px}
  
  /* Full-width touch buttons */
  .btn{min-height:46px;font-size:14px;padding:10px 16px;border-radius:12px;justify-content:center;width:100%}
  .btn-sm{min-height:36px;padding:6px 12px;width:auto;font-size:12px}
  .toolbar{flex-direction:column;align-items:stretch;gap:8px}
  .toolbar .btn{width:100%}
  
  /* Mobile Tables & Cards */
  .tbl-wrap{border-radius:12px;overflow-x:auto;-webkit-overflow-scrolling:touch}
  .tbl th,.tbl td{padding:10px 8px;font-size:12.5px;white-space:nowrap}

  /* Mobile Modals (Bottom Sheet) */
  .modal-overlay{align-items:flex-end;padding:0;background:rgba(0,0,0,.55)}
  .modal-card{width:100%!important;max-width:100%!important;border-bottom-left-radius:0;border-bottom-right-radius:0;border-top-left-radius:24px;border-top-right-radius:24px;max-height:90vh;overflow-y:auto;padding:20px 16px;animation:slideUp .25s ease-out}
  @keyframes slideUp{from{transform:translateY(100%)}to{transform:translateY(0)}}

  /* Mobile Bottom App Navigation Bar */
  .mobile-bottom-bar{display:flex!important;position:fixed;bottom:0;left:0;right:0;height:64px;background:rgba(255,255,255,0.92);backdrop-filter:blur(20px);-webkit-backdrop-filter:blur(20px);border-top:1px solid rgba(0,0,0,0.1);z-index:250;justify-content:space-around;align-items:center;box-shadow:0 -4px 20px rgba(0,0,0,0.06)}
  .mbb-item{display:flex;flex-direction:column;align-items:center;justify-content:center;gap:3px;color:var(--muted);text-decoration:none;font-size:10.5px;font-weight:600;flex:1;height:100%;cursor:pointer;transition:.15s}
  .mbb-item i{font-size:20px}
  .mbb-item.active{color:var(--accent);font-weight:700}

  /* Mobile Floating Quick Action Button */
  .mobile-fab{display:flex!important;position:fixed;bottom:76px;right:16px;width:56px;height:56px;border-radius:50%;background:var(--grad);color:#fff;align-items:center;justify-content:center;font-size:24px;box-shadow:0 10px 25px rgba(10,132,255,0.4);z-index:240;border:0;cursor:pointer;transition:.15s}
  .mobile-fab:active{transform:scale(0.92)}
}

/* Mobile-First Navbar Header Rules */
.nav-sub, .theme-picker, .nav-pill, .nav-prof, .nav-prof-nm, .desktop-only { display: none !important; }

@media(min-width: 901px) {
  .theme-picker { display: flex !important; }
  .nav-pill { display: flex !important; }
  .nav-prof { display: flex !important; }
  .nav-prof-nm { display: inline !important; }
  .nav-sub { display: block !important; }
  .desktop-only { display: flex !important; }
}
/* Calendar */
.cal-wrap{overflow-x:auto}
.cal-grid{display:grid;grid-template-columns:repeat(7,1fr);gap:2px;min-width:280px}
.cal-head{display:grid;grid-template-columns:repeat(7,1fr);gap:2px;margin-bottom:4px}
.cal-dh{text-align:center;font-size:11px;font-weight:700;color:var(--muted);padding:4px 0}
.cal-cell{min-height:64px;padding:4px 5px;border-radius:10px;border:1px solid var(--line2);background:var(--bg2);position:relative;font-size:11.5px;cursor:default;transition:.1s}
.cal-cell:hover{background:rgba(10,132,255,.06)}
.cal-cell.today{border-color:var(--accent);background:rgba(10,132,255,.07)}
.cal-cell.other-month{opacity:.35}
.cal-day-num{font-weight:700;font-size:13px;color:var(--ink2);margin-bottom:2px}
.cal-cell.today .cal-day-num{color:var(--accent)}
.cal-dot{display:block;font-size:10px;border-radius:4px;padding:1px 4px;margin-bottom:1px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.cal-dot-sick{background:rgba(255,59,48,.15);color:#c9271f}
.cal-dot-personal{background:rgba(255,159,10,.15);color:#c26a00}
.cal-dot-vacation{background:rgba(10,132,255,.15);color:#0060df}
.cal-dot-maternity{background:rgba(175,82,222,.15);color:#8944ab}
.cal-dot-other{background:rgba(52,199,89,.15);color:#248a3d}
.cal-nav{display:flex;align-items:center;gap:12px;margin-bottom:16px}
.cal-nav-title{flex:1;font-family:Kanit;font-weight:700;font-size:17px}

/* Stats/Charts */
.stat-bar-wrap{display:flex;gap:4px;align-items:flex-end;height:80px;margin-top:8px}
.stat-bar-col{display:flex;flex-direction:column;align-items:center;flex:1;gap:2px}
.stat-bar{background:var(--grad);border-radius:6px 6px 0 0;width:100%;transition:.4s;min-height:2px}
.stat-bar-lbl{font-size:9.5px;color:var(--muted);font-weight:600}
.stat-bar-val{font-size:9px;color:var(--ink2);font-weight:700}
.kpi-row{display:flex;flex-wrap:wrap;gap:12px;margin-bottom:20px}
.kpi-card{flex:1 1 130px;background:var(--bg2);border:1px solid var(--line2);border-radius:16px;padding:14px 16px;box-shadow:var(--shadow-sm)}
.kpi-val{font-family:Kanit;font-weight:800;font-size:28px;line-height:1.1}
.kpi-lbl{font-size:11.5px;color:var(--muted);margin-top:2px}
.kpi-icon{font-size:22px;margin-bottom:4px}

/* Attachments */
.att-list{display:flex;flex-direction:column;gap:8px;margin-top:12px}
.att-item{display:flex;align-items:center;gap:10px;background:var(--bg);border:1px solid var(--line2);border-radius:10px;padding:9px 12px}
.att-icon{font-size:22px;color:var(--accent);flex:0 0 auto}
.att-name{flex:1;font-size:13px;font-weight:500;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.att-size{font-size:11px;color:var(--muted)}
.att-drop{border:2px dashed var(--line);border-radius:14px;padding:24px;text-align:center;color:var(--muted);cursor:pointer;transition:.15s}
.att-drop:hover{border-color:var(--accent);background:rgba(10,132,255,.04)}

/* History Chart */
.hist-table{width:100%;border-collapse:collapse;font-size:13px}
.hist-table th{text-align:left;padding:8px 10px;border-bottom:2px solid var(--line);color:var(--muted);font-size:11.5px;font-weight:700}
.hist-table td{padding:8px 10px;border-bottom:1px solid var(--line2)}
.hist-bar-cell{width:120px}
.hist-bar-inner{height:8px;background:var(--grad);border-radius:99px;transition:.4s}

/* Dark mode bottom bar */
[data-dark="1"] .mobile-bottom-bar{background:rgba(28,28,30,.94)}
[data-dark="1"] .att-item{background:var(--bg2)}

/* Filter row */
.filter-row{display:flex;flex-wrap:wrap;gap:8px;margin-bottom:12px}
.filter-row .select,.filter-row .input{padding:7px 10px;font-size:12.5px}

/* Dark mode toggle button */
.dark-toggle{width:38px;height:38px;border:1px solid var(--line);border-radius:10px;background:var(--bg2);color:var(--ink);font-size:17px;display:flex;align-items:center;justify-content:center;cursor:pointer;transition:.15s}
.dark-toggle:hover{background:var(--line2)}

/* Boot Loader Overlay System */
#boot-loader{position:fixed;top:0;left:0;right:0;bottom:0;background:var(--bg);display:flex;flex-direction:column;align-items:center;justify-content:center;z-index:99999;transition:opacity .3s ease,visibility .3s ease}
.boot-ring{width:64px;height:64px;border-radius:50%;border:4px solid rgba(10,132,255,.15);border-top-color:var(--accent);display:flex;align-items:center;justify-content:center;font-size:26px;color:var(--accent);animation:spin 1s linear infinite;margin-bottom:16px}
.boot-txt{font-family:'Kanit',sans-serif;font-weight:600;font-size:15px;color:var(--ink2)}

</style>
</head>
<body>

<div id="boot-loader">
  <div class="boot-ring"><i class="bi bi-file-earmark-text-fill"></i></div>
  <div class="boot-txt" id="bl-text">กำลังเริ่มต้นระบบ…</div>
</div>

<script>
// Fail-safe: Automatically dismiss boot-loader after 1.5s maximum under any network condition
setTimeout(function() {
  var bl = document.getElementById('boot-loader');
  if (bl) {
    bl.style.opacity = '0';
    bl.style.visibility = 'hidden';
    setTimeout(function() { if (bl && bl.parentNode) bl.parentNode.removeChild(bl); }, 350);
  }
}, 1500);
</script>

<div id="app-root"></div>
<div id="modal-host"></div>
<div id="spin-host"></div>
<div id="toast-host"></div>
<div id="print-area"></div>

<script>
// -------------------------------------------------------------
// Core SPA Application Engine with Quota, Modals & Notifications
// -------------------------------------------------------------

(function () {
  'use strict';

  window.onerror = function (msg, url, line) { console.error('[LRS Exception]', msg, 'at', url, 'line', line); };
  window.onunhandledrejection = function (e) { console.error('[LRS Unhandled Promise]', e.reason); };

  var Store = { token: '', user: null, caps: [], boot: {}, cache: {} };
  var Routes = {};

  function $(s, r) { return (r || document).querySelector(s); }
  function $$(s, r) { return Array.prototype.slice.call((r || document).querySelectorAll(s)); }
  function el(id) { return document.getElementById(id); }
  function esc(s) { return String(s == null ? '' : s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#39;'); }
  function num(v) { var n = Number(String(v == null ? '' : v).replace(/,/g, '')); return isNaN(n) ? 0 : n; }
  function hideBoot() {
    var bl = el('boot-loader');
    if (bl) {
      bl.style.transition = 'opacity 0.3s ease, visibility 0.3s ease';
      bl.style.opacity = '0';
      bl.style.visibility = 'hidden';
      setTimeout(function() { if (bl && bl.parentNode) bl.parentNode.removeChild(bl); }, 350);
    }
  }

  // Date Formatting
  var TH = {
    M: ['มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน', 'กรกฎาคม', 'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม'],
    MS: ['ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.', 'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'],
    parse: function(v) { if (!v) return null; if (v instanceof Date) return v; var s = String(v); var d = new Date(/^\d{4}-\d{2}-\d{2}$/.test(s) ? s + 'T00:00:00' : s); return isNaN(d.getTime()) ? null : d; },
    date: function(v) { var d = this.parse(v); if (!d) return '-'; return d.getDate() + ' ' + this.MS[d.getMonth()] + ' ' + (d.getFullYear() + 543); },
    dateLong: function(v) { var d = this.parse(v); if (!d) return '-'; return d.getDate() + ' ' + this.M[d.getMonth()] + ' ' + (d.getFullYear() + 543); },
    time: function(v) { var d = this.parse(v); if (!d) return '-'; return _p2(d.getHours()) + ':' + _p2(d.getMinutes()) + ' น.'; },
    dt: function(v) { return this.date(v) + ' ' + this.time(v); }
  };
  function _p2(n) { return n < 10 ? '0' + n : '' + n; }

  function applyTheme(theme) {
    theme = theme || localStorage.getItem('lrs_theme') || 'macblue';
    document.documentElement.setAttribute('data-theme', theme);
    localStorage.setItem('lrs_theme', theme);
    $$('.theme-dot').forEach(function(dot) {
      dot.classList.toggle('active', dot.getAttribute('data-t') === theme);
    });
  }

  function hasCap(cap) {
    if (!cap || cap === '*') return true;
    var caps = Store.caps || [];
    return String(cap).split('|').some(function(c) {
      c = String(c || '').trim();
      if (!c) return false;
      if (c === '*') return true;
      if (caps.indexOf(c) >= 0) return true;
      return false;
    });
  }

  // Toast Notification System
  function toast(msg, type, dur) {
    var host = el('toast-host'); if (!host) return;
    type = type || 'info';
    var existing = $$('.toast .tm', host);
    for (var i = 0; i < existing.length; i++) {
      if (existing[i].textContent === msg) return;
    }
    var ic = { success: 'check-circle-fill', error: 'x-circle-fill', warning: 'exclamation-triangle-fill', info: 'info-circle-fill' }[type];
    var d = document.createElement('div');
    d.className = 'toast ' + type;
    d.innerHTML = '<i class="bi bi-' + ic + ' ti"></i><div class="tm">' + esc(msg) + '</div>';
    host.appendChild(d);
    setTimeout(function () {
      d.style.transition = '.3s'; d.style.opacity = '0'; d.style.transform = 'translateX(30px)';
      setTimeout(function () { d.remove(); }, 300);
    }, dur || 3500);
  }

  // API Call Wrapper with Auto-Retry Logic
  function call(action, payload, timeoutMs, isRetry) {
    timeoutMs = timeoutMs || 15000;
    return new Promise(function (resolve, reject) {
      var isDone = false;
      var timer = setTimeout(function() {
        if (!isDone) {
          isDone = true;
          reject(new Error('การเชื่อมต่อหมดเวลา กรุณาลองใหม่อีกครั้ง'));
        }
      }, timeoutMs);

      fetch('api.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json;charset=utf-8' },
        body: JSON.stringify({ action: action, token: Store.token || '', payload: payload || {} })
      })
      .then(function(r) { return r.json(); })
      .then(function(res) {
        if (isDone) return;
        isDone = true;
        clearTimeout(timer);

        if (res && res.ok) {
          resolve(res.data);
        } else {
          var errText = (res && res.error) || 'เกิดข้อผิดพลาดในการรับข้อมูล';
          if (errText.indexOf('เซสชันหมดอายุ') >= 0 || errText.indexOf('ต้องเข้าสู่ระบบก่อน') >= 0) {
            Store.token = '';
            Store.user = null;
            try { localStorage.removeItem('lrs_token'); } catch(e){}
            renderLanding();
          }
          reject(new Error(errText));
        }
      })
      .catch(function(e) {
        if (isDone) return;
        if (!isRetry) {
          clearTimeout(timer);
          setTimeout(function() {
            call(action, payload, timeoutMs, true).then(resolve).catch(reject);
          }, 300);
          return;
        }
        isDone = true;
        clearTimeout(timer);
        reject(new Error('ไม่สามารถเชื่อมต่อเซิร์ฟเวอร์ได้ (' + e.message + ')'));
      });
    });
  }

  var Spinner = {
    show: function (msg) {
      var host = el('spin-host'); if (!host) return;
      host.innerHTML = '<div class="spin-card"><div class="spin-orbit"><i class="bi bi-hourglass-split"></i></div><div class="spin-msg">' + esc(msg || 'กำลังดำเนินการ…') + '</div></div>';
      host.classList.add('on');
    },
    hide: function () {
      var host = el('spin-host'); if (host) host.classList.remove('on');
    }
  };

  function field(name, label, opts) {
    opts = opts || {};
    var type = opts.type || 'text';
    var val = opts.value == null ? '' : opts.value;
    var req = opts.required ? '<span class="req"> *</span>' : '';
    var attrs = 'name="' + esc(name) + '" data-f="' + esc(name) + '"' + (opts.attrs || '');
    var inner;
    if (type === 'textarea') inner = '<textarea class="textarea" ' + attrs + ' placeholder="' + esc(opts.ph || '') + '">' + esc(val) + '</textarea>';
    else if (type === 'select') {
      inner = '<select class="select" ' + attrs + '>' + (opts.options || []).map(function (o) {
        return '<option value="' + esc(o.v) + '"' + (String(o.v) === String(val) ? ' selected' : '') + '>' + esc(o.t) + '</option>';
      }).join('') + '</select>';
    } else {
      inner = '<input class="input" type="' + type + '" ' + attrs + ' value="' + esc(val) + '" placeholder="' + esc(opts.ph || '') + '">';
    }
    return '<div class="field" data-fw="' + esc(name) + '"><label>' + esc(label) + req + '</label>' + inner + (opts.hint ? '<div class="field-hint">' + esc(opts.hint) + '</div>' : '') + '</div>';
  }

  function route(hash, fn) { Routes[hash] = fn; }
  function go(hash) { if (hash === location.hash) dispatch(); else location.hash = hash; }
  function dispatch() {
    if (!Store.user) { renderLanding(); return; }
    if (!el('shell-root')) enterShell();
    var hash = location.hash || '#/dashboard';
    var handler = Routes[hash] || Routes[hash.split('?')[0]];
    if (!handler) handler = Routes['#/dashboard'];
    setActiveNav(hash);
    var page = el('page'); if (!page) return;
    try { handler(hash); } catch (e) { console.error('[LRS Handler Error]', e); }
    window.scrollTo({ top: 0, behavior: 'smooth' });
    loadNotifications();
  }

  function enterShell() {
    var root = el('app-root'); root.className = 'shell';
    root.innerHTML = sidebarHtml() 
      + '<div class="sidebar-backdrop" id="sidebar-backdrop" data-action="close-sidebar"></div>'
      + '<div class="main-area" id="shell-root">' 
      + navbarHtml() 
      + '<div class="page-wrap" id="page"></div>' 
      + mobileBottomBarHtml()
      + '<button class="mobile-fab" data-hash="#/new" title="ยื่นใบลาใหม่"><i class="bi bi-plus-lg"></i></button>'
      + '</div>';
    startClock();
    applyTheme();
    hideBoot();
    loadNotifications();
  }

  function mobileBottomBarHtml() {
    return '<nav class="mobile-bottom-bar" id="mobile-bottom-bar">'
      + '<a class="mbb-item" data-hash="#/dashboard"><i class="bi bi-grid-1x2-fill"></i><span>หน้าแรก</span></a>'
      + '<a class="mbb-item" data-hash="#/new"><i class="bi bi-plus-circle-fill" style="font-size:22px"></i><span>ยื่นใบลา</span></a>'
      + '<a class="mbb-item" data-hash="#/my"><i class="bi bi-folder-fill"></i><span>ของฉัน</span></a>'
      + '<a class="mbb-item" data-hash="#/inbox"><i class="bi bi-inbox-fill"></i><span>กล่องงาน</span></a>'
      + '<a class="mbb-item" data-hash="#/profile"><i class="bi bi-person-circle"></i><span>โปรไฟล์</span></a>'
      + '</nav>';
  }

  function sidebarHtml() {
    var u = Store.user;
    var s = Store.boot.settings || {};
    var logo = (s.logo_image && s.logo_image.trim()) 
      ? '<div class="sb-logo-ic" style="position:relative;overflow:hidden"><img class="sb-logo-img" src="' + esc(s.logo_image) + '" style="width:100%;height:100%;object-fit:contain;background:#fff;padding:2px;border-radius:12px" onerror="this.style.display=\'none\';var ic=this.parentNode.querySelector(\'i\');if(ic)ic.style.display=\'block\';"><i class="bi bi-file-earmark-text-fill" style="display:none"></i></div>' 
      : '<div class="sb-logo-ic"><i class="bi bi-file-earmark-text-fill"></i></div>';
    
    var navItems = [
      { hash: '#/dashboard', icon: 'grid-1x2-fill', label: 'แดชบอร์ด', cap: '*' },
      { hash: '#/new', icon: 'plus-square-fill', label: 'ยื่นเอกสารใบลา', cap: 'request.create' },
      { hash: '#/my', icon: 'folder-fill', label: 'เอกสารของฉัน', cap: 'request.view_own' },
      { hash: '#/calendar', icon: 'calendar3', label: 'ปฏิทินวันลา', cap: '*' },
      { hash: '#/history', icon: 'clock-history', label: 'ประวัติการลา', cap: '*' },
      { hash: '#/inbox', icon: 'inbox-fill', label: 'กล่องงานอนุมัติ', cap: 'request.review|request.approve|request.view_all' },
      { hash: '#/all', icon: 'files', label: 'เอกสารทั้งหมด', cap: 'request.view_all' },
      { hash: '#/reports', icon: 'bar-chart-fill', label: 'รายงานสรุป', cap: 'report.view_all|report.manage' },
      { hash: '#/audit', icon: 'shield-lock-fill', label: 'ประวัติระบบ (Audit Log)', cap: 'audit.view_all' },
      { hash: '#/users', icon: 'people-fill', label: 'ผู้ใช้งาน', cap: 'user.manage|user.view_all' },
      { hash: '#/settings', icon: 'gear-fill', label: 'ตั้งค่าระบบ', cap: 'setting.manage' },
      { hash: '#/profile', icon: 'person-circle', label: 'โปรไฟล์ส่วนตัว', cap: '*' }
    ];

    var navHtml = navItems.filter(function(i) { return hasCap(i.cap); }).map(function(i) {
      return '<a class="sb-link" data-hash="' + i.hash + '"><i class="bi bi-' + i.icon + '"></i><span>' + esc(i.label) + '</span></a>';
    }).join('');

    return '<aside class="sidebar" id="sidebar"><div class="sb-head"><div class="sb-logo">' + logo + '<div><div class="sb-name">ระบบใบลาราชการ</div><div class="sb-org">' + esc(s.org_name || 'โรงเรียนปากพูน') + '</div></div></div>'
      + '<div class="sb-clock"><div class="sb-clock-t" id="sb-clock-t">--:--:--</div><div class="sb-clock-d" id="sb-clock-d"></div></div>'
      + '<div class="sb-user"><div class="sb-av">' + avatarHtml(u) + '</div><div class="sb-uinfo"><div class="sb-uname">' + esc((u.prefix || '') + u.full_name) + '</div>' + roleChip(u.role) + '</div></div>'
      + '</div><nav class="sb-nav">' + navHtml + '<div class="sb-cat">ออกจากระบบ</div><a class="sb-link is-logout" data-action="logout"><i class="bi bi-box-arrow-right"></i><span>ออกจากระบบ</span></a></nav></aside>';
  }

  function navbarHtml() {
    var u = Store.user;
    var s = Store.boot.settings || {};
    var themes = [
      { id: 'macblue', c: '#0a84ff' },
      { id: 'macteal', c: '#30b0c7' },
      { id: 'macpurple', c: '#af52de' },
      { id: 'macorange', c: '#ff9500' },
      { id: 'macgreen', c: '#34c759' },
      { id: 'macpink', c: '#ff2d55' }
    ];
    var dots = themes.map(function(t) {
      return '<span class="theme-dot" data-action="set-theme" data-t="' + t.id + '" style="background:' + t.c + '" title="ธีม ' + t.id + '"></span>';
    }).join('');

    var navLogo = (s.logo_image && s.logo_image.trim()) 
      ? '<span style="display:inline-flex;width:100%;height:100%;align-items:center;justify-content:center"><img src="' + esc(s.logo_image) + '" style="width:100%;height:100%;object-fit:contain;background:#fff;padding:2px;border-radius:8px" onerror="this.style.display=\'none\';var ic=this.parentNode.querySelector(\'i\');if(ic)ic.style.display=\'inline\';"><i class="bi bi-file-earmark-text" style="display:none"></i></span>' 
      : '<i class="bi bi-file-earmark-text"></i>';

    return '<header class="navbar"><button class="nav-burger" data-action="burger"><i class="bi bi-list"></i></button>'
      + '<div class="nav-pageic">' + navLogo + '</div>'
      + '<div><div class="nav-title" id="nav-title">ระบบเอกสารใบลาราชการ</div><div class="nav-sub" id="nav-sub">' + esc(s.org_name || 'โรงเรียนปากพูน') + ' · สพฐ.</div></div>'
      + '<div class="nav-right">'
      + '<div class="nav-notify-wrap">'
      + '<button class="nav-bell" data-action="toggle-notify"><i class="bi bi-bell-fill"></i><span class="nav-badge" id="notify-badge" style="display:none">0</span></button>'
      + '<div class="notify-popover" id="notify-popover" style="display:none">'
      + '<div class="notify-header"><span><i class="bi bi-bell-fill"></i> การแจ้งเตือน</span><button class="btn btn-sm btn-ghost" data-action="mark-all-read">อ่านทั้งหมด</button></div>'
      + '<div class="notify-list" id="notify-list-body">กำลังโหลด…</div>'
      + '</div>'
      + '</div>'
      + '<button class="dark-toggle" data-action="toggle-dark" id="dark-toggle-btn" title="สลับ Dark/Light Mode"><i class="bi bi-moon-stars-fill" id="dark-toggle-ic"></i></button>'
      + '<div class="theme-picker desktop-only" title="เปลี่ยนโทนสีธีม">' + dots + '</div>'
      + '<div class="nav-pill desktop-only"><span class="dot"></span> ออนไลน์</div>'
      + '<button class="nav-prof desktop-only" data-hash="#/profile"><span class="nav-prof-av">' + avatarHtml(u) + '</span><span class="nav-prof-nm">' + esc(u.full_name) + '</span></button>'
      + '</div></header>';
  }

  function loadNotifications() {
    var badge = el('notify-badge');
    var list = el('notify-list-body');
    if (!badge || !Store.user) return;

    call('notify.list', {}).then(function(d) {
      if (d.unread > 0) {
        badge.textContent = d.unread > 99 ? '99+' : d.unread;
        badge.style.display = 'inline-block';
      } else {
        badge.style.display = 'none';
      }

      if (list) {
        if (!d.items || d.items.length === 0) {
          list.innerHTML = '<div style="padding:20px;text-align:center;color:var(--muted)">ไม่มีการแจ้งเตือน</div>';
          return;
        }
        list.innerHTML = d.items.map(function(n) {
          return '<div class="notify-item ' + (n.is_read !== 'yes' ? 'unread' : '') + '" data-action="read-notify" data-id="' + esc(n.id) + '" data-link="' + esc(n.link || '#/my') + '">'
            + '<i class="bi bi-bell" style="font-size:18px;color:var(--accent);margin-top:2px"></i>'
            + '<div><div class="notify-item-title">' + esc(n.title) + '</div><div class="notify-item-sub">' + esc(n.message) + '</div><small style="color:var(--muted);font-size:10px">' + TH.dt(n.created_at) + '</small></div>'
            + '</div>';
        }).join('');
      }
    }).catch(function(){});
  }

  function setActiveNav(hash) {
    var base = (hash || '').split('?')[0];
    $$('.sb-link[data-hash]').forEach(function (a) {
      a.classList.toggle('active', a.getAttribute('data-hash') === base);
    });
    $$('.mbb-item[data-hash]').forEach(function (a) {
      a.classList.toggle('active', a.getAttribute('data-hash') === base);
    });
  }

  function handleImageFileSelect(file, targetInputId, targetPreviewImgId, targetPreviewInitId, maxDim) {
    if (!file) return;
    if (!file.type.match('image.*')) {
      toast('กรุณาเลือกไฟล์รูปภาพเท่านั้น (JPG, PNG, GIF, WebP)', 'warning');
      return;
    }
    
    maxDim = maxDim || 600;
    var reader = new FileReader();
    reader.onload = function(e) {
      var img = new Image();
      img.onload = function() {
        var canvas = document.createElement('canvas');
        var w = img.width, h = img.height;
        if (w > maxDim || h > maxDim) {
          if (w > h) { h = Math.round((h * maxDim) / w); w = maxDim; }
          else { w = Math.round((w * maxDim) / h); h = maxDim; }
        }
        canvas.width = w; canvas.height = h;
        var ctx = canvas.getContext('2d');
        ctx.drawImage(img, 0, 0, w, h);
        var isSig = targetInputId.indexOf('signature') >= 0;
        var mime = isSig ? 'image/png' : 'image/jpeg';
        var dataUrl = canvas.toDataURL(mime, 0.80);
        
        var inp = el(targetInputId); if (inp) inp.value = dataUrl;
        var prevImg = el(targetPreviewImgId);
        if (prevImg) {
          prevImg.src = dataUrl;
          prevImg.style.display = 'block';
        }
        var prevInit = el(targetPreviewInitId);
        if (prevInit) {
          prevInit.style.display = 'none';
        }
        toast('ปรับขนาดและประมวลผลรูปภาพเรียบร้อยแล้ว', 'success');
      };
      img.src = e.target.result;
    };
    reader.readAsDataURL(file);
  }

  function avatarHtml(u) {
    var url = u && u.avatar_url;
    var init = ((u && (u.full_name || u.username)) || '?').trim().charAt(0);
    if (!url) return esc(init);
    return '<img src="' + esc(url) + '" alt="" onerror="this.onerror=null;this.style.display=\'none\';if(this.parentNode)this.parentNode.innerHTML=\'' + esc(init) + '\';">';
  }

  function roleChip(r) {
    var labels = { teacher: 'ครู/บุคลากร', clerk: 'ธุรการ', director: 'ผู้อำนวยการ', admin: 'ผู้ดูแลระบบ' };
    return '<span class="role-chip role-' + esc(r) + '"><i class="bi bi-shield-check"></i> ' + esc(labels[r] || r) + '</span>';
  }

  function startClock() {
    if (window.__lrsClock) clearInterval(window.__lrsClock);
    function tick() {
      var n = new Date(); var t = el('sb-clock-t'); if (!t) return;
      t.textContent = _p2(n.getHours()) + ':' + _p2(n.getMinutes()) + ':' + _p2(n.getSeconds());
      el('sb-clock-d').textContent = TH.dateLong(n);
    }
    tick(); window.__lrsClock = setInterval(tick, 1000);
  }

  function hideBoot() { var b = el('boot-loader'); if (b) b.style.display = 'none'; }

  // -------------------------------------------------------------
  // QUOTA CARDS WIDGET ( Dashboard & Request Form )
  // -------------------------------------------------------------

  function renderQuotaWidgetHtml(stats) {
    var items = stats || [
      { label: 'ลาป่วย', count: 0, days: 0, quota: 60, pct: 0, icon: 'thermometer-half' },
      { label: 'ลากิจส่วนตัว', count: 0, days: 0, quota: 45, pct: 0, icon: 'person' },
      { label: 'ลาพักผ่อน', count: 0, days: 0, quota: 10, pct: 0, icon: 'sun' },
      { label: 'ลาคลอดบุตร', count: 0, days: 0, quota: 90, pct: 0, icon: 'heart-pulse' }
    ];

    var cards = items.map(function(q) {
      var rem = Math.max(0, q.quota - q.days);
      var pct = q.quota > 0 ? Math.min(100, Math.round((q.days / q.quota) * 100)) : 0;
      var barClass = pct >= 80 ? 'background:linear-gradient(135deg,#ff3b30,#ff9f0a)' : 'background:var(--grad)';
      return '<div class="qc">'
        + '<div class="qc-top"><div class="qc-ttl"><i class="bi bi-' + esc(q.icon || 'file-earmark') + '" style="color:var(--accent)"></i> ' + esc(q.label) + '</div><div class="qc-val">' + q.days + '<span style="font-size:12px;font-weight:500;color:var(--muted)"> / ' + q.quota + ' วัน</span></div></div>'
        + '<div class="qc-bar-bg"><div class="qc-bar-fill" style="width:' + pct + '%;' + barClass + '"></div></div>'
        + '<div class="qc-sub"><span>ใช้ไป ' + q.count + ' ครั้ง (' + pct + '%)</span><span>คงเหลือ ' + rem + ' วัน</span></div>'
        + '</div>';
    }).join('');

    return '<div class="panel" style="margin-bottom:18px"><div class="panel-h"><div class="ic"><i class="bi bi-pie-chart-fill"></i></div><div class="ttl">สิทธิและวันลาคงเหลือในปีงบประมาณนี้</div></div><div class="quota-card-grid">' + cards + '</div></div>';
  }

  // -------------------------------------------------------------
  // PRINT BUILDERS FOR ALL 5 OFFICIAL THAI LEAVE FORMS
  // -------------------------------------------------------------

  function sigImageOrDots(url, name) {
    if (url) return '<img class="pr-sig-img" src="' + esc(url) + '"><br>( ' + esc(name) + ' )';
    return '(ลงชื่อ) ...........................................................<br>( &nbsp;&nbsp;&nbsp;&nbsp;' + esc(name) + '&nbsp;&nbsp;&nbsp;&nbsp; )';
  }

  function buildOfficialThaiPrintForm(r, s) {
    var owner = r._owner || {};
    var ownerName = (owner.prefix || '') + (owner.full_name || 'ข้าราชการครู');
    var ownerPos = owner.position || 'ครู';
    var docDateObj = TH.parse(r.doc_date || new Date());
    var dDay = docDateObj ? docDateObj.getDate() : '';
    var dMonth = docDateObj ? TH.M[docDateObj.getMonth()] : '';
    var dYear = docDateObj ? (docDateObj.getFullYear() + 543) : '';

    var reqType = r.req_type || 'leave';

    // 1. ใบลาพักผ่อน
    if (reqType === 'vacation') return buildVacationPrintForm(r, s);
    // 2. ใบขอยกเลิกวันลา
    if (reqType === 'cancel') return buildCancelPrintForm(r, s);
    // 3. หนังสือขอลาออกจากราชการ
    if (reqType === 'resign') return buildResignPrintForm(r, s);
    // 4. คำขอมีบัตรประจำตัว
    if (reqType === 'idcard') return buildIdCardPrintForm(r, s);

    // 5. ใบลาป่วย/กิจ/คลอด
    var isSick = r.leave_kind === 'sick';
    var isPersonal = r.leave_kind === 'personal' || !r.leave_kind;
    var isMaternity = r.leave_kind === 'maternity';

    var startDateStr = r.start_date ? TH.dateLong(r.start_date) : '';
    var endDateStr = r.end_date ? TH.dateLong(r.end_date) : '';
    var totalDays = r.days || 1;

    var statPrev = r.stat_prev || {};
    var statThis = r.stat_this || {};
    var statTotal = r.stat_total || {};

    var reviewerName = r._reviewer_name || s.clerk_name || 'นายธงชัย ศักดามาศ';
    var reviewerPos = s.clerk_position || 'หัวหน้ากลุ่มบริหารงานบุคคล';
    var subDirectorName = s.sub_director_name || 'นางสาวพรพรรณ ผลไชย';
    var subDirectorPos = s.sub_director_position || 'รองผู้อำนวยการโรงเรียนปากพูน';
    var approverName = r._approver_name || s.director_name || 'นายวิริยะ วุฒิมานพ';
    var approverPos = s.director_position || 'ผู้อำนวยการโรงเรียนปากพูน';

    var isApproved = r.status === 'approved' || r.approver_decision === 'approve';
    var isRejected = r.approver_decision === 'reject';

    return '<div class="pr-doc"><div class="pr-page">'
      + '<div class="pr-title">แบบใบลาป่วย ลากิจส่วนตัว ลาคลอดบุตร</div>'
      + '<div class="pr-header-right">เขียนที่ <span class="pr-line-dots" style="min-width:180px">' + esc(s.org_name || 'โรงเรียนปากพูน') + '</span><br>'
      + 'วันที่ <span class="pr-line-dots" style="min-width:40px">' + dDay + '</span> เดือน <span class="pr-line-dots" style="min-width:100px">' + dMonth + '</span> พ.ศ. <span class="pr-line-dots" style="min-width:60px">' + dYear + '</span></div>'
      
      + '<div class="pr-prow">เรื่อง <span class="pr-line-dots" style="min-width:80%">' + esc(r.subject || 'ขออนุญาตลาป่วย/กิจส่วนตัว') + '</span></div>'
      + '<div class="pr-prow">เรียน <span class="pr-line-dots" style="min-width:80%">ผู้อำนวยการ' + esc(s.org_name || 'โรงเรียนปากพูน') + '</span></div>'
      + '<div class="pr-prow">ข้าพเจ้า <span class="pr-line-dots" style="min-width:260px">' + esc(ownerName) + '</span> ตำแหน่ง <span class="pr-line-dots" style="min-width:200px">' + esc(ownerPos) + '</span></div>'
      + '<div class="pr-prow">' + esc(s.org_name || 'โรงเรียนปากพูน') + ' สังกัด <span class="pr-line-dots" style="min-width:75%">' + esc(s.sae_zone || 'สำนักงานเขตพื้นที่การศึกษามัธยมศึกษานครศรีธรรมราช') + '</span></div>'
      
      + '<div class="pr-prow" style="margin-top:10px">'
      + '<span class="pr-chk ' + (isSick ? 'on' : '') + '"></span> ลาป่วย &nbsp;&nbsp;'
      + '<span class="pr-chk ' + (isMaternity ? 'on' : '') + '"></span> ลาคลอดบุตร &nbsp;&nbsp;'
      + '<span class="pr-chk ' + (isPersonal ? 'on' : '') + '"></span> ลากิจส่วนตัว เนื่องจาก <span class="pr-line-dots" style="min-width:40%">' + esc(r.reason || '-') + '</span>'
      + '</div>'

      + '<div class="pr-prow" style="margin-top:6px">ตั้งแต่วันที่ <span class="pr-line-dots" style="min-width:170px">' + esc(startDateStr) + '</span> ถึงวันที่ <span class="pr-line-dots" style="min-width:170px">' + esc(endDateStr) + '</span> มีกำหนด <span class="pr-line-dots" style="min-width:50px">' + totalDays + '</span> วัน</div>'
      + '<div class="pr-prow">ข้าพเจ้าได้ลา ( &nbsp; ) ป่วย ( &nbsp; ) ลาคลอดบุตร ( &nbsp; ) กิจส่วนตัว ครั้งสุดท้ายตั้งแต่วันที่ <span class="pr-line-dots" style="min-width:140px">' + esc(r.last_start ? TH.dateLong(r.last_start) : '.........................') + '</span></div>'
      + '<div class="pr-prow">ถึงวันที่ <span class="pr-line-dots" style="min-width:140px">' + esc(r.last_end ? TH.dateLong(r.last_end) : '.........................') + '</span> มีกำหนด <span class="pr-line-dots" style="min-width:40px">' + (r.last_days || '............') + '</span> วัน ในระหว่างลาจะติดต่อข้าพเจ้าได้ที่ <span class="pr-line-dots" style="min-width:280px">' + esc(r.contact_addr || '-') + '</span></div>'
      + '<div class="pr-prow" style="justify-content:flex-end">หมายเลขโทรศัพท์ <span class="pr-line-dots" style="min-width:220px">' + esc(r.contact_phone || '-') + '</span></div>'

      + '<div class="pr-sign-block">' + sigImageOrDots(owner.signature_url, ownerName) + '</div>'

      + '<div class="pr-bottom-grid">'
      + '<div>'
      + '<div style="font-weight:700;margin-bottom:6px">สถิติการลาในปีงบประมาณนี้</div>'
      + '<table class="pr-stat-tbl">'
      + '<thead><tr><th>ประเภทลา</th><th>ลามาแล้ว<br>(วัน)</th><th>ลาครั้งนี้<br>(วัน)</th><th>รวมเป็น<br>(วัน)</th></tr></thead>'
      + '<tbody>'
      + '<tr><td>ป่วย</td><td>' + (statPrev.sick || 0) + '</td><td>' + (isSick ? totalDays : (statThis.sick || 0)) + '</td><td>' + (num(statPrev.sick||0) + (isSick ? totalDays : num(statThis.sick||0))) + '</td></tr>'
      + '<tr><td>กิจส่วนตัว</td><td>' + (statPrev.personal || 0) + '</td><td>' + (isPersonal ? totalDays : (statThis.personal || 0)) + '</td><td>' + (num(statPrev.personal||0) + (isPersonal ? totalDays : num(statThis.personal||0))) + '</td></tr>'
      + '<tr><td>คลอดบุตร</td><td>' + (statPrev.maternity || 0) + '</td><td>' + (isMaternity ? totalDays : (statThis.maternity || 0)) + '</td><td>' + (num(statPrev.maternity||0) + (isMaternity ? totalDays : num(statThis.maternity||0))) + '</td></tr>'
      + '</tbody>'
      + '</table>'
      + '<div style="margin-top:14px;text-align:center">'
      + sigImageOrDots(null, reviewerName) + '<br>' + esc(reviewerPos)
      + '</div>'
      + '</div>'

      + '<div>'
      + '<div style="font-weight:700">ความคิดเห็นผู้บังคับบัญชา</div>'
      + '<div style="border-bottom:1px dotted #000;height:24px;margin-bottom:8px">' + esc(r.reviewer_note || 'เห็นควรอนุญาต') + '</div>'
      + '<div style="text-align:center;margin-bottom:14px">'
      + sigImageOrDots(null, subDirectorName) + '<br>' + esc(subDirectorPos)
      + '</div>'

      + '<div style="font-weight:700">คำสั่ง</div>'
      + '<div style="margin:4px 0"><span class="pr-chk ' + (isApproved ? 'on' : '') + '"></span> อนุญาต &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <span class="pr-chk ' + (isRejected ? 'on' : '') + '"></span> ไม่อนุญาต</div>'
      + '<div style="border-bottom:1px dotted #000;height:24px;margin-bottom:8px">' + esc(r.approver_note || '') + '</div>'
      + '<div style="text-align:center">'
      + sigImageOrDots(null, approverName) + '<br>' + esc(approverPos)
      + '</div>'
      + '</div>'

      + '</div>'
      + '</div></div>';
  }

  function buildVacationPrintForm(r, s) {
    var owner = r._owner || {};
    var ownerName = (owner.prefix || '') + (owner.full_name || 'ข้าราชการครู');
    var docDateObj = TH.parse(r.doc_date || new Date());
    var dDay = docDateObj ? docDateObj.getDate() : '';
    var dMonth = docDateObj ? TH.M[docDateObj.getMonth()] : '';
    var dYear = docDateObj ? (docDateObj.getFullYear() + 543) : '';

    return '<div class="pr-doc"><div class="pr-page">'
      + '<div class="pr-title">แบบใบลาพักผ่อน</div>'
      + '<div class="pr-header-right">เขียนที่ <span class="pr-line-dots" style="min-width:180px">' + esc(s.org_name || 'โรงเรียนปากพูน') + '</span><br>'
      + 'วันที่ <span class="pr-line-dots" style="min-width:40px">' + dDay + '</span> เดือน <span class="pr-line-dots" style="min-width:100px">' + dMonth + '</span> พ.ศ. <span class="pr-line-dots" style="min-width:60px">' + dYear + '</span></div>'
      + '<div class="pr-prow">เรื่อง <span class="pr-line-dots" style="min-width:80%">' + esc(r.subject || 'ขออนุญาตลาพักผ่อน') + '</span></div>'
      + '<div class="pr-prow">เรียน <span class="pr-line-dots" style="min-width:80%">ผู้อำนวยการ' + esc(s.org_name || 'โรงเรียนปากพูน') + '</span></div>'
      + '<div class="pr-prow">ข้าพเจ้า <span class="pr-line-dots" style="min-width:260px">' + esc(ownerName) + '</span> ตำแหน่ง <span class="pr-line-dots" style="min-width:200px">' + esc(owner.position || 'ครู') + '</span></div>'
      + '<div class="pr-prow">สังกัด <span class="pr-line-dots" style="min-width:80%">' + esc(s.sae_zone || 'สำนักงานเขตพื้นที่การศึกษามัธยมศึกษานครศรีธรรมราช') + '</span></div>'
      + '<div class="pr-prow">มีวันลาพักผ่อนสะสม <span class="pr-line-dots" style="min-width:50px">' + (r.accum_days || 0) + '</span> วันทำการ มีสิทธิลาพักผ่อนประจำปีนี้อีก <span class="pr-line-dots" style="min-width:50px">' + (r.right_days || 10) + '</span> วันทำการ รวมเป็น <span class="pr-line-dots" style="min-width:50px">' + (r.total_right || 10) + '</span> วันทำการ</div>'
      + '<div class="pr-prow">ขอลาพักผ่อนตั้งแต่วันที่ <span class="pr-line-dots" style="min-width:180px">' + esc(TH.dateLong(r.start_date)) + '</span> ถึงวันที่ <span class="pr-line-dots" style="min-width:180px">' + esc(TH.dateLong(r.end_date)) + '</span> มีกำหนด <span class="pr-line-dots" style="min-width:50px">' + (r.days || 1) + '</span> วัน</div>'
      + '<div class="pr-prow">ในระหว่างลาจะติดต่อข้าพเจ้าได้ที่ <span class="pr-line-dots" style="min-width:300px">' + esc(r.contact_addr || '-') + '</span> โทร. <span class="pr-line-dots" style="min-width:150px">' + esc(r.contact_phone || '-') + '</span></div>'
      + '<div class="pr-sign-block">' + sigImageOrDots(owner.signature_url, ownerName) + '</div>'
      + '<div class="pr-bottom-grid">'
      + '<div><div style="font-weight:700">คำสั่ง</div><div><span class="pr-chk ' + (r.status === 'approved' ? 'on' : '') + '"></span> อนุญาต &nbsp;&nbsp; <span class="pr-chk ' + (r.approver_decision === 'reject' ? 'on' : '') + '"></span> ไม่อนุญาต</div></div>'
      + '<div><div style="text-align:center">' + sigImageOrDots(null, r._approver_name || s.director_name) + '<br>' + esc(s.director_position) + '</div></div>'
      + '</div></div></div>';
  }

  function buildCancelPrintForm(r, s) {
    var owner = r._owner || {};
    var ownerName = (owner.prefix || '') + (owner.full_name || 'ข้าราชการครู');
    return '<div class="pr-doc"><div class="pr-page">'
      + '<div class="pr-title">แบบใบขอยกเลิกวันลา</div>'
      + '<div class="pr-prow">เรื่อง <span class="pr-line-dots" style="min-width:80%">' + esc(r.subject || 'ขอยกเลิกวันลา') + '</span></div>'
      + '<div class="pr-prow">เรียน <span class="pr-line-dots" style="min-width:80%">ผู้อำนวยการ' + esc(s.org_name) + '</span></div>'
      + '<div class="pr-prow">ข้าพเจ้า <span class="pr-line-dots" style="min-width:260px">' + esc(ownerName) + '</span> ตำแหน่ง <span class="pr-line-dots" style="min-width:200px">' + esc(owner.position || 'ครู') + '</span></div>'
      + '<div class="pr-prow">ได้รับอนุมัติให้ลา <span class="pr-line-dots" style="min-width:150px">' + esc(r.orig_leave || 'กิจส่วนตัว') + '</span> ตั้งแต่วันที่ <span class="pr-line-dots" style="min-width:150px">' + esc(TH.dateLong(r.orig_start)) + '</span> ถึงวันที่ <span class="pr-line-dots" style="min-width:150px">' + esc(TH.dateLong(r.orig_end)) + '</span> รวม <span class="pr-line-dots" style="min-width:40px">' + (r.orig_days || 1) + '</span> วัน นั้น</div>'
      + '<div class="pr-prow">เนื่องจาก <span class="pr-line-dots" style="min-width:80%">' + esc(r.reason || 'ภารกิจเสร็จสิ้นก่อนกำหนด') + '</span> จึงขอยกเลิกวันลาจำนวน <span class="pr-line-dots" style="min-width:50px">' + (r.cancel_days || 1) + '</span> วัน ตั้งแต่วันที่ <span class="pr-line-dots" style="min-width:160px">' + esc(TH.dateLong(r.cancel_start || r.start_date)) + '</span> ถึงวันที่ <span class="pr-line-dots" style="min-width:160px">' + esc(TH.dateLong(r.cancel_end || r.end_date)) + '</span></div>'
      + '<div class="pr-sign-block">' + sigImageOrDots(owner.signature_url, ownerName) + '</div>'
      + '</div></div>';
  }

  function buildResignPrintForm(r, s) {
    var owner = r._owner || {};
    var ownerName = (owner.prefix || '') + (owner.full_name || 'ข้าราชการครู');
    return '<div class="pr-doc"><div class="pr-page">'
      + '<div class="pr-title">หนังสือขอลาออกจากราชการ</div>'
      + '<div class="pr-prow">เรื่อง <span class="pr-line-dots" style="min-width:80%">ขอลาออกจากราชการ</span></div>'
      + '<div class="pr-prow">เรียน <span class="pr-line-dots" style="min-width:80%">ผู้อำนวยการ' + esc(s.org_name) + '</span></div>'
      + '<div class="pr-prow">ข้าพเจ้า <span class="pr-line-dots" style="min-width:260px">' + esc(ownerName) + '</span> ตำแหน่ง <span class="pr-line-dots" style="min-width:200px">' + esc(r.position_now || owner.position || 'ครู') + '</span></div>'
      + '<div class="pr-prow">อัตราเงินเดือน <span class="pr-line-dots" style="min-width:120px">' + esc(r.salary_amount || '-') + '</span> บาท มีความประสงค์ขอลาออกจากราชการเนื่องจาก <span class="pr-line-dots" style="min-width:60%">' + esc(r.resign_reason || r.reason || 'ปัญหาสุขภาพ/ภารกิจครอบครัว') + '</span></div>'
      + '<div class="pr-prow">โดยขอให้มีผลตั้งแต่วันที่ <span class="pr-line-dots" style="min-width:220px">' + esc(TH.dateLong(r.resign_date || r.start_date)) + '</span> เป็นต้นไป</div>'
      + '<div class="pr-sign-block">' + sigImageOrDots(owner.signature_url, ownerName) + '</div>'
      + '</div></div>';
  }

  function buildIdCardPrintForm(r, s) {
    var owner = r._owner || {};
    var ownerName = (owner.prefix || '') + (owner.full_name || 'ข้าราชการครู');
    return '<div class="pr-doc"><div class="pr-page">'
      + '<div class="pr-title">คำขอมีบัตรประจำตัวเจ้าหน้าที่ของรัฐ</div>'
      + '<div class="pr-prow">ข้าพเจ้า <span class="pr-line-dots" style="min-width:260px">' + esc(ownerName) + '</span> เลขประจำตัวประชาชน <span class="pr-line-dots" style="min-width:220px">' + esc(owner.id_card || '-') + '</span></div>'
      + '<div class="pr-prow">ตำแหน่ง <span class="pr-line-dots" style="min-width:200px">' + esc(owner.position || 'ครู') + '</span> สังกัด <span class="pr-line-dots" style="min-width:250px">' + esc(s.org_name) + '</span></div>'
      + '<div class="pr-prow">มีความประสงค์ขอมีบัตรประจำตัวเจ้าหน้าที่ของรัฐ เนื่องจาก <span class="pr-line-dots" style="min-width:50%">' + esc(r.idc_reason || 'ขอมีบัตรใหม่/เปลี่ยนตำแหน่ง') + '</span></div>'
      + '<div class="pr-sign-block">' + sigImageOrDots(owner.signature_url, ownerName) + '</div>'
      + '</div></div>';
  }

  // -------------------------------------------------------------
  // INTERACTIVE MODALS FOR REVIEW & APPROVAL
  // -------------------------------------------------------------

  function openReviewModal(r) {
    var statPrev = r.stat_prev || { sick: 2, personal: 3, maternity: 0 };
    var statThis = r.stat_this || { sick: r.leave_kind === 'sick' ? r.days : 0, personal: r.leave_kind === 'personal' ? r.days : 0, maternity: r.leave_kind === 'maternity' ? r.days : 0 };
    
    var html = '<div class="modal-overlay" id="review-modal">'
      + '<div class="modal-card">'
      + '<div class="modal-head"><h3 class="modal-ttl"><i class="bi bi-clipboard-check-fill" style="color:var(--accent)"></i> ตรวจสอบเอกสารใบลา (เลขที่ ' + esc(r.doc_no) + ')</h3><button class="btn btn-ghost" style="padding:4px 8px" onclick="document.getElementById(\'review-modal\').remove()"><i class="bi bi-x-lg"></i></button></div>'
      + '<form id="form-review">'
      + '<p><b>ผู้ขอลา:</b> ' + esc(r._owner ? r._owner.full_name : '-') + ' (' + esc(r.subject) + ')</p>'
      + '<div style="font-weight:700;margin:12px 0 6px">สถิติการลาเดิมในปีงบประมาณนี้ (วันทำการ)</div>'
      + '<div class="form-grid fg-3">'
      + field('stat_prev_sick', 'ป่วยเดิม', { type: 'number', value: statPrev.sick || 0 })
      + field('stat_prev_personal', 'กิจเดิม', { type: 'number', value: statPrev.personal || 0 })
      + field('stat_prev_maternity', 'คลอดเดิม', { type: 'number', value: statPrev.maternity || 0 })
      + '</div>'
      + field('note', 'ความเห็นของผู้ตรวจสอบ', { type: 'textarea', value: r.reviewer_note || 'ตรวจสอบเอกสารหลักฐานถูกต้องแล้ว เห็นควรเสนอผู้อำนวยการพิจารณาอนุมัติ' })
      + '<div style="display:flex;justify-content:flex-end;gap:10px;margin-top:20px">'
      + '<button type="button" class="btn btn-ghost" onclick="document.getElementById(\'review-modal\').remove()">ยกเลิก</button>'
      + '<button type="submit" class="btn btn-primary"><i class="bi bi-check-circle-fill"></i> ยืนยันผ่านการตรวจสอบ</button>'
      + '</div>'
      + '</form>'
      + '</div></div>';

    var div = document.createElement('div');
    div.innerHTML = html;
    document.body.appendChild(div.firstElementChild);

    el('form-review').onsubmit = function(e) {
      e.preventDefault();
      var form = e.target;
      var note = form.note.value;
      var pSick = num(form.stat_prev_sick.value);
      var pPers = num(form.stat_prev_personal.value);
      var pMat = num(form.stat_prev_maternity.value);

      var payload = {
        id: r.id,
        note: note,
        stat_prev: { sick: pSick, personal: pPers, maternity: pMat },
        stat_this: statThis,
        stat_total: { sick: pSick + num(statThis.sick), personal: pPers + num(statThis.personal), maternity: pMat + num(statThis.maternity) }
      };

      Spinner.show('กำลังบันทึกการตรวจสอบ…');
      call('request.review', payload).then(function() {
        Spinner.hide();
        el('review-modal').remove();
        toast('ตรวจสอบเอกสารเรียบร้อยแล้ว', 'success');
        go('#/inbox');
      }).catch(function(err) { Spinner.hide(); toast(err.message, 'error'); });
    };
  }

  function openApproveModal(r) {
    var html = '<div class="modal-overlay" id="approve-modal">'
      + '<div class="modal-card">'
      + '<div class="modal-head"><h3 class="modal-ttl"><i class="bi bi-patch-check-fill" style="color:var(--ok)"></i> พิจารณาอนุมัติใบลา (เลขที่ ' + esc(r.doc_no) + ')</h3><button class="btn btn-ghost" style="padding:4px 8px" onclick="document.getElementById(\'approve-modal\').remove()"><i class="bi bi-x-lg"></i></button></div>'
      + '<form id="form-approve">'
      + '<p><b>ผู้ขอลา:</b> ' + esc(r._owner ? r._owner.full_name : '-') + ' | <b>ประเภท:</b> ' + esc(r.subject) + ' (' + esc(r.days) + ' วัน)</p>'
      + '<div class="field"><label>คำสั่งผู้อนุมัติ</label>'
      + '<div style="display:flex;gap:16px;margin:6px 0">'
      + '<label style="cursor:pointer;font-weight:600;display:flex;align-items:center;gap:6px"><input type="radio" name="decision" value="approve" checked> <span class="badge b-approved">✅ อนุญาต / อนุมัติ</span></label>'
      + '<label style="cursor:pointer;font-weight:600;display:flex;align-items:center;gap:6px"><input type="radio" name="decision" value="reject"> <span class="badge b-rejected">❌ ไม่อนุญาต</span></label>'
      + '</div></div>'
      + field('note', 'หมายเหตุ / สั่งการเพิ่มเติม', { type: 'textarea', value: r.approver_note || 'อนุญาตตามเสนอ', ph: 'กรอกความเห็นหรือสั่งการเพิ่มเติม' })
      + '<div style="display:flex;justify-content:flex-end;gap:10px;margin-top:20px">'
      + '<button type="button" class="btn btn-ghost" onclick="document.getElementById(\'approve-modal\').remove()">ยกเลิก</button>'
      + '<button type="submit" class="btn btn-ok"><i class="bi bi-send-check-fill"></i> ลงนามสั่งการ</button>'
      + '</div>'
      + '</form>'
      + '</div></div>';

    var div = document.createElement('div');
    div.innerHTML = html;
    document.body.appendChild(div.firstElementChild);

    el('form-approve').onsubmit = function(e) {
      e.preventDefault();
      var form = e.target;
      var decision = form.decision.value;
      var note = form.note.value;

      Spinner.show('กำลังลงนามอนุมัติเอกสาร…');
      call('request.approve', { id: r.id, decision: decision, note: note }).then(function() {
        Spinner.hide();
        el('approve-modal').remove();
        toast(decision === 'approve' ? 'อนุมัติเอกสารเรียบร้อยแล้ว' : 'บันทึกคำสั่งไม่อนุมัติเรียบร้อย', decision === 'approve' ? 'success' : 'warning');
        go('#/inbox');
      }).catch(function(err) { Spinner.hide(); toast(err.message, 'error'); });
    };
  }

  // -------------------------------------------------------------
  // PAGE HANDLERS
  // -------------------------------------------------------------

  route('#/dashboard', function () {
    var page = el('page'); if (!page) return;
    page.innerHTML = '<div class="hero"><div class="hero-title">กำลังโหลดข้อมูลแดชบอร์ด…</div></div>';
    
    Promise.all([
      call('report.dashboard', {}),
      call('request.stats', {})
    ]).then(function (res) {
      var d = res[0];
      var stats = res[1];
      var u = Store.user;

      var hero = '<div class="hero"><div class="hero-pill"><i class="bi bi-calendar-check"></i> ' + esc(TH.dateLong(new Date())) + '</div>'
        + '<div class="hero-title">สวัสดี · ' + esc(u.full_name) + '</div><div class="hero-sub">ภาพรวมเอกสารใบลาราชการของคุณและสถานะล่าสุด</div>'
        + '<div class="hero-kpis"><div class="hk"><div class="hk-v">' + d.total + '</div><div class="hk-l">เอกสารทั้งหมด</div></div>'
        + '<div class="hk"><div class="hk-v">' + d.by_status.approved + '</div><div class="hk-l">อนุมัติแล้ว</div></div>'
        + '<div class="hk"><div class="hk-v">' + d.pending + '</div><div class="hk-l">รอดำเนินการ</div></div>'
        + '<div class="hk"><div class="hk-v">' + d.days_approved + '</div><div class="hk-l">วันลารวม</div></div></div></div>';

      var quotaHtml = renderQuotaWidgetHtml(stats.types);

      var s = d.by_status;
      var statsHtml = '<div class="grid-stats">'
        + '<div class="stat"><div class="stat-ic g-gray"><i class="bi bi-file-earmark"></i></div><div class="stat-v">' + (s.draft||0) + '</div><div class="stat-l">ฉบับร่าง</div></div>'
        + '<div class="stat"><div class="stat-ic g-orange"><i class="bi bi-hourglass-split"></i></div><div class="stat-v">' + (s.submitted||0) + '</div><div class="stat-l">รอตรวจสอบ</div></div>'
        + '<div class="stat"><div class="stat-ic g-blue"><i class="bi bi-clipboard-check"></i></div><div class="stat-v">' + (s.reviewed||0) + '</div><div class="stat-l">รออนุมัติ</div></div>'
        + '<div class="stat"><div class="stat-ic g-green"><i class="bi bi-check-circle"></i></div><div class="stat-v">' + (s.approved||0) + '</div><div class="stat-l">อนุมัติแล้ว</div></div>'
        + '<div class="stat"><div class="stat-ic g-red"><i class="bi bi-x-circle"></i></div><div class="stat-v">' + (s.rejected||0) + '</div><div class="stat-l">ไม่อนุมัติ</div></div>'
        + '</div>';

      var recentRows = d.recent.map(function(r) {
        return '<tr class="clk" data-hash="#/detail?id=' + esc(r.id) + '"><td class="mono">' + esc(r.doc_no) + '</td>'
          + '<td><span class="type-chip">' + esc(r.subject || r.req_type) + '</span></td>'
          + '<td>' + esc(r.owner_name) + '</td>'
          + '<td><span class="badge b-' + esc(r.status) + '">' + esc(r.status) + '</span></td></tr>';
      }).join('');

      var recentTable = '<div class="panel"><div class="panel-h"><div class="ic"><i class="bi bi-clock-history"></i></div><div class="ttl">รายการเอกสารล่าสุด</div></div>'
        + '<div class="tbl-wrap"><table class="tbl"><thead><tr><th>เลขที่</th><th>เรื่อง</th><th>ผู้ยื่น</th><th>สถานะ</th></tr></thead><tbody>' + (recentRows || '<tr><td colspan="4" style="text-align:center">ยังไม่มีข้อมูล</td></tr>') + '</tbody></table></div></div>';

      page.innerHTML = hero + quotaHtml + statsHtml + recentTable;
    }).catch(function(e) {
      if (page) {
        page.innerHTML = '<div class="panel" style="text-align:center;padding:40px">'
          + '<i class="bi bi-exclamation-triangle" style="font-size:36px;color:var(--warn)"></i>'
          + '<h3>' + esc(e.message) + '</h3>'
          + '<button class="btn btn-primary mt16" onclick="location.reload()">โหลดใหม่อีกครั้ง</button>'
          + '</div>';
      }
    });
  });

  var WIZ = { type: null, step: 0, data: {} };
  route('#/new', function () {
    var page = el('page'); if (!page) return;
    
    if (!WIZ.type) {
      page.innerHTML = '<div class="hero"><div class="hero-title">ยื่นเอกสารใบลา</div><div class="hero-sub">เลือกประเภทแบบฟอร์มการลาที่ต้องการยื่นต่อสถานศึกษา</div></div>'
        + '<div class="panel"><div class="panel-h"><div class="ic"><i class="bi bi-file-earmark-plus-fill"></i></div><div class="ttl">เลือกแบบฟอร์มการลา</div></div>'
        + '<div class="radio-cards">'
        + '<div class="radio-card" data-action="pick-type" data-type="leave"><div class="rc-ic g-blue"><i class="bi bi-clipboard2-pulse"></i></div><div><div class="rc-t">ใบลาป่วย/กิจ/คลอดบุตร</div><div class="rc-s">ยื่นขอลาป่วย ลากิจส่วนตัว หรือลาคลอดบุตร</div></div></div>'
        + '<div class="radio-card" data-action="pick-type" data-type="vacation"><div class="rc-ic g-orange"><i class="bi bi-sun"></i></div><div><div class="rc-t">ใบลาพักผ่อน</div><div class="rc-s">ยื่นขอลาพักผ่อนประจำปี</div></div></div>'
        + '<div class="radio-card" data-action="pick-type" data-type="cancel"><div class="rc-ic g-red"><i class="bi bi-x-octagon"></i></div><div><div class="rc-t">ใบขอยกเลิกวันลา</div><div class="rc-s">ขอยกเลิกการลาที่ได้รับการอนุมัติแล้ว</div></div></div>'
        + '<div class="radio-card" data-action="pick-type" data-type="resign"><div class="rc-ic g-purple"><i class="bi bi-box-arrow-right"></i></div><div><div class="rc-t">หนังสือขอลาออกจากราชการ</div><div class="rc-s">ยื่นหนังสือขอลาออกจากราชการ</div></div></div>'
        + '<div class="radio-card" data-action="pick-type" data-type="idcard"><div class="rc-ic g-teal"><i class="bi bi-person-badge"></i></div><div><div class="rc-t">คำขอมีบัตรประจำตัว</div><div class="rc-s">ขอมีบัตร หรือขอเปลี่ยนบัตรประจำตัวเจ้าหน้าที่</div></div></div>'
        + '</div></div>';
      return;
    }

    var formFields = '';
    if (WIZ.type === 'leave') {
      formFields = field('subject', 'เรื่องที่ยื่นลา', { required: true, value: WIZ.data.subject || 'ขออนุญาตลาป่วย/กิจส่วนตัว' })
        + '<div class="form-grid fg-2 mt16">' + field('start_date', 'ตั้งแต่วันที่', { type: 'date', required: true, value: WIZ.data.start_date }) + field('end_date', 'ถึงวันที่', { type: 'date', required: true, value: WIZ.data.end_date }) + '</div>'
        + '<div class="form-grid fg-2 mt16">' + field('days', 'จำนวนวันลา (วันทำการ)', { type: 'number', value: WIZ.data.days || 1 }) + field('leave_kind', 'ประเภทการลา', { type: 'select', value: WIZ.data.leave_kind || 'personal', options: [{v:'sick',t:'ลาป่วย'},{v:'personal',t:'ลากิจส่วนตัว'},{v:'maternity',t:'ลาคลอดบุตร'}] }) + '</div>'
        + field('reason', 'เหตุผลการลา', { type: 'textarea', value: WIZ.data.reason, ph: 'กรอกรายละเอียดเหตุผลในการขอลา' })
        + field('contact_addr', 'ระหว่างลาติดต่อข้าพเจ้าได้ที่', { value: WIZ.data.contact_addr || Store.user.address || '' })
        + field('contact_phone', 'หมายเลขโทรศัพท์ติดต่อ', { value: WIZ.data.contact_phone || Store.user.phone || '' });
    } else if (WIZ.type === 'vacation') {
      formFields = field('subject', 'เรื่องที่ยื่นลา', { required: true, value: WIZ.data.subject || 'ขออนุญาตลาพักผ่อน' })
        + '<div class="form-grid fg-2 mt16">' + field('start_date', 'ตั้งแต่วันที่', { type: 'date', required: true, value: WIZ.data.start_date }) + field('end_date', 'ถึงวันที่', { type: 'date', required: true, value: WIZ.data.end_date }) + '</div>'
        + '<div class="form-grid fg-3 mt16">' + field('days', 'จำนวนวันลาครั้งนี้', { type: 'number', value: WIZ.data.days || 1 }) + field('accum_days', 'วันลาสะสมคงเหลือ', { type: 'number', value: WIZ.data.accum_days || 0 }) + field('right_days', 'สิทธิลาพักผ่อนปีนี้', { type: 'number', value: WIZ.data.right_days || 10 }) + '</div>'
        + field('reason', 'เหตุผลประกอบ', { type: 'textarea', value: WIZ.data.reason, ph: 'ลาพักผ่อนประจำปี' })
        + field('contact_addr', 'ระหว่างลาติดต่อได้ที่', { value: WIZ.data.contact_addr || Store.user.address || '' })
        + field('contact_phone', 'โทรศัพท์ติดต่อ', { value: WIZ.data.contact_phone || Store.user.phone || '' });
    } else if (WIZ.type === 'cancel') {
      formFields = field('subject', 'เรื่อง', { required: true, value: WIZ.data.subject || 'ขอยกเลิกวันลา' })
        + field('orig_leave', 'ประเภทวันลาเดิมที่ได้รับอนุมัติ', { value: WIZ.data.orig_leave || 'ลากิจส่วนตัว' })
        + '<div class="form-grid fg-2 mt16">' + field('orig_start', 'วันที่เริ่มลาเดิม', { type: 'date', value: WIZ.data.orig_start }) + field('orig_end', 'ถึงวันที่เดิม', { type: 'date', value: WIZ.data.orig_end }) + '</div>'
        + '<div class="form-grid fg-2 mt16">' + field('start_date', 'ขอยกเลิกตั้งแต่วันที่', { type: 'date', value: WIZ.data.start_date }) + field('days', 'จำนวนวันที่ขอยกเลิก', { type: 'number', value: WIZ.data.days || 1 }) + '</div>'
        + field('reason', 'เหตุผลการขอยกเลิก', { type: 'textarea', value: WIZ.data.reason, ph: 'เสร็จสิ้นภารกิจก่อนกำหนด' });
    } else if (WIZ.type === 'resign') {
      formFields = field('subject', 'เรื่อง', { required: true, value: WIZ.data.subject || 'ขอลาออกจากราชการ' })
        + field('position_now', 'ตำแหน่งปัจจุบัน', { value: WIZ.data.position_now || Store.user.position || 'ครู' })
        + field('salary_amount', 'อัตราเงินเดือนปัจจุบัน (บาท)', { value: WIZ.data.salary_amount || '' })
        + field('start_date', 'ให้มีผลตั้งแต่วันที่', { type: 'date', required: true, value: WIZ.data.start_date })
        + field('reason', 'เหตุผลในการขอลาออก', { type: 'textarea', value: WIZ.data.reason, ph: 'กรอกเหตุผลในการขอลาออกจากราชการ' });
    } else if (WIZ.type === 'idcard') {
      formFields = field('subject', 'เรื่อง', { required: true, value: WIZ.data.subject || 'คำขอมีบัตรประจำตัวเจ้าหน้าที่ของรัฐ' })
        + field('idc_case', 'กรณียื่นคำขอ', { type: 'select', value: WIZ.data.idc_case || 'first', options: [{v:'first',t:'ขอมีบัตรครั้งแรก'},{v:'renew',t:'ขอเปลี่ยนบัตร (หมดอายุ/เปลี่ยนตำแหน่ง)'},{v:'lost',t:'ขอทำบัตรแทน (สูญหาย/ชำรุด)'}] })
        + field('idc_reason', 'เหตุผลประกอบ', { value: WIZ.data.idc_reason || 'ขอเปลี่ยนบัตรประจำตัวเนื่องจากบัตรเดิมหมดอายุ' })
        + field('idc_old_no', 'เลขที่บัตรเดิม (ถ้ามี)', { value: WIZ.data.idc_old_no || '' });
    }

    page.innerHTML = '<div class="hero"><div class="hero-title">กรอกรายละเอียดเอกสาร</div><div class="hero-sub">แบบฟอร์ม ' + esc(WIZ.type) + '</div></div>'
      + '<div class="panel">' + formFields
      + '<div class="wiz-nav"><button class="btn btn-ghost" data-action="wiz-reset">ยกเลิก/เปลี่ยนแบบฟอร์ม</button><button class="btn btn-primary" id="btn-wiz-submit" data-action="wiz-submit"><i class="bi bi-send-check"></i> บันทึก & ยื่นเอกสาร</button></div>'
      + '</div>';
  });

  function renderRequestList(mode) {
    var page = el('page'); if (!page) return;
    var titles = { my: 'เอกสารของฉัน', inbox: 'กล่องงานอนุมัติ', all: 'เอกสารทั้งหมด' };
    var isInbox = (mode === 'inbox');
    var isManager = hasCap('request.review') || hasCap('request.approve');

    page.innerHTML = '<div class="hero"><div class="hero-title">' + titles[mode] + '</div></div>'
      + '<div class="toolbar" style="display:flex;gap:12px;align-items:center;flex-wrap:wrap;margin-bottom:14px">'
      + '<div class="search" style="flex:1"><i class="bi bi-search"></i><input id="q-search" placeholder="ค้นหาเลขที่เอกสาร / เรื่อง / ผู้ยื่น"></div>'
      + (isInbox && isManager ? '<button class="btn btn-ok" id="btn-bulk-approve" data-action="bulk-approve-selected"><i class="bi bi-check-all"></i> อนุมัติรายการที่เลือก (Bulk Approve)</button>' : '')
      + '</div>'
      + '<div id="list-body" class="panel">กำลังโหลดรายการ…</div>';

    call('request.list', { status: isInbox ? 'submitted' : 'all', page: 1 }).then(function(d) {
      var rows = d.items.map(function(r) {
        var chk = (isInbox && isManager) 
          ? '<td onclick="event.stopPropagation()"><input type="checkbox" class="chk-inbox-item" value="' + esc(r.id) + '"></td>' 
          : '';
        var statusBadge = r.status === 'cancel_requested' 
          ? '<span class="badge" style="background:#ff9500;color:#fff">⚠️ ขอยกเลิก</span>'
          : '<span class="badge b-' + esc(r.status) + '">' + esc(r.status) + '</span>';

        return '<tr class="clk" data-hash="#/detail?id=' + esc(r.id) + '">'
          + chk
          + '<td class="mono">' + esc(r.doc_no) + '</td>'
          + '<td>' + esc(r.subject || r.req_type) + '</td>'
          + '<td>' + esc(r.owner_name) + '</td>'
          + '<td>' + esc(TH.date(r.start_date)) + '</td>'
          + '<td>' + statusBadge + '</td>'
          + '<td><button class="btn btn-sm" data-hash="#/detail?id=' + esc(r.id) + '">ดูรายละเอียด</button></td></tr>';
      }).join('');

      var chkHeader = (isInbox && isManager) ? '<th style="width:36px"><input type="checkbox" id="chk-all-inbox"></th>' : '';

      el('list-body').innerHTML = '<div class="tbl-wrap"><table class="tbl"><thead><tr>' + chkHeader + '<th>เลขที่</th><th>เรื่อง</th><th>ผู้ยื่น</th><th>วันที่เริ่มลา</th><th>สถานะ</th><th></th></tr></thead><tbody>' + (rows || '<tr><td colspan="7" style="text-align:center">ไม่พบเอกสาร</td></tr>') + '</tbody></table></div>';

      if (isInbox && isManager) {
        var chkAll = el('chk-all-inbox');
        if (chkAll) {
          chkAll.onchange = function() {
            $$('.chk-inbox-item').forEach(function(c) { c.checked = chkAll.checked; });
          };
        }
      }
    }).catch(function(e) { toast(e.message, 'error'); });
  }

  route('#/my', function () { renderRequestList('my'); });
  route('#/inbox', function () { renderRequestList('inbox'); });
  route('#/all', function () { renderRequestList('all'); });

  // --- Calendar View ---
  route('#/calendar', function () {
    var page = el('page'); if (!page) return;
    var now = new Date();
    var calYear = now.getFullYear();
    var calMonth = now.getMonth() + 1;

    function renderCalendar(year, month) {
      call('calendar.leaves', { year: year, month: month }).then(function(d) {
        var thDays = ['อา','จ','อ','พ','พฤ','ศ','ส'];
        var thMonths = ['','ม.ค.','ก.พ.','มี.ค.','เม.ย.','พ.ค.','มิ.ย.','ก.ค.','ส.ค.','ก.ย.','ต.ค.','พ.ย.','ธ.ค.'];
        var beYear = year + 543;
        var firstDay = new Date(year, month - 1, 1).getDay();
        var daysInMonth = new Date(year, month, 0).getDate();
        var prevDays = new Date(year, month - 1, 0).getDate();
        var todayStr = now.getFullYear() + '-' + String(now.getMonth()+1).padStart(2,'0') + '-' + String(now.getDate()).padStart(2,'0');

        // Build leave map
        var leaveMap = {};
        (d.items || []).forEach(function(item) {
          var sd = new Date(item.start_date), ed = new Date(item.end_date || item.start_date);
          for (var dt = new Date(sd); dt <= ed; dt.setDate(dt.getDate()+1)) {
            var key = dt.getFullYear() + '-' + String(dt.getMonth()+1).padStart(2,'0') + '-' + String(dt.getDate()).padStart(2,'0');
            if (!leaveMap[key]) leaveMap[key] = [];
            leaveMap[key].push(item);
          }
        });

        var kindColor = { sick:'sick', personal:'personal', vacation:'vacation', maternity:'maternity' };
        var headHtml = thDays.map(function(d){ return '<div class="cal-dh">' + d + '</div>'; }).join('');
        var cells = '';
        var total = firstDay + daysInMonth;
        var rows = Math.ceil(total / 7);

        for (var r = 0; r < rows * 7; r++) {
          var dayNum = r - firstDay + 1;
          var isOther = (dayNum < 1 || dayNum > daysInMonth);
          var displayDay = isOther ? (dayNum < 1 ? prevDays + dayNum : dayNum - daysInMonth) : dayNum;
          var yStr = year, mStr = month;
          if (dayNum < 1) { yStr = month === 1 ? year-1 : year; mStr = month === 1 ? 12 : month-1; }
          else if (dayNum > daysInMonth) { yStr = month === 12 ? year+1 : year; mStr = month === 12 ? 1 : month+1; }
          var dateStr = yStr + '-' + String(mStr).padStart(2,'0') + '-' + String(displayDay).padStart(2,'0');
          var isToday = (dateStr === todayStr);
          var leaves = leaveMap[dateStr] || [];
          var dotHtml = leaves.slice(0,3).map(function(lv) {
            var cls = kindColor[lv.leave_kind] || 'other';
            return '<span class="cal-dot cal-dot-' + cls + '">' + esc(lv.full_name || '') + '</span>';
          }).join('');
          if (leaves.length > 3) dotHtml += '<span class="cal-dot" style="color:var(--muted)">+' + (leaves.length-3) + ' อีก</span>';

          cells += '<div class="cal-cell' + (isOther?' other-month':'') + (isToday?' today':'') + '">'
            + '<div class="cal-day-num">' + displayDay + '</div>'
            + dotHtml + '</div>';
        }

        el('cal-container').innerHTML =
          '<div class="cal-nav">'
            + '<button class="btn btn-ghost btn-sm" id="cal-prev"><i class="bi bi-chevron-left"></i></button>'
            + '<div class="cal-nav-title">' + thMonths[month] + ' ' + beYear + '</div>'
            + '<button class="btn btn-ghost btn-sm" id="cal-next"><i class="bi bi-chevron-right"></i></button>'
          + '</div>'
          + '<div class="cal-wrap"><div class="cal-head">' + headHtml + '</div><div class="cal-grid">' + cells + '</div></div>';

        el('cal-prev').onclick = function() { calMonth--; if(calMonth<1){calMonth=12;calYear--;} renderCalendar(calYear,calMonth); };
        el('cal-next').onclick = function() { calMonth++; if(calMonth>12){calMonth=1;calYear++;} renderCalendar(calYear,calMonth); };
      }).catch(function(e){ toast(e.message,'error'); });
    }

    page.innerHTML = '<div class="hero"><div class="hero-title"><i class="bi bi-calendar3"></i> ปฏิทินวันลา</div><div class="hero-sub">ภาพรวมการลาของบุคลากรในโรงเรียน</div></div>'
      + '<div class="panel"><div id="cal-container"><div style="text-align:center;padding:40px"><i class="bi bi-hourglass-split" style="font-size:28px;color:var(--accent)"></i><br>กำลังโหลด…</div></div></div>';

    renderCalendar(calYear, calMonth);
  });

  // --- Leave History ---
  route('#/history', function () {
    var page = el('page'); if (!page) return;
    page.innerHTML = '<div class="hero"><div class="hero-title"><i class="bi bi-clock-history"></i> ประวัติการลาย้อนหลัง</div><div class="hero-sub">สถิติการลาเปรียบเทียบย้อนหลัง 5 ปี</div></div>'
      + '<div class="panel"><div style="text-align:center;padding:30px"><i class="bi bi-hourglass-split" style="font-size:28px;color:var(--accent)"></i></div></div>';

    call('leave.history', {}).then(function(d) {
      var items = d.items || [];
      if (!items.length) {
        page.innerHTML = '<div class="hero"><div class="hero-title">ประวัติการลาย้อนหลัง</div></div>'
          + '<div class="panel"><div style="text-align:center;padding:40px;color:var(--muted)"><i class="bi bi-calendar-x" style="font-size:32px"></i><br><br>ยังไม่มีประวัติการลา</div></div>';
        return;
      }
      var maxDays = Math.max.apply(null, items.map(function(i){ return parseFloat(i.sick||0)+parseFloat(i.personal||0)+parseFloat(i.vacation||0)+parseFloat(i.maternity||0); })) || 1;

      var rows = items.map(function(i) {
        var total = parseFloat(i.sick||0)+parseFloat(i.personal||0)+parseFloat(i.vacation||0)+parseFloat(i.maternity||0);
        var pct = Math.round((total/maxDays)*100);
        return '<tr>'
          + '<td><strong>' + (parseInt(i.yr)+543) + '</strong></td>'
          + '<td>' + parseFloat(i.sick||0).toFixed(1) + ' วัน</td>'
          + '<td>' + parseFloat(i.personal||0).toFixed(1) + ' วัน</td>'
          + '<td>' + parseFloat(i.vacation||0).toFixed(1) + ' วัน</td>'
          + '<td>' + parseFloat(i.maternity||0).toFixed(1) + ' วัน</td>'
          + '<td><strong>' + total.toFixed(1) + '</strong></td>'
          + '<td class="hist-bar-cell"><div class="hist-bar-inner" style="width:' + pct + '%"></div></td>'
          + '</tr>';
      }).join('');

      page.innerHTML = '<div class="hero"><div class="hero-title"><i class="bi bi-clock-history"></i> ประวัติการลาย้อนหลัง</div><div class="hero-sub">สถิติการลาเปรียบเทียบย้อนหลัง 5 ปี</div></div>'
        + '<div class="panel">'
        + '<div class="tbl-wrap"><table class="hist-table">'
        + '<thead><tr><th>ปี (พ.ศ.)</th><th>ลาป่วย</th><th>ลากิจ</th><th>พักผ่อน</th><th>คลอด</th><th>รวม</th><th style="width:140px">สัดส่วน</th></tr></thead>'
        + '<tbody>' + rows + '</tbody>'
        + '</table></div></div>';
    }).catch(function(e){ toast(e.message,'error'); });
  });

  // --- Reset Password Page (token from URL) ---
  route('#/reset', function () {
    var params = new URLSearchParams(location.search);
    var resetToken = params.get('reset_token');
    var page = el('page');
    if (!page) return;

    if (!resetToken) {
      page.innerHTML = '<div class="panel" style="max-width:440px;margin:40px auto;text-align:center">'
        + '<i class="bi bi-exclamation-triangle" style="font-size:36px;color:var(--warn)"></i>'
        + '<h3>ลิ้งก์ไม่ถูกต้อง</h3><p>ไม่พบ reset token กรุณาขอรีเซ็ตรหัสผ่านใหม่อีกครั้ง</p></div>';
      return;
    }

    page.innerHTML = '<div class="panel" style="max-width:440px;margin:40px auto">'
      + '<h2 style="margin:0 0 20px"><i class="bi bi-key-fill" style="color:var(--warn)"></i> ตั้งรหัสผ่านใหม่</h2>'
      + field('new_password', 'รหัสผ่านใหม่', { type: 'password', ph: 'อย่างน้อย 6 ตัวอักษร' })
      + field('new_password2', 'ยืนยันรหัสผ่านใหม่', { type: 'password', ph: 'พิมพ์รหัสผ่านอีกครั้ง' })
      + '<div class="mt16"><button class="btn btn-primary" data-action="do-reset-password" data-token="' + esc(resetToken) + '"><i class="bi bi-floppy"></i> บันทึกรหัสผ่านใหม่</button></div>'
      + '</div>';
  });

  route('#/detail', function (hash) {
    var page = el('page'); if (!page) return;
    var id = hash.split('id=')[1];
    page.innerHTML = '<div class="hero"><div class="hero-title">โหลดรายละเอียดเอกสาร…</div></div>';

    call('request.get', { id: id }).then(function (r) {
      window._currentDoc = r;
      var canReview = r.status === 'submitted' && hasCap('request.review');
      var canApprove = (r.status === 'reviewed' || r.status === 'submitted') && hasCap('request.approve');
      var canRequestCancel = (r.status === 'approved') && (r.user_id === Store.user.id || hasCap('request.manage'));
      var canApproveCancel = (r.status === 'cancel_requested') && (hasCap('request.approve') || hasCap('request.review'));

      var actions = '<button class="btn btn-ghost" data-action="print-doc" data-id="' + esc(r.id) + '"><i class="bi bi-printer"></i> พิมพ์เอกสาร A4</button>';
      if (canReview) actions += '<button class="btn btn-primary" data-action="open-review-modal" data-id="' + esc(r.id) + '"><i class="bi bi-clipboard-check"></i> ตรวจสอบเอกสาร</button>';
      if (canApprove) actions += '<button class="btn btn-ok" data-action="open-approve-modal" data-id="' + esc(r.id) + '"><i class="bi bi-check-lg"></i> พิจารณาอนุมัติ</button>';
      if (canRequestCancel) actions += '<button class="btn btn-outline" style="color:var(--warn);border-color:var(--warn)" data-action="open-cancel-request-modal" data-id="' + esc(r.id) + '"><i class="bi bi-x-circle-fill"></i> ยื่นขอยกเลิกใบลานี้</button>';
      if (canApproveCancel) actions += '<button class="btn btn-ok" data-action="approve-cancel-req" data-id="' + esc(r.id) + '"><i class="bi bi-check-circle-fill"></i> อนุมัติการยกเลิกใบลา</button><button class="btn btn-danger" data-action="reject-cancel-req" data-id="' + esc(r.id) + '"><i class="bi bi-x-circle"></i> ปฏิเสธการยกเลิก</button>';

      var printArea = el('print-area');
      if (printArea) {
        printArea.innerHTML = buildOfficialThaiPrintForm(r, Store.boot.settings || {});
      }

      var statusBadge = r.status === 'cancel_requested'
        ? '<span class="badge" style="background:#ff9500;color:#fff">⚠️ ยื่นขอยกเลิกใบลา</span>'
        : '<span class="badge b-' + esc(r.status) + '">' + esc(r.status) + '</span>';

      page.innerHTML = '<div class="hero"><div class="hero-title">เลขที่ ' + esc(r.doc_no) + '</div><div class="hero-sub">' + esc(r.subject) + '</div></div>'
        + '<div class="panel"><h3>รายละเอียดเอกสาร</h3>'
        + '<p><b>ผู้ยื่น:</b> ' + esc(r._owner ? r._owner.full_name : '-') + ' (' + esc(r._owner ? r._owner.department : '-') + ')</p>'
        + '<p><b>ประเภทเอกสาร:</b> ' + esc(r.req_type) + ' (' + esc(r.leave_kind || '-') + ')</p>'
        + '<p><b>วันที่เริ่ม - ถึง:</b> ' + esc(TH.date(r.start_date)) + ' - ' + esc(TH.date(r.end_date)) + ' (' + esc(r.days) + ' วัน)</p>'
        + '<p><b>เหตุผล:</b> ' + esc(r.reason || '-') + '</p>'
        + '<p><b>สถานะปัจจุบัน:</b> ' + statusBadge + '</p>'
        + (r.reviewer_note ? '<p><b>ความเห็นผู้ตรวจสอบ:</b> ' + esc(r.reviewer_note) + '</p>' : '')
        + (r.approver_note ? '<p><b>คำสั่งผู้อนุมัติ:</b> ' + esc(r.approver_note) + '</p>' : '')
        + '<div class="wiz-nav" style="gap:10px;flex-wrap:wrap">' + actions + '</div></div>';
    }).catch(function(e) { toast(e.message, 'error'); });
  });

  route('#/reports', function () {
    var page = el('page'); if (!page) return;
    page.innerHTML = '<div class="hero" style="display:flex;justify-content:space-between;align-items:center">'
      + '<div><div class="hero-title">รายงานสรุปการลา</div><div class="hero-sub">สถิติวันลาสะสมของบุคลากรประจำปีงบประมาณ</div></div>'
      + '<button class="btn btn-primary" data-action="export-csv"><i class="bi bi-file-earmark-excel-fill"></i> ดาวน์โหลด CSV (Excel)</button>'
      + '</div>'
      + '<div id="report-body" class="panel">กำลังโหลดรายงาน…</div>';

    call('report.leave_summary', {}).then(function (d) {
      window._lastReportData = d;
      var rows = d.rows.map(function(r) {
        return '<tr><td>' + esc(r.name) + '</td><td>' + esc(r.dept) + '</td><td>' + r.total_count + ' ครั้ง</td><td><b>' + r.total_days + '</b> วัน</td></tr>';
      }).join('');
      el('report-body').innerHTML = '<div class="tbl-wrap"><table class="tbl"><thead><tr><th>ชื่อ-สกุล</th><th>กลุ่มสาระ/สังกัด</th><th>จำนวนครั้ง</th><th>วันลารวม</th></tr></thead><tbody>' + (rows || '<tr><td colspan="4" style="text-align:center">ไม่มีข้อมูล</td></tr>') + '</tbody></table></div>';
    }).catch(function(e) { toast(e.message, 'error'); });
  });

  route('#/users', function () {
    var page = el('page'); if (!page) return;
    page.innerHTML = '<div class="hero" style="display:flex;justify-content:space-between;align-items:center">'
      + '<div><div class="hero-title">จัดการผู้ใช้งานและอีเมล</div><div class="hero-sub">เพิ่ม แก้ไขข้อมูลบุคลากร และระบุอีเมลเพื่อรับการแจ้งเตือนผลการอนุมัติใบลา</div></div>'
      + '<button class="btn btn-primary" data-action="open-user-modal"><i class="bi bi-person-plus-fill"></i> + เพิ่มผู้ใช้งานใหม่</button>'
      + '</div>'
      + '<div class="panel" id="user-body">กำลังโหลดรายชื่อผู้ใช้…</div>';

    call('user.list', {}).then(function (d) {
      window._userListCache = d.items;
      var rows = d.items.map(function(u) {
        var emailBadge = u.email 
          ? '<span class="badge b-approved" style="font-size:11px"><i class="bi bi-envelope"></i> ' + esc(u.email) + '</span>'
          : '<span class="badge" style="background:#e0e0e0;color:#666;font-size:11px">⚪ ยังไม่ระบุ</span>';

        return '<tr>'
          + '<td><b>' + esc((u.prefix||'') + ' ' + u.full_name) + '</b><br><small style="color:var(--text-sub)">' + esc(u.position || 'บุคลากร') + ' · ' + esc(u.department || '-') + '</small></td>'
          + '<td><code>' + esc(u.username) + '</code></td>'
          + '<td>' + roleChip(u.role) + '</td>'
          + '<td>' + emailBadge + '</td>'
          + '<td>'
          + '<button class="btn btn-primary btn-sm" data-action="edit-user" data-id="' + esc(u.id) + '"><i class="bi bi-pencil-square"></i> แก้ไข / ระบุอีเมล</button>'
          + '</td>'
          + '</tr>';
      }).join('');

      el('user-body').innerHTML = '<div class="tbl-wrap"><table class="tbl"><thead><tr><th>ชื่อ-สกุล / ตำแหน่ง</th><th>ชื่อผู้ใช้</th><th>บทบาท</th><th>อีเมล (Email)</th><th>จัดการ</th></tr></thead><tbody>' + (rows || '<tr><td colspan="5" style="text-align:center">ไม่มีข้อมูลผู้ใช้</td></tr>') + '</tbody></table></div>';
    }).catch(function(e) { toast(e.message, 'error'); });
  });

  route('#/audit', function () {
    var page = el('page'); if (!page) return;
    page.innerHTML = '<div class="hero"><div class="hero-title"><i class="bi bi-shield-lock-fill"></i> ประวัติระบบ (Audit Log)</div><div class="hero-sub">บันทึกประวัติการทำรายการ กิจกรรม และความปลอดภัยของระบบ LRS</div></div>'
      + '<div class="toolbar" style="margin-bottom:16px"><div class="search" style="flex:1"><i class="bi bi-search"></i><input id="audit-search" placeholder="ค้นหาผู้ใช้ / การกระทำ / Target"></div></div>'
      + '<div class="panel" id="audit-body">กำลังโหลดประวัติการทำรายการ…</div>';

    function loadAudit(q, pg) {
      call('audit.list', { query: q || '', page: pg || 1 }).then(function(d) {
        var rows = (d.items || []).map(function(item) {
          var metaStr = '';
          try { metaStr = JSON.stringify(JSON.parse(item.meta || '{}')); } catch(e){ metaStr = item.meta || ''; }
          return '<tr>'
            + '<td><small class="mono">' + esc(TH.dt(item.ts)) + '</small></td>'
            + '<td><b>' + esc(item.username || 'System') + '</b></td>'
            + '<td><span class="badge b-approved">' + esc(item.action) + '</span></td>'
            + '<td>' + esc(item.target_type || '-') + ' ' + esc(item.target_id ? '(' + item.target_id.substring(0,8) + '...)' : '') + '</td>'
            + '<td><small class="mono" style="font-size:11px;color:var(--muted)">' + esc(metaStr) + '</small></td>'
            + '</tr>';
        }).join('');

        var elBody = el('audit-body');
        if (elBody) {
          elBody.innerHTML = '<div class="tbl-wrap"><table class="tbl"><thead><tr><th>เวลา</th><th>ผู้ทำรายการ</th><th>Action</th><th>Target</th><th>Metadata</th></tr></thead><tbody>' + (rows || '<tr><td colspan="5" style="text-align:center">ไม่พบประวัติการทำรายการ</td></tr>') + '</tbody></table></div>'
            + '<div style="margin-top:12px;display:flex;justify-content:space-between;align-items:center"><small>รวมทั้งหมด ' + d.total + ' รายการ</small><div>'
            + (d.page > 1 ? '<button class="btn btn-sm btn-ghost" id="audit-prev-pg"><i class="bi bi-chevron-left"></i> ก่อนหน้า</button> ' : '')
            + (d.page * d.limit < d.total ? '<button class="btn btn-sm btn-ghost" id="audit-next-pg">ถัดไป <i class="bi bi-chevron-right"></i></button>' : '')
            + '</div></div>';

          var btnPrev = el('audit-prev-pg'), btnNext = el('audit-next-pg');
          if (btnPrev) btnPrev.onclick = function() { loadAudit(q, d.page - 1); };
          if (btnNext) btnNext.onclick = function() { loadAudit(q, d.page + 1); };
        }
      }).catch(function(e) { toast(e.message, 'error'); });
    }

    loadAudit('', 1);
    var searchInp = el('audit-search');
    if (searchInp) {
      var timer = null;
      searchInp.oninput = function() {
        clearTimeout(timer);
        timer = setTimeout(function() { loadAudit(searchInp.value, 1); }, 400);
      };
    }
  });

  route('#/settings', function () {
    var page = el('page'); if (!page) return;

    page.innerHTML = '<div class="hero"><div class="hero-title">ตั้งค่าระบบ</div></div>'
      + '<div class="panel" id="setting-body">กำลังโหลดค่าตั้งต้น…</div>';

    call('setting.get', {}).then(function (s) {
      el('setting-body').innerHTML = field('org_name', 'ชื่อโรงเรียน', { value: s.org_name || 'โรงเรียนปากพูน' })
        + field('sae_zone', 'สังกัด', { value: s.sae_zone || 'สำนักงานเขตพื้นที่การศึกษามัธยมศึกษานครศรีธรรมราช' })
        + field('director_name', 'ชื่อผู้อำนวยการโรงเรียน', { value: s.director_name || 'นายวิริยะ วุฒิมานพ' })
        + field('sub_director_name', 'ชื่อรองผู้อำนวยการโรงเรียน', { value: s.sub_director_name || 'นางสาวพรพรรณ ผลไชย' })
        + field('clerk_name', 'ชื่อหัวหน้ากลุ่มบริหารงานบุคคล / ผู้ตรวจสอบ', { value: s.clerk_name || 'นายธงชัย ศักดามาศ' })
        + field('clerk_position', 'ตำแหน่งผู้ตรวจสอบ', { value: s.clerk_position || 'หัวหน้ากลุ่มบริหารงานบุคคล' })
        + field('app_url', 'URL เว็บไซต์ระบบ (สำหรับปุ่มในอีเมล)', { value: s.app_url || (location.origin + location.pathname), ph: 'เช่น http://10.176.51.106:8000 หรือ http://localhost/LRS', hint: 'ใช้สำหรับปุ่มกดในอีเมลแจ้งเตือนให้เปิดเอกสารได้ทันที' })
        
        + '<hr style="border:0;border-top:1px solid var(--line);margin:20px 0">'
        + '<h3><i class="bi bi-pie-chart-fill" style="color:var(--accent)"></i> กำหนดโควต้าวันลาสูงสุดประจำปี (Annual Leave Quotas)</h3>'
        + '<div class="form-grid fg-2 mt16">'
        + field('quota_sick', 'โควต้าวันลาป่วยสูงสุด (วัน/ปี)', { value: s.quota_sick || '60', ph: '60' })
        + field('quota_personal', 'โควต้าวันลากิจสูงสุด (วัน/ปี)', { value: s.quota_personal || '45', ph: '45' })
        + '</div>'
        + '<div class="form-grid fg-2 mt16">'
        + field('quota_vacation', 'โควต้าวันลาพักผ่อนสูงสุด (วัน/ปี)', { value: s.quota_vacation || '10', ph: '10' })
        + field('quota_maternity', 'โควต้าวันลาคลอดบุตรสูงสุด (วัน/ปี)', { value: s.quota_maternity || '90', ph: '90' })
        + '</div>'

        + '<div class="field mt16">'
        + '<label><i class="bi bi-image-fill" style="color:var(--accent)"></i> โลโก้ตราประจำโรงเรียน / สถาบัน (School Logo Image)</label>'
        + '<div style="display:flex;align-items:center;gap:16px;margin-top:8px;flex-wrap:wrap">'
        + '<div style="width:68px;height:68px;border-radius:14px;overflow:hidden;background:var(--grad);display:flex;align-items:center;justify-content:center;color:#fff;font-size:28px;border:2px solid var(--line);box-shadow:var(--shadow-sm);flex:0 0 68px;padding:4px">'
        + '<img id="prev-logo-img" src="' + esc(s.logo_image || '') + '" style="width:100%;height:100%;object-fit:contain;' + (s.logo_image ? '' : 'display:none') + '" onerror="this.style.display=\'none\';var ic=document.getElementById(\'prev-logo-icon\');if(ic)ic.style.display=\'block\';">'
        + '<i id="prev-logo-icon" class="bi bi-file-earmark-text-fill" style="' + (s.logo_image ? 'display:none' : '') + '"></i>'
        + '</div>'
        + '<div>'
        + '<input type="file" id="input-file-logo" accept="image/*" style="display:none">'
        + '<input type="hidden" data-f="logo_image" id="inp-logo-image" value="' + esc(s.logo_image || '') + '">'
        + '<div style="display:flex;gap:8px;flex-wrap:wrap">'
        + '<button type="button" class="btn btn-outline btn-sm" data-action="pick-logo-file"><i class="bi bi-upload"></i> เลือกไฟล์โลโก้โรงเรียน</button>'
        + '<button type="button" class="btn btn-ghost btn-sm" data-action="clear-logo" style="color:var(--danger)"><i class="bi bi-trash"></i> ลบโลโก้</button>'
        + '</div>'
        + '<div class="field-hint" style="margin-top:4px">แนะนำไฟล์ภาพ PNG พื้นหลังใส หรือ JPG เพื่อแสดงผลด้านบน แถบเมนู และหน้าล็อกอิน</div>'
        + '</div>'
        + '</div>'
        + '</div>'
        + '<div style="background:rgba(10,132,255,.06);border:1px solid rgba(10,132,255,.2);border-radius:14px;padding:16px;margin:20px 0">'
        + '<h4 style="margin:0 0 10px;color:var(--accent);display:flex;align-items:center;gap:8px;font-size:15px"><i class="bi bi-toggle-on" style="font-size:20px"></i> สวิตช์แสดงปุ่มทดลองใช้งาน (Demo Quick Login)</h4>'
        + field('enable_demo_login', 'สถานะการแสดงปุ่ม Demo ในหน้าเข้าสู่ระบบ', { type: 'select', value: s.enable_demo_login || 'yes', options: [{v:'yes',t:'🟢 เปิดแสดงปุ่ม Demo (สำหรับช่วงทดสอบระบบ)'},{v:'no',t:'🔴 ปิดการแสดงปุ่ม Demo (ซ่อนปุ่ม Demo เพื่อใช้งานจริง)'}], hint: 'เมื่อเลือกปิดแล้ว หน้าเข้าสู่ระบบจะซ่อนกล่องปุ่มตัวอย่างทั้งหมดทันที' })
        + '</div>'
        + '<hr style="border:0;border-top:1px solid var(--line);margin:20px 0">'
        + '<h3><i class="bi bi-envelope-check-fill" style="color:var(--accent)"></i> การตั้งค่าการแจ้งเตือนผ่านอีเมล (Email & SMTP Engine)</h3>'
        + field('email_enabled', 'สถานะการแจ้งเตือนผ่านอีเมล', { type: 'select', value: s.email_enabled || 'yes', options: [{v:'yes',t:'เปิดการใช้งาน ( Active )'},{v:'no',t:'ปิดการใช้งาน'}] })
        + field('email_mode', 'โหมดการส่งอีเมล', { type: 'select', value: s.email_mode || 'smtp', options: [{v:'smtp',t:'ส่งผ่าน SMTP Server (แนะนำสำหรับ Gmail/Outlook)'},{v:'mail',t:'ส่งผ่าน PHP Native mail()'}] })
        + '<div class="form-grid fg-2 mt16">'
        + field('smtp_host', 'SMTP Server Host', { value: s.smtp_host || 'smtp.gmail.com', ph: 'เช่น smtp.gmail.com หรือ smtp.office365.com' })
        + field('smtp_port', 'SMTP Port', { value: s.smtp_port || '587', ph: '587 (TLS) หรือ 465 (SSL)' })
        + '</div>'
        + field('smtp_secure', 'ระบบความปลอดภัย (Security Encryption)', { type: 'select', value: s.smtp_secure || 'tls', options: [{v:'tls',t:'STARTTLS (Port 587 - แนะนำ)'},{v:'ssl',t:'SSL (Port 465)'},{v:'none',t:'ไม่มี (None)'}] })
        + '<div class="form-grid fg-2 mt16">'
        + field('smtp_user', 'SMTP Username / อีเมลผู้ส่ง', { value: s.smtp_user || 'noreply@pakpoon.ac.th', ph: 'เช่น youraccount@gmail.com' })
        + field('smtp_pass', 'SMTP Password / App Password', { type: 'password', value: s.smtp_pass || '', ph: 'รหัสผ่านแอป (App Password 16 หลัก)' })
        + '</div>'
        + '<div class="form-grid fg-2 mt16">'
        + field('sender_name', 'ชื่อแสดงผู้ส่ง (Sender Name)', { value: s.sender_name || 'ระบบใบลาราชการ โรงเรียนปากพูน' })
        + field('sender_email', 'อีเมลผู้ส่ง (Sender Email)', { value: s.sender_email || 'noreply@pakpoon.ac.th' })
        + '</div>'
        + '<hr style="border:0;border-top:1px solid var(--line);margin:20px 0">'
        + '<h3><i class="bi bi-database-fill-gear" style="color:var(--accent)"></i> การดูแลรักษาฐานข้อมูล (Database Maintenance)</h3>'
        + '<div style="display:flex;flex-wrap:wrap;gap:10px;margin-top:12px">'
        + '<button class="btn btn-outline" data-action="backup-db"><i class="bi bi-database-down"></i> 💾 สำรองไฟล์ฐานข้อมูล (.db)</button>'
        + '<button class="btn btn-outline" data-action="restore-db-btn"><i class="bi bi-database-up"></i> 📥 กู้คืนฐานข้อมูล (.db)</button>'
        + '<button class="btn btn-ghost" data-action="vacuum-db" style="color:var(--accent)"><i class="bi bi-magic"></i> 🧹 กระชับฐานข้อมูล (VACUUM)</button>'
        + '<input type="file" id="inp-restore-db" accept=".db" style="display:none">'
        + '</div>'
        + '<div style="display:flex;flex-wrap:wrap;gap:10px;margin-top:24px">'
        + '<button class="btn btn-primary" data-action="save-settings"><i class="bi bi-floppy"></i> บันทึกการตั้งค่าทั้งหมด</button>'
        + '<button class="btn btn-ok" data-action="test-email"><i class="bi bi-send-check-fill"></i> ทดสอบส่งอีเมลแจ้งเตือน</button>'
        + '<button class="btn btn-outline" data-action="open-pwa-guide"><i class="bi bi-phone-fill"></i> 📱 วิธีติดตั้งเป็นแอปมือถือ</button>'
        + '</div>';
    }).catch(function(e) { toast(e.message, 'error'); });
  });

  route('#/profile', function () {
    var page = el('page'); if (!page) return;
    var u = Store.user;
    var avSrc = u.avatar_url || '';
    var sigSrc = u.signature_url || '';

    page.innerHTML = '<div class="hero"><div class="hero-title">โปรไฟล์ส่วนตัว</div><div class="hero-sub">' + esc(u.full_name) + '</div></div>'
      + '<div class="panel">'
      + field('prefix', 'คำนำหน้า', { value: u.prefix || 'นาย' })
      + field('full_name', 'ชื่อ-สกุล', { value: u.full_name })
      + field('position', 'ตำแหน่ง', { value: u.position || '' })
      + field('department', 'กลุ่มสาระ/ฝ่าย', { value: u.department || '' })
      + field('email', 'อีเมล (สำหรับรับผลการอนุมัติใบลา)', { value: u.email || '', ph: 'เช่น teacher@pakpoon.ac.th', required: true, hint: 'ระบบจะส่งผลการอนุมัติหรือปฏิเสธใบลามายังอีเมลนี้' })
      + field('phone', 'เบอร์โทรศัพท์', { value: u.phone || '' })
      
      + '<hr style="border:0;border-top:1px solid var(--line);margin:20px 0">'
      
      + '<div class="field">'
      + '<label><i class="bi bi-person-bounding-box" style="color:var(--accent)"></i> รูปโปรไฟล์ (Avatar Image)</label>'
      + '<div style="display:flex;align-items:center;gap:16px;margin-top:8px;flex-wrap:wrap">'
      + '<div style="width:76px;height:76px;border-radius:50%;overflow:hidden;background:var(--grad);display:flex;align-items:center;justify-content:center;color:#fff;font-size:30px;font-weight:700;border:3px solid var(--line2);box-shadow:var(--shadow-sm);flex:0 0 76px">'
      + '<img id="prev-avatar-img" src="' + esc(avSrc) + '" style="width:100%;height:100%;object-fit:cover;' + (avSrc ? '' : 'display:none') + '" onerror="this.style.display=\'none\';var init=document.getElementById(\'prev-avatar-init\');if(init)init.style.display=\'block\';">'
      + '<span id="prev-avatar-init" style="' + (avSrc ? 'display:none' : '') + '">' + esc(((u.full_name||'').trim().charAt(0))||'?') + '</span>'
      + '</div>'
      + '<div>'
      + '<input type="file" id="input-file-avatar" accept="image/*" style="display:none">'
      + '<input type="hidden" data-f="avatar_url" id="inp-avatar-url" value="' + esc(avSrc) + '">'
      + '<div style="display:flex;gap:8px;flex-wrap:wrap">'
      + '<button type="button" class="btn btn-outline btn-sm" data-action="pick-avatar-file"><i class="bi bi-upload"></i> เลือกไฟล์รูปภาพโปรไฟล์</button>'
      + '<button type="button" class="btn btn-ghost btn-sm" data-action="clear-avatar" style="color:var(--danger)"><i class="bi bi-trash"></i> ลบรูปภาพ</button>'
      + '</div>'
      + '<div class="field-hint" style="margin-top:4px">กดเลือกไฟล์รูปภาพจากมือถือหรือคอมพิวเตอร์ (ปรับขนาดรูปภาพให้อัตโนมัติ)</div>'
      + '</div>'
      + '</div>'
      + '</div>'
      
      + '<div class="field mt16">'
      + '<label><i class="bi bi-pen-fill" style="color:var(--accent)"></i> รูปภาพลายเซ็นดิจิทัล (Digital Signature Image)</label>'
      + '<div style="margin-top:8px">'
      + '<div style="min-height:90px;max-width:340px;border:1px dashed var(--line);border-radius:14px;padding:12px;display:flex;align-items:center;justify-content:center;background:repeating-conic-gradient(#f0f0f0 0% 25%, #fff 0% 50%) 50% / 16px 16px;margin-bottom:10px">'
      + '<img id="prev-sig-img" src="' + esc(sigSrc) + '" style="max-height:70px;max-width:100%;object-fit:contain;' + (sigSrc ? '' : 'display:none') + '" onerror="this.style.display=\'none\';var none=document.getElementById(\'prev-sig-none\');if(none)none.style.display=\'block\';">'
      + '<div id="prev-sig-none" style="color:var(--muted);font-size:12px;' + (sigSrc ? 'display:none' : '') + '"><i class="bi bi-file-earmark-image"></i> ยังไม่มีภาพลายเซ็นดิจิทัล</div>'
      + '</div>'
      + '<input type="file" id="input-file-signature" accept="image/*" style="display:none">'
      + '<input type="hidden" data-f="signature_url" id="inp-signature-url" value="' + esc(sigSrc) + '">'
      + '<div style="display:flex;gap:8px;flex-wrap:wrap">'
      + '<button type="button" class="btn btn-outline btn-sm" data-action="pick-signature-file"><i class="bi bi-file-earmark-arrow-up"></i> เลือกไฟล์ภาพลายเซ็น (PNG พื้นหลังใส)</button>'
      + '<button type="button" class="btn btn-ghost btn-sm" data-action="clear-signature" style="color:var(--danger)"><i class="bi bi-trash"></i> ลบลายเซ็น</button>'
      + '</div>'
      + '<div class="field-hint" style="margin-top:4px">แนะนำไฟล์ภาพ PNG พื้นหลังใส เพื่อประทับลายเซ็นลงบนเอกสารใบลา A4 ได้คมชัดตามระเบียบ</div>'
      + '</div>'
      + '</div>'
      
      + '<div class="mt16"><button class="btn btn-primary" data-action="save-profile"><i class="bi bi-floppy"></i> บันทึกโปรไฟล์</button></div>'
      
      + '<hr style="border:0;border-top:1px solid var(--line);margin:24px 0">'
      + '<div style="background:rgba(255,159,10,.06);border:1px solid rgba(255,159,10,.25);border-radius:16px;padding:20px">'
      + '<h3 style="margin:0 0 12px;color:var(--warn);display:flex;align-items:center;gap:8px"><i class="bi bi-shield-lock-fill"></i> เปลี่ยนรหัสผ่านส่วนตัว (Change Password)</h3>'
      + '<div class="form-grid fg-2">'
      + field('cur_password', 'รหัสผ่านปัจจุบัน', { type: 'password', ph: 'กรอกรหัสผ่านปัจจุบัน' })
      + field('new_password_p', 'รหัสผ่านใหม่', { type: 'password', ph: 'อย่างน้อย 6 ตัวอักษร' })
      + '</div>'
      + '<div class="mt16"><button class="btn btn-primary" data-action="change-my-password" style="background:var(--warn);border-color:var(--warn)"><i class="bi bi-key-fill"></i> ยืนยันเปลี่ยนรหัสผ่าน</button></div>'
      + '</div>'
      + '</div>';
  });

  function renderLanding() {
    var root = el('app-root'); root.className = '';
    var demo = Store.boot.demo || [];
    var s = Store.boot.settings || {};
    var enableDemo = s.enable_demo_login !== 'no';
    var savedUser = '';
    try { savedUser = localStorage.getItem('lrs_remember_username') || ''; } catch(e){}

    var demoSectionHtml = '';
    if (enableDemo && demo && demo.length > 0) {
      var demoHtml = demo.map(function(u) {
        return '<button class="lf-demo-card" data-action="demo-login" data-user="' + esc(u.username) + '" data-role="' + esc(u.role) + '"><div class="lf-demo-ic"><i class="bi bi-person-fill"></i></div><div class="lf-demo-role">' + esc(u.role) + '</div><div class="lf-demo-user">' + esc(u.username) + '</div></button>';
      }).join('');

      demoSectionHtml = '<div class="lf-demo"><div class="lf-demo-h">ทดลองใช้งานด้วยบัญชีตัวอย่าง <span class="tag">DEMO</span></div><div class="lf-demo-grid">' + demoHtml + '</div></div>';
    }

    var brandLogo = (s.logo_image && s.logo_image.trim()) 
      ? '<span style="display:inline-flex;width:100%;height:100%;align-items:center;justify-content:center"><img src="' + esc(s.logo_image) + '" style="width:100%;height:100%;object-fit:contain;background:#fff;padding:4px;border-radius:12px" onerror="this.style.display=\'none\';var ic=this.parentNode.querySelector(\'i\');if(ic)ic.style.display=\'inline\';"><i class="bi bi-file-earmark-text-fill" style="display:none"></i></span>' 
      : '<i class="bi bi-file-earmark-text-fill"></i>';

    root.innerHTML = '<div class="lf-stage"><div class="lf-win">'
      + '<div class="lf-brandpanel"><div class="lf-logo">' + brandLogo + '</div>'
      + '<div class="lf-title">ระบบเอกสาร<br>ใบลาราชการ</div><div class="lf-sub">' + esc(s.org_name || 'โรงเรียนปากพูน') + ' · สพฐ.</div>'
      + '<div class="lf-stats"><div><div class="lf-stat-v">5</div><div class="lf-stat-l">แบบฟอร์ม</div></div><div><div class="lf-stat-v">100%</div><div class="lf-stat-l">ถูกต้องตามระเบียบ</div></div></div>'
      + '</div>'
      + '<div class="lf-formpanel">'
      + '<div class="lf-mobile-header"><div class="lf-logo"><i class="bi bi-file-earmark-text-fill"></i></div><div class="lf-title">ระบบเอกสารใบลาราชการ</div><div class="lf-sub">' + esc(s.org_name || 'โรงเรียนปากพูน') + ' · สพฐ.</div></div>'
      + '<div class="lf-h">ยินดีต้อนรับ</div><div class="lf-hs">เข้าสู่ระบบเพื่อยื่นและจัดการเอกสารใบลา</div>'
      + '<form class="lf-form" id="login-form"><div class="lf-input-wrap"><i class="bi bi-person lf-icon"></i><input name="username" placeholder="ชื่อผู้ใช้ หรือ อีเมล" value="' + esc(savedUser) + '" required></div>'
      + '<div class="lf-input-wrap"><i class="bi bi-lock lf-icon"></i><input name="password" type="password" placeholder="รหัสผ่าน" required></div>'
      + '<button type="submit" class="lf-submit">เข้าสู่ระบบ</button></form>'
      + '<div style="text-align:center;margin-top:12px"><a href="#" data-action="show-forgot-pw" style="font-size:12.5px;color:var(--muted)"><i class="bi bi-key"></i> ลืมรหัสผ่าน?</a></div>'
      + demoSectionHtml
      + '</div>'
      + '</div></div>';
    
    hideBoot();

    var form = el('login-form');
    if (form) {
      form.onsubmit = function(e) {
        e.preventDefault();
        var un = form.querySelector('input[name="username"]').value;
        var pw = form.querySelector('input[name="password"]').value;
        doLogin(un, pw);
      };
    }
  }

  function doLogin(username, password) {
    Spinner.show('กำลังเข้าสู่ระบบ…');
    call('auth.login', { username: username, password: password }).then(function(d) {
      Spinner.hide();
      Store.token = d.token;
      Store.user = d.user;
      Store.caps = d.caps;
      try {
        localStorage.setItem('lrs_token', d.token);
        localStorage.setItem('lrs_remember_username', username);
      } catch(e){}
      enterShell();
      location.hash = '#/dashboard';
      dispatch();
    }).catch(function(e) {
      Spinner.hide();
      toast(e.message, 'error');
    });
  }

  // Global Event Listeners
  document.addEventListener('click', function(e) {
    var actEl = e.target.closest('[data-action]');
    var hashEl = e.target.closest('[data-hash]');

    if (actEl) {
      if (actEl.disabled) return;
      var act = actEl.getAttribute('data-action');

      if (act === 'burger') {
        var sb = el('sidebar'); var sbd = el('sidebar-backdrop');
        if (sb) sb.classList.toggle('open');
        if (sbd) sbd.classList.toggle('open');
      } else if (act === 'close-sidebar') {
        var sb = el('sidebar'); var sbd = el('sidebar-backdrop');
        if (sb) sb.classList.remove('open');
        if (sbd) sbd.classList.remove('open');
      } else if (act === 'pick-logo-file') {
        var inp = el('input-file-logo');
        if (inp) {
          inp.onchange = function() {
            if (this.files && this.files[0]) {
              handleImageFileSelect(this.files[0], 'inp-logo-image', 'prev-logo-img', 'prev-logo-icon', 300, 'branding');
            }
          };
          inp.click();
        }
      } else if (act === 'clear-logo') {
        var urlInp = el('inp-logo-image'); if (urlInp) urlInp.value = '';
        var img = el('prev-logo-img'); if (img) img.style.display = 'none';
        var ic = el('prev-logo-icon'); if (ic) ic.style.display = 'block';
        toast('ลบโลโก้เรียบร้อยแล้ว กด "บันทึกการตั้งค่า" เพื่อยืนยัน', 'info');
      } else if (act === 'pick-avatar-file') {
        var inp = el('input-file-avatar');
        if (inp) {
          inp.onchange = function() {
            if (this.files && this.files[0]) {
              handleImageFileSelect(this.files[0], 'inp-avatar-url', 'prev-avatar-img', 'prev-avatar-init', 240, 'avatars');
            }
          };
          inp.click();
        }
      } else if (act === 'clear-avatar') {
        var urlInp = el('inp-avatar-url'); if (urlInp) urlInp.value = '';
        var img = el('prev-avatar-img'); if (img) img.style.display = 'none';
        var init = el('prev-avatar-init'); if (init) init.style.display = 'block';
        toast('ลบรูปภาพโปรไฟล์เรียบร้อยแล้ว', 'info');
      } else if (act === 'pick-signature-file') {
        var inp = el('input-file-signature');
        if (inp) {
          inp.onchange = function() {
            if (this.files && this.files[0]) {
              handleImageFileSelect(this.files[0], 'inp-signature-url', 'prev-sig-img', 'prev-sig-none', 600, 'signatures');
            }
          };
          inp.click();
        }
      } else if (act === 'clear-signature') {
        var urlInp = el('inp-signature-url'); if (urlInp) urlInp.value = '';
        var img = el('prev-sig-img'); if (img) img.style.display = 'none';
        var noneTxt = el('prev-sig-none'); if (noneTxt) noneTxt.style.display = 'block';
        toast('ลบลายเซ็นเรียบร้อยแล้ว', 'info');
      } else if (act === 'set-theme') {
        applyTheme(actEl.getAttribute('data-t'));
      } else if (act === 'toggle-dark') {
        var isDark = document.documentElement.getAttribute('data-dark') === '1';
        var newDark = isDark ? '0' : '1';
        document.documentElement.setAttribute('data-dark', newDark);
        try { localStorage.setItem('lrs_dark', newDark); } catch(e){}
        var ic = el('dark-toggle-ic');
        if (ic) ic.className = newDark === '1' ? 'bi bi-sun-fill' : 'bi bi-moon-stars-fill';
      } else if (act === 'show-forgot-pw') {
        e.preventDefault();
        openModal('<div class="modal-card"><h3 style="margin:0 0 16px"><i class="bi bi-key-fill" style="color:var(--warn)"></i> ลืมรหัสผ่าน</h3>'
          + '<p style="color:var(--muted);font-size:13px">กรอกอีเมลที่ลงทะเบียนไว้ในระบบ ระบบจะส่งลิ้งก์รีเซ็ตรหัสผ่านให้ทางอีเมล</p>'
          + field('forgot_email', 'อีเมล', { type: 'email', ph: 'กรอกอีเมลของคุณ' })
          + '<div class="mt16" style="display:flex;gap:10px">'
          + '<button class="btn btn-primary" data-action="send-forgot-pw"><i class="bi bi-send"></i> ส่งลิ้งก์รีเซ็ต</button>'
          + '<button class="btn btn-ghost" data-action="close-modal">ยกเลิก</button>'
          + '</div></div>');
      } else if (act === 'send-forgot-pw') {
        var emailInp = $('[data-f="forgot_email"]');
        var emailVal = emailInp ? emailInp.value.trim() : '';
        if (!emailVal) { toast('กรุณากรอกอีเมล', 'warning'); return; }
        actEl.disabled = true; actEl.textContent = 'กำลังส่ง…';
        call('auth.request_reset', { email: emailVal }).then(function(d) {
          closeModal();
          toast(d.message || 'ส่งลิ้งก์รีเซ็ตรหัสผ่านเรียบร้อย ตรวจสอบอีเมลของคุณ', 'success');
        }).catch(function(err) {
          actEl.disabled = false; actEl.textContent = 'ส่งลิ้งก์รีเซ็ต';
          toast(err.message, 'error');
        });
      } else if (act === 'do-reset-password') {
        var pw1 = ($('[data-f="new_password"]') || {}).value || '';
        var pw2 = ($('[data-f="new_password2"]') || {}).value || '';
        var tok = actEl.getAttribute('data-token') || '';
        if (!pw1 || pw1.length < 6) { toast('รหัสผ่านต้องมีอย่างน้อย 6 ตัวอักษร', 'warning'); return; }
        if (pw1 !== pw2) { toast('รหัสผ่านทั้งสองไม่ตรงกัน', 'warning'); return; }
        actEl.disabled = true;
        call('auth.do_reset', { token: tok, password: pw1 }).then(function(d) {
          toast(d.message, 'success');
          // Clear reset_token from URL
          history.replaceState({}, '', location.pathname);
          go('#/login');
          renderLanding();
        }).catch(function(err) {
          actEl.disabled = false;
          toast(err.message, 'error');
        });
      } else if (act === 'export-csv') {
        var statusFilter = ($('[data-f="filter_status"]') || {}).value || 'all';
        var dateFrom = ($('[data-f="filter_date_from"]') || {}).value || '';
        var dateTo = ($('[data-f="filter_date_to"]') || {}).value || '';
        Spinner.show('กำลัง Export CSV…');
        call('export.csv', { status: statusFilter, date_from: dateFrom, date_to: dateTo }).then(function(d) {
          Spinner.hide();
          var bytes = atob(d.csv_b64);
          var blob = new Blob([bytes], { type: 'text/csv;charset=utf-8' });
          var url = URL.createObjectURL(blob);
          var a = document.createElement('a');
          a.href = url; a.download = d.filename; a.click();
          URL.revokeObjectURL(url);
          toast('Export ' + d.count + ' รายการเรียบร้อย', 'success');
        }).catch(function(err){ Spinner.hide(); toast(err.message, 'error'); });
      } else if (act === 'req-notify-permission') {
        if ('Notification' in window) {
          Notification.requestPermission().then(function(perm) {
            if (perm === 'granted') {
              toast('เปิดการแจ้งเตือนเรียบร้อย', 'success');
              try { localStorage.setItem('lrs_notif_asked', '1'); } catch(e){}
            }
          });
        }
      } else if (act === 'dismiss-pwa') {
        var b = document.getElementById('pwa-install-banner');
        if (b) b.remove();
        try { localStorage.setItem('lrs_pwa_dismissed', '1'); } catch(e){}
      } else if (act === 'toggle-notify') {
        var pop = el('notify-popover');
        if (pop) pop.style.display = pop.style.display === 'none' ? 'flex' : 'none';
      } else if (act === 'mark-all-read') {
        call('notify.mark_all_read', {}).then(function() {
          loadNotifications();
          toast('ทำเครื่องหมายอ่านแล้วทั้งหมด', 'success');
        });
      } else if (act === 'read-notify') {
        var nid = actEl.getAttribute('data-id');
        var nlink = actEl.getAttribute('data-link');
        call('notify.mark_read', { id: nid }).then(function() {
          loadNotifications();
          if (nlink) go(nlink);
        });
      } else if (act === 'logout') {
        call('auth.logout', {}).then(function() {
          Store.token = ''; Store.user = null;
          try { localStorage.removeItem('lrs_token'); } catch(e){}
          renderLanding();
          toast('ออกจากระบบเรียบร้อย', 'success');
        }).catch(function() {
          Store.token = ''; Store.user = null;
          try { localStorage.removeItem('lrs_token'); } catch(e){}
          renderLanding();
        });
      } else if (act === 'demo-login') {
        var du = actEl.getAttribute('data-user');
        doLogin(du, '123456');
      } else if (act === 'pick-type') {
        WIZ.type = actEl.getAttribute('data-type');
        WIZ.data = {};
        dispatch();
      } else if (act === 'wiz-reset') {
        WIZ = { type: null, step: 0, data: {} };
        dispatch();
      } else if (act === 'wiz-submit') {
        actEl.disabled = true;
        var page = el('page');
        WIZ.data.req_type = WIZ.type || 'leave';
        WIZ.data.subject = ($('[data-f="subject"]', page) || {}).value || '';
        WIZ.data.reason = ($('[data-f="reason"]', page) || {}).value || '';
        WIZ.data.start_date = ($('[data-f="start_date"]', page) || {}).value || '';
        WIZ.data.end_date = ($('[data-f="end_date"]', page) || {}).value || WIZ.data.start_date;
        WIZ.data.days = ($('[data-f="days"]', page) || {}).value || 1;
        WIZ.data.leave_kind = ($('[data-f="leave_kind"]', page) || {}).value || 'personal';
        WIZ.data.contact_addr = ($('[data-f="contact_addr"]', page) || {}).value || '';
        WIZ.data.contact_phone = ($('[data-f="contact_phone"]', page) || {}).value || '';

        // Extra fields
        WIZ.data.accum_days = ($('[data-f="accum_days"]', page) || {}).value || 0;
        WIZ.data.right_days = ($('[data-f="right_days"]', page) || {}).value || 10;
        WIZ.data.orig_leave = ($('[data-f="orig_leave"]', page) || {}).value || '';
        WIZ.data.orig_start = ($('[data-f="orig_start"]', page) || {}).value || '';
        WIZ.data.orig_end = ($('[data-f="orig_end"]', page) || {}).value || '';
        WIZ.data.position_now = ($('[data-f="position_now"]', page) || {}).value || '';
        WIZ.data.salary_amount = ($('[data-f="salary_amount"]', page) || {}).value || '';
        WIZ.data.idc_case = ($('[data-f="idc_case"]', page) || {}).value || 'first';
        WIZ.data.idc_reason = ($('[data-f="idc_reason"]', page) || {}).value || '';

        if (!WIZ.data.subject) {
          actEl.disabled = false;
          toast('กรุณากรอกเรื่องที่ต้องการยื่นใบลา', 'warning');
          return;
        }

        Spinner.show('กำลังบันทึกเอกสาร…');
        call('request.save', WIZ.data).then(function(res) {
          return call('request.submit', { id: res.id });
        }).then(function() {
          Spinner.hide();
          toast('ยื่นเอกสารเรียบร้อยแล้ว', 'success');
          WIZ = { type: null, step: 0, data: {} };
          go('#/my');
        }).catch(function(err) {
          actEl.disabled = false;
          Spinner.hide();
          toast(err.message, 'error');
        });
      } else if (act === 'open-review-modal') {
        if (window._currentDoc) openReviewModal(window._currentDoc);
      } else if (act === 'open-approve-modal') {
        if (window._currentDoc) openApproveModal(window._currentDoc);
      } else if (act === 'print-doc') {
        document.body.classList.add('printing');
        window.print();
        setTimeout(function() { document.body.classList.remove('printing'); }, 500);
      } else if (act === 'bulk-approve-selected') {
        var selected = [];
        $$('.chk-inbox-item:checked').forEach(function(c) { selected.push(c.value); });
        if (selected.length === 0) { toast('กรุณาเลือกรายการที่ต้องการอนุมัติอย่างน้อย 1 รายการ', 'warning'); return; }
        if (confirm('ยืนยันอนุมัติคำขอที่เลือกจำนวน ' + selected.length + ' รายการ?')) {
          Spinner.show('กำลังดำเนินการอนุมัติแบบกลุ่ม…');
          call('request.bulk_approve', { ids: selected, decision: 'approve', note: 'อนุมัติแบบกลุ่ม' }).then(function(res) {
            Spinner.hide();
            toast('อนุมัติแบบกลุ่มสำเร็จเรียบร้อย ' + res.updated_count + ' รายการ', 'success');
            dispatch();
          }).catch(function(err) { Spinner.hide(); toast(err.message, 'error'); });
        }
      } else if (act === 'open-cancel-request-modal') {
        var reqId = actEl.getAttribute('data-id');
        var reason = prompt('กรุณาระบุเหตุผลการยื่นขอยกเลิกวันลา:');
        if (reason !== null && reason.trim() !== '') {
          Spinner.show('กำลังยื่นคำขอยกเลิกวันลา…');
          call('request.request_cancel', { id: reqId, reason: reason.trim() }).then(function() {
            Spinner.hide();
            toast('ยื่นคำขอยกเลิกวันลาเรียบร้อยแล้ว รอการอนุมัติ', 'success');
            dispatch();
          }).catch(function(err) { Spinner.hide(); toast(err.message, 'error'); });
        }
      } else if (act === 'approve-cancel-req') {
        var reqId = actEl.getAttribute('data-id');
        if (confirm('ยืนยันอนุมัติการยกเลิกใบลานี้? (ระบบจะคืนโควต้าวันลาให้ผู้ยื่น)')) {
          Spinner.show('กำลังอนุมัติการยกเลิกใบลา…');
          call('request.approve_cancel', { id: reqId, decision: 'approve', note: 'อนุมัติการยกเลิกวันลา' }).then(function() {
            Spinner.hide();
            toast('อนุมัติการยกเลิกใบลาและคืนโควต้าวันลาสำเร็จ', 'success');
            dispatch();
          }).catch(function(err) { Spinner.hide(); toast(err.message, 'error'); });
        }
      } else if (act === 'reject-cancel-req') {
        var reqId = actEl.getAttribute('data-id');
        var note = prompt('ระบุเหตุผลไม่อนุมัติการยกเลิกใบลา:') || 'ปฏิเสธการยกเลิกวันลา';
        Spinner.show('กำลังดำเนินการ…');
        call('request.approve_cancel', { id: reqId, decision: 'reject', note: note }).then(function() {
          Spinner.hide();
          toast('ปฏิเสธคำขอยกเลิกใบลาเรียบร้อยแล้ว', 'info');
          dispatch();
        }).catch(function(err) { Spinner.hide(); toast(err.message, 'error'); });
      } else if (act === 'restore-db-btn') {
        var fileInp = el('inp-restore-db');
        if (fileInp) {
          fileInp.onchange = function() {
            var file = fileInp.files[0];
            if (!file) return;
            if (!confirm('คำเตือน: การกู้คืนฐานข้อมูลจะทดแทนข้อมูลปัจจุบันด้วยไฟล์สำรอง "' + file.name + '" ยืนยันการดำเนินการหรือไม่?')) return;
            var reader = new FileReader();
            reader.onload = function(evt) {
              var dataUrl = evt.target.result;
              Spinner.show('กำลังกู้คืนฐานข้อมูล…');
              call('setting.restore_db', { data_b64: dataUrl }).then(function(res) {
                Spinner.hide();
                toast(res.message || 'กู้คืนฐานข้อมูลสำเร็จ', 'success');
                setTimeout(function() { location.reload(); }, 1200);
              }).catch(function(err) { Spinner.hide(); toast(err.message, 'error'); });
            };
            reader.readAsDataURL(file);
          };
          fileInp.click();
        }
      } else if (act === 'vacuum-db') {
        Spinner.show('กำลังกระชับและปรับแต่งประสิทธิภาพฐานข้อมูล (VACUUM)…');
        call('setting.vacuum_db', {}).then(function(res) {
          Spinner.hide();
          toast(res.message || 'กระชับฐานข้อมูลสำเร็จ', 'success');
        }).catch(function(err) { Spinner.hide(); toast(err.message, 'error'); });
      } else if (act === 'export-csv') {
        if (!window._lastReportData || !window._lastReportData.rows) {
          toast('ไม่มีข้อมูลรายงานสำหรับดาวน์โหลด', 'warning');
          return;
        }
        var csv = "\uFEFFชื่อ-สกุล,กลุ่มสาระ/สังกัด,จำนวนครั้ง,วันลารวม\n";
        window._lastReportData.rows.forEach(function(r) {
          csv += '"' + r.name + '","' + r.dept + '",' + r.total_count + ',' + r.total_days + "\n";
        });
        var blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
        var url = URL.createObjectURL(blob);
        var a = document.createElement('a');
        a.href = url; a.download = 'LRS_Leave_Report_' + new Date().toISOString().slice(0,10) + '.csv';
        a.click();
        toast('ดาวน์โหลดรายงาน CSV เรียบร้อย', 'success');
      } else if (act === 'save-settings') {
        actEl.disabled = true;
        var page = el('page');
        var payload = {
          org_name: ($('[data-f="org_name"]', page) || {}).value || 'โรงเรียนปากพูน',
          sae_zone: ($('[data-f="sae_zone"]', page) || {}).value || 'สำนักงานเขตพื้นที่การศึกษามัธยมศึกษานครศรีธรรมราช',
          director_name: ($('[data-f="director_name"]', page) || {}).value || 'นายวิริยะ วุฒิมานพ',
          sub_director_name: ($('[data-f="sub_director_name"]', page) || {}).value || 'นางสาวพรพรรณ ผลไชย',
          clerk_name: ($('[data-f="clerk_name"]', page) || {}).value || 'นายธงชัย ศักดามาศ',
          clerk_position: ($('[data-f="clerk_position"]', page) || {}).value || 'หัวหน้ากลุ่มบริหารงานบุคคล',
          app_url: ($('[data-f="app_url"]', page) || {}).value || (location.origin + location.pathname),
          email_enabled: ($('[data-f="email_enabled"]', page) || {}).value || 'yes',
          email_mode: ($('[data-f="email_mode"]', page) || {}).value || 'smtp',
          smtp_host: ($('[data-f="smtp_host"]', page) || {}).value || 'smtp.gmail.com',
          smtp_port: ($('[data-f="smtp_port"]', page) || {}).value || '587',
          smtp_secure: ($('[data-f="smtp_secure"]', page) || {}).value || 'tls',
          smtp_user: ($('[data-f="smtp_user"]', page) || {}).value || '',
          smtp_pass: ($('[data-f="smtp_pass"]', page) || {}).value || '',
          sender_name: ($('[data-f="sender_name"]', page) || {}).value || '',
          sender_email: ($('[data-f="sender_email"]', page) || {}).value || '',
          logo_image: ($('[data-f="logo_image"]', page) || {}).value || '',
          enable_demo_login: ($('[data-f="enable_demo_login"]', page) || {}).value || 'yes'
        };
        call('setting.update', payload).then(function() {
          toast('บันทึกการตั้งค่าระบบเรียบร้อยแล้ว', 'success');
          location.reload();
        }).catch(function(err) { actEl.disabled = false; toast(err.message, 'error'); });
      } else if (act === 'save-profile') {
        actEl.disabled = true;
        var page = el('page');
        var payload = {
          prefix: ($('[data-f="prefix"]', page) || {}).value || '',
          full_name: ($('[data-f="full_name"]', page) || {}).value || '',
          position: ($('[data-f="position"]', page) || {}).value || '',
          department: ($('[data-f="department"]', page) || {}).value || '',
          email: ($('[data-f="email"]', page) || {}).value || '',
          phone: ($('[data-f="phone"]', page) || {}).value || '',
          avatar_url: ($('[data-f="avatar_url"]', page) || {}).value || '',
          signature_url: ($('[data-f="signature_url"]', page) || {}).value || ''
        };
        Spinner.show('กำลังบันทึกโปรไฟล์…');
        call('profile.update', payload).then(function(res) {
          Spinner.hide();
          toast('บันทึกโปรไฟล์เรียบร้อยแล้ว', 'success');
          if (res && res.user) {
            Store.user = res.user;
            $$('.sb-av').forEach(function(el){ el.innerHTML = avatarHtml(res.user); });
            $$('.nav-prof-av').forEach(function(el){ el.innerHTML = avatarHtml(res.user); });
            $$('.sb-uname').forEach(function(el){ el.textContent = (res.user.prefix || '') + res.user.full_name; });
            $$('.nav-prof-nm').forEach(function(el){ el.textContent = res.user.full_name; });
          }
          actEl.disabled = false;
        }).catch(function(err) {
          Spinner.hide();
          actEl.disabled = false;
          toast(err.message, 'error');
        });
      } else if (act === 'test-email') {
        actEl.disabled = true;
        var page = el('page');
        var testTo = ($('[data-f="sender_email"]', page) || {}).value || Store.user.email || '';

        if (!testTo) {
          actEl.disabled = false;
          toast('กรุณาระบุอีเมลผู้รับการทดสอบในช่อง Sender Email หรือ โปรไฟล์ของคุณ', 'warning');
          return;
        }

        Spinner.show('กำลังส่งอีเมลแจ้งเตือนทดสอบ…');
        call('setting.test_email', { test_email: testTo }).then(function(res) {
          Spinner.hide();
          actEl.disabled = false;
          toast(res.message || 'ส่งอีเมลทดสอบเรียบร้อยแล้ว', 'success');
        }).catch(function(err) {
          Spinner.hide();
          actEl.disabled = false;
          toast(err.message, 'error');
        });
      } else if (act === 'open-pwa-guide') {
        openPwaGuideModal();
      } else if (act === 'backup-db') {
        Spinner.show('กำลังเตรียมไฟล์สำรองฐานข้อมูล…');
        call('setting.backup_db', {}).then(function(d) {
          Spinner.hide();
          var bytes = atob(d.data_b64);
          var array = new Uint8Array(bytes.length);
          for (var i = 0; i < bytes.length; i++) array[i] = bytes.charCodeAt(i);
          var blob = new Blob([array], { type: 'application/x-sqlite3' });
          var url = URL.createObjectURL(blob);
          var a = document.createElement('a');
          a.href = url; a.download = d.filename; a.click();
          URL.revokeObjectURL(url);
          toast('ดาวน์โหลดไฟล์สำรองฐานข้อมูลเรียบร้อยแล้ว (' + d.filename + ')', 'success');
        }).catch(function(err){ Spinner.hide(); toast(err.message, 'error'); });
      } else if (act === 'change-my-password') {
        var page = el('page');
        var cur = ($('[data-f="cur_password"]', page) || {}).value || '';
        var nw = ($('[data-f="new_password_p"]', page) || {}).value || '';
        if (!cur || !nw) { toast('กรุณากรอกรหัสผ่านปัจจุบันและรหัสผ่านใหม่', 'warning'); return; }
        if (nw.length < 6) { toast('รหัสผ่านใหม่ต้องมีอย่างน้อย 6 ตัวอักษร', 'warning'); return; }
        actEl.disabled = true;
        call('profile.change_password', { current: cur, newpass: nw }).then(function() {
          actEl.disabled = false;
          ($('[data-f="cur_password"]', page) || {}).value = '';
          ($('[data-f="new_password_p"]', page) || {}).value = '';
          toast('เปลี่ยนรหัสผ่านเรียบร้อยแล้ว', 'success');
        }).catch(function(err) {
          actEl.disabled = false;
          toast(err.message, 'error');
        });
      } else if (act === 'open-user-modal') {
        openUserModal(null);
      } else if (act === 'edit-user') {
        var uid = actEl.getAttribute('data-id');
        var u = (window._userListCache || []).find(function(x) { return x.id === uid; });
        openUserModal(u);
      }
    }

    if (hashEl) {
      e.preventDefault();
      var sb = el('sidebar'); var sbd = el('sidebar-backdrop');
      if (sb) sb.classList.remove('open');
      if (sbd) sbd.classList.remove('open');
      var h = hashEl.getAttribute('data-hash');
      if (h) go(h);
    }
  });

  function openUserModal(u) {
    var isEdit = !!u;
    var html = '<div class="modal-overlay" id="user-modal">'
      + '<div class="modal-card">'
      + '<div class="modal-head">'
      + '<h3 class="modal-ttl"><i class="bi bi-person-badge-fill" style="color:var(--accent)"></i> ' + (isEdit ? 'แก้ไขข้อมูลผู้ใช้งาน' : 'เพิ่มผู้ใช้งานใหม่') + '</h3>'
      + '<button class="btn btn-ghost" style="padding:4px 8px" onclick="document.getElementById(\'user-modal\').remove()"><i class="bi bi-x-lg"></i></button>'
      + '</div>'
      + '<form id="user-form">'
      + '<input type="hidden" name="id" value="' + esc(u ? u.id : '') + '">'
      + field('username', 'ชื่อผู้ใช้งาน (Username)', { value: u ? u.username : '', ph: 'เช่น kru_somchai', req: true })
      + field('password', 'รหัสผ่าน ' + (isEdit ? '(เว้นว่างไว้หากไม่ต้องการเปลี่ยน)' : ''), { type: 'password', ph: 'อย่างน้อย 6 ตัวอักษร', req: !isEdit })
      + '<div class="form-grid fg-2">'
      + field('prefix', 'คำนำหน้า', { value: u ? u.prefix : 'นาย' })
      + field('full_name', 'ชื่อ-สกุล', { value: u ? u.full_name : '', req: true })
      + '</div>'
      + '<div class="form-grid fg-2">'
      + field('position', 'ตำแหน่ง', { value: u ? u.position : 'ครู' })
      + field('role', 'บทบาทในระบบ', { type: 'select', value: u ? u.role : 'teacher', options: [{v:'teacher',t:'ครู/บุคลากร'},{v:'clerk',t:'เจ้าหน้าที่ธุรการ/ผู้ตรวจสอบ'},{v:'director',t:'ผู้อำนวยการโรงเรียน'},{v:'admin',t:'ผู้ดูแลระบบ (Admin)'}] })
      + '</div>'
      + field('department', 'กลุ่มสาระ/ฝ่าย', { value: u ? u.department : 'กลุ่มสาระการเรียนรู้ภาษาไทย' })
      + field('email', 'อีเมล (สำหรับรับผลการอนุมัติใบลา)', { value: u ? u.email : '', ph: 'เช่น teacher@pakpoon.ac.th', req: true })
      + field('line_user_id', 'LINE User ID (สำหรับแจ้งเตือนส่วนตัวผ่าน LINE)', { value: u ? (u.line_user_id || '') : '', ph: 'เช่น U1234567890abcdef...' })
      + '<div style="display:flex;justify-content:flex-end;gap:10px;margin-top:20px">'
      + '<button type="button" class="btn btn-ghost" onclick="document.getElementById(\'user-modal\').remove()">ยกเลิก</button>'
      + '<button type="submit" class="btn btn-primary"><i class="bi bi-floppy"></i> บันทึกข้อมูลผู้ใช้</button>'
      + '</div>'
      + '</form>'
      + '</div></div>';

    var div = document.createElement('div');
    div.innerHTML = html;
    document.body.appendChild(div.firstElementChild);

    document.getElementById('user-form').addEventListener('submit', function(e) {
      e.preventDefault();
      var form = e.target;
      var payload = {
        id: form.id.value,
        username: form.username.value,
        password: form.password.value,
        prefix: form.prefix.value,
        full_name: form.full_name.value,
        position: form.position.value,
        role: form.role.value,
        department: form.department.value,
        email: form.email.value,
        line_user_id: form.line_user_id ? form.line_user_id.value : ''
      };
      Spinner.show('กำลังบันทึกข้อมูลผู้ใช้…');
      call('user.save', payload).then(function() {
        Spinner.hide();
        document.getElementById('user-modal').remove();
        toast('บันทึกข้อมูลผู้ใช้งานเรียบร้อยแล้ว', 'success');
        dispatch();
      }).catch(function(err) {
        Spinner.hide();
        toast(err.message, 'error');
      });
    });
  }

  function openPwaGuideModal() {
    var html = '<div class="modal-overlay" id="pwa-guide-modal">'
      + '<div class="modal-card" style="max-width:480px">'
      + '<div class="modal-head">'
      + '<h3 class="modal-ttl"><i class="bi bi-phone-fill" style="color:var(--accent)"></i> วิธีติดตั้งระบบเป็นแอปบนมือถือ</h3>'
      + '<button class="btn btn-ghost" style="padding:4px 8px" onclick="document.getElementById(\'pwa-guide-modal\').remove()"><i class="bi bi-x-lg"></i></button>'
      + '</div>'
      + '<div style="padding:4px 0">'
      + '<div style="background:rgba(10,132,255,.07);border:1px solid rgba(10,132,255,.2);border-radius:14px;padding:14px;margin-bottom:14px">'
      + '<strong style="color:var(--accent);display:flex;align-items:center;gap:6px;font-size:14px"><i class="bi bi-android2"></i> สำหรับระบบ Android (Google Chrome):</strong>'
      + '<ol style="margin:8px 0 0;padding-left:20px;font-size:13px;line-height:1.65">'
      + '<li>กดจุด 3 จุด (<b>⋮</b>) ที่มุมขวาบนของเบราว์เซอร์ Chrome</li>'
      + '<li>เลือกเมนู <b>"ติดตั้งแอป"</b> หรือ <b>"เพิ่มลงในหน้าจอหลัก"</b></li>'
      + '<li>กดตอบรับ <b>"ติดตั้ง"</b> ไอคอนแอปจะไปปรากฏที่หน้าจอมือถือทันที</li>'
      + '</ol>'
      + '</div>'
      + '<div style="background:rgba(255,159,10,.07);border:1px solid rgba(255,159,10,.25);border-radius:14px;padding:14px;margin-bottom:14px">'
      + '<strong style="color:var(--warn);display:flex;align-items:center;gap:6px;font-size:14px"><i class="bi bi-apple"></i> สำหรับ iPhone / iPad (iOS Safari):</strong>'
      + '<ol style="margin:8px 0 0;padding-left:20px;font-size:13px;line-height:1.65">'
      + '<li>เปิดเว็บบนเบราว์เซอร์ <b>Safari</b></li>'
      + '<li>กดปุ่ม <b>แชร์ (📤)</b> ที่แถบเมนูด้านล่างสุด</li>'
      + '<li>เลื่อนลงเลือก <b>"เพิ่มไปยังหน้าจอโฮม"</b> (Add to Home Screen ➕)</li>'
      + '<li>กด <b>"เพิ่ม"</b> (Add) ที่มุมขวาบน</li>'
      + '</ol>'
      + '</div>'
      + (window._pwaPrompt ? '<div style="text-align:center;margin-top:16px"><button class="btn btn-primary" onclick="if(window._pwaPrompt){window._pwaPrompt.prompt();}"><i class="bi bi-download"></i> กดติดตั้งเป็นแอปทันที (Install Now)</button></div>' : '')
      + '</div>'
      + '</div></div>';

    var div = document.createElement('div');
    div.innerHTML = html;
    document.body.appendChild(div.firstElementChild);
  }

  window.addEventListener('hashchange', function() { if (Store.user) dispatch(); });

  function initApp() {
    var savedToken = '';
    try { savedToken = localStorage.getItem('lrs_token') || ''; } catch(e){}

    // Check Auto-login Token from Email URL parameter
    var searchStr = location.search || '';
    if (!searchStr && location.hash && location.hash.indexOf('autologin=') !== -1) {
      var idx = location.hash.indexOf('autologin=');
      searchStr = '?' + location.hash.substring(idx);
    }
    
    var urlParams = new URLSearchParams(searchStr);
    var autoToken = urlParams.get('autologin');

    if (autoToken) {
      call('auth.autologin', { autologin: autoToken }).then(function(d) {
        Store.token = d.token;
        Store.user = d.user;
        Store.caps = d.caps;
        try {
          localStorage.setItem('lrs_token', d.token);
          localStorage.setItem('lrs_remember_username', d.user.username);
        } catch(e){}
        
        fetch('api.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json;charset=utf-8' },
          body: JSON.stringify({ action: 'app.bootstrap', token: d.token })
        }).then(function(r) { return r.json(); }).then(function(res) {
          if (res && res.ok) Store.boot = res.data;
          
          var cleanHash = location.hash ? location.hash.split('?')[0] : '#/dashboard';
          if (cleanHash === '#/' || !cleanHash) cleanHash = '#/dashboard';
          history.replaceState(null, '', location.pathname + cleanHash);
          
          enterShell();
          dispatch();
          toast('เข้าสู่ระบบอัตโนมัติจากอีเมลสำเร็จ (' + d.user.full_name + ')', 'success');
        });
      }).catch(function(err) {
        toast('รหัสเข้าสู่ระบบจากอีเมลหมดอายุ: ' + err.message, 'warning');
        normalBoot(savedToken);
      });
      return;
    }

    normalBoot(savedToken);
  }

  function normalBoot(savedToken) {
    var bootTimeout = setTimeout(function() {
      console.warn('[LRS] app.bootstrap timeout - fallback to landing page');
      hideBoot();
      try { localStorage.removeItem('lrs_token'); } catch(e){}
      renderLanding();
    }, 3000);

    fetch('api.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json;charset=utf-8' },
      body: JSON.stringify({ action: 'app.bootstrap', token: savedToken })
    })
    .then(function(r) { return r.json(); })
    .then(function(res) {
      clearTimeout(bootTimeout);
      if (res && res.ok) {
        var d = res.data;
        Store.boot = d;
        if (d.me && savedToken) {
          Store.token = savedToken;
          Store.user = d.me;
          Store.caps = d.caps || [];
          enterShell();
          dispatch();
        } else {
          try { localStorage.removeItem('lrs_token'); } catch(e){}
          renderLanding();
        }
      } else {
        try { localStorage.removeItem('lrs_token'); } catch(e){}
        renderLanding();
      }
    })
    .catch(function() {
      clearTimeout(bootTimeout);
      try { localStorage.removeItem('lrs_token'); } catch(e){}
      renderLanding();
    });
  }

  // --- Dark Mode Init ---
  var savedDark = '';
  try { savedDark = localStorage.getItem('lrs_dark') || ''; } catch(e){}
  if (!savedDark && window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
    savedDark = '1';
  }
  if (savedDark === '1') {
    document.documentElement.setAttribute('data-dark', '1');
  }

  function openVerificationModal(res) {
    var isVer = res && res.verified;
    var html = '<div class="modal-overlay" id="verify-doc-modal">'
      + '<div class="modal-card" style="max-width:440px;text-align:center">'
      + '<div style="font-size:48px;margin-bottom:12px">' + (isVer ? '✅' : '❌') + '</div>'
      + '<h3 class="modal-ttl" style="justify-content:center">' + (isVer ? 'ผลการตรวจสอบความถูกต้องเอกสาร' : 'ไม่พบเอกสารในระบบ') + '</h3>'
      + '<p style="color:var(--muted);font-size:13px;margin:8px 0 16px">' + (isVer ? 'เอกสารใบลาราชการฉบับนี้เป็นเอกสารจริงที่ได้รับการอนุมัติอย่างถูกต้องจากระบบ LRS' : 'ไม่พบเอกสารเลขที่ดังกล่าวในระบบ LRS โรงเรียนปากพูน') + '</p>'
      + (isVer ? ('<div style="background:rgba(52,199,89,.08);border:1px solid rgba(52,199,89,.25);border-radius:14px;padding:14px;text-align:left;font-size:13px;line-height:1.7">'
          + '<div><b>เลขที่เอกสาร:</b> ' + esc(res.doc_no) + '</div>'
          + '<div><b>ผู้ยื่นใบลา:</b> ' + esc(res.owner_name) + ' (' + esc(res.owner_position || '-') + ')</div>'
          + '<div><b>เรื่อง:</b> ' + esc(res.subject) + '</div>'
          + '<div><b>วันที่ลา:</b> ' + esc(TH.date(res.start_date)) + ' ถึง ' + esc(TH.date(res.end_date)) + ' (' + res.days + ' วัน)</div>'
          + '<div><b>สังกัด:</b> ' + esc(res.org_name || 'โรงเรียนปากพูน') + '</div>'
          + '<div><b>สถานะ:</b> <span class="badge b-' + esc(res.status) + '">' + esc(res.status) + '</span></div>'
          + '</div>') : '')
      + '<div style="margin-top:20px"><button class="btn btn-primary" onclick="document.getElementById(\'verify-doc-modal\').remove()">ปิดหน้าต่างนี้</button></div>'
      + '</div></div>';

    var div = document.createElement('div');
    div.innerHTML = html;
    document.body.appendChild(div.firstElementChild);
  }

  // Check URL query for verify_doc
  if (location.search && location.search.indexOf('verify_doc=') !== -1) {
    try {
      var params = new URLSearchParams(location.search);
      var docNo = params.get('verify_doc');
      if (docNo) {
        call('request.verify', { doc_no: docNo }).then(function(res) {
          openVerificationModal(res);
        }).catch(function(e) { toast(e.message, 'error'); });
      }
    } catch(e){}
  }

  initApp();

  // --- Service Worker Registration (PWA) ---
  if ('serviceWorker' in navigator) {
    window.addEventListener('load', function() {
      navigator.serviceWorker.register('sw.js').then(function(reg) {
        reg.update();
      }).catch(function(){});
    });
  }

  // --- Browser Push Notification Prompt ---
  window.addEventListener('lrs:logged-in', function() {
    try {
      var asked = localStorage.getItem('lrs_notif_asked');
      if (!asked && 'Notification' in window && Notification.permission === 'default') {
        setTimeout(function() {
          var banner = document.createElement('div');
          banner.id = 'notif-prompt-banner';
          banner.style.cssText = 'position:fixed;bottom:80px;left:12px;right:12px;background:var(--grad);color:#fff;border-radius:16px;padding:14px 16px;display:flex;align-items:center;gap:12px;z-index:300;box-shadow:0 8px 24px rgba(10,132,255,.35);animation:slideUp .3s ease';
          banner.innerHTML = '<i class="bi bi-bell-fill" style="font-size:22px;flex:0 0 auto"></i>'
            + '<div style="flex:1;font-size:13px"><strong>เปิดการแจ้งเตือน</strong><br>รับแจ้งเตือนทันทีเมื่อใบลาได้รับการอัพเดต</div>'
            + '<button data-action="req-notify-permission" style="background:rgba(255,255,255,.2);border:0;color:#fff;border-radius:8px;padding:6px 10px;cursor:pointer;font-weight:600">เปิด</button>'
            + '<button data-action="dismiss-pwa" style="background:0;border:0;color:#fff;font-size:18px;cursor:pointer;padding:2px 6px">✕</button>';
          document.body.appendChild(banner);
          setTimeout(function(){ if(banner.parentNode) banner.remove(); }, 12000);
        }, 3000);
      }
    } catch(e){}
  });

  // --- PWA Install Prompt ---
  var _pwaPrompt = null;
  window.addEventListener('beforeinstallprompt', function(e) {
    e.preventDefault();
    _pwaPrompt = e;
    try {
      if (localStorage.getItem('lrs_pwa_dismissed')) return;
    } catch(x){}
    setTimeout(function() {
      var b = document.createElement('div');
      b.id = 'pwa-install-banner';
      b.style.cssText = 'position:fixed;bottom:80px;left:12px;right:12px;z-index:299';
      b.innerHTML = '<div style="background:linear-gradient(135deg,#34c759,#248a3d);color:#fff;border-radius:16px;padding:14px 16px;display:flex;align-items:center;gap:12px;box-shadow:0 8px 24px rgba(52,199,89,.35)">'
        + '<i class="bi bi-phone-fill" style="font-size:22px;flex:0 0 auto"></i>'
        + '<div style="flex:1;font-size:13px"><strong>ติดตั้งเป็นแอป</strong><br>เพิ่มลงหน้าจอเพื่อใช้งานได้เร็วขึ้น</div>'
        + '<button onclick="if(window._pwaPrompt){_pwaPrompt.prompt();}" style="background:rgba(255,255,255,.2);border:0;color:#fff;border-radius:8px;padding:6px 10px;cursor:pointer;font-weight:600">ติดตั้ง</button>'
        + '<button data-action="dismiss-pwa" style="background:0;border:0;color:#fff;font-size:18px;cursor:pointer;padding:2px 6px">✕</button></div>';
      document.body.appendChild(b);
    }, 5000);
  });

})();
</script>

</body>
</html>
