<?php
session_start();

// ตรวจสอบว่าผู้ใช้ได้ล็อกอินหรือยัง
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

// เชื่อมต่อฐานข้อมูล
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "care_link";

$conn = new mysqli($servername, $username, $password, $dbname);
$conn->set_charset("utf8mb4");

// ดึง user_id จาก session
$user_id = $_SESSION["user_id"];



// ใช้ prepared statement เพื่อดึงข้อมูลผู้ดูแล
$stmt = $conn->prepare("SELECT * FROM caregiver WHERE caregiver_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["profile_pic"]) && $_FILES["profile_pic"]["error"] == 0) {
    $allowedTypes = array("image/jpeg", "image/png", "image/gif");
    if (!in_array($_FILES["profile_pic"]["type"], $allowedTypes)) {
        echo "<script>alert('ประเภทไฟล์ไม่รองรับ! กรุณาอัพโหลดไฟล์ JPEG, PNG หรือ GIF'); window.location.href='caregiver_profile.php';</script>";
        exit();
    }
    $imgData = file_get_contents($_FILES["profile_pic"]["tmp_name"]);
    
    // อัพเดตรูปโปรไฟล์ในตาราง caregiver
    $stmt = $conn->prepare("UPDATE caregiver SET img = ? WHERE caregiver_id = ?");
    $stmt->bind_param("si", $imgData, $user_id);
    $stmt->execute();
    $stmt->close();
    
    echo "<script>alert('อัพเดตรูปโปรไฟล์สำเร็จ'); window.location.href='caregiver_profile.php';</script>";
    exit();
}

$row = $result->fetch_assoc(); // << ตอนนี้ $row ถูกเซ็ตแล้ว

// ประมวลผลวันทำงาน (หลังจากได้ $row แล้ว)
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
    $display = "ทำงานวัน: " . implode(', ', $working_days);
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>Caregiver Profile</title>
    <style>
body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }

        
        

    .profile-container {
        max-width: 800px;
        margin: auto;
        background: #ffffff;
        padding: 32px 28px;
        border-radius: 16px;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);  /* เงานุ่ม ๆ */
        text-align: center;
    }



        .container { 
            max-width: 800px; 
            margin: 0 auto; 
        }

        .profile-container img {
            border-radius: 50%;
            width: 150px;
            height: 150px;
            object-fit: cover;
            margin: 0 -30px;
            border: 3px solid #ddd;
            transition: border-color 0.3s ease;
        }

        .profile-container img:hover {
            border-color: #007bff;
        }

        h2 {
        margin: 18px 0 6px;
        font-size: 20px;
        color: #111827;              /* ดำเทาอ่านง่าย */
    }

    p {
        text-align: left;
        margin: 4px 0;
        font-size: 16px;
    }

    /* ป้ายหัวข้อย่อย */
    p strong {
        color: #111827;
        font-weight: 600;
    }

        label {
            display: block;
            margin-top: 10px;
            color: #333;
            font-weight: bold;
        }

        input[type="text"], input[type="date"], textarea, select {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #f9f9f9;
            font-size: 16px;
        }

        input[type="file"] {
            margin-top: 5px;
        }

        input[type="submit"], button {
            margin-top: 15px;
            background: #28a745;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
            font-size: 16px;
        }

        input[type="submit"]:hover, button:hover {
            background: #218838;
        }

        a.button {
            display: inline-block;
            text-decoration: none;
            background: #007BFF;
            color: white;
            padding: 12px 18px;
            border-radius: 5px;
            margin-top: 10px;
            font-size: 16px;
        }

        a.button:hover {
            background: #0056b3;
        }

        .child-list-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: space-between;
        }

        .child-profile-card {
            flex: 1 1 calc(50% - 20px);
            box-sizing: border-box;
        }

        @media (max-width: 600px) {
            .child-profile-card {
                flex: 1 1 100%;
            }
        }
        a.button { 
            display: inline-block; 
            text-decoration: none; 
            background: #007BFF; 
            color: white; 
            padding: 10px 15px; 
            border-radius: 5px; 
            margin-top: 10px; 
        }



    </style>
</head>
<body>
<a href="caregiver_dashboard.php" class="button">กลับหน้าหลัก</a>

<div class="container">
    <div class="profile-container">
        <form id="profile_pic_form" action="caregiver_profile.php" method="POST" enctype="multipart/form-data">
            <label for="profile_pic" style="cursor:pointer; width: 100px; display: block; margin: 0 300px;">
                <?php if (!empty($row['img'])): 
                    $img_info = getimagesizefromstring($row['img']);
                    $mime = isset($img_info['mime']) ? $img_info['mime'] : 'image/jpeg';
                ?>
                    <img src="data:<?php echo htmlspecialchars($mime); ?>;base64,<?php echo base64_encode($row['img']); ?>" alt="รูปโปรไฟล์">
                <?php else: ?>
                    <img src="https://via.placeholder.com/150" alt="ไม่มีรูปโปรไฟล์">
                <?php endif; ?>
            </label>
            <!-- ซ่อน input file ไว้ และเมื่อเลือกไฟล์แล้วจะ submit ฟอร์มอัตโนมัติ -->
            <input type="file" id="profile_pic" name="profile_pic" accept="image/*" style="display:none;" onchange="document.getElementById('profile_pic_form').submit();">
        </form>

        <div class="info">
            <p><strong>ชื่อ:</strong> <?php echo htmlspecialchars($row["first_name"] . " " . $row["last_name"]); ?></p>
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
            <!-- <p><strong>สถานะ:</strong> <?php echo htmlspecialchars($row["status"]); ?></p> -->
            <p><strong>ประเภทการดูแล:</strong> <?php echo htmlspecialchars($row["type"]); ?></p>
        </div>


        <button class="cta-button" onclick="location.href='logout.php'">ลงชื่อออก</button>
    </div>
    </div>
</body>
</html>
