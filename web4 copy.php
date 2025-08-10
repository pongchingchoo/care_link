<?php
include('config.php'); // เชื่อมต่อฐานข้อมูล

session_start(); // เปิดใช้งาน session

// ตรวจสอบว่า parent ได้ล็อกอินแล้วหรือไม่
if (!isset($_SESSION["parent_id"])) {
    header("Location: login.php");
    exit();
}

$parent_id = $_SESSION['parent_id']; // ID ของผู้ปกครองที่ล็อกอิน

// ดึงข้อมูลการจ้างงานที่เกี่ยวข้องกับ parent นี้
$sql = "SELECT cb.booking_id, cb.caregiver_id, cb.booking_type, cb.start_date, cb.end_date, cb.total_price, cb.payment_status, cb.status, 
               c.first_name AS caregiver_first_name, c.last_name AS caregiver_last_name, 
               p.first_name AS parent_first_name, p.last_name AS parent_last_name
        FROM caregiver_booking cb
        JOIN caregiver c ON cb.caregiver_id = c.caregiver_id
        JOIN parent p ON cb.parent_id = p.parent_id
        WHERE cb.parent_id = ?
        ORDER BY cb.booking_id DESC";


$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $parent_id);
$stmt->execute();
$result = $stmt->get_result();
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
            <img src="logo1.png" alt="Logo" height="60px">
        </div>
        <nav>
            <a href="#" onclick="location.href='home.php'">หน้าหลัก</a>
            <a href="#" onclick="location.href='web2.php'">ค้นหาผู้ดูแล</a>
            <!-- <a href="#">ระดับการดูแล</a> -->
            <a href="#" onclick="location.href='web3.php'">การหาที่ตรงใจ</a>
            <a href="#" style="color: #ff9100;">การจ้าง</a>
            <!-- <a href="#">ติดต่อเรา</a> -->
        </nav>
        <div class="user-info" style=" margin-top: 20px;">
        <?php
            // ตรวจสอบว่า session มีค่า parent_name หรือไม่
            if (isset($_SESSION['user_name'])) {
                echo "<p>สวัสดี, " . htmlspecialchars($_SESSION['user_name']) . "</p>";
            }
        ?>

        </div>
        
    
    </div>

    <div class="icon-circle">
    <img src="get_profile_image_parent.php" onclick="location.href='profile.php'" style="width:50px; height:50px; border-radius:50%; cursor:pointer;" alt="โปรไฟล์">
    </div>



</header>
    <h2 class="mb-3" style="margin: 0 150px;    margin-top: 60px;">การจ้างงานของคุณ</h2>
    <div class="box" style="margin: 0 200px;    margin-top: 20px;">

    <?php if ($result->num_rows > 0): ?>
    <div class="list-group">
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="list-group-item">
                <h5 class="mb-1">👤 ผู้ปกครอง: <?= htmlspecialchars($row['parent_first_name'] . " " . $row['parent_last_name']) ?></h5>
                <p class="mb-1">🧑‍⚕️ ผู้ดูแล: <a href="profile_caregiver.php?id=<?= urlencode($row['caregiver_id']) ?>"><?= htmlspecialchars($row['caregiver_first_name'] . " " . $row['caregiver_last_name']) ?></a></p>
                <p class="mb-1">📅 ประเภทการจ้าง: <?= $row['booking_type'] === 'daily' ? 'รายวัน' : 'รายเดือน' ?></p>
                <?php if ($row['booking_type'] === 'daily'): ?>
                    <p>📆 วันที่เริ่ม: <?= htmlspecialchars($row['start_date'] ?? 'ไม่มีข้อมูล') ?> - วันที่สิ้นสุด: <?= htmlspecialchars($row['end_date'] ?? 'ไม่มีข้อมูล') ?></p>
                <?php else: ?>
                    <p>📆 เดือนที่เริ่ม: <?= htmlspecialchars($row['start_date'] ?? 'ไม่มีข้อมูล') ?> - เดือนที่สิ้นสุด: <?= htmlspecialchars($row['end_date'] ?? 'ไม่มีข้อมูล') ?></p>
                <?php endif; ?>
                <p>💰 จำนวนเงิน: <?= htmlspecialchars($row['total_price'] ?? 0) ?> บาท</p>
                <p>💳 สถานะการชำระเงิน: <?= htmlspecialchars($row['payment_status'] === 'paid' ? '✅ ชำระแล้ว' : '⏳ รอการชำระเงิน') ?></p>

                <?php if ($row['status'] === 'confirmed'): ?>
                    <!-- แสดงปุ่มชำระเงินเมื่อสถานะการจองเป็น confirmed และยังไม่ชำระ -->
                    <?php if ($row['payment_status'] !== 'paid'): ?>
                        <form onsubmit="openPopup(<?= $row['booking_id'] ?>); return false;">
                            <button type="submit" class="btn btn-primary">ชำระเงิน</button>
                        </form>
<script>
    function openPopup(bookingId) {
        const url = 'payment_popup.php?booking_id=' + bookingId;
        window.open(url, 'payment_popup', 'width=500,height=600,scrollbars=yes');
    }
</script>
                    <?php endif; ?>

                    <!-- แสดงปุ่มลบรายการการจองเมื่อยังไม่ชำระ -->
                    <?php if ($row['payment_status'] !== 'paid'): ?>
                        <form action="delete_booking.php" method="POST" onsubmit="return confirm('คุณแน่ใจหรือไม่ที่จะลบการจองนี้?');">
                            <input type="hidden" name="booking_id" value="<?= $row['booking_id'] ?>">
                            <button type="submit" class="btn btn-danger btn-sm">❌ ยกเลิกการจอง</button>
                        </form>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        <?php endwhile; ?>
    </div>
<?php else: ?>
    <div class="alert alert-warning">❌ ไม่มีข้อมูลการจ้างงาน</div>
<?php endif; ?>


</body>
</html>

<?php
$stmt->close();
$conn->close();
?>

