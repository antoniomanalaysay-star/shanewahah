<?php
// UI only — sample data below is hard-coded; there is no database or backend.
$classes = [
  ['name' => 'IT 301 · Database Systems', 'code' => 'A1B2C3', 'section' => 'Section A', 'students' => 32, 'time' => '08:00 – 09:00'],
  ['name' => 'CS 210 · Web Development', 'code' => 'W3D4E5', 'section' => 'Section B', 'students' => 28, 'time' => '09:30 – 10:30'],
  ['name' => 'MATH 101 · Calculus', 'code' => 'M7N8P9', 'section' => 'Section C', 'students' => 40, 'time' => '13:00 – 14:00'],
];
$students = [
  ['Juan Dela Cruz', '2024-1001', 'Present', '08:02'],
  ['Maria Santos', '2024-1002', 'Present', '08:04'],
  ['Pedro Reyes', '2024-1003', 'Late', '08:21'],
  ['Ana Gonzales', '2024-1004', 'Absent', '—'],
  ['Shane Wahah', '2024-1005', 'Present', '07:59'],
];
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Instructor workspace · AtClam</title>
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
    .grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(250px,1fr));gap:14px}
    .tile{background:var(--ink700);border:1px solid var(--line);border-radius:12px;padding:16px}
    .tile h3{margin:0 0 6px;font-size:15px;color:var(--gold)}
    .muted{color:var(--muted);font-size:12px}
    .code{display:inline-block;background:rgba(245,197,24,.12);color:var(--gold);border-radius:6px;padding:3px 8px;font-size:11px;font-weight:800;letter-spacing:1px;margin:8px 0}
    .btn{border:0;border-radius:9px;padding:9px 14px;background:var(--gold);color:var(--ink900);font-weight:800;cursor:pointer;font-size:12px}
    .btn.ghost{background:transparent;color:var(--lime);border:1px solid var(--line)}
    .btn.danger{background:var(--danger);color:#fff}
    .btn.small{padding:6px 10px;font-size:11px}
    .actions{display:flex;gap:6px;margin-top:14px;flex-wrap:wrap}
    input,select,textarea{background:var(--ink700);border:1px solid var(--line);color:var(--text);border-radius:10px;padding:10px 12px;outline:none;width:100%}
    input:focus,select:focus{border-color:var(--lime)}
    label{display:block;font-size:12px;color:var(--label);margin-bottom:6px}
    .row{display:flex;gap:12px;flex-wrap:wrap;align-items:end}
    .row>*{flex:1;min-width:170px}
    .row .fixed{flex:0 0 200px}
    table{width:100%;border-collapse:collapse;font-size:12px}
    th,td{text-align:left;padding:10px;border:1px solid var(--line)}
    th{color:var(--lime);background:var(--ink700)}
    .badge{border-radius:12px;padding:3px 9px;font-size:11px;font-weight:700}
    .badge.present{background:rgba(76,175,80,.18);color:#7ddc83}
    .badge.late{background:rgba(245,197,24,.16);color:var(--gold)}
    .badge.absent{background:rgba(255,107,107,.16);color:var(--danger)}
    .panel{display:none}.panel.active{display:block}
    .camera{height:260px;border:1px dashed var(--line);border-radius:12px;background:radial-gradient(circle,#0d3320,#04100a);display:grid;place-items:center;text-align:center;color:var(--muted);font-size:13px}
    .camera .ic{font-size:46px;display:block;color:var(--gold);margin-bottom:8px}
    .live{display:inline-flex;align-items:center;gap:7px;color:#7ddc83;font-size:12px;font-weight:700}
    .dot{width:8px;height:8px;border-radius:50%;background:var(--leaf)}
    .toast{position:fixed;right:20px;bottom:20px;background:var(--ink700);border:1px solid var(--lime);padding:12px 16px;border-radius:10px;font-size:12px;opacity:0;transform:translateY(10px);transition:.2s;pointer-events:none}
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
      <a class="active" data-tab="classes"><span class="ic">▦</span> My classes</a>
      <a data-tab="live"><span class="ic">▣</span> Live camera</a>
      <a data-tab="attendance"><span class="ic">▤</span> Attendance</a>
      <a data-tab="profile"><span class="ic">◎</span> My profile</a>
    </nav>
    <div class="side-foot">
      <b>Demo Instructor</b><span>demo.instructor@classroom.local</span><br>
      <a href="login.php">↩ Switch role</a>
    </div>
  </aside>

  <div class="main">
    <header class="top">
      <span class="title" id="pageTitle">My classes</span>
      <div class="spacer"></div>
      <span class="pill">INSTRUCTOR</span>
      <span class="bell">🔔</span>
      <div class="avatar">D</div>
      <a class="btn ghost small" href="login.php">Switch role</a>
    </header>

    <main class="content">
      <!-- MY CLASSES -->
      <section class="panel active" id="classes">
        <h1>Instructor workspace</h1>
        <p class="sub">Welcome back, Demo Instructor. Create classes, share the code, post announcements and check attendance.</p>
        <div class="card">
          <div class="card-head"><h2>New class</h2><span class="muted">Visible to students after you share the code.</span></div>
          <form class="row" onsubmit="event.preventDefault();toast('Class created (UI demo)')">
            <div><label>Class name</label><input placeholder="e.g. IT 301" required></div>
            <div><label>Section</label><input placeholder="Section A"></div>
            <div><label>Present / late / end</label><input placeholder="08:00 / 08:15 / 09:00"></div>
            <div><label>Description</label><input placeholder="Optional"></div>
            <div class="fixed"><button class="btn" type="submit">Create class</button></div>
          </form>
        </div>
        <div class="card">
          <div class="card-head"><h2>My classes</h2><button class="btn ghost small" onclick="toast('Refreshed (UI demo)')">Refresh</button></div>
          <div class="grid">
            <?php foreach ($classes as $class): ?>
              <article class="tile">
                <h3><?= htmlspecialchars($class['name']) ?></h3>
                <div class="muted"><?= htmlspecialchars($class['section']) ?> · <?= (int)$class['students'] ?> students</div>
                <div class="muted">Schedule: <?= htmlspecialchars($class['time']) ?></div>
                <div class="code"><?= htmlspecialchars($class['code']) ?></div>
                <div class="actions">
                  <button class="btn small" onclick="toast('Opening class (UI demo)')">Open</button>
                  <button class="btn ghost small" onclick="toast('Edit dialog (UI demo)')">Edit</button>
                  <button class="btn danger small" onclick="toast('Delete dialog (UI demo)')">Delete</button>
                </div>
              </article>
            <?php endforeach; ?>
          </div>
        </div>
        <div class="card">
          <h2>Announcement</h2>
          <form class="row" onsubmit="event.preventDefault();toast('Announcement posted (UI demo)')">
            <div><label>Title</label><input placeholder="Reminder" required></div>
            <div><label>Message</label><input placeholder="Bring your laptop tomorrow"></div>
            <div class="fixed"><button class="btn" type="submit">Post</button></div>
          </form>
        </div>
      </section>

      <!-- LIVE CAMERA -->
      <section class="panel" id="live">
        <h1>Live camera</h1>
        <p class="sub">Start a session, then point the camera at the class to mark attendance.</p>
        <div class="card">
          <div class="card-head">
            <h2>Camera preview</h2>
            <span class="live"><span class="dot"></span><span id="sessionState">No active session</span></span>
          </div>
          <div class="camera"><div><span class="ic">▣</span>Camera preview placeholder<br><span class="muted">No backend connected — UI demo only</span></div></div>
          <div class="row" style="margin-top:14px">
            <button class="btn" onclick="document.getElementById('sessionState').textContent='Session live (UI demo)';toast('Session started')">Start session</button>
            <button class="btn ghost" onclick="document.getElementById('sessionState').textContent='No active session';toast('Session ended')">End session</button>
            <button class="btn ghost" onclick="toast('Face detection reset')">Reset tracking</button>
          </div>
        </div>
      </section>

      <!-- ATTENDANCE -->
      <section class="panel" id="attendance">
        <h1>Attendance</h1>
        <p class="sub">Sample roster with manual status corrections.</p>
        <div class="card">
          <div class="card-head"><h2>IT 301 · Today</h2><button class="btn ghost small" onclick="toast('CSV export (UI demo)')">Export CSV</button></div>
          <table>
            <thead><tr><th>Student</th><th>Student ID</th><th>Status</th><th>Time in</th><th>Action</th></tr></thead>
            <tbody>
              <?php foreach ($students as $student): ?>
                <tr>
                  <td><?= htmlspecialchars($student[0]) ?></td>
                  <td><?= htmlspecialchars($student[1]) ?></td>
                  <td><span class="badge <?= strtolower($student[2]) ?>"><?= htmlspecialchars($student[2]) ?></span></td>
                  <td><?= htmlspecialchars($student[3]) ?></td>
                  <td><button class="btn ghost small" onclick="toast('Change status dialog (UI demo)')">Edit</button></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </section>

      <!-- PROFILE -->
      <section class="panel" id="profile">
        <h1>My profile</h1>
        <p class="sub">Manage your account details and picture.</p>
        <div class="card">
          <h2>Account details</h2>
          <form class="row" onsubmit="event.preventDefault();toast('Profile saved (UI demo)')">
            <div><label>Full name</label><input value="Demo Instructor"></div>
            <div><label>Username</label><input value="demo_instructor" disabled></div>
            <div><label>Email</label><input value="demo.instructor@classroom.local" disabled></div>
            <div class="fixed"><button class="btn" type="submit">Save name</button></div>
          </form>
        </div>
      </section>
    </main>
  </div>
</div>
<div class="toast" id="toast">Done</div>
<script>
  function toast(message){
    const el=document.getElementById('toast');
    el.textContent=message;el.classList.add('show');
    setTimeout(()=>el.classList.remove('show'),2200);
  }
  document.querySelectorAll('.nav a[data-tab]').forEach(link=>{
    link.addEventListener('click',()=>{
      document.querySelectorAll('.nav a').forEach(a=>a.classList.remove('active'));
      document.querySelectorAll('.panel').forEach(p=>p.classList.remove('active'));
      link.classList.add('active');
      document.getElementById(link.dataset.tab).classList.add('active');
      document.getElementById('pageTitle').textContent=link.textContent.trim();
    });
  });
</script>
</body>
</html>
