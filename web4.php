<?php
include('config.php');
session_start();

if (!isset($_SESSION["parent_id"])) {
    header("Location: login.php");
    exit();
}

$parent_id = $_SESSION['parent_id'];

// ⏱ อัปเดตสถานะ "cancelled" สำหรับการจองที่ยังไม่ชำระและเกิน 10 นาที
// ลบการจองที่ยังไม่ชำระและเกิน 10 นาที
$delete_sql = "
    DELETE FROM caregiver_booking
    WHERE payment_status = 'pending'
      AND (status = 'confirmed' OR status = 'pending')
      AND TIMESTAMPDIFF(MINUTE, created_at, NOW()) > 1440
";
$conn->query($delete_sql);


// 📥 ดึงข้อมูลการจองของผู้ปกครอง
$select_sql = "
SELECT
cb.created_at,
    cb.booking_id,
    cb.caregiver_id,
    cb.booking_type,
    cb.start_date,
    cb.start_time,
    cb.end_time,
    cb.end_date,
    cb.start_month,
    cb.end_month,
    cb.total_price,
    cb.payment_status,
    cb.status,
    c.first_name   AS caregiver_first_name,
    c.last_name    AS caregiver_last_name,
    p.first_name   AS parent_first_name,
    p.last_name    AS parent_last_name,
    pay.paymentslip
FROM caregiver_booking cb
JOIN caregiver c       ON cb.caregiver_id = c.caregiver_id
JOIN parent p          ON cb.parent_id    = p.parent_id
LEFT JOIN payments pay ON cb.booking_id   = pay.booking_id
WHERE cb.parent_id = ?
ORDER BY cb.booking_id DESC
";

$stmt = $conn->prepare($select_sql);
$stmt->bind_param("i", $parent_id);
$stmt->execute();
$result = $stmt->get_result();



$bookings = [];

while ($row = $result->fetch_assoc()) {
    $bookingTime = new DateTime($row['created_at']);
    $now = new DateTime();
    $interval = $bookingTime->diff($now);
    $minutesPassed = ($interval->days * 24 * 60) + ($interval->h * 60) + $interval->i;
    $row['isExpired'] = $minutesPassed > 10;
    $bookings[] = $row;
}

?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>การจ้างงานของคุณ</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="web4.css">
</head>
<body>
<header>
    <div class="head">
        <div class="logo">
            <img src="logo2.png" alt="Logo" height="60px">
        </div>
        <nav>
            <a href="home.php">หน้าหลัก</a>
            <a href="web2.php">ค้นหาผู้ดูแล</a>
            <a href="web3.php">การหาที่ตรงใจ</a>
            <a href="#" style="color: #ff9100;">การจ้าง</a>
            <a href="web5.php" >ประวัติการจอง</a>
        </nav>
        <div class="user-info" style="margin-top:20px;">
            <?php if (isset($_SESSION['user_name'])): ?>
                <p>สวัสดี, ผู้ปกครอง <?= htmlspecialchars($_SESSION['user_name']) ?></p>
            <?php endif; ?>
        </div>
    </div>
    <div class="icon-circle">
        <img src="get_profile_image_parent.png"
             onclick="location.href='profile.php'"
             style="width:50px; height:50px; border-radius:50%; cursor:pointer;"
             alt="โปรไฟล์">
    </div>
</header>

<h2 class="mb-3" style="margin:0 150px; margin-top:60px;">การจ้างงานของคุณ</h2>
<div class="box" style="margin:0 200px; margin-top:20px;">

<style>
    .booking-table {
        width: 100%;
        border-collapse: collapse;
        font-family: Arial, sans-serif;
        margin-top: 15px;
    }
    .booking-table th, .booking-table td {
        border: 1px solid #ccc;
        padding: 8px 12px;
        vertical-align: middle;
        text-align: left;
    }
    .booking-table th {
        background-color: #fafafa;
        color: black;
        font-weight: bold;
    }
    .booking-table tr:nth-child(even) {
        background-color: #f9f9f9;
    }
    .btn {
        padding: 6px 12px;
        font-size: 14px;
        cursor: pointer;
        border: none;
        border-radius: 4px;
        margin-right: 5px;
    }
    .btn-primary {
        background-color: #0072ff;
        color: white;
    }
    .btn-danger {
        background-color: #dc3545;
        color: white;
    }
    a {
        color: #0072ff;
        text-decoration: none;
    }
    a:hover {
        text-decoration: underline;
    }
</style>

<div class="box">
<?php if (count($bookings) > 0): ?>
    <table class="booking-table">
        <thead>
            <tr>
                <th>ผู้ปกครอง</th>
                <th>ผู้ดูแล</th>
                <th>ประเภท</th>
                <th>ช่วงเวลา</th>
                <th>จำนวนเงิน (บาท)</th>
                <th>สถานะชำระเงิน</th>
                <th>ให้ชำระก่อนเวลา</th>
                <th>จัดการ</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($bookings as $row):
    if ($row['status'] === 'rejected' || $row['payment_status'] !== 'pending') continue;

    $hasSlip = !empty($row['paymentslip']);
    $isPaid = ($row['payment_status'] === 'paid');

    $timeRange = ($row['booking_type'] === 'daily') 
        ? htmlspecialchars($row['start_date']) . " / " . htmlspecialchars($row['end_date']) 
        : htmlspecialchars($row['start_month']) . " / " . htmlspecialchars($row['end_month']);
?>
<tr>
    <td><?= htmlspecialchars($row['parent_first_name'] . ' ' . $row['parent_last_name']) ?></td>
    <td>
        <a href="caregiver_profile_show3.php?id=<?= urlencode($row['caregiver_id']) ?>">
            <?= htmlspecialchars($row['caregiver_first_name'] . ' ' . $row['caregiver_last_name']) ?>
        </a><br><small>(จองซ้ำคลิกเลย)</small>
    </td>
    <td><?= $row['booking_type'] === 'daily' ? 'รายวัน' : 'รายเดือน' ?></td>
    <td><?= $timeRange ?></td>
    <td><?= number_format($row['total_price'] ?? 0) ?></td>
    <td>⏳ รอการชำระเงิน</td>
    <td><?= htmlspecialchars($row['end_time']) ?></td>

                <td>


                    <?php if (
    $row['status'] === 'confirmed' &&
    $row['payment_status'] === 'pending'
): ?>
    <form onsubmit="openPopup(<?= (int)$row['booking_id'] ?>); return false;">
        <button type="submit" class="btn btn-primary">💳 ชำระเงิน</button>
    </form>
<?php endif; ?>

                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <div class="alert alert-warning">❌ ไม่มีข้อมูลการจ้างงาน</div>
<?php endif; ?>
</div>

<script>
function openPopup(bookingId) {
    const url = 'payment_popup.php?booking_id=' + bookingId;
    window.open(url, 'payment_popup', 'width=500,height=600,scrollbars=yes');
}
</script>

</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
