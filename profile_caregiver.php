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
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }

        .profile-container {
            max-width: 500px;
            margin: auto;
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .profile-container img {
            border-radius: 50%;
            width: 150px;
            height: 150px;
            object-fit: cover;
            border: 3px solid #ddd;
        }

        h2 {
            color: #333;
            margin-top: 10px;
        }

        p {
            font-size: 16px;
            color: #666;
        }

        a.button {
            display: inline-block;
            margin-top: 15px;
            padding: 10px 20px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }

        a.button:hover {
            background: #0056b3;
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
    <p><strong>วันทำงาน:</strong> <?php echo htmlspecialchars($display); ?></p>
    <p><strong>เวลาทำงาน:</strong> <?php echo htmlspecialchars($row["working_hours"]); ?></p>
    <p><strong>รายละเอียด:</strong> <?php echo nl2br(htmlspecialchars($row["bio"])); ?></p>
    <p><strong>ประเภทการดูแล:</strong> <?php echo htmlspecialchars($row["type"]); ?></p>

    <a href="web4.php" class="button">กลับหน้าหลัก</a>

    <a href="booking_form.php?caregiver_id=<?php echo urlencode($row['caregiver_id']); ?>" class="button">
    จองบริการซ้ำ
    </a>


</div>

</body>
</html>
