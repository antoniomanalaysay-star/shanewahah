<?php
header('Content-Type: text/css; charset=utf-8');
?>
:root{
  --ink900:#04100A; --ink800:#071A10; --ink700:#0A2415;
  --line:#1D5236; --leaf:#4CAF50; --lime:#A8E063; --forest:#1F7A43;
  --gold:#F5C518; --goldHi:#FFE066; --text:#E7F3E9; --muted:#8FAE99;
  --label:#9CC4A8; --hint:#5D7A68; --danger:#FF6B6B; --red:#E53935; --blue:#42A5F5;
}
*{box-sizing:border-box}
html,body{margin:0;min-height:100%;background:var(--ink900);color:var(--text)}
body{font-family:Roboto,-apple-system,BlinkMacSystemFont,"Segoe UI",Arial,sans-serif;font-size:14px;line-height:1.45;-webkit-font-smoothing:antialiased}
a{color:inherit;text-decoration:none}
button,input,select,textarea{font:inherit}
button{cursor:pointer}
h1,h2,h3,p{margin:0}
.icon{flex:none;vertical-align:middle}
.muted{color:var(--muted)}
.small{font-size:12px}
.gap12{height:12px}.gap16{height:16px}.gap18{height:18px}.gap20{height:20px}
.spacer{flex:1}
.splash{display:flex;flex-direction:column;align-items:center;justify-content:center;min-height:100vh;gap:24px}
.splash-spinner{width:34px;height:34px;border-radius:50%;border:4px solid rgba(245,197,24,.25);border-top-color:var(--gold);animation:spin .8s linear infinite}
@keyframes spin{to{transform:rotate(360deg)}}
.clam-logo{display:block;flex:none}

/* ---------------- auth ---------------- */
.auth-layout{display:flex;min-height:100vh}
.auth-hero{flex:0 0 460px;background:var(--ink800);display:flex;align-items:center}
.auth-hero-inner{width:100%;padding:40px 48px}
.brand-row{display:flex;align-items:center;gap:14px}
.brand-name{font-size:26px;font-weight:800;line-height:1;letter-spacing:-.3px}
.brand-name span{display:inline-block;margin-top:6px;color:var(--muted);font-size:11px;font-weight:600;letter-spacing:1.4px}
.brand-name.small{font-size:17px}.brand-name.small span{font-size:8px;letter-spacing:1px}
.auth-hero h1{font-size:38px;font-weight:800;line-height:1.12;margin-top:36px;white-space:pre-line}
.hero-desc{color:var(--muted);font-size:15px;line-height:1.6;margin-top:18px}
.hero-point{display:flex;gap:14px;margin-top:16px;margin-bottom:16px}
.hero-point span{color:var(--gold);font-size:13px;font-weight:800;padding-top:2px}
.hero-point p{font-size:14px;line-height:1.4}
.hero-footer{margin-top:24px;color:var(--gold);font-size:13px}
.auth-main{flex:1;display:flex;align-items:center;justify-content:center;padding:48px}
.auth-form{width:100%;max-width:460px}
.auth-form h1{font-size:28px;font-weight:800}
.auth-sub{color:var(--muted);line-height:1.5;margin-top:8px}
.auth-form .field{margin-top:16px}
.auth-form .forgot{display:block;margin:10px 0 0 auto}

/* ---------------- fields / buttons ---------------- */
.field{position:relative;display:flex;align-items:center;width:100%;background:var(--ink700);border:1px solid var(--line);border-radius:12px;min-height:56px;transition:border-color .15s,box-shadow .15s}
.field:focus-within{border:1.6px solid var(--lime);box-shadow:0 0 0 1px rgba(168,224,99,.15)}
.field.error{border-color:var(--danger)}
.field input,.field select,.field textarea{flex:1;min-width:0;width:100%;height:54px;padding:16px 14px 6px;background:transparent;border:0;outline:0;color:var(--text)}
.field input::placeholder,.field textarea::placeholder{color:transparent}
.field>label{position:absolute;left:14px;top:17px;color:var(--label);font-size:14px;pointer-events:none;transition:.15s ease-out}
.field .field-prefix,.field .field-suffix{display:flex;align-items:center;padding:0 10px 0 14px;color:var(--muted)}
.field .field-suffix{padding:0 12px}
.field:focus-within>label,.field input:not(:placeholder-shown)+label,.field select:not(:placeholder-shown)+label{top:7px;font-size:11px;color:var(--lime)}
.field .field-prefix~input{padding-left:6px}
.field .field-prefix~label{left:44px}
.field .field-prefix~input:not(:placeholder-shown)+label,.field:focus-within .field-prefix~label{color:var(--lime)}
.field select+label{top:7px;font-size:11px;color:var(--lime)}
.field input[type=date]+label,.field input[type=time]+label{top:7px;font-size:11px;color:var(--lime)}
.field-suffix{background:none;border:0}
.btn{display:inline-flex;align-items:center;justify-content:center;gap:8px;min-height:36px;padding:0 14px;border-radius:10px;border:1px solid transparent;font-size:12px;font-weight:700;white-space:nowrap;transition:filter .15s,background .15s}
.btn:hover{filter:brightness(1.08)}
.btn:disabled{opacity:.5;cursor:default}
.btn-primary{background:var(--gold);color:var(--ink900)}
.btn-primary.btn-block{width:100%;height:52px;font-size:16px;border-radius:12px}
.btn-danger{background:var(--danger);color:#fff}
.btn-outline{background:transparent;color:var(--text);border-color:var(--line)}
.btn-outline-danger{background:transparent;color:var(--danger);border-color:var(--danger)}
.btn-outline-lime{background:transparent;color:var(--lime);border-color:var(--line);min-height:36px}
.btn-tonal{background:rgba(168,224,99,.14);color:var(--lime)}
.btn-sm{min-height:34px;font-size:12px;padding:0 14px}
.text-btn{display:inline-flex;align-items:center;gap:6px;background:none;border:0;padding:6px 8px;font-size:12px;font-weight:700}
.tone-gold{color:var(--gold)}.tone-lime{color:var(--lime)}.tone-danger{color:var(--danger)}.tone-muted{color:var(--muted)}
.icon-btn{display:inline-flex;align-items:center;justify-content:center;width:40px;height:40px;border-radius:50%;border:0;background:transparent;color:var(--text);position:relative;flex:none}
.icon-btn:hover{background:rgba(255,255,255,.06)}
.error-banner{display:flex;gap:10px;align-items:flex-start;padding:12px;border:1px solid rgba(255,107,107,.5);background:rgba(107,107,107,.12);border-radius:12px;color:var(--text);white-space:pre-line}
.error-banner svg{color:var(--danger)}

/* ---------------- cards / shared ---------------- */
.info-card{width:100%;padding:16px;background:var(--ink700);border:1px solid var(--line);border-radius:16px}
.info-card.tight{padding:12px 16px}
.section-card{width:100%;padding:18px 18px 18px;background:var(--ink700);border:1px solid var(--line);border-radius:16px}
.section-head{display:flex;align-items:center;gap:12px}
.section-head h2{font-size:18px;font-weight:700;flex:1}
.section-divider{height:1px;background:var(--line);margin:14px 0 16px}
.section-body{display:flex;flex-direction:column;gap:14px}
.page-title h1{font-size:26px;font-weight:800}
.page-title p{margin-top:6px;color:var(--muted)}
.page-actions{margin-top:14px;display:flex;gap:8px;flex-wrap:wrap}
.workspace-body{padding:24px;overflow:auto;flex:1}
.workspace-body>.info-card,.workspace-body>.section-card,.workspace-body>.page-title{margin-bottom:18px}
.page-title+.section-card{margin-top:20px}
.lime-pill{padding:3px 8px;border-radius:20px;background:rgba(168,224,99,.12);color:var(--lime);font-size:11px;white-space:nowrap}
.gold-pill{padding:3px 8px;border-radius:20px;background:rgba(245,197,24,.15);color:var(--gold);font-size:11px;white-space:nowrap}
.code-box,.code-chip{display:inline-flex;align-items:center;gap:6px;padding:6px 10px;background:var(--ink900);border:1px solid var(--line);border-radius:8px;color:var(--gold);font-family:ui-monospace,SFMono-Regular,Menlo,monospace;font-weight:700;font-size:14px}
.code-chip{border:0}

/* ---------------- shell ---------------- */
.shell{display:flex;min-height:100vh}
.sidebar{width:236px;flex:none;background:var(--ink800);border-right:1px solid var(--line);display:flex;flex-direction:column}
.side-brand{height:66px;display:flex;align-items:center;gap:10px;padding:0 18px;border-bottom:1px solid var(--line)}
.nav-label{padding:22px 18px 8px;color:var(--hint);font-size:10px;font-weight:800;letter-spacing:1.4px}
.side-nav{display:flex;flex-direction:column}
.nav-item{display:flex;align-items:center;gap:12px;height:50px;padding:0 18px;color:var(--muted);font-size:13px}
.nav-item.active{color:var(--text);background:rgba(245,197,24,.08);font-weight:700;box-shadow:inset 3px 0 0 var(--gold)}
.side-foot{margin:auto 16px 16px;padding:12px;background:var(--ink700);border:1px solid var(--line);border-radius:12px;display:flex;gap:8px;align-items:flex-start;color:var(--muted);font-size:10px;line-height:1.4}
.side-foot .dot{width:8px;height:8px;border-radius:50%;background:var(--leaf);margin-top:4px;flex:none}
.workspace{flex:1;min-width:0;display:flex;flex-direction:column}
.topbar{height:66px;flex:none;display:flex;align-items:center;gap:12px;padding:0 22px;background:var(--ink800);border-bottom:1px solid var(--line)}
.topbar strong{font-size:16px}
.role-pill{padding:4px 10px;border-radius:20px;border:1px solid rgba(168,224,99,.4);background:rgba(168,224,99,.12);color:var(--lime);font-size:10px;font-weight:800;letter-spacing:.8px}
.topbar-user{font-size:13px;color:var(--text)}
.avatar,.circle-avatar{display:inline-flex;align-items:center;justify-content:center;border-radius:50%;background:var(--forest);color:var(--text);font-weight:700}
.avatar{width:34px;height:34px;font-size:13px}
.account-wrap{position:relative}
.account-menu{display:none;position:absolute;right:0;top:44px;z-index:30;min-width:180px;background:var(--ink800);border:1px solid var(--line);border-radius:12px;padding:6px;box-shadow:0 10px 30px rgba(0,0,0,.5)}
.account-menu.open{display:block}
.account-menu button{display:block;width:100%;text-align:left;background:none;border:0;color:var(--text);padding:10px 12px;border-radius:8px;font-size:13px}
.account-menu button:hover{background:rgba(255,255,255,.06)}
.badge{position:absolute;right:2px;top:2px;min-width:16px;height:16px;padding:0 3px;display:flex;align-items:center;justify-content:center;background:var(--danger);color:#fff;border-radius:10px;font-size:10px;font-weight:700}
.drawer-toggle{display:none}

/* ---------------- app bar / tabs ---------------- */
.appbar{height:60px;display:flex;align-items:center;gap:12px;padding:0 18px;background:var(--ink800);border-bottom:1px solid var(--line)}
.appbar-titles{display:flex;flex-direction:column;line-height:1.2}
.appbar-titles strong{font-size:16px}
.appbar-titles small{color:var(--muted);font-size:11px;font-weight:400}
.tabs{display:flex;gap:8px;padding:0 18px;background:var(--ink800);border-bottom:1px solid var(--line);overflow-x:auto}
.tab{position:relative;padding:12px 10px;color:var(--muted);font-size:13px;white-space:nowrap}
.tab.active{color:var(--gold);font-weight:700}
.tab.active:after{content:"";position:absolute;left:6px;right:6px;bottom:0;height:2px;background:var(--gold)}
.class-body{padding:24px;min-height:calc(100vh - 110px)}
.class-body>.info-card{margin-bottom:18px}

/* ---------------- tables ---------------- */
.stat-row{display:flex;gap:12px;flex-wrap:wrap;margin-bottom:18px}
.stat-card{width:180px;padding:14px 18px;background:var(--ink700);border:1px solid var(--line);border-radius:14px}
.stat-card span{display:block;color:var(--muted);font-size:12px}
.stat-card strong{display:block;color:var(--gold);font-size:28px;font-weight:800}
.tab-buttons{display:flex;gap:8px;flex-wrap:wrap;margin-bottom:14px}
.tab-btn{padding:8px 14px;min-height:36px;border-radius:8px;border:1px solid var(--line);background:transparent;color:var(--muted);font-size:12px;font-weight:700}
.tab-btn.active{background:var(--gold);border-color:var(--gold);color:var(--ink900)}
.table-wrap{overflow-x:auto}
table.data{width:100%;border-collapse:collapse;font-size:13px}
table.data th,table.data td{border:1px solid var(--line);padding:10px 12px;text-align:left;vertical-align:middle}
table.data th{background:var(--ink800);color:var(--lime);font-weight:700;white-space:nowrap}
table.data td .sub{display:block;color:var(--muted);font-size:11px}
table.data td .name{font-weight:700}
.empty-state{padding:22px;color:var(--muted);text-align:center}

/* ---------------- class cards ---------------- */
.class-grid{display:flex;flex-wrap:wrap;gap:14px}
.class-card{width:440px;max-width:100%;min-width:280px;background:var(--ink800);border:1px solid var(--line);border-radius:14px;overflow:hidden;display:flex;flex-direction:column}
.class-card-head{display:flex;align-items:flex-start;gap:8px;padding:12px 14px;border-bottom:1px solid var(--line)}
.class-card-head strong{flex:1;font-size:17px;font-weight:800;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}
.class-card-body{padding:14px;display:flex;flex-direction:column;gap:10px}
.clamp2{display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}
.code-row{display:flex;align-items:center;gap:8px}
.card-actions{display:flex;gap:8px;flex-wrap:wrap;margin-top:2px}

/* ---------------- calendar ---------------- */
.cal-controls{display:flex;align-items:center;justify-content:space-between}
.cal-controls strong{font-size:17px}
.chip-row{display:flex;gap:8px;flex-wrap:wrap;margin-top:8px}
.status-chip{display:inline-flex;align-items:center;gap:6px;padding:4px 10px;border-radius:18px;background:var(--ink800);font-size:12px;border:1px solid var(--line)}
.status-chip:before{content:"";width:10px;height:10px;border-radius:50%;background:var(--muted)}
.status-present:before{background:var(--leaf)}.status-late:before{background:var(--gold)}
.status-absent:before{background:var(--red)}.status-excused:before{background:var(--blue)}
.filter-form{margin-top:12px}
.filter-form select{width:100%;background:var(--ink800);border:0;border-bottom:1px solid var(--line);color:var(--text);padding:8px 4px;outline:0}
.calendar-grid{display:grid;grid-template-columns:repeat(7,1fr);gap:4px}
.cal-head{text-align:center;color:var(--muted);font-size:12px;padding-bottom:6px}
.cal-day{aspect-ratio:1;display:flex;align-items:center;justify-content:center;border:1px solid var(--line);border-radius:8px;background:var(--ink800);font-size:12px}
.cal-present{background:rgba(76,175,80,.35);border:2px solid var(--leaf)}
.cal-late{background:rgba(245,197,24,.35);border:2px solid var(--gold)}
.cal-absent{background:rgba(229,57,53,.35);border:2px solid var(--red)}
.cal-excused{background:rgba(66,165,245,.35);border:2px solid var(--blue)}

/* ---------------- lists / routing ---------------- */
.list-row{display:flex;align-items:center;gap:12px;padding:8px 0;border-bottom:1px solid rgba(29,82,54,.5)}
.list-row:last-child{border-bottom:0}
.list-row .circle-avatar{width:40px;height:40px;flex:none;font-size:14px}
.list-row .row-text{flex:1;min-width:0}
.list-row .row-text strong{display:block;font-size:13px}
.list-row .row-text small{display:block;color:var(--muted);font-size:11px}
.list-row.selected .circle-avatar{background:var(--gold);color:var(--ink900)}
.students-layout{display:grid;grid-template-columns:320px 1fr;gap:20px}
.students-layout>.info-card{max-width:320px}
.roster-status{font-size:11px;color:var(--muted);white-space:nowrap}
.roster-status.present{color:var(--leaf)}.roster-status.late{color:var(--gold)}
.face-pick-row{cursor:pointer}
.face-pick-preview{width:52px;height:52px;object-fit:cover;border-radius:10px;border:1px solid var(--line);flex:none}
.selected-label{color:var(--lime);font-weight:400}
.face-slots{display:flex;gap:8px}
.face-slot{flex:1;height:92px;border:1px solid var(--line);border-radius:12px;background:var(--ink700);display:flex;flex-direction:column;align-items:center;justify-content:center;gap:6px;color:var(--muted);font-size:11px;overflow:hidden;cursor:pointer}
.face-slot.has-photo{border-color:var(--lime)}
.face-slot img{width:100%;height:100%;object-fit:cover}
.role-grid{display:flex;gap:12px}
.role-card{flex:1;padding:14px;border-radius:14px;border:1px solid var(--line);background:var(--ink800);cursor:pointer}
.role-card.selected{background:var(--ink700);border:1.6px solid var(--gold)}
.role-card .role-row{display:flex;align-items:center;gap:8px;font-weight:700}
.role-card small{display:block;color:var(--muted);font-size:11px;margin-top:6px}
.profile-grid{display:grid;grid-template-columns:1fr 1fr;gap:14px;align-items:start}
.center-form{max-width:420px;margin:0 auto}
.center-form.wide{max-width:480px}
.center-form form{display:flex;flex-direction:column;gap:14px}
.center-form .btn-block{margin-top:6px}
.appbar-actions{display:flex;align-items:center;gap:8px}
.roster-meta{display:flex;gap:10px;align-items:center;color:var(--muted);font-size:12px}
.status-dot{width:10px;height:10px;border-radius:50%;background:var(--danger);flex:none}
.status-dot.present{background:var(--leaf)}.status-dot.late{background:var(--gold)}.status-dot.excused{background:var(--blue)}

/* ---------------- dialogs ---------------- */
dialog{border:0;padding:0;color:var(--text);background:transparent;max-width:calc(100vw - 32px)}
dialog::backdrop{background:rgba(0,0,0,.62)}
.flutter-dialog{width:520px;max-width:520px;background:var(--ink800);border-radius:28px;padding:24px;box-shadow:0 20px 60px rgba(0,0,0,.6)}
.flutter-dialog h3{font-size:20px;font-weight:700;margin-bottom:16px}
.dialog-body{max-height:60vh;overflow:auto}
.dialog-body .field{margin-bottom:12px}
.dialog-body p{margin-bottom:8px}
.dialog-actions{display:flex;justify-content:flex-end;gap:8px;margin-top:20px}
.notify-dialog{width:520px;max-height:70vh;background:var(--ink800);border-radius:16px;display:none;flex-direction:column;overflow:hidden}
.notify-dialog[open]{display:flex}
.notify-head{display:flex;align-items:center;justify-content:space-between;padding:16px;border-bottom:1px solid var(--line)}
.notify-list{overflow:auto;padding:8px 0}
.notification-item{width:100%;display:flex;gap:12px;align-items:flex-start;text-align:left;background:none;border:0;border-bottom:1px solid var(--line);color:var(--text);padding:12px 16px}
.notification-item span{min-width:0}
.notification-item strong{display:block;font-size:13px}
.notification-item small{display:block;color:var(--muted);font-size:12px}
.snackbar{position:fixed;left:50%;bottom:24px;transform:translateX(-50%);z-index:100;background:#26362C;color:#fff;padding:14px 20px;border-radius:6px;box-shadow:0 8px 24px rgba(0,0,0,.5);font-size:14px;opacity:0;pointer-events:none;transition:opacity .2s,transform .2s}
.snackbar.show{opacity:1;transform:translateX(-50%);animation:snack .25s ease-out}
.snackbar.error{background:#5A2222}
@keyframes snack{from{opacity:0;transform:translate(-50%,12px)}to{opacity:1;transform:translate(-50%,0)}}

/* ---------------- scanning ---------------- */
.camera-box{height:260px;background:var(--ink800);border:1px solid var(--line);border-radius:16px;overflow:hidden;position:relative;display:flex;align-items:center;justify-content:center}
.camera-box video{width:100%;height:100%;object-fit:cover}
.face-overlay{position:absolute;inset:0;pointer-events:none}
.face-box{position:absolute;border:3px solid var(--leaf);border-radius:6px;pointer-events:none}
.face-box>span{position:absolute;top:0;left:0;background:var(--leaf);color:#fff;font-size:11px;padding:2px 4px;white-space:nowrap}
.face-blink{border-color:var(--gold)}.face-blink>span{background:var(--gold);color:var(--ink900)}
.face-checking{border-color:var(--muted)}.face-checking>span{background:var(--muted)}
.face-unknown{border-color:var(--danger)}.face-unknown>span{background:var(--danger)}
.avatar.small{width:28px;height:28px;font-size:11px}
.rule-row{display:flex;gap:10px;align-items:flex-start;margin-bottom:10px}
.rule-dot{width:10px;height:10px;border-radius:50%;margin-top:5px;flex:none}
.time-fields{display:flex;gap:12px;flex-wrap:wrap}
.time-fields .field{width:210px}
.time-fields .field.wide-field{width:320px}
.present-dot{background:var(--leaf)}.late-dot{background:var(--gold)}.absent-dot{background:var(--danger)}
.date-input{color-scheme:dark}

@media (max-width:900px){
  .auth-hero{display:none}
  .auth-main{padding:24px}
  .students-layout{grid-template-columns:1fr}
  .students-layout>.info-card{max-width:none}
  .profile-grid{grid-template-columns:1fr}
  .class-grid{justify-content:center}
}
@media (max-width:819px){
  .shell{display:block}
  .sidebar{position:fixed;left:0;top:0;bottom:0;z-index:40;transform:translateX(-100%);transition:transform .2s;width:260px}
  .sidebar.open{transform:translateX(0)}
  .drawer-toggle{display:inline-flex}
  .topbar{position:sticky;top:0;z-index:20;gap:8px;padding:0 12px}
  .topbar .brand-name,.topbar-user,.role-pill,.logout-link{display:none}
  .workspace-body{padding:16px}
  .flutter-dialog{width:calc(100vw - 32px);padding:20px;border-radius:20px}
  .stat-card{width:calc(50% - 6px)}
}
@media (max-width:520px){
  .stat-card{width:100%}
  .role-grid{flex-direction:column}
  .time-fields .field{width:100%}
  .face-slots{flex-direction:column}
  .face-slot{height:84px}
}
