<?php
// UI only — sample data below is hard-coded; there is no database or backend.
$classes = [
  ['name' => 'IT 301 · Database Systems', 'code' => 'A1B2C3', 'instructor' => 'Mr. Shane', 'students' => 32, 'attendance' => '92%'],
  ['name' => 'CS 210 · Web Development', 'code' => 'W3D4E5', 'instructor' => 'Ms. Ana', 'students' => 28, 'attendance' => '88%'],
  ['name' => 'MATH 101 · Calculus', 'code' => 'M7N8P9', 'instructor' => 'Mr. Jose', 'students' => 40, 'attendance' => '95%'],
];
$attendance = [
  ['Nov 24, 2026', 'Database Systems', 'Present', '08:02'],
  ['Nov 23, 2026', 'Web Development', 'Late', '08:21'],
  ['Nov 20, 2026', 'Calculus', 'Present', '07:58'],
  ['Nov 19, 2026', 'Database Systems', 'Absent', '—'],
  ['Nov 18, 2026', 'Web Development', 'Present', '08:05'],
];
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Student workspace · AtClam</title>
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
    .avatar{width:30px;height:30px;border-radius:50%;background:var(--forest,#1f7a43);display:grid;place-items:center;font-size:12px;font-weight:800}
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
    .btn.small{padding:6px 10px;font-size:11px}
    input,select,textarea{background:var(--ink700);border:1px solid var(--line);color:var(--text);border-radius:10px;padding:10px 12px;outline:none;width:100%}
    input:focus,select:focus{border-color:var(--lime)}
    label{display:block;font-size:12px;color:var(--label);margin-bottom:6px}
    .row{display:flex;gap:12px;flex-wrap:wrap;align-items:end}
    .row>*{flex:1;min-width:180px}
    .row .fixed{flex:0 0 220px}
    table{width:100%;border-collapse:collapse;font-size:12px}
    th,td{text-align:left;padding:10px;border:1px solid var(--line)}
    th{color:var(--lime);background:var(--ink700)}
    .badge{border-radius:12px;padding:3px 9px;font-size:11px;font-weight:700}
    .badge.present{background:rgba(76,175,80,.18);color:#7ddc83}
    .badge.late{background:rgba(245,197,24,.16);color:var(--gold)}
    .badge.absent{background:rgba(255,107,107,.16);color:var(--danger)}
    .stats{display:flex;gap:12px;flex-wrap:wrap;margin-bottom:18px}
    .stat{background:var(--ink800);border:1px solid var(--line);border-radius:12px;padding:14px 18px;min-width:140px}
    .stat b{display:block;color:var(--gold);font-size:26px;line-height:1.2}
    .stat span{color:var(--muted);font-size:11px}
    .panel{display:none}.panel.active{display:block}
    .face{display:flex;align-items:center;gap:12px;background:rgba(168,224,99,.08);border:1px solid var(--line);border-radius:12px;padding:12px 16px;margin-bottom:18px}
    .face .ic{font-size:22px}
    .face .txt{flex:1;font-size:13px}
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
      <a data-tab="attendance"><span class="ic">▤</span> Attendance</a>
      <a data-tab="profile"><span class="ic">◎</span> My profile</a>
    </nav>
    <div class="side-foot">
      <b>Demo Student</b><span>demo.student@classroom.local</span><br>
      <a href="login.php">↩ Switch role</a>
    </div>
  </aside>

  <div class="main">
    <header class="top">
      <span class="title" id="pageTitle">My classes</span>
      <div class="spacer"></div>
      <span class="pill">STUDENT</span>
      <span class="bell">🔔</span>
      <div class="avatar">D</div>
      <a class="btn ghost small" href="login.php">Switch role</a>
    </header>

    <main class="content">
      <!-- MY CLASSES -->
      <section class="panel active" id="classes">
        <h1>Student workspace</h1>
        <p class="sub">Join a class with its code, read announcements and track your attendance.</p>

        <div class="face">
          <span class="ic">🙂</span>
          <span class="txt">Your face is registered. The instructor's camera can recognize you.</span>
          <button class="btn ghost small" onclick="toast('Face photo editor opened (UI demo)')">Update photos</button>
        </div>

        <div class="card">
          <div class="card-head">
            <h2>My classes</h2>
            <button class="btn ghost small" onclick="toast('Enter a class code below to join')">＋ Join a class</button>
          </div>
          <form class="row" onsubmit="event.preventDefault();toast('Joined the class (UI demo)')" style="margin-bottom:16px">
            <div><label>Class code</label><input placeholder="e.g. A1B2C3" required></div>
            <div class="fixed"><button class="btn" type="submit">Join class</button></div>
          </form>
          <div class="grid">
            <?php foreach ($classes as $class): ?>
              <article class="tile">
                <h3><?= htmlspecialchars($class['name']) ?></h3>
                <div class="muted"><?= htmlspecialchars($class['instructor']) ?> · <?= (int)$class['students'] ?> students</div>
                <div class="code"><?= htmlspecialchars($class['code']) ?></div>
                <div class="muted">Attendance: <?= htmlspecialchars($class['attendance']) ?></div>
                <div style="margin-top:14px"><button class="btn small" onclick="toast('Opening <?= htmlspecialchars($class['name']) ?> (UI demo)')">Open class</button></div>
              </article>
            <?php endforeach; ?>
          </div>
        </div>
      </section>

      <!-- ATTENDANCE -->
      <section class="panel" id="attendance">
        <h1>My attendance</h1>
        <p class="sub">A sample monthly record with present, late and absent days.</p>
        <div class="stats">
          <div class="stat"><b>42</b><span>Present</span></div>
          <div class="stat"><b>5</b><span>Late</span></div>
          <div class="stat"><b>3</b><span>Absent</span></div>
        </div>
        <div class="card">
          <h2>Recent days</h2>
          <table>
            <thead><tr><th>Date</th><th>Class</th><th>Status</th><th>Time in</th></tr></thead>
            <tbody>
              <?php foreach ($attendance as $row): ?>
                <tr>
                  <td><?= htmlspecialchars($row[0]) ?></td>
                  <td><?= htmlspecialchars($row[1]) ?></td>
                  <td><span class="badge <?= strtolower($row[2]) ?>"><?= htmlspecialchars($row[2]) ?></span></td>
                  <td><?= htmlspecialchars($row[3]) ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
        <div class="card">
          <h2>Excuse letter</h2>
          <form class="row" onsubmit="event.preventDefault();toast('Excuse submitted (UI demo)')">
            <div><label>Date</label><input type="date"></div>
            <div><label>Reason</label><input placeholder="Short note"></div>
            <div class="fixed"><button class="btn" type="submit">Submit</button></div>
          </form>
        </div>
      </section>

      <!-- PROFILE -->
      <section class="panel" id="profile">
        <h1>My profile</h1>
        <p class="sub">Manage your account details and picture.</p>
        <div class="card">
          <h2>Account details</h2>
          <form class="row" onsubmit="event.preventDefault();toast('Profile saved (UI demo)')">
            <div><label>Full name</label><input value="Demo Student"></div>
            <div><label>Username</label><input value="demo_student" disabled></div>
            <div><label>Email</label><input value="demo.student@classroom.local" disabled></div>
            <div><label>Section</label><input value="Section A"></div>
            <div class="fixed"><button class="btn" type="submit">Save name</button></div>
          </form>
        </div>
        <div class="card">
          <h2>Profile picture</h2>
          <button class="btn ghost" onclick="toast('Picture picker opened (UI demo)')">Choose picture</button>
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
