# No-login dashboard build

This folder is a separate copy of `classroom-attendance`. It keeps the
**student, instructor and admin dashboards** with all their working functions,
but removes the login / signup flow entirely.

## What changed

- The app opens on a **role picker** (`Student`, `Instructor`, `Admin`) instead
  of a login screen. It remembers the last role you used and reopens it
  automatically next time.
- `Switch role` in the top bar / account menu goes back to the picker. There is
  no logout, password change, registration or token storage.
- All dashboard screens, class screens, attendance calendar, live camera, face
  registration, notifications and profile editing still work.
- The backend accepts an `X-Demo-User-Id` header instead of a JWT when
  `DEMO_MODE` is enabled (it is **on by default in this copy**). Only the three
  `demo_*` accounts can be used this way.
- New endpoint: `GET /demo/users` returns the three demo identities for the
  picker. They are created automatically on backend startup if missing.

## Run it

Start **this copy's backend** (not the old one on port 8000). Stop any existing
AtClam server first, then:

```bash
cd /Users/hyxmanalaysay/Downloads/classroom-attendancenew/backend
python3 -m uvicorn main:app --host 0.0.0.0 --port 8000 --reload
```

Then run the Flutter app from this folder:

```bash
cd /Users/hyxmanalaysay/Downloads/classroom-attendancenew
flutter run -d chrome        # or a connected device
# web build already exists in build/web
```

If you need the backend on a different port, build with:

```bash
flutter run -d chrome --dart-define=API_BASE_URL=http://localhost:8001
```

Demo accounts (created in the configured PostgreSQL database):

| Role | Username | Name |
| --- | --- | --- |
| Student | `demo_student` | Demo Student |
| Instructor | `demo_instructor` | Demo Instructor |
| Admin | `demo_admin` | Demo Administrator |

The demo instructor has no classes on first run; create one from the instructor
dashboard and share its code with the demo student to try joining / attendance.

## Notes

- This copy uses the same `backend/.env` settings as the original project, so it
  shares the same database. To keep demo data separate, change `DB_NAME` in this
  copy's `backend/.env` (e.g. `AtClamDemo`) — tables are created automatically.
- To restore normal JWT login behaviour in this backend, set `DEMO_MODE=0` in
  the environment (the Flutter app in this folder would then need a login
  screen again).
- The original `~/Downloads/classroom-attendance` project was not modified.
