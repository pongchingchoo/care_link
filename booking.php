<?php
include('config.php'); // เชื่อมต่อฐานข้อมูล

session_start(); // เปิดใช้งาน session

// ตรวจสอบว่า caregiver ได้ล็อกอินแล้วหรือไม่
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$caregiver_id = $_SESSION['user_id'];

// ดึงข้อมูลการจองที่เกี่ยวข้องกับ caregiver นี้
$sql = "SELECT cb.*, p.first_name, p.last_name, p.phone_number, p.email, cb.status, cb.address
        FROM caregiver_booking cb
        LEFT JOIN parent p ON cb.parent_id = p.parent_id
        WHERE cb.caregiver_id = ?
        ORDER BY cb.booking_id DESC";


$stmt = $pdo->prepare($sql);
$stmt->execute([$caregiver_id]);
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>การจ้างงานของคุณ</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="booking.css">
</head>

<body>
<header>
    <div class="head">
        <div class="logo">
            <img src="logo2.png" alt="Logo" height="60px">
        </div>
        <nav>
            <a href="caregiver_dashboard.php">หน้าหลัก</a>
            <a href="#" onclick="location.href='contract.php'">การทำสัญญา</a>
            <a class="test_caregiver" href="test_caregiver.php">ทำแบบทดสอบ</a>
            <a class="test_caregiver" href="booking.php" style="color: #ff9100;">แจ้งเตือนการจ้าง</a>
            <a class="test_caregiver" href="working.php">การทำงาน</a>
        </nav>
        <div class="user-info" style="margin-top: 20px;">
            <?php if (isset($_SESSION['user_name'])): ?>
                <p>สวัสดี, ผู้ดูแล <?= htmlspecialchars($_SESSION['user_name']) ?></p>
            <?php endif; ?>
        </div>
        
    </div>

    <div class="icon-circle">
        <img src="get_profile_image_parent.png" onclick="location.href='caregiver_profile.php'" style="width:50px; height:50px; border-radius:50%; cursor:pointer;" alt="โปรไฟล์">
    </div>
</header>

<h2 class="mb-3" style="margin: 0 150px; margin-top: 60px;">🔔 การแจ้งเตือนการจ้างงาน</h2>

<style>
    table.booking-table {
        width: 100%;
        border-collapse: collapse;
        font-family: Arial, sans-serif;
        margin-top: 15px;
    }

    table.booking-table th, table.booking-table td {
        border: 1px solid #ccc;
        padding: 10px;
        vertical-align: top;
        font-size: 16px;
    }

    table.booking-table th {
        background-color: #f2f2f2;
        text-align: center;
    }

    table.booking-table tr:nth-child(even) {
        background-color: #fafafa;
    }

    .btn {
        padding: 2px 6px;
        text-decoration: none;
        color: white;
        border-radius: 3px;
        font-size: 14px;
    }

    .btn-primary {
        background-color: #007bff;
    }

    .btn-danger {
        background-color: #dc3545;
    }

    .text-center {
        text-align: center;
    }

    .text-muted {
        color: #777;
    }
</style>

<div class="box">
<?php if (!empty($result)): ?>
    <table class="booking-table">
        <thead>
            <tr>
                <th>ผู้ปกครอง</th>
                <th>ประเภท</th>
                <th>ช่วงเวลา</th>
                <th>เวลา (รายวัน)</th>
                <th>ที่อยู่</th>
                <th>จำนวนเด็ก</th>
                <th>ค่าใช้จ่าย</th>
                <th>ติดต่อ</th>
                <th>สถานะ</th>
                <th>การจัดการ</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($result as $row): ?>
            <?php
            // ข้ามรายการที่ถูกยืนยันหรือปฏิเสธแล้ว
            if (in_array(($row['status'] ?? ''), ['rejected', 'confirmed'])) continue;

            $isDaily = $row['booking_type'] === 'daily';
            $range = $isDaily
    ? ($row['start_date'] . " " . $row['end_date'])
    : ($row['start_month'] . " " . $row['end_month']);

$timeRange = $isDaily
    ? ($row['start_time'] . " - " . $row['end_time'])
    : '-';
?>

            <tr>
                <td>
                    <a href="profile_parent.php?id=<?= urlencode($row['parent_id']) ?>">
                        <?= htmlspecialchars($row['first_name'] . " " . $row['last_name']) ?>
                    </a>
                </td>
                <td class="text-center"><?= $isDaily ? 'รายวัน' : 'รายเดือน' ?></td>
                <td class="text-center"><?= htmlspecialchars($range) ?></td>
                <td class="text-center"><?= htmlspecialchars($timeRange) ?></td>
                <td><?= htmlspecialchars($row['address'] ?? 'ไม่ระบุ') ?></td>
                <td class="text-center"><?= htmlspecialchars($row['children_count'] ?? '0') ?> คน</td>
                <td class="text-center"><?= number_format($row['total_price'] ?? 0) ?> บาท</td>
                <td>
                    <?= htmlspecialchars($row['phone_number'] ?? 'ไม่มีข้อมูล') ?><br>
                    <span class="text-muted">(<?= htmlspecialchars($row['email'] ?? 'ไม่มีข้อมูล') ?>)</span>
                </td>
                <td class="text-center">
                    <strong class="text-muted">⏳ รอการยืนยัน</strong>
                </td>
                <td class="text-center">
                    <a class="btn btn-primary" href="update_status.php?booking_id=<?= $row['booking_id'] ?>">✅ ยืนยัน</a><br><br>
                    <a class="btn btn-danger" href="reject_booking.php?booking_id=<?= $row['booking_id'] ?>" onclick="return confirm('คุณแน่ใจหรือไม่ว่าต้องการปฏิเสธการจองนี้?')">❌ ปฏิเสธ</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>ไม่พบรายการการจอง</p>
<?php endif; ?>
</div>

</body>
</html>
