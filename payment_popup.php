<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// --- เชื่อมต่อฐานข้อมูล ---
$conn = new mysqli("localhost", "root", "", "care_link");
if ($conn->connect_error) {
    die("เชื่อมต่อฐานข้อมูลล้มเหลว: " . $conn->connect_error);
}

// รองรับทั้ง GET และ POST ในรอบแรก (สำหรับโหลดหน้า) และ POST เมื่อส่งฟอร์ม
if (!isset($_GET['booking_id'])) {
    die("<script>alert('Booking ID ไม่ถูกต้อง'); window.close();</script>");
}
$booking_id = (int) $_GET['booking_id'];

// กรณีโหลด popup จาก form (action มาจากหน้าแม่)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['booking_id'])) {
    $booking_id = (int) $_POST['booking_id'];
}

// หรือกรณีโหลดผ่าน URL (GET)
elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['booking_id'])) {
    $booking_id = (int) $_GET['booking_id'];
}

if (!$booking_id) {
    die("<script>alert('Booking ID ไม่ถูกต้อง'); window.close();</script>");
}

// ตรวจสอบว่า booking_id มีอยู่ในฐานข้อมูล
$chkBooking = $conn->prepare("SELECT 1 FROM caregiver_booking WHERE booking_id = ?");
$chkBooking->bind_param("i", $booking_id);
$chkBooking->execute();
$chkBooking->store_result();

if ($chkBooking->num_rows === 0) {
    die("<script>alert('ไม่พบการจองหมายเลขนี้'); window.close();</script>");
}
$chkBooking->close();

// หากมีการอัปโหลดสลิป
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['payment_slip'], $_POST['payment_date'], $_POST['payment_time'], $_POST['amount'])) {
    $payment_date = $_POST['payment_date'] ?? '';
    $payment_time = $_POST['payment_time'] ?? '';
    $amount = $_POST['amount'] ?? '';

    // ลบการเช็ค $address ออกเพราะลบไปแล้ว
    if (empty($payment_date) || empty($payment_time) || empty($amount)) {
        echo "<script>alert('กรุณากรอกข้อมูลให้ครบ');</script>";
    } else {
        if ($_FILES['payment_slip']['error'] === UPLOAD_ERR_OK) {
            $fileData = file_get_contents($_FILES['payment_slip']['tmp_name']);

            $chk = $conn->prepare("SELECT payments_id FROM payments WHERE booking_id = ?");
            $chk->bind_param("i", $booking_id);
            $chk->execute();
            $chk->store_result();

            if ($chk->num_rows > 0) {
                $sql = "UPDATE payments 
                        SET paymentslip = ?, payment_date = ?, payment_time = ?, amount = ?
                        WHERE booking_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("sssdi", $fileData, $payment_date, $payment_time, $amount, $booking_id);
            } else {
                $sql = "INSERT INTO payments (booking_id, paymentslip, payment_date, payment_time, amount) 
                        VALUES (?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("isssd", $booking_id, $fileData, $payment_date, $payment_time, $amount);
            }

            if ($stmt->execute()) {
                echo "<script>alert('บันทึกข้อมูลการชำระเงินสำเร็จ'); window.close();</script>";
                exit();
            } else {
                echo "<script>alert('เกิดข้อผิดพลาดในการบันทึก');</script>";
            }
            $stmt->close();
        } else {
            echo "<script>alert('เกิดข้อผิดพลาดในการอัปโหลดไฟล์');</script>";
        }
    }
}
?>


<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ชำระเงิน</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            text-align: center;
        }
        .container {
            max-width: 500px;
            margin: 0 auto;
        }
        .qr-code {
            margin-bottom: 20px;
        }
        .upload-section {
            margin-top: 20px;
        }
        .upload-section input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
        }
        .btn {
            padding: 10px 20px;
            background-color: #28a745;
            color: white;
            border: none;
            cursor: pointer;
        }
        .btn:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>

<div class="container">
    <h3>กรุณาสแกน QR Code เพื่อทำการชำระเงิน</h3>

    <div class="qr-code">
        <img src="QR.jpg" alt="QR Code" style="width: 100%; max-width: 250px;">
    </div>

    <h3>อัปโหลดสลิปการโอนเงิน</h3>
    <form method="POST" enctype="multipart/form-data">
        <div>
        <label for="payment_date">วันที่โอนเงิน:</label><br>
        <input type="date" id="payment_date" name="payment_date" required style="padding: 8px; margin-bottom: 10px;" min="<?= date('Y-m-d') ?>">
    </div>
    <div>
    <label for="payment_time">เวลาที่โอนเงิน:</label><br>
    <input type="time" id="payment_time" name="payment_time" required style="padding: 8px; margin-bottom: 10px;">
</div>
<div>
    <label for="amount">จำนวนเงินที่โอน (บาท):</label><br>
    <input type="number" step="0.01" id="amount" name="amount" placeholder="เช่น 500.00" required style="padding: 8px; margin-bottom: 10px;">
</div>
        <div>
            <input type="file" name="payment_slip" accept="image/*" required onchange="preview(event)">
        </div>
        <div id="preview" style="margin:10px 0;">
            <img id="img" style="max-width:100%; display:none;">
        </div>
        <button type="submit" class="btn">อัปโหลดสลิป</button>
    </form>
</div>
<script>
    function preview(e) {
        const file = e.target.files[0], img = document.getElementById('img');
        if (file) {
            const reader = new FileReader();
            reader.onload = ev => { 
                img.src = ev.target.result; 
                img.style.display = 'block'; 
            };
            reader.readAsDataURL(file);
        }
    }
</script>
</body>
</html>
<!-- 45/1 หมู่5 ต.ถนนขาด อ.เมือง จ. นครปฐม -->