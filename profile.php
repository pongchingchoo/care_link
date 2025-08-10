<?php
session_start();

// ตรวจสอบว่าผู้ใช้ล็อกอินหรือไม่
if (!isset($_SESSION['parent_id'])) {
    echo "<script>alert('กรุณาเข้าสู่ระบบก่อน!'); window.location.href='LOG-IN.php';</script>";
    exit();
}

$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "care_link";

// เชื่อมต่อฐานข้อมูล
$conn = new mysqli($servername, $username, $password, $dbname);
$conn->set_charset("utf8mb4");

// ตัวแปรสำหรับแจ้งผลการเพิ่มข้อมูลเด็ก
$child_msg = "";

// ถ้ามีการส่งฟอร์มอัพโหลดรูปโปรไฟล์
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["profile_pic"]) && $_FILES["profile_pic"]["error"] == 0) {
    $allowedTypes = array("image/jpeg", "image/png", "image/gif");
    if (!in_array($_FILES["profile_pic"]["type"], $allowedTypes)) {
        echo "<script>alert('ประเภทไฟล์ไม่รองรับ! กรุณาอัพโหลดไฟล์ JPEG, PNG หรือ GIF'); window.location.href='profile.php';</script>";
        exit();
    }
    $imgData = file_get_contents($_FILES["profile_pic"]["tmp_name"]);
    $parent_id = $_SESSION["parent_id"];
    
    // อัพเดตรูปโปรไฟล์ในตาราง parent
    $stmt = $conn->prepare("UPDATE parent SET img = ? WHERE parent_id = ?");
    $stmt->bind_param("si", $imgData, $parent_id);
    $stmt->execute();
    $stmt->close();
    
    echo "<script>alert('อัพเดตรูปโปรไฟล์สำเร็จ'); window.location.href='profile.php';</script>";
    exit();
}

// ตรวจสอบการส่งฟอร์มสำหรับเพิ่มข้อมูลเด็ก
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['child_submit'])) {
    // รับข้อมูลจากฟอร์ม (ไม่รับ child_id เพราะระบบจะจัดการให้เอง)
    $first_name      = $_POST['first_name'];
    $child_type      = $_POST['child_type'];
    $nickname        = $_POST['nickname'];
    $gender          = $_POST['gender'];
    $birth_date      = $_POST['birth_date'];
    $medical_history = $_POST['medical_history'];
    $allergy         = $_POST['allergy'];
    $parent_id       = $_SESSION['parent_id'];

    // ตรวจสอบว่ามีการอัปโหลดรูปภาพมาหรือไม่
    $img_data = null;
    if (isset($_FILES['child_img']) && $_FILES['child_img']['error'] == 0) {
         $img_data = file_get_contents($_FILES['child_img']['tmp_name']);
    }

    // เตรียมคำสั่ง SQL สำหรับ INSERT (child_id จะถูกกำหนดโดย AUTO_INCREMENT)
    if ($img_data === null) {
        $stmt_child = $conn->prepare("INSERT INTO child (parent_id, first_name, child_type, nickname, gender, birth_date, medical_history, allergy, img) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NULL)");
        $stmt_child->bind_param("isssssss", $parent_id, $first_name, $child_type, $nickname, $gender, $birth_date, $medical_history, $allergy);
    } else {
        // เปลี่ยน type ของ bind_param สำหรับรูปเด็กจาก "b" เป็น "s"
        $stmt_child = $conn->prepare("INSERT INTO child (parent_id, first_name, child_type, nickname, gender, birth_date, medical_history, allergy, img) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt_child->bind_param("issssssss", $parent_id, $first_name, $child_type, $nickname, $gender, $birth_date, $medical_history, $allergy, $img_data);
    }

    if ($stmt_child->execute()) {
         $child_msg = "ข้อมูลเด็กถูกเพิ่มเรียบร้อยแล้ว";
    } else {
         $child_msg = "เกิดข้อผิดพลาด: " . $stmt_child->error;
    }
    $stmt_child->close();
}

// ดึงข้อมูลโปรไฟล์ของผู้ปกครองจากตาราง parent
$parent_id = $_SESSION['parent_id'];
$stmt = $conn->prepare("SELECT * FROM parent WHERE parent_id = ?");
$stmt->bind_param("i", $parent_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<script>alert('ไม่พบข้อมูล'); window.location.href='home.php';</script>";
    exit();
}

$parent = $result->fetch_assoc();
$stmt->close();

// ดึงข้อมูลเด็กที่เกี่ยวข้องกับผู้ปกครอง
$stmt_child_select = $conn->prepare("SELECT * FROM child WHERE parent_id = ?");
$stmt_child_select->bind_param("i", $parent_id);
$stmt_child_select->execute();
$result_child = $stmt_child_select->get_result();
$children = [];
while ($row = $result_child->fetch_assoc()) {
    $children[] = $row;
}
$stmt_child_select->close();

$conn->close();
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>โปรไฟล์ผู้ปกครองและข้อมูลเด็ก</title>
    <style>
        
        body { 
            font-family: Arial, sans-serif; 
            background-color: #f4f4f4; 
            margin: 0; 
            padding: 20px;
        }
        .container { 
            max-width: 800px; 
            margin: 0 auto; 
        }
        .profile-container, .child-profile-card, .child-form-container {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .profile-container, .child-profile-card {
            text-align: center;
        }
        .profile-container img, .child-profile-card img { 
            border-radius: 50%; 
            width: 150px; 
            height: 150px; 
            object-fit: cover; 
            margin-bottom: 15px; 
        }
        h2 {
  margin: 1rem 0 0.5rem;
  color: #222;
  font-size: 20px;
  font-weight: 600;
  line-height: 1.3;
}

p {
  margin: 0.25rem 100px 0.75rem;
  color: #555;
  font-size: 16px;
  line-height: 1.6;
  text-align: left;
}

        label { display: block; margin-top: 10px; }
        input[type="text"], input[type="date"], textarea, select { 
            width: 100%; 
            padding: 8px; 
            margin-top: 5px; 
            border: 1px solid #ccc; 
            border-radius: 4px; 
        }
        input[type="file"] { margin-top: 5px; }
        input[type="submit"], button { 
            margin-top: 15px; 
            background: #28a745; 
            color: white; 
            padding: 10px; 
            border: none; 
            border-radius: 5px; 
            cursor: pointer; 
            width: 100%; 
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
        /* Style for child list as cards */
        .child-list-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
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
    </style>
    <script>
        // ฟังก์ชันสำหรับแสดง/ซ่อนฟอร์มเพิ่มข้อมูลเด็ก
        function toggleChildForm() {
            var formContainer = document.getElementById("childFormContainer");
            if (formContainer.style.display === "none" || formContainer.style.display === "") {
                formContainer.style.display = "block";
            } else {
                formContainer.style.display = "none";
            }
        }
    </script>
</head>
<body>
<a href="home.php" class="button" style="cursor:pointer;  margin: 10px 0px 0 50px;">กลับหน้าแรก</a>
<div class="container">
    <!-- แสดงโปรไฟล์ผู้ปกครอง -->
    
    <div class="profile-container">
    
        <!-- ฟอร์มสำหรับอัพโหลดรูปโปรไฟล์ -->
        <form id="profile_pic_form" action="profile.php" method="POST" enctype="multipart/form-data">
        <label for="profile_pic" style="cursor:pointer; width: 100px; display: block; margin: 0 300px;">
                <?php if (!empty($parent['img'])): 
                    $img_info = getimagesizefromstring($parent['img']);
                    $mime = isset($img_info['mime']) ? $img_info['mime'] : 'image/jpeg';
                ?>
                    <img src="data:<?php echo htmlspecialchars($mime); ?>;base64,<?php echo base64_encode($parent['img']); ?>" alt="รูปโปรไฟล์">
                <?php else: ?>
                    <img src="https://via.placeholder.com/150" alt="ไม่มีรูปโปรไฟล์">
                <?php endif; ?>
            </label>
            <!-- ซ่อน input file ไว้ และเมื่อเลือกไฟล์แล้วจะ submit ฟอร์มอัตโนมัติ -->
            <input type="file" id="profile_pic" name="profile_pic" accept="image/*" style="display:none;" onchange="document.getElementById('profile_pic_form').submit();">
        </form>

        <h2><?php echo htmlspecialchars($parent['first_name'] . " " . $parent['last_name']); ?></h2>
        <p><strong>ที่อยู่:</strong> <?php echo htmlspecialchars($parent['sub_district'] . ", " . $parent['district'] . ", " . $parent['province']); ?></p>
        <p><strong>จำนวนสมาชิกในครอบครัว:</strong> <?php echo htmlspecialchars($parent['family_members']); ?></p>
        <p><strong>สถานะผู้ปกครอง:</strong> <?php echo htmlspecialchars($parent['guardian_status']); ?></p>
        <p><strong>จำนวนบุตร:</strong> <?php echo htmlspecialchars($parent['total_children']); ?></p>
        <p><strong>สัตว์เลี้ยงในบ้าน:</strong> <?php echo htmlspecialchars($parent['pets_in_home']); ?></p>
        <p><strong>ประเภทที่อยู่อาศัย:</strong> <?php echo htmlspecialchars($parent['housing_type']); ?></p>
        <p><strong>รายละเอียดที่อยู่อาศัย:</strong> <?php echo htmlspecialchars($parent['housing_detail']); ?></p>
        <br>
        <button class="cta-button" onclick="location.href='logout.php'">ลงชื่อออก</button>
    </div>

    <!-- แสดงข้อมูลเด็กในรูปแบบการ์ด -->
    <div class="child-list-container">
        <?php if (count($children) > 0): ?>
            <?php foreach ($children as $child): ?>
                <div class="child-profile-card">
                    <?php 
                        if (!empty($child['img'])):
                            $child_img_info = getimagesizefromstring($child['img']);
                            $child_mime = isset($child_img_info['mime']) ? $child_img_info['mime'] : 'image/jpeg';
                    ?>
                        <img src="data:<?php echo htmlspecialchars($child_mime); ?>;base64,<?php echo base64_encode($child['img']); ?>" alt="รูปเด็ก">
                    <?php else: ?>
                        <img src="https://via.placeholder.com/150" alt="ไม่มีรูปเด็ก">
                    <?php endif; ?>
                    <h2><?php echo htmlspecialchars($child['first_name']); ?></h2>
                    <p><strong>ประเภทเด็ก:</strong> <?php echo htmlspecialchars($child['child_type']); ?></p>
                    <p><strong>ชื่อเล่น:</strong> <?php echo htmlspecialchars($child['nickname']); ?></p>
                    <p><strong>เพศ:</strong> <?php echo htmlspecialchars($child['gender']); ?></p>
                    <p><strong>วันเกิด:</strong> <?php echo htmlspecialchars($child['birth_date']); ?></p>
                    <p><strong>ประวัติการแพทย์:</strong> <?php echo htmlspecialchars($child['medical_history']); ?></p>
                    <p><strong>การแพ้:</strong> <?php echo htmlspecialchars($child['allergy']); ?></p>
                    <!-- ปุ่มลบเด็ก -->


                <!-- ปุ่มลบ -->

    <button class="delete-btn" onclick="deleteChild(<?php echo $child['child_id']; ?>)">
        🗑️ ลบ
    </button>



                <script>
function deleteChild(childId) {
    if (confirm("คุณแน่ใจหรือไม่ว่าต้องการลบข้อมูลเด็กนี้?")) {
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "delete_child.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

        xhr.onreadystatechange = function () {
            if (xhr.readyState == 4 && xhr.status == 200) {
                var response = xhr.responseText;
                if (response === "success") {
                    alert("ลบข้อมูลเด็กเรียบร้อยแล้ว!");
                    // ลบการ์ดของเด็กที่ถูกลบ
                    document.getElementById("child-" + childId).remove();
                } else if (response === "not_found") {
                    alert("ไม่พบข้อมูลเด็กที่ต้องการลบ!");
                } else {
                    alert("เกิดข้อผิดพลาดในการลบข้อมูล!");
                }
            }
        };
        xhr.send("child_id=" + childId);
    }
}
</script>




                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>ยังไม่มีข้อมูลเด็ก</p>
        <?php endif; ?>
    </div>

    <!-- ปุ่มแสดงฟอร์มเพิ่มข้อมูลเด็ก -->
    <div style="text-align:center; margin-bottom:20px;">
        <button onclick="toggleChildForm()">เพิ่มข้อมูลลูก</button>
    </div>

    <!-- แบบฟอร์มสำหรับเพิ่มข้อมูลเด็ก (ซ่อนอยู่เริ่มต้น) -->
    <div class="child-form-container" id="childFormContainer" style="display:none;">
        <h2>เพิ่มข้อมูลเด็ก</h2>
        <?php if ($child_msg != ""): ?>
            <p><?php echo htmlspecialchars($child_msg); ?></p>
        <?php endif; ?>
        <form action="" method="post" enctype="multipart/form-data">
            <label for="first_name">ชื่อเด็ก:</label>
            <input type="text" id="first_name" name="first_name" required>
            
            <label for="child_type">ประเภทเด็ก:</label>
            <select id="child_type" name="child_type" required>
                <option value="">--เลือกประเภทเด็ก--</option>
                <option value="เด็กปกติ">เด็กปกติ</option>
                <option value="เด็กออทิสติก">เด็กออทิสติก</option>
            </select>
            
            <label for="nickname">ชื่อเล่น:</label>
            <input type="text" id="nickname" name="nickname">
            
            <label for="gender">เพศ:</label>
            <select id="gender" name="gender" required>
                <option value="">--เลือกเพศ--</option>
                <option value="ชาย">ชาย</option>
                <option value="หญิง">หญิง</option>
                <option value="อื่นๆ">อื่นๆ</option>
            </select>
            
            <label for="birth_date">วันเกิด:</label>
            <input type="date" id="birth_date" name="birth_date">
            
            <label for="medical_history">ประวัติการแพทย์:</label>
            <textarea id="medical_history" name="medical_history"></textarea>
            
            <label for="allergy">การแพ้:</label>
            <input type="text" id="allergy" name="allergy">
            
            <label for="child_img">อัปโหลดรูปเด็ก (ถ้ามี):</label>
            <input type="file" id="child_img" name="child_img" accept="image/*">
            
            <input type="submit" name="child_submit" value="ยืนยัน">
        </form>
    </div>
</div>
</body>
</html>
