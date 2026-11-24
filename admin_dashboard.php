<?php
// UI only — sample data below is hard-coded; there is no database or backend.
$students = [
  ['Juan Dela Cruz', 'juan', 'IT-301', 'Registered'],
  ['Maria Santos', 'maria', 'IT-301', 'Registered'],
  ['Pedro Reyes', 'pedro', 'CS-210', 'No photos'],
  ['Ana Gonzales', 'ana', 'MATH-101', 'Registered'],
];
$instructors = [
  ['Mr. Shane', 'shane', 'Faculty', 'Not required'],
  ['Ms. Ana', 'ana.m', 'Faculty', 'Not required'],
  ['Mr. Jose', 'jose', 'Faculty', 'Not required'],
];
$classes = [
  ['IT 301 · Database Systems', 'A1B2C3', 'Mr. Shane', 32],
  ['CS 210 · Web Development', 'W3D4E5', 'Ms. Ana', 28],
  ['MATH 101 · Calculus', 'M7N8P9', 'Mr. Jose', 40],
];
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Administration · AtClam</title>
  <style>
    :root{--ink900:#04100a;--ink800:#071a10;--ink700:#0a2415;--line:#1d5236;--gold:#f5c518;--lime:#a8e063;--text:#e7f3e9;--muted:#8fae99;--label:#9cc4a8;--leaf:#4caf50;--danger:#ff6b6b}
    *{box-sizing:border-box}
    body{margin:0;background:var(--ink900);color:var(--text);font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Arial,sans-serif}
    a{color:inherit;text-decoration:none}
    button,input,select,textarea{font:inherit}
    .app{display:flex;min-height:100vh}
    .side{width:236px;flex:0 0 236px;background:var(--ink800);border-right:1px solid var(--line);padding:18px 12px;display:flex;flex-direction:column}
    .brand{display:flex;align-items:center;gap:10px;padding:4px 8px 22px}
    .logo{width:32px;height:32px;border-radius:9px;background:linear-gradient(135deg,#ffe066,#a8e063,#1f7a43);display:grid;place-items:center;color:var(--ink900);font-weight:900;font-size:13px}
    .brand b{font-size:16px;letter-spacing:1px;display:block;line-height:1.1}
    .brand small{font-size:8px;color:var(--muted);letter-spacing:1.1px}
    .nav-label{font-size:10px;color:#5d7a68;font-weight:800;letter-spacing:1.4px;padding:0 12px 8px}
    .nav a{display:flex;align-items:center;gap:10px;padding:10px 12px;border-radius:10px;color:var(--muted);font-size:13px;margin-bottom:4px;cursor:pointer}
    .nav a.active,.nav a:hover{background:rgba(245,197,24,.1);color:var(--text)}
    .nav .ic{width:18px;text-align:center}
    .side-foot{margin-top:auto;border-top:1px solid var(--line);padding:14px 10px 4px;font-size:12px}
    .side-foot b{display:block}
    .side-foot span{color:var(--muted);font-size:11px}
    .side-foot a{display:inline-block;margin-top:10px;color:var(--gold);font-size:11px;font-weight:700}
    .main{flex:1;min-width:0}
    .top{height:66px;display:flex;align-items:center;gap:12px;padding:0 24px;background:var(--ink800);border-bottom:1px solid var(--line)}
    .top .title{font-weight:700;font-size:15px}
    .spacer{flex:1}
    .pill{font-size:10px;font-weight:800;letter-spacing:.8px;color:var(--lime);border:1px solid rgba(168,224,99,.4);background:rgba(168,224,99,.12);border-radius:16px;padding:4px 10px}
    .bell{font-size:16px;color:var(--muted)}
    .avatar{width:30px;height:30px;border-radius:50%;background:#1f7a43;display:grid;place-items:center;font-size:12px;font-weight:800}
    .content{padding:24px;max-width:1200px}
    h1{font-size:24px;margin:0 0 6px}
    .sub{color:var(--muted);font-size:13px;margin:0 0 22px;line-height:1.5}
    .card{background:var(--ink800);border:1px solid var(--line);border-radius:14px;padding:18px;margin-bottom:18px}
    .card h2{margin:0 0 14px;font-size:15px}
    .card-head{display:flex;align-items:center;justify-content:space-between;gap:12px;margin-bottom:14px;flex-wrap:wrap}
    .card-head h2{margin:0}
    .muted{color:var(--muted);font-size:12px}
    .btn{border:0;border-radius:9px;padding:9px 14px;background:var(--gold);color:var(--ink900);font-weight:800;cursor:pointer;font-size:12px}
    .btn.ghost{background:transparent;color:var(--lime);border:1px solid var(--line)}
    .btn.danger{background:var(--danger);color:#fff}
    .btn.small{padding:6px 10px;font-size:11px}
    input,select,textarea{background:var(--ink700);border:1px solid var(--line);color:var(--text);border-radius:10px;padding:10px 12px;outline:none;width:100%}
    input:focus,select:focus{border-color:var(--lime)}
    label{display:block;font-size:12px;color:var(--label);margin-bottom:6px}
    .row{display:flex;gap:12px;flex-wrap:wrap;align-items:end}
    .row>*{flex:1;min-width:170px}
    .row .fixed{flex:0 0 190px}
    table{width:100%;border-collapse:collapse;font-size:12px}
    th,td{text-align:left;padding:10px;border:1px solid var(--line)}
    th{color:var(--lime);background:var(--ink700)}
    .stats{display:flex;gap:12px;flex-wrap:wrap;margin-bottom:18px}
    .stat{background:var(--ink800);border:1px solid var(--line);border-radius:12px;padding:14px 18px;min-width:150px}
    .stat b{display:block;color:var(--gold);font-size:26px;line-height:1.2}
    .stat span{color:var(--muted);font-size:11px}
    .tabs{display:flex;gap:8px;flex-wrap:wrap;margin-bottom:16px}
    .tab{border:1px solid var(--line);background:transparent;color:var(--muted);padding:7px 15px;border-radius:18px;cursor:pointer;font-size:12px}
    .tab.active{background:var(--gold);color:var(--ink900);font-weight:800}
    .panel{display:none}.panel.active{display:block}
    .camera{height:230px;border:1px dashed var(--line);border-radius:12px;background:radial-gradient(circle,#0d3320,#04100a);display:grid;place-items:center;text-align:center;color:var(--muted);font-size:13px}
    .camera .ic{font-size:42px;display:block;color:var(--gold);margin-bottom:8px}
    .modal{position:fixed;inset:0;background:rgba(0,0,0,.7);display:none;place-items:center;padding:20px;z-index:5}
    .modal.open{display:grid}
    .modal-box{background:var(--ink800);border:1px solid var(--line);border-radius:14px;padding:22px;width:100%;max-width:520px}
    .modal-box h2{margin:0 0 16px;font-size:16px}
    .toast{position:fixed;right:20px;bottom:20px;background:var(--ink700);border:1px solid var(--lime);padding:12px 16px;border-radius:10px;font-size:12px;opacity:0;transform:translateY(10px);transition:.2s;pointer-events:none;z-index:9}
    .toast.show{opacity:1;transform:none}
    @media(max-width:820px){.side{display:none}.content{padding:16px}.top{padding:0 16px}}
  </style>
</head>
<body>
<div class="app">
  <aside class="side">
    <div class="brand"><div class="logo">AC</div><div><b>AtClam</b><small>CLASSROOM ATTENDANCE</small></div></div>
    <div class="nav-label">WORKSPACE</div>
    <nav class="nav">
      <a class="active" data-tab="records"><span class="ic">▦</span> People & classes</a>
      <a data-tab="live"><span class="ic">▣</span> Live camera</a>
      <a data-tab="profile"><span class="ic">◎</span> My profile</a>
    </nav>
    <div class="side-foot">
      <b>Demo Administrator</b><span>demo.admin@classroom.local</span><br>
      <a href="login.php">↩ Switch role</a>
    </div>
  </aside>

  <div class="main">
    <header class="top">
      <span class="title" id="pageTitle">People & classes</span>
      <div class="spacer"></div>
      <span class="pill">ADMIN</span>
      <span class="bell">🔔</span>
      <div class="avatar">D</div>
      <a class="btn ghost small" href="login.php">Switch role</a>
    </header>

    <main class="content">
      <!-- RECORDS -->
      <section class="panel active" id="records">
        <h1>Administration</h1>
        <p class="sub">Manage instructors, students and every class in the system.</p>
        <div class="stats">
          <div class="stat"><b><?= count($instructors) ?></b><span>Instructors</span></div>
          <div class="stat"><b><?= count($students) ?></b><span>Students</span></div>
          <div class="stat"><b><?= count($classes) ?></b><span>Classes</span></div>
        </div>

        <div class="card">
          <div class="card-head">
            <h2>Records</h2>
            <button class="btn" onclick="openModal()">＋ Add new</button>
          </div>
          <div class="tabs">
            <button class="tab active" data-set="students">Students</button>
            <button class="tab" data-set="instructors">Instructors</button>
            <button class="tab" data-set="classes">Classes</button>
          </div>

          <div id="students">
            <table>
              <thead><tr><th>Name</th><th>Username</th><th>Section</th><th>Face</th><th>Actions</th></tr></thead>
              <tbody>
                <?php foreach ($students as $row): ?>
                  <tr>
                    <td><?= htmlspecialchars($row[0]) ?></td><td><?= htmlspecialchars($row[1]) ?></td>
                    <td><?= htmlspecialchars($row[2]) ?></td><td><?= htmlspecialchars($row[3]) ?></td>
                    <td>
                      <button class="btn ghost small" onclick="openModal()">Edit</button>
                      <button class="btn danger small" onclick="toast('Delete confirmed (UI demo)')">Delete</button>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>

          <div id="instructors" style="display:none">
            <table>
              <thead><tr><th>Name</th><th>Username</th><th>Section</th><th>Face</th><th>Actions</th></tr></thead>
              <tbody>
                <?php foreach ($instructors as $row): ?>
                  <tr>
                    <td><?= htmlspecialchars($row[0]) ?></td><td><?= htmlspecialchars($row[1]) ?></td>
                    <td><?= htmlspecialchars($row[2]) ?></td><td><?= htmlspecialchars($row[3]) ?></td>
                    <td>
                      <button class="btn ghost small" onclick="openModal()">Edit</button>
                      <button class="btn danger small" onclick="toast('Delete confirmed (UI demo)')">Delete</button>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>

          <div id="classes" style="display:none">
            <table>
              <thead><tr><th>Class</th><th>Code</th><th>Instructor</th><th>Students</th><th>Actions</th></tr></thead>
              <tbody>
                <?php foreach ($classes as $row): ?>
                  <tr>
                    <td><?= htmlspecialchars($row[0]) ?></td><td><?= htmlspecialchars($row[1]) ?></td>
                    <td><?= htmlspecialchars($row[2]) ?></td><td><?= (int)$row[3] ?></td>
                    <td>
                      <button class="btn ghost small" onclick="openModal()">Edit</button>
                      <button class="btn danger small" onclick="toast('Delete confirmed (UI demo)')">Delete</button>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </section>

      <!-- LIVE CAMERA -->
      <section class="panel" id="live">
        <h1>Live camera</h1>
        <p class="sub">Monitoring placeholder for the classroom camera.</p>
        <div class="card">
          <h2>Camera preview</h2>
          <div class="camera"><div><span class="ic">▣</span>Camera preview placeholder<br><span class="muted">No backend connected — UI demo only</span></div></div>
        </div>
      </section>

      <!-- PROFILE -->
      <section class="panel" id="profile">
        <h1>My profile</h1>
        <p class="sub">Manage your account details and picture.</p>
        <div class="card">
          <h2>Account details</h2>
          <form class="row" onsubmit="event.preventDefault();toast('Profile saved (UI demo)')">
            <div><label>Full name</label><input value="Demo Administrator"></div>
            <div><label>Username</label><input value="demo_admin" disabled></div>
            <div><label>Email</label><input value="demo.admin@classroom.local" disabled></div>
            <div class="fixed"><button class="btn" type="submit">Save name</button></div>
          </form>
        </div>
      </section>
    </main>
  </div>
</div>

<!-- ADD / EDIT MODAL (UI only) -->
<div class="modal" id="modal">
  <div class="modal-box">
    <h2 id="modalTitle">Add record</h2>
    <form class="row" onsubmit="event.preventDefault();closeModal();toast('Saved (UI demo)')">
      <div><label>Full name / Class name</label><input placeholder="Name" required></div>
      <div><label>Username / Code</label><input placeholder="Username"></div>
      <div><label>Section / Instructor</label><input placeholder="Section"></div>
      <div><label>Role</label><select><option>Student</option><option>Instructor</option><option>Class</option></select></div>
      <div class="fixed"><button class="btn" type="submit">Save</button></div>
    </form>
    <div style="margin-top:16px;text-align:right"><button class="btn ghost" onclick="closeModal()">Cancel</button></div>
  </div>
</div>

<div class="toast" id="toast">Done</div>
<script>
  function toast(message){
    const el=document.getElementById('toast');
    el.textContent=message;el.classList.add('show');
    setTimeout(()=>el.classList.remove('show'),2200);
  }
  function openModal(title){
    document.getElementById('modalTitle').textContent=title||'Add record';
    document.getElementById('modal').classList.add('open');
  }
  function closeModal(){document.getElementById('modal').classList.remove('open');}
  document.querySelectorAll('.nav a[data-tab]').forEach(link=>{
    link.addEventListener('click',()=>{
      document.querySelectorAll('.nav a').forEach(a=>a.classList.remove('active'));
      document.querySelectorAll('.panel').forEach(p=>p.classList.remove('active'));
      link.classList.add('active');
      document.getElementById(link.dataset.tab).classList.add('active');
      document.getElementById('pageTitle').textContent=link.textContent.trim();
    });
  });
  document.querySelectorAll('.tab[data-set]').forEach(tab=>{
    tab.addEventListener('click',()=>{
      document.querySelectorAll('.tab[data-set]').forEach(t=>t.classList.remove('active'));
      ['students','instructors','classes'].forEach(id=>{
        document.getElementById(id).style.display=(id===tab.dataset.set)?'block':'none';
      });
      tab.classList.add('active');
    });
  });
  document.getElementById('modal').addEventListener('click',e=>{
    if(e.target.id==='modal') closeModal();
  });
</script>
</body>
</html>
