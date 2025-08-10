<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_FILES['id_card_front']) || $_FILES['id_card_front']['error'] != 0 ||
        !isset($_FILES['id_card_back']) || $_FILES['id_card_back']['error'] != 0) {
        die("Error: กรุณาอัปโหลดไฟล์ให้ครบ");
    }

    // ตรวจสอบประเภทไฟล์
    $allowed_types = ['image/png', 'application/pdf'];
    if (!in_array($_FILES['id_card_front']['type'], $allowed_types) || 
        !in_array($_FILES['id_card_back']['type'], $allowed_types)) {
        die("Error: รองรับเฉพาะไฟล์ PNG หรือ PDF เท่านั้น");
    }

    // แปลงไฟล์เป็น base64 และเก็บใน session
    $_SESSION['caregiver_data2'] = [
        'id_card_front' => base64_encode(file_get_contents($_FILES['id_card_front']['tmp_name'])),
        'id_card_back' => base64_encode(file_get_contents($_FILES['id_card_back']['tmp_name'])),
    ];
    
    // รวมข้อมูลจาก Step 1
    $_SESSION['caregiver_data'] = array_merge($_SESSION['caregiver_data1'], $_SESSION['caregiver_data2']);
    
    header("Location: care_giver3.php");
    exit();
    
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>สมัครเป็นผู้ดูแล</title>
    <link rel="stylesheet" href="care_giver2.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>
<body>

    <div class="container">
        <div class="form-section">
            <h1>สมัครเป็นผู้ดูแล</h1>
            <div class="line"></div>
            <div class="avatar">
                <i class="fas fa-user"></i>
                <span class="edit-icon">&#9998;</span>
            </div>

            <div class="section">
                <div class="steps">
                    <div class="active"><span><i class="fa-solid fa-check"></i></span> ประวัติส่วนตัว</div>
                    <div class="active"><span>2</span> ยืนยันตัวตน</div>
                    <div><span>3</span> ผลงาน</div>
                    <div><span>4</span> กำลังตรวจสอบ</div>
                </div>
                <div class="line2"></div>
                <div class="form-content">

                    
<!-- ✅ ต้องใช้ method="POST" และ enctype="multipart/form-data" -->
<form method="POST" enctype="multipart/form-data">
                        <!-- ✅ ถ้าหน้านี้เป็น Step 2 ต้องส่งค่าจาก Step 1 มาด้วย -->
                        
                        <div class="upload-section">
                            <!-- อัปโหลดบัตรประชาชน ด้านหน้า -->
                            <div class="exp"><img src="card-f.jpg" alt="Icon"> ด้านหน้า</div>
                            <label for="id_card_front" class="upload-box">
                                <span id="file-name-front">แนบไฟล์</span>
                            </label>
                            <input type="file" id="id_card_front" name="id_card_front" accept=".png, .pdf" required hidden onchange="updateFileName('id_card_front', 'file-name-front')">

                            <!-- อัปโหลดบัตรประชาชน ด้านหลัง -->
                            <div class="exp"><img src="card-b.jpg" alt="Icon"> ด้านหลัง</div>
                            <label for="id_card_back" class="upload-box">
                                <span id="file-name-back">แนบไฟล์</span>
                            </label>
                            <input type="file" id="id_card_back" name="id_card_back" accept=".png, .pdf" required hidden onchange="updateFileName('id_card_back', 'file-name-back')">
                        </div>

                        <div class="submit-button">
                        <button type="submit">ต่อไป</button>
                    </div>

                        <div class="submit-button-back">
                            <button type="button" onclick="window.location.href='care_giver.php'">กลับไป</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

    <script>
    function updateFileName(inputId, labelId) {
        const input = document.getElementById(inputId);
        const label = document.getElementById(labelId);
        if (input.files.length > 0) {
            label.textContent = input.files[0].name; // แสดงชื่อไฟล์ที่เลือก
        } else {
            label.textContent = "แนบไฟล์";
        }
    }
    </script>
        
</body>
</html>
