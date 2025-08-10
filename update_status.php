<?php
// เชื่อมต่อฐานข้อมูล
include('config.php');

if (isset($_GET['booking_id'])) {
    $booking_id = $_GET['booking_id'];

    // อัปเดตสถานะและบันทึก created_at
    $update_sql = "
        UPDATE caregiver_booking 
        SET status = 'confirmed',
            created_at = NOW() 
        WHERE booking_id = ?
    ";
    
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param('i', $booking_id);
    
    if ($stmt->execute()) {
        echo "สถานะได้รับการยืนยันแล้ว";
    } else {
        echo "เกิดข้อผิดพลาดในการอัปเดตสถานะ";
    }
    
    $stmt->close();

    // ย้ายไปหน้า dashboard
    header("Location: caregiver_dashboard.php");
    exit();
} else {
    echo "ไม่พบข้อมูลการจอง";
}
?>
