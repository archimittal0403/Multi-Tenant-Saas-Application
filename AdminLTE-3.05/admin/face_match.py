from flask import Flask, request, jsonify
from insightface.app import FaceAnalysis
import cv2
import numpy as np
import base64
import json
import os
import math
import mediapipe as mp

# ================= MEDIAPIPE =================

mp_face_mesh = mp.solutions.face_mesh

face_mesh = mp_face_mesh.FaceMesh(
    static_image_mode=False,
    max_num_faces=1,
    refine_landmarks=True,
    min_detection_confidence=0.5,
    min_tracking_confidence=0.5
)

# ================= FLASK =================

app = Flask(__name__)
app.config["MAX_CONTENT_LENGTH"] = 20 * 1024 * 1024

# ================= INSIGHTFACE =================

face_app = FaceAnalysis(name="buffalo_l")
face_app.prepare(ctx_id=-1, det_size=(640, 640))
prev_nose_x = None
movement_count = 0

# ================= UTIL =================

def normalize(emb):
    return emb / np.linalg.norm(emb)


def base64_to_image(data):
    try:
        if "," in data:
            data = data.split(",")[1]

        img = base64.b64decode(data)
        npimg = np.frombuffer(img, np.uint8)

        return cv2.imdecode(npimg, cv2.IMREAD_COLOR)

    except Exception:
        return None


def get_embedding(img):

    if img is None:
        return None

    faces = face_app.get(img)

    if len(faces) == 0:
        return None

    face = max(
        faces,
        key=lambda x: (x.bbox[2] - x.bbox[0]) * (x.bbox[3] - x.bbox[1])
    )

    return normalize(face.embedding)


def distance(p1, p2):

    return math.sqrt(
        (p1.x - p2.x) ** 2 +
        (p1.y - p2.y) ** 2
    )


def eye_aspect_ratio(landmarks, eye):

    if eye == "left":

        h1 = distance(landmarks[160], landmarks[144])
        h2 = distance(landmarks[159], landmarks[145])
        w = distance(landmarks[33], landmarks[133])

    else:

        h1 = distance(landmarks[385], landmarks[380])
        h2 = distance(landmarks[386], landmarks[374])
        w = distance(landmarks[362], landmarks[263])

    return (h1 + h2) / (2.0 * w)


def get_eye_state(img):

    if img is None:
        return None

    rgb = cv2.cvtColor(img, cv2.COLOR_BGR2RGB)

    result = face_mesh.process(rgb)

    if not result.multi_face_landmarks:
        return None

    landmarks = result.multi_face_landmarks[0].landmark

    left_ear = eye_aspect_ratio(landmarks, "left")
    right_ear = eye_aspect_ratio(landmarks, "right")

    ear = (left_ear + right_ear) / 2

    print("EAR =", round(ear, 3))

    if ear < 0.25:
        return "closed"

    return "open"


def blink_detected(states):

    opened = False
    closed = False

    for s in states:

        if s == "open":
            opened = True

        if opened and s == "closed":
            closed = True

        if opened and closed and s == "open":
            return True

    return False
def detect_head_movement(landmarks):
    global prev_nose_x, movement_count

    nose = landmarks[1]

    if prev_nose_x is not None:

        diff = abs(nose.x - prev_nose_x)

        print("Head movement:", diff)

        if diff > 0.003:
            movement_count += 1
            print("Movement Count =", movement_count)

    prev_nose_x = nose.x

# ================= API =================

@app.route("/compare", methods=["POST"])
def compare():

    try:

        global movement_count, prev_nose_x

        movement_count = 0
        prev_nose_x = None

 
        # ---------- Frames ----------
        frames_json = request.form.get("frames")

        if frames_json is None:
            return jsonify({
                "match": False,
                "error": "frames missing"
            })

        frames = json.loads(frames_json)

        # ---------- DB IMAGE ----------
        db_file = request.files.get("db")

        if db_file is None:
            return jsonify({
                "match": False,
                "error": "db image missing"
            })

        os.makedirs("temp", exist_ok=True)

        db_path = os.path.join("temp", "db.jpg")
        db_file.save(db_path)

        db_img = cv2.imread(db_path)

        faces = face_app.get(db_img)

        print("DB Faces:", len(faces))

        for i, f in enumerate(faces):
            print(f"DB Face {i} bbox:", f.bbox)

        db_emb = get_embedding(db_img)

        if db_emb is None:
            return jsonify({
                "match": False,
                "error": "Database face not detected"
            })

        # ---------- BEST SCORE ----------
        best_score = -1
        eye_states = []

        for frame in frames:

            img = base64_to_image(frame)

            # Blink Detection
            state = get_eye_state(img)
            rgb = cv2.cvtColor(img, cv2.COLOR_BGR2RGB)
            result = face_mesh.process(rgb)
 
            if result.multi_face_landmarks:
             landmarks = result.multi_face_landmarks[0].landmark
             detect_head_movement(landmarks)

            print("Eye State:", state)

            if state is not None:
                eye_states.append(state)

            # Face Matching
            emb = get_embedding(img)

            if emb is None:
                continue

            score = float(np.dot(db_emb, emb))

            print("Score:", score)

            if score > best_score:
                best_score = score

        print("Eye Sequence:", eye_states)

        live = blink_detected(eye_states)
        print("Final Movement Count =", movement_count)
        def head_ok():
         return movement_count >= 2
        if not head_ok():
         return jsonify({
        "match": False,
        "liveness": False,
        "error": "No head movement detected"
    })
        print("Blink:", live)

        if not live:
            return jsonify({
                "match": False,
                "liveness": False,
                "error": "Blink not detected"
            })

        if best_score == -1:
            return jsonify({
                "match": False,
                "error": "Live face not detected"
            })

        threshold = 0.54

        print("RETURNING =>")
        print({
            "match": best_score >= threshold,
            "score": round(best_score, 20),
            "liveness": True
        })

        return jsonify({
            "match": best_score >= threshold,
            "score": round(best_score, 20),
            "liveness": live
        })

    except Exception as e:

        print("ERROR:", e)

        return jsonify({
            "match": False,
            "error": str(e)
        })


def eyes_detected(img):

    if img is None:
        return False

    rgb = cv2.cvtColor(img, cv2.COLOR_BGR2RGB)

    result = face_mesh.process(rgb)

    if result.multi_face_landmarks:
        return True

    return False


if __name__ == "__main__":

    print("Face API Running...")

    app.run(
        host="127.0.0.1",
        port=5001,
        debug=True,
        use_reloader=False
    )