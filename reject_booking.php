<?php
// reject_booking.php
require 'config.php';

if (isset($_GET['booking_id'])) {
    $booking_id = $_GET['booking_id'];

    $stmt = $pdo->prepare("UPDATE caregiver_booking SET status = 'rejected' WHERE booking_id = ?");
    if ($stmt->execute([$booking_id])) {
        header("Location: booking.php?msg=reject_success");
        exit;
    } else {
        echo "เกิดข้อผิดพลาดในการปฏิเสธการจอง";
    }
} else {
    echo "ข้อมูลไม่ถูกต้อง";
}
