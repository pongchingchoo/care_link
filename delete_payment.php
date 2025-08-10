<?php
include('config.php');
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['booking_id'])) {
    $booking_id = intval($_POST['booking_id']);

    // ตรวจสอบว่า booking_id นี้มีอยู่จริง
    $chk = $conn->prepare("SELECT COUNT(*) FROM caregiver_booking WHERE booking_id = ?");
    $chk->bind_param("i", $booking_id);
    $chk->execute();
    $chk->bind_result($count);
    $chk->fetch();
    $chk->close();

    if ($count > 0) {
        $conn->begin_transaction();
        try {
            // ลบจากตาราง payments
            $delPay = $conn->prepare("DELETE FROM payments WHERE booking_id = ?");
            $delPay->bind_param("i", $booking_id);
            if (!$delPay->execute()) {
                throw new Exception("ลบจาก payments ไม่สำเร็จ: " . $delPay->error);
            }
            $delPay->close();

            // ลบจากตาราง caregiver_booking
            $delBooking = $conn->prepare("DELETE FROM caregiver_booking WHERE booking_id = ?");
            $delBooking->bind_param("i", $booking_id);
            if (!$delBooking->execute()) {
                throw new Exception("ลบจาก caregiver_booking ไม่สำเร็จ: " . $delBooking->error);
            }
            $delBooking->close();

            $conn->commit();
            $_SESSION['message'] = "✅ ลบข้อมูลเรียบร้อยแล้ว (Booking ID: $booking_id)";
        } catch (Exception $e) {
            $conn->rollback();
            $_SESSION['error'] = "❌ เกิดข้อผิดพลาดในการลบ: " . $e->getMessage();
        }
    } else {
        $_SESSION['error'] = "❗ ไม่พบรายการจองที่ต้องการลบ (Booking ID: $booking_id)";
    }
} else {
    $_SESSION['error'] = "ไม่ได้ส่งข้อมูล booking_id มา";
}

// กลับไปหน้าก่อนหน้า
header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? 'index.php'));
exit();
?>

