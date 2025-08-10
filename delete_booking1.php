<?php
include('config.php'); // เชื่อมต่อฐานข้อมูล

// ตรวจสอบว่าได้รับ booking_id หรือไม่
if (!isset($_GET['booking_id']) || empty($_GET['booking_id'])) {
    die("ไม่พบข้อมูลการจองที่ต้องการลบ");
}

$booking_id = $_GET['booking_id'];

// ลบการจอง
$sql_delete = "DELETE FROM caregiver_booking WHERE booking_id = ?";
$stmt_delete = $conn->prepare($sql_delete);
$stmt_delete->bind_param("i", $booking_id);

if ($stmt_delete->execute()) {
    // ถ้าลบสำเร็จ, ไปหน้า admin booking view
    header("Location: view_booking.php?message=ลบสำเร็จ");
    exit();
} else {
    echo "เกิดข้อผิดพลาดในการลบการจอง";
}

$stmt_delete->close();
$conn->close();
?>
