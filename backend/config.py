"""Settings for the AtClam backend (face recognition, camera and attendance)."""
from pathlib import Path

BASE = Path(__file__).resolve().parent
DATA = BASE / "data"
FACES = DATA / "faces"
AVATARS = DATA / "avatars"
DB_PATH = DATA / "attendance.db"
SECRET_PATH = DATA / ".jwt_secret"

# ---- recognition (evaluate.py helps you choose these) ----
MATCH_THRESHOLD = 0.40   # cosine similarity needed to call it a known person
MATCH_MARGIN = 0.05      # best match must beat 2nd best (protects against lookalikes)
SAME_PERSON_MIN = 0.30   # the 3 enrollment photos must agree with each other

# ---- stability ----
RECOGNIZE_EVERY = 3      # run recognition on every Nth frame of a tracked face
VOTE_WINDOW = 5          # look at the last N recognition results...
VOTES_NEEDED = 3         # ...and require this many to agree before trusting it

# ---- liveness (blocks still photos) ----
REQUIRE_BLINK = True
BLINK_DROP = 0.75
LIVE_SECONDS = 12        # a blink counts as "live" for this many seconds

# ---- camera / detection ----
CAMERA_W, CAMERA_H = 1280, 720
DETECTION_CONF = 0.6
MIN_FACE_PX = 55
MAX_FACES = 8
FRAMES_TO_LOSE = 10      # frames a face may vanish before the track is dropped

# ---- attendance ----
LATE_AFTER_MIN = 15      # fallback when a class has no configured times

# ---- server ----
HOST = "0.0.0.0"
PORT = 8000
