<?php
include('config.php'); // เชื่อมต่อฐานข้อมูล

session_start(); // เปิดใช้งาน session

// ตรวจสอบว่า parent ได้ล็อกอินแล้วหรือไม่
if (!isset($_SESSION["parent_id"])) {
    header("Location: login.php");
    exit();
}

// ตรวจสอบว่าได้รับ booking_id หรือไม่
if (!isset($_POST['booking_id']) || empty($_POST['booking_id'])) {
    die("ไม่พบข้อมูลการจองที่ต้องการยกเลิก");
}

$booking_id = $_POST['booking_id'];
$parent_id = $_SESSION['parent_id']; // ID ของผู้ปกครองที่ล็อกอิน

// ตรวจสอบว่าการจองนี้เป็นของผู้ปกครองที่ล็อกอินหรือไม่
$sql_check = "SELECT booking_id FROM caregiver_booking WHERE booking_id = ? AND parent_id = ?";
$stmt_check = $conn->prepare($sql_check);
$stmt_check->bind_param("ii", $booking_id, $parent_id);
$stmt_check->execute();
$result_check = $stmt_check->get_result();

if ($result_check->num_rows === 0) {
    die("ไม่พบการจองนี้หรือไม่ใช่ของคุณ");
}

// ลบการจอง
$sql_delete = "DELETE FROM caregiver_booking WHERE booking_id = ?";
$stmt_delete = $conn->prepare($sql_delete);
$stmt_delete->bind_param("i", $booking_id);

if ($stmt_delete->execute()) {
    // ถ้าลบสำเร็จ, ไปหน้า home
    header("Location: web4.php");
    exit();
} else {
    echo "เกิดข้อผิดพลาดในการลบการจอง";
}

$stmt_delete->close();
$conn->close();
?>
