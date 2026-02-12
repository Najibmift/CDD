import cv2
import os
import time
import datetime
import requests
import threading
import numpy as np
from ultralytics import YOLO
from keras.models import load_model
import sys

# ==== Load Model ====
try:
    model_deteksi_kerusakan = YOLO("./Damage_Detect/yolov11n_AdamW_300_lr0=0.0001/weights/best.pt")
    body_container = YOLO('./Damage_Detect/model_kontainer/weights/best.pt')
    container_detector = YOLO('./OCR/hasil_training/deteksi.pt')
    char_detector = YOLO('./OCR/hasil_training/segmentasi.pt')
    resnet_model = load_model('./OCR/hasil_training/model_ocr_28x28.keras')
except Exception as e:
    print(f"❌ Error saat memuat model: {e}")
    sys.exit(1)

# ==== Konfigurasi ====
folder_path = "./GATE1"
url_api = "http://localhost:8080/api/upload"
lokasi_gate = "Gate 1"
characters = list("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ")

id_to_label = {
    0: "Damage"
}

# <<< PERUBAHAN DI SINI: Variabel untuk menyimpan waktu inferensi >>>
inference_times = []


# ==== Helper ====
def preprocess_char(img):
    if len(img.shape) == 3:
        gray = cv2.cvtColor(img, cv2.COLOR_BGR2GRAY)
    else:
        gray = img
    _, thresh = cv2.threshold(gray, 0, 255, cv2.THRESH_BINARY_INV + cv2.THRESH_OTSU)
    resized = cv2.resize(thresh, (28, 28))
    normalized = resized.astype("float32") / 255.0
    img_array = np.expand_dims(normalized, axis=-1)
    return np.expand_dims(img_array, axis=0), resized


def print_temp_status(message, duration=2):
    sys.stdout.write(message)
    sys.stdout.flush()
    time.sleep(duration)
    sys.stdout.write('\r' + ' ' * (len(message) + 5) + '\r')
    sys.stdout.flush()


def recognize_container_number(frame):
    print("🔍 Mendeteksi nomor kontainer...")
    results = container_detector(frame, verbose=False)
    boxes = [b for b in results[0].boxes if b.conf.cpu().numpy()[0] > 0.4]

    if not boxes:
        print_temp_status("📊 [OCR selesai - nomor tidak ditemukan]")
        return "UNKNOWN"

    full_number = ""
    for box in boxes:
        x1, y1, x2, y2 = map(int, box.xyxy[0].cpu().numpy())
        roi = frame[y1:y2, x1:x2]

        if roi.shape[0] > roi.shape[1]:
            roi = cv2.rotate(roi, cv2.ROTATE_90_CLOCKWISE)

        char_results = char_detector(roi, verbose=False)
        char_boxes = sorted(char_results[0].boxes.xyxy.cpu().numpy(), key=lambda b: b[0])

        recognized_chars = ""
        for char_box in char_boxes:
            cx1, cy1, cx2, cy2 = map(int, char_box)
            char_img = roi[cy1:cy2, cx1:cx2]

            if char_img.size == 0:
                continue

            input_tensor, _ = preprocess_char(char_img)
            preds = resnet_model.predict(input_tensor, verbose=0)
            confidence = np.max(preds)
            if confidence >= 0.7:
                label = characters[np.argmax(preds)]
                recognized_chars += label

        full_number += recognized_chars

    print_temp_status("📊 [OCR selesai - nomor kontainer dibaca]")
    return full_number if full_number else "UNKNOWN"


def upload_frame(image_bytes, waktu_masuk, tempat, jenis_kerusakan, nomor_kontainer):
    files = {'gambar': ('deteksi.jpg', image_bytes, 'image/jpeg')}
    data = {
        "waktu_masuk": waktu_masuk,
        "tempat": tempat,
        "jenis_kerusakan": jenis_kerusakan,
        "nomor_kontainer": nomor_kontainer
    }
    try:
        response = requests.post(url_api, data=data, files=files, timeout=10)
        if response.status_code == 200:
            print("✅ Upload sukses!")
        else:
            print(f"❌ Upload gagal: {response.status_code} - {response.text}")
    except requests.exceptions.RequestException as e:
        print(f"❌ Error upload: {e}")


def process_upload_thread(image_bytes, waktu_masuk, tempat, jenis_kerusakan, nomor_kontainer):
    thread = threading.Thread(
        target=upload_frame,
        args=(image_bytes, waktu_masuk, tempat, jenis_kerusakan, nomor_kontainer),
        daemon=True
    )
    thread.start()


def detect_image(file_path):
    try:
        print(f"\n📸 Memproses gambar: {os.path.basename(file_path)}")
        frame = cv2.imread(file_path)
        if frame is None:
            print(f"❌ Gagal membaca gambar: {file_path}")
            return

        print("🔍 Mendeteksi kontainer (validasi body)...")
        kontainer_result = body_container(frame, verbose=False)
        boxes = kontainer_result[0].boxes

        if not boxes or len(boxes) == 0:
            print_temp_status("📛 Kontainer tidak terdeteksi! Lewati gambar.")
            return

        x1, y1, x2, y2 = map(int, boxes[0].xyxy[0].cpu().numpy())
        cropped_frame = frame[y1:y2, x1:x2]

        print("🔍 Mendeteksi kerusakan kontainer (pada area crop)...")

        # <<< PERUBAHAN DI SINI: Catat waktu sebelum inferensi >>>
        start_inference_time = time.time()

        results = model_deteksi_kerusakan.predict(source=cropped_frame, conf=0.15, save=False, verbose=False)

        # <<< PERUBAHAN DI SINI: Catat waktu setelah inferensi dan hitung durasi >>>
        end_inference_time = time.time()
        inference_duration = (end_inference_time - start_inference_time) * 1000  # Ubah ke milidetik
        inference_times.append(inference_duration)
        print(f"⏱️  Waktu inferensi gambar ini: {inference_duration:.2f} ms")

        boxes = results[0].boxes

        if boxes and len(boxes) > 0:
            print_temp_status("📊 [YOLO selesai - kerusakan terdeteksi]")
            annotated_frame = results[0].plot()
            cls_ids = boxes.cls.cpu().numpy().astype(int)
            labels_detected = [id_to_label.get(cls_id, "Unknown") for cls_id in cls_ids]
            jenis_kerusakan_terdeteksi = ", ".join(sorted(set(labels_detected)))

            nomor_kontainer = recognize_container_number(frame.copy())

            _, img_encoded = cv2.imencode('.jpg', annotated_frame)
            img_bytes = img_encoded.tobytes()

            print(f"✅ Nomor kontainer: {nomor_kontainer}")
            print(f"⚠️ Kerusakan: {jenis_kerusakan_terdeteksi}")
            print("🚀 Upload ke server...")

            process_upload_thread(
                image_bytes=img_bytes,
                waktu_masuk=datetime.datetime.now().strftime("%Y-%m-%d %H:%M:%S"),
                tempat=lokasi_gate,
                jenis_kerusakan=jenis_kerusakan_terdeteksi,
                nomor_kontainer=nomor_kontainer
            )
        else:
            print_temp_status("📊 [YOLO selesai - tidak ada kerusakan]")
            print("ℹ️ Tidak ada kerusakan terdeteksi.")
    except Exception as e:
        import traceback
        print(f"❌ Error saat deteksi: {e}")
        traceback.print_exc()


# ==== Main Loop ====
if not os.path.exists(folder_path):
    print(f"📂 Membuat folder monitoring: {folder_path}")
    os.makedirs(folder_path)
else:
    print(f"📂 Monitoring folder: {folder_path}")

print("🟢 Sistem aktif. Letakkan gambar (.jpg/.jpeg/.png) di folder untuk deteksi.")

processed_files = set(os.listdir(folder_path))

while True:
    try:
        current_files = set(os.listdir(folder_path))
        new_files = current_files - processed_files

        if new_files:
            # <<< PERUBAHAN DI SINI: Catat waktu awal saat batch baru terdeteksi >>>
            batch_start_time = time.time()
            print("\n" + "=" * 50)
            print(f"📦 Batch baru terdeteksi dengan {len(new_files)} gambar. Memulai proses...")

            for filename in new_files:
                if filename.lower().endswith(('.jpg', '.jpeg', '.png')):
                    file_path = os.path.join(folder_path, filename)
                    time.sleep(0.5)
                    detect_image(file_path)

            # <<< PERUBAHAN DI SINI: Hitung dan tampilkan hasil waktu setelah batch selesai >>>
            batch_end_time = time.time()
            total_batch_duration = batch_end_time - batch_start_time

            print("\n" + "=" * 50)
            print("📈 HASIL PENGUJIAN WAKTU")
            print("=" * 50)
            print(f"  - Total Waktu Proses per Batch ({len(new_files)} gambar): {total_batch_duration:.2f} detik")
            if inference_times:
                avg_inference_time = sum(inference_times) / len(inference_times)
                print(f"  - Waktu Inferensi Rata-rata per Gambar: {avg_inference_time:.2f} ms")
                inference_times.clear()  # Reset untuk batch selanjutnya
            print("=" * 50 + "\n")

        processed_files = current_files
        time.sleep(1)
    except KeyboardInterrupt:
        print("\n🛑 Sistem dihentikan oleh pengguna.")
        break
    except Exception as e:
        print(f"❌ Error utama: {e}")
        time.sleep(5)