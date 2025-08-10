<?php
session_start();
require_once 'config.php';

// ตรวจสอบว่า parent_id มีอยู่ใน session หรือไม่
if (!isset($_SESSION['parent_id'])) {
    die("ผู้ปกครองไม่ได้ล็อกอิน");
}
$parent_id = $_SESSION['parent_id'];

// รับค่า caregiver_id
$caregiver_id = $_GET['caregiver_id'] ?? $_POST['caregiver_id'] ?? null;
if (!$caregiver_id || !is_numeric($caregiver_id)) {
    die("ไม่พบข้อมูลผู้ดูแล");
}
$caregiver_id = intval($caregiver_id);

// รับข้อมูลจากฟอร์ม
$children_count = isset($_POST['children_count']) ? intval($_POST['children_count']) : null;
$booking_type   = $_POST['booking_type'] ?? null;
$status         = 'pending';

if (!$children_count || $children_count < 1) {
    die("จำนวนเด็กไม่ถูกต้อง");
}

// กำหนดค่าตามประเภทการจ้าง
if ($booking_type === 'daily') {
    $start_date = $_POST['start_date'] ?? null;
    $end_date   = $_POST['end_date'] ?? null;
    $start_time = $_POST['start_time'] ?? null;
    $end_time   = $_POST['end_time'] ?? null;
    $start_month = null;
    $end_month   = null;

    if (!$start_date || !$end_date) {
        die("กรุณากรอกวันที่เริ่มต้นและสิ้นสุด");
    }
} elseif ($booking_type === 'monthly') {
    $start_date = null;
    $end_date   = null;
    $start_time = null;
    $end_time   = null;
    $start_month = $_POST['start_month'] ?? null;
    $end_month   = $_POST['end_month'] ?? null;

    if (!$start_month || !$end_month) {
        die("กรุณากรอกเดือนที่เริ่มต้นและสิ้นสุด");
    }
} else {
    die("ประเภทการจ้างไม่ถูกต้อง");
}

try {
    // ตรวจสอบ caregiver_id
    $stmt_check = $pdo->prepare("SELECT caregiver_id, price FROM caregiver WHERE caregiver_id = :caregiver_id");
    $stmt_check->bindParam(':caregiver_id', $caregiver_id, PDO::PARAM_INT);
    $stmt_check->execute();
    $caregiver = $stmt_check->fetch(PDO::FETCH_ASSOC);

    if (!$caregiver) {
        die("ไม่พบผู้ดูแลที่เลือก");
    }

    $price = $caregiver['price'];

    // คำนวณค่าใช้จ่าย
    if ($booking_type === 'daily') {
        $start = new DateTime($start_date);
        $end = new DateTime($end_date);
        $days = $start->diff($end)->days + 1;

        $total_price = $price * $days;
        if ($children_count > 1) {
            $total_price += ($children_count - 1) * 100 * $days;
        }
    } elseif ($booking_type === 'monthly') {
        $startMonth = new DateTime($start_month . "-01");
        $endMonth = new DateTime($end_month . "-01");
        $monthDiff = $endMonth->diff($startMonth)->m + 1;

        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $startMonth->format('m'), $startMonth->format('Y'));
        
        $total_price = $price * $daysInMonth * $monthDiff * 0.8;
        if ($children_count > 1) {
            $total_price += ($children_count - 1) * 100 * $daysInMonth * $monthDiff * 0.8;
        }
    }

    
$address = $_POST['address'] ?? null;
if (!$address) {
    die("กรุณากรอกที่อยู่สำหรับการดูแล");
}
    // INSERT ข้อมูลลงตาราง caregiver_booking
$stmt = $pdo->prepare("INSERT INTO caregiver_booking 
    (parent_id, caregiver_id, children_count, address, booking_type, start_date, end_date, start_time, end_time, start_month, end_month, Status, total_price)
    VALUES (:parent_id, :caregiver_id, :children_count, :address, :booking_type, :start_date, :end_date, :start_time, :end_time, :start_month, :end_month, :status, :total_price)");

$stmt->execute([
    ':parent_id' => $parent_id,
    ':caregiver_id' => $caregiver_id,
    ':children_count' => $children_count,
    ':address' => $address,  // เพิ่มบรรทัดนี้
    ':booking_type' => $booking_type,
    ':start_date' => $start_date,
    ':end_date' => $end_date,
    ':start_time' => $start_time,
    ':end_time' => $end_time,
    ':start_month' => $start_month,
    ':end_month' => $end_month,
    ':status' => $status,
    ':total_price' => $total_price
]);


    header("Location: home.php");
    exit();
} catch (PDOException $e) {
    echo "เกิดข้อผิดพลาด: " . $e->getMessage();
}
?>

