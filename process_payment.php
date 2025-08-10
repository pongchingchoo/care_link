<?php
// เชื่อมต่อกับฐานข้อมูล
include('config.php'); 

// ตรวจสอบว่าไฟล์ได้รับการอัปโหลดหรือไม่
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['payment_slip']) && $_FILES['payment_slip']['error'] === UPLOAD_ERR_OK) {
    
    // รับข้อมูลจากฟอร์ม
    $booking_id = $_POST['booking_id'];  // booking_id ที่ถูกส่งมาจากฟอร์ม
    $payment_amount = 500.00;  // ตัวอย่างจำนวนเงิน (สามารถดึงจากฐานข้อมูลได้)
    $payment_method = 'QR Code';  // วิธีการชำระเงิน (สามารถดึงจากฐานข้อมูลได้)
    $payment_status = 'paid';  // สถานะการชำระเงิน

    // ตั้งชื่อไฟล์สลิปที่อัปโหลด
    $payment_slip = 'uploads/' . basename($_FILES['payment_slip']['name']);
    
    // ย้ายไฟล์ที่อัปโหลดไปยังโฟลเดอร์ที่ต้องการ
    if (move_uploaded_file($_FILES['payment_slip']['tmp_name'], $payment_slip)) {

        // บันทึกข้อมูลการชำระเงินลงในตาราง 'pay'
        $sql = "INSERT INTO pay (booking_id, payment_amount, payment_method, payment_status, payment_slip)
                VALUES (?, ?, ?, ?, ?)";

        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("idsss", $booking_id, $payment_amount, $payment_method, $payment_status, $payment_slip);
            $stmt->execute();
            $stmt->close();

            // อัปเดตสถานะการชำระเงินในตาราง caregiver_booking
            $update_sql = "UPDATE caregiver_booking SET payment_status = ? WHERE booking_id = ?";
            if ($update_stmt = $conn->prepare($update_sql)) {
                $update_stmt->bind_param("si", $payment_status, $booking_id);
                $update_stmt->execute();
                $update_stmt->close();
            }

            // แจ้งให้ผู้ใช้ทราบว่าการชำระเงินสำเร็จ
            echo "<script>alert('การชำระเงินสำเร็จ'); window.close();</script>";
        } else {
            // หากไม่สามารถบันทึกข้อมูลลงในฐานข้อมูลได้
            echo "<script>alert('เกิดข้อผิดพลาดในการบันทึกข้อมูล');</script>";
        }
    } else {
        // หากเกิดข้อผิดพลาดในการอัปโหลดไฟล์
        echo "<script>alert('ไม่สามารถอัปโหลดสลิปการโอนเงินได้');</script>";
    }
} else {
    // หากไม่มีข้อมูลที่ถูกส่งมา
    echo "<script>alert('ไม่มีข้อมูลการชำระเงิน'); window.close();</script>";
}

$conn->close();
?>

