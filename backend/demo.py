"""No-login demo mode for this copy of AtClam.

The Flutter app in this folder has no login or signup screens. Instead it picks
a demo role and sends an ``X-Demo-User-Id`` header with every request. When
``DEMO_MODE`` is on (the default here), the backend trusts that header instead
of a JWT.

This is intended for local demos only. Only accounts whose username starts
with ``demo_`` can be selected this way, and the token path still works as
before if you turn demo mode off with ``DEMO_MODE=0``.
"""
import os
import time

from fastapi import APIRouter, HTTPException

from db import get_connection, user_public

DEMO_MODE = os.getenv("DEMO_MODE", "1").strip().lower() not in (
    "0", "false", "no", "off",
)

DEMO_PREFIX = "demo_"
DEMO_PASSWORD = "Demo1234"

DEMO_ACCOUNTS = (
    {
        "username": "demo_student",
        "email": "demo.student@classroom.local",
        "full_name": "Demo Student",
        "role": "student",
        "section": "Section A",
        "school_id": "S-1001",
    },
    {
        "username": "demo_instructor",
        "email": "demo.instructor@classroom.local",
        "full_name": "Demo Instructor",
        "role": "instructor",
        "section": "Faculty",
        "school_id": "",
    },
    {
        "username": "demo_admin",
        "email": "demo.admin@classroom.local",
        "full_name": "Demo Administrator",
        "role": "admin",
        "section": "",
        "school_id": "",
    },
)

router = APIRouter(prefix="/demo", tags=["demo"])


def ensure_demo_accounts() -> None:
    """Create the three demo identities if they are not in the database yet."""
    from auth import hash_password  # imported late to avoid a circular import

    password_hash = hash_password(DEMO_PASSWORD)
    with get_connection() as con, con:
        for account in DEMO_ACCOUNTS:
            exists = con.execute(
                "SELECT 1 FROM users WHERE username=? LIMIT 1", (account["username"],)
            ).fetchone()
            if exists:
                continue
            con.execute(
                """INSERT INTO users(full_name, username, email, password_hash, role,
                                     school_id, section, created_at)
                   VALUES(?,?,?,?,?,?,?,?)""",
                (account["full_name"], account["username"], account["email"],
                 password_hash, account["role"], account["school_id"],
                 account["section"], time.time()),
            )


def demo_user(user_id: int):
    """Return a public demo user, or None if the id is not a demo account."""
    if not DEMO_MODE:
        return None
    with get_connection() as con:
        row = con.execute("SELECT * FROM users WHERE id=?", (user_id,)).fetchone()
    user = dict(row) if row else None
    if not user or not user["username"].startswith(DEMO_PREFIX):
        return None
    if not user.get("is_active", 1):
        return None
    return user_public(user)


@router.get("/users")
def list_demo_users():
    """Roles the Flutter role picker can open, keyed by 'student'/'instructor'/'admin'."""
    ensure_demo_accounts()
    roles = {}
    with get_connection() as con:
        for account in DEMO_ACCOUNTS:
            row = con.execute(
                "SELECT * FROM users WHERE username=? LIMIT 1", (account["username"],)
            ).fetchone()
            if row:
                roles[account["role"]] = user_public(dict(row))
    if len(roles) != 3:
        raise HTTPException(status_code=503, detail="Demo accounts are not ready yet.")
    return roles
