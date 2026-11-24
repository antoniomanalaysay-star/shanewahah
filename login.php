<?php
// UI only — no database, no backend, no real authentication.
$demoRoles = ['student' => 'Student', 'instructor' => 'Instructor', 'admin' => 'Admin'];
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Sign in · AtClam</title>
  <style>
    :root{--ink900:#04100a;--ink800:#071a10;--ink700:#0a2415;--line:#1d5236;--gold:#f5c518;--lime:#a8e063;--text:#e7f3e9;--muted:#8fae99;--label:#9cc4a8;--danger:#ff6b6b}
    *{box-sizing:border-box}
    body{margin:0;min-height:100vh;background:radial-gradient(circle at 20% 10%,#0d3320 0,var(--ink900) 55%);color:var(--text);font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Arial,sans-serif;display:grid;place-items:center;padding:24px}
    a{color:inherit;text-decoration:none}
    button,input,select{font:inherit}
    .auth{width:100%;max-width:420px;background:var(--ink800);border:1px solid var(--line);border-radius:18px;padding:32px;box-shadow:0 24px 60px rgba(0,0,0,.45)}
    .brand{display:flex;align-items:center;gap:12px;justify-content:center;margin-bottom:26px}
    .logo{width:44px;height:44px;border-radius:12px;background:linear-gradient(135deg,#ffe066,#a8e063,#1f7a43);display:grid;place-items:center;color:var(--ink900);font-weight:900;font-size:18px}
    .brand b{font-size:20px;letter-spacing:2px;display:block;line-height:1}
    .brand small{font-size:9px;letter-spacing:1.6px;color:var(--muted)}
    h1{font-size:22px;margin:0 0 6px;text-align:center}
    .sub{text-align:center;color:var(--muted);font-size:13px;margin:0 0 24px}
    label{display:block;font-size:12px;color:var(--label);margin-bottom:6px}
    input,select{width:100%;background:var(--ink700);border:1px solid var(--line);color:var(--text);border-radius:11px;padding:11px 13px;margin-bottom:15px;outline:none}
    input:focus,select:focus{border-color:var(--lime)}
    .btn{width:100%;border:0;border-radius:11px;padding:12px;background:var(--gold);color:var(--ink900);font-weight:800;cursor:pointer;margin-top:4px}
    .btn:hover{filter:brightness(1.06)}
    .foot{text-align:center;color:var(--muted);font-size:12px;margin-top:20px}
    .foot a{color:var(--lime);font-weight:700}
    .hint{background:var(--ink700);border:1px dashed var(--line);border-radius:11px;padding:12px;font-size:12px;color:var(--muted);margin-top:18px;text-align:center}
    .links{display:flex;gap:8px;justify-content:center;margin-top:10px;flex-wrap:wrap}
    .links a{font-size:11px;color:var(--gold);border:1px solid var(--line);border-radius:16px;padding:5px 10px}
    .toast{position:fixed;left:50%;bottom:24px;transform:translate(-50%,12px);background:var(--ink700);border:1px solid var(--lime);padding:11px 16px;border-radius:10px;font-size:12px;opacity:0;transition:.2s;pointer-events:none}
    .toast.show{opacity:1;transform:translate(-50%,0)}
  </style>
</head>
<body>
  <main class="auth">
    <div class="brand">
      <div class="logo">AC</div>
      <div><b>AT CLAM</b><small>CLASSROOM ATTENDANCE</small></div>
    </div>
    <h1>Welcome back</h1>
    <p class="sub">Sign in to open your workspace.</p>

    <form id="loginForm">
      <label for="identity">Email or username</label>
      <input id="identity" type="text" placeholder="you@school.edu" required>

      <label for="password">Password</label>
      <input id="password" type="password" placeholder="••••••••" required>

      <label for="role">Open as</label>
      <select id="role">
        <?php foreach ($demoRoles as $key => $label): ?>
          <option value="<?= htmlspecialchars($key) ?>"><?= htmlspecialchars($label) ?></option>
        <?php endforeach; ?>
      </select>

      <button class="btn" type="submit">Sign in</button>
    </form>

    <p class="foot">No account yet? <a href="signup.php">Sign up</a></p>

    <div class="hint">
      UI demo — any credentials work. Pick a role above, or jump straight in:
      <div class="links">
        <a href="student_dashboard.php">Student</a>
        <a href="instructor_dashboard.php">Instructor</a>
        <a href="admin_dashboard.php">Admin</a>
      </div>
    </div>
  </main>
  <div class="toast" id="toast">Signed in</div>

  <script>
    function toast(message){
      const el=document.getElementById('toast');
      el.textContent=message;el.classList.add('show');
      setTimeout(()=>el.classList.remove('show'),1200);
    }
    document.getElementById('loginForm').addEventListener('submit',function(e){
      e.preventDefault();
      const role=document.getElementById('role').value;
      toast('Opening '+role+' dashboard…');
      setTimeout(()=>{location.href=role+'_dashboard.php';},700);
    });
  </script>
</body>
</html>
