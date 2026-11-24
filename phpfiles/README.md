# AtClam PHP UI — Demo mode

This folder is a browser (PHP) replacement for the Flutter UI in `../classroom-attendance`.

It now runs **completely standalone**: no FastAPI server, no PostgreSQL, no camera model,
no external data. Every API call is answered locally from a small demo state kept only in
the PHP session. Close the browser session and everything returns to the seeded demo.

## Run it

```bash
cd ~/Downloads/phpfiles
php -S localhost:8080
```

Open `http://localhost:8080/login.php`.

## Demo login

- Type any username/email and any password.
- Names containing `admin` open the admin dashboard.
- Names containing `instructor`, `teacher` or `prof` open the instructor dashboard.
- Anything else opens the student dashboard.

Built-in examples: `ThirdyAdmin`, `instructor`, `student`, `jane`, `alex`.

Registration also works without uploading photos — the three photo slots are still shown.

## What works

- All dashboards, tabs, class pages, dialogs, forms, notifications and calendars.
- Create/edit/delete demo classes and accounts during the session.
- Create classes, join with a seeded code (`A1B2C3` or `B2C3D4`), post announcements,
  add excuse notes, start/end a demo class session, and scan with the browser camera.
- Face recognition is simulated: the scanner shows sample face boxes and marks attendance.

## Files

- `core.php` – sessions, helpers and the complete local demo API (no cURL)
- `ui.php` – UI components (logo, fields, shell, cards, dialogs, calendar)
- `styles.php` / `scripts.php` – CSS/JS assets served through PHP
- `api.php` – same-origin JSON proxy for the browser JavaScript (also demo-only)
- page files – one PHP page per Flutter screen

## Note

The camera preview needs `localhost` or HTTPS permission, but no face model or backend
is contacted. Real recognition would require the original FastAPI backend.
