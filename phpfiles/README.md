# AtClam PHP UI

This folder is a browser (PHP) replacement for the Flutter UI in `../classroom-attendance`.
The visuals, labels, colors, tabs, cards and dialogs mirror the original app. Behavior is
wired to the **existing FastAPI backend**, so face detection, blink liveness, PDF class
lists and PostgreSQL storage remain exactly the same as in the Flutter app.

## Run it

1. Start the backend from the original project:

   ```bash
   cd ../classroom-attendance/backend
   pip install -r requirements.txt
   uvicorn main:app --host 0.0.0.0 --port 8000
   ```

2. Serve this folder with any PHP 8.1+ server, for example:

   ```bash
   cd ~/Downloads/phpfiles
   php -S localhost:8080
   ```

3. Open `http://localhost:8080/login.php`.

If the backend is on another machine, open the account menu → **Server address** and enter
its LAN address, e.g. `http://192.168.1.10:8000`. The choice is saved in a cookie and used
by both PHP and the browser camera scanner.

## Files

- `core.php` – sessions, backend calls, auth helpers, shared validation
- `ui.php` – UI components (logo, fields, shell, cards, dialogs, calendar)
- `styles.php` / `scripts.php` – the CSS/JS assets served through PHP
- `api.php` – same-origin JSON proxy that safely attaches the session token
- page files – one PHP page per Flutter screen (including `splash.php` for the brief startup gate)

## Notes

- Login accepts the same email/username + password as the Flutter app.
- Camera pages require `getUserMedia`, which works on `localhost` or HTTPS.
- `admin.php`, `instructor.php` and `student.php` are the role dashboards reached from the
  root `index.php`.
