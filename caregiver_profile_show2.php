<?php
// เชื่อมต่อฐานข้อมูล
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "care_link";

$conn = new mysqli($servername, $username, $password, $dbname);
$conn->set_charset("utf8mb4");

// ตรวจสอบว่าได้รับ caregiver_id จาก URL หรือไม่
if (!isset($_GET["id"])) {
    die("ไม่พบข้อมูลผู้ดูแล");
}

$caregiver_id = $_GET["id"];

// ใช้ prepared statement เพื่อดึงข้อมูลผู้ดูแลจากฐานข้อมูล
$stmt = $conn->prepare("SELECT * FROM caregiver WHERE caregiver_id = ?");
$stmt->bind_param("i", $caregiver_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

$booking_stmt = $conn->prepare("SELECT * FROM caregiver_booking WHERE caregiver_id = ? ORDER BY booking_id DESC");
$booking_stmt->bind_param("i", $caregiver_id);
$booking_stmt->execute();
$booking_result = $booking_stmt->get_result();

// ประมวลผลวันทำงาน
$working_days = explode(',', $row["working_days"]);
$working_days = array_map('trim', $working_days);

$weekdays = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
$weekends = ['Saturday', 'Sunday'];

if (count($working_days) === 7) {
    $display = "ทำงานทุกวัน";
} elseif (empty(array_diff($weekdays, $working_days)) && count($working_days) === 5) {
    $display = "ทำงานวันปกติ (จันทร์-ศุกร์)";
} elseif (empty(array_diff($weekends, $working_days)) && count($working_days) === 2) {
    $display = "ทำงานเฉพาะวันหยุดสุดสัปดาห์ (เสาร์-อาทิตย์)";
} else {
    $display = "" . implode(', ', $working_days);
}


// ปิดการเชื่อมต่อฐานข้อมูล
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>โปรไฟล์ผู้ดูแล</title>
<style>
    /* ฟอนต์ระบบดูสะอาดตา */
    body {
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue",
                     Arial, sans-serif;
        background: #f9fafb;          /* เทาอ่อนออกขาว สบายตา */
        margin: 0;
        padding: 24px;
        line-height: 1.55;
        color: #374151;               /* เทาเข้มนุ่ม ๆ */
    }

    /* กล่องโปรไฟล์ */
    .profile-container {
        max-width: 540px;
        margin: auto;
        background: #ffffff;
        padding: 32px 28px;
        border-radius: 16px;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);  /* เงานุ่ม ๆ */
        text-align: center;
    }

    /* รูปโปรไฟล์ */
    .profile-container img {
        width: 160px;
        height: 160px;
        border-radius: 50%;
        object-fit: cover;
        border: 4px solid #e5e7eb;   /* กรอบเทาอ่อน */
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
    }

    h2 {
        margin: 18px 0 6px;
        font-size: 1.45rem;
        color: #111827;              /* ดำเทาอ่านง่าย */
    }

    p {
        text-align: left;
        margin: 4px 0;
        font-size: 0.95rem;
    }

    /* ป้ายหัวข้อย่อย */
    p strong {
        color: #111827;
        font-weight: 600;
    }

    /* ปุ่มลิงก์ */
    a.button {
        display: inline-block;
        margin: 16px 6px 0;
        padding: 10px 22px;
        border-radius: 8px;
        text-decoration: none;
        font-size: 0.95rem;
        font-weight: 500;
        transition: background 0.25s, transform 0.15s;
    }

    /* โทนหลักและสำรอง */
    a.button:first-of-type {          /* กลับหน้าหลัก */
        background: #f3f4f6;
        color: #374151;
    }
    a.button:last-of-type {           /* จองบริการ */
        background: #3b82f6;
        color: #ffffff;
    }

    a.button:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }

    a.button:first-of-type:hover {
        background: #e5e7eb;
    }
    a.button:last-of-type:hover {
        background: #2563eb;
    }

    /* รายการวันที่ถูกจอง */
    .booking-dates {
        margin-top: 28px;
        text-align: left;
    }

    .booking-dates h3 {
        margin-bottom: 10px;
        font-size: 1.1rem;
        color: #111827;
    }

    .booking-dates ul {
        list-style: none;
        padding-left: 0;
        margin: 0;
    }

    .booking-dates li {
        background: #f3f4f6;
        border-radius: 8px;
        padding: 8px 12px;
        margin-bottom: 6px;
        font-size: 0.9rem;
    }

    /* รองรับหน้าจอเล็ก */
    @media (max-width: 480px) {
        body { padding: 16px; }
        .profile-container { padding: 24px 20px; }
        h2 { font-size: 1.3rem; }
        p { font-size: 0.9rem; }
        a.button { padding: 10px 18px; }
    }
</style>
</head>
<body>

<div class="profile-container">
    <!-- แสดงรูปโปรไฟล์ -->
    <?php if (!empty($row['img'])): ?>
        <img src="data:image/jpeg;base64,<?php echo base64_encode($row['img']); ?>" alt="รูปโปรไฟล์">
    <?php else: ?>
        <img src="https://via.placeholder.com/150" alt="ไม่มีรูปโปรไฟล์">
    <?php endif; ?>

    <h2><?php echo htmlspecialchars($row["first_name"] . " " . $row["last_name"]); ?></h2>
    <p><strong>อายุ:</strong> 
    <?php 
        $birthdate = $row["birthdate"]; // รับค่าวันเกิดจากฐานข้อมูล
        $today = new DateTime(); // วันที่ปัจจุบัน
        $dob = new DateTime($birthdate); // แปลงวันเกิดเป็น DateTime
        $age = $today->diff($dob)->y; // คำนวณส่วนต่างเป็นปี
        echo htmlspecialchars($age) . " ปี"; 
    ?>
</p>
    <p><strong>เพศ:</strong> <?php echo htmlspecialchars($row["gender"]); ?></p>
    <p><strong>อีเมล:</strong> <?php echo htmlspecialchars($row["email"]); ?></p>
    <p><strong>ที่อยู่:</strong> <?php echo htmlspecialchars($row["sub_district"] . ", " . $row["district"] . ", " . $row["province"]); ?></p>
<p><strong>เบอร์ติดต่อ:</strong> 
    <?php echo '0' . htmlspecialchars($row["phone"]); ?>
</p>
    
    <p><strong>วันทำงาน:</strong> <?php echo htmlspecialchars($display); ?></p>
    <p><strong>เวลาทำงาน:</strong> <?php echo htmlspecialchars($row["working_hours"]); ?></p>
    <p><strong>รายละเอียด:</strong> <?php echo nl2br(htmlspecialchars($row["bio"])); ?></p>
    <p><strong>ประเภทการดูแล:</strong> <?php echo htmlspecialchars($row["type"]); ?></p>

    <a href="web3.php" class="button">กลับหน้าหลัก</a>
    <a href="booking_form.php?caregiver_id=<?php echo urlencode($row['caregiver_id']); ?>" class="button">
    จองบริการ
    </a>
    <!-- แสดงวันที่ถูกจองแล้ว -->
    <div class="booking-dates">
        <h3>📅 วันที่ถูกจองแล้ว:</h3>
        <?php if ($booking_result->num_rows > 0): ?>
            <ul>
                <?php
while ($booking = $booking_result->fetch_assoc()):
    // แสดงเฉพาะรายการที่ status เป็น 'confirmed'
    if ($booking['Status'] !== 'confirmed') continue;

    $today = new DateTime();

    if ($booking['booking_type'] === 'daily') {
        $end = new DateTime($booking['end_date']);
        if ($end < $today) continue; // ข้ามรายการที่หมดอายุแล้ว
    } else {
        // แปลง start_month / end_month เป็นวันที่
        $end = DateTime::createFromFormat('Y-m', $booking['end_month']);
        $end->modify('last day of this month');
        if ($end < $today) continue; // ข้ามรายการหมดอายุ
    }
?>
    <li>
        <?= $booking['booking_type'] === 'daily'
            ? "📆 " . htmlspecialchars($booking['start_date']) . " ถึง " . htmlspecialchars($booking['end_date'])
            : "🗓️ " . htmlspecialchars($booking['start_month']) . " ถึง " . htmlspecialchars($booking['end_month'])
        ?> 
        (<?= $booking['booking_type'] === 'daily' ? 'รายวัน' : 'รายเดือน' ?>)
    </li>
<?php endwhile; ?>
            </ul>
        <?php else: ?>
            <p>ยังไม่มีการจอง</p>
        <?php endif; ?>
    </div>
</div>



</body>
</html>
