<?php
// UI only — no database, no backend, no account is actually created.
$roles = ['student' => 'Student', 'instructor' => 'Instructor'];
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Sign up · AtClam</title>
  <style>
    :root{--ink900:#04100a;--ink800:#071a10;--ink700:#0a2415;--line:#1d5236;--gold:#f5c518;--lime:#a8e063;--text:#e7f3e9;--muted:#8fae99;--label:#9cc4a8;--danger:#ff6b6b}
    *{box-sizing:border-box}
    body{margin:0;min-height:100vh;background:radial-gradient(circle at 80% 10%,#0d3320 0,var(--ink900) 55%);color:var(--text);font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Arial,sans-serif;display:grid;place-items:center;padding:24px}
    a{color:inherit;text-decoration:none}
    button,input,select{font:inherit}
    .auth{width:100%;max-width:520px;background:var(--ink800);border:1px solid var(--line);border-radius:18px;padding:32px;box-shadow:0 24px 60px rgba(0,0,0,.45)}
    .brand{display:flex;align-items:center;gap:12px;justify-content:center;margin-bottom:26px}
    .logo{width:44px;height:44px;border-radius:12px;background:linear-gradient(135deg,#ffe066,#a8e063,#1f7a43);display:grid;place-items:center;color:var(--ink900);font-weight:900;font-size:18px}
    .brand b{font-size:20px;letter-spacing:2px;display:block;line-height:1}
    .brand small{font-size:9px;letter-spacing:1.6px;color:var(--muted)}
    h1{font-size:22px;margin:0 0 6px;text-align:center}
    .sub{text-align:center;color:var(--muted);font-size:13px;margin:0 0 24px}
    label{display:block;font-size:12px;color:var(--label);margin-bottom:6px}
    input,select{width:100%;background:var(--ink700);border:1px solid var(--line);color:var(--text);border-radius:11px;padding:11px 13px;margin-bottom:14px;outline:none}
    input:focus,select:focus{border-color:var(--lime)}
    .row{display:flex;gap:12px;flex-wrap:wrap}
    .row>*{flex:1;min-width:180px}
    .btn{width:100%;border:0;border-radius:11px;padding:12px;background:var(--gold);color:var(--ink900);font-weight:800;cursor:pointer;margin-top:4px}
    .btn:hover{filter:brightness(1.06)}
    .foot{text-align:center;color:var(--muted);font-size:12px;margin-top:20px}
    .foot a{color:var(--lime);font-weight:700}
    .hint{background:var(--ink700);border:1px dashed var(--line);border-radius:11px;padding:12px;font-size:12px;color:var(--muted);margin-top:18px;text-align:center}
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
    <h1>Create your account</h1>
    <p class="sub">Fill in the details to request a workspace.</p>

    <form id="signupForm">
      <div class="row">
        <div>
          <label for="name">Full name</label>
          <input id="name" type="text" placeholder="Juan Dela Cruz" required>
        </div>
        <div>
          <label for="username">Username</label>
          <input id="username" type="text" placeholder="juan" required>
        </div>
      </div>
      <div class="row">
        <div>
          <label for="email">Email</label>
          <input id="email" type="email" placeholder="you@school.edu" required>
        </div>
        <div>
          <label for="section">Section</label>
          <input id="section" type="text" placeholder="IT-301">
        </div>
      </div>
      <label for="role">I am a</label>
      <select id="role">
        <?php foreach ($roles as $key => $label): ?>
          <option value="<?= htmlspecialchars($key) ?>"><?= htmlspecialchars($label) ?></option>
        <?php endforeach; ?>
      </select>
      <div class="row">
        <div>
          <label for="password">Password</label>
          <input id="password" type="password" minlength="8" placeholder="At least 8 characters" required>
        </div>
        <div>
          <label for="confirm">Confirm password</label>
          <input id="confirm" type="password" minlength="8" placeholder="Repeat password" required>
        </div>
      </div>
      <button class="btn" type="submit">Create account</button>
    </form>

    <p class="foot">Already registered? <a href="login.php">Sign in</a></p>
    <div class="hint">UI demo only — nothing is saved. You will be returned to the sign-in screen.</div>
  </main>
  <div class="toast" id="toast">Account details checked</div>

  <script>
    function toast(message){
      const el=document.getElementById('toast');
      el.textContent=message;el.classList.add('show');
      setTimeout(()=>el.classList.remove('show'),1400);
    }
    document.getElementById('signupForm').addEventListener('submit',function(e){
      e.preventDefault();
      if(document.getElementById('password').value!==document.getElementById('confirm').value){
        toast('Passwords do not match');return;
      }
      toast('Account created (demo) — redirecting…');
      setTimeout(()=>{location.href='login.php';},1100);
    });
  </script>
</body>
</html>
