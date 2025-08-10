<?php
session_start();

// ตรวจสอบว่า session 'caregiver_data' มีข้อมูลหรือไม่
if (!isset($_SESSION['caregiver_data'])) {
    die("Error: ข้อมูล session ไม่ถูกต้อง กรุณากรอกข้อมูลใหม่");
}

// ตรวจสอบว่ามีการอัปโหลดไฟล์แล้ว
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $required_files = ['resume', 'certificate1', 'certificate2', 'certificate3'];
    $allowed_types = ['image/png', 'application/pdf'];

    // ตรวจสอบไฟล์ทั้งหมด
    foreach ($required_files as $file) {
        if (!isset($_FILES[$file]) || $_FILES[$file]['error'] !== 0) {
            die("Error: กรุณาอัปโหลดไฟล์ให้ครบ");
        }
        if (!in_array($_FILES[$file]['type'], $allowed_types)) {
            die("Error: รองรับเฉพาะไฟล์ PNG หรือ PDF เท่านั้น");
        }
        if (!is_uploaded_file($_FILES[$file]['tmp_name'])) {
            die("Error: มีปัญหาในการอัปโหลดไฟล์ $file");
        }
    }

    // เข้ารหัสไฟล์เป็น base64 และเก็บไว้ใน session
    $_SESSION['caregiver_data3'] = [
        'certificate1' => base64_encode(file_get_contents($_FILES['certificate1']['tmp_name'])),
        'certificate2' => base64_encode(file_get_contents($_FILES['certificate2']['tmp_name'])),
        'certificate3' => base64_encode(file_get_contents($_FILES['certificate3']['tmp_name'])),
        'resume' => base64_encode(file_get_contents($_FILES['resume']['tmp_name']))
    ];

    // รวมข้อมูล session จากทุกขั้นตอน
    $_SESSION['caregiver_data4'] = array_merge($_SESSION['caregiver_data'], $_SESSION['caregiver_data3']);

    // ตรวจสอบค่าที่เก็บไว้ใน session
    // echo "<pre>";
    // var_dump($_SESSION);
    // echo "</pre>";

    // ส่งข้อมูลไปหน้า LOG-IN1.php หลังจากบันทึกข้อมูล
    // print_r($_SESSION['caregiver_data4']);
    header("Location: LOG-IN1.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>สมัครเป็นผู้ดูแล</title>
    <link rel="stylesheet" href="care_giver3.css">
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
                    <div class="active"><span><i class="fa-solid fa-check"></i></span> ยืนยันตัวตน</div>
                    <div class="active"><span>3</span> ผลงาน</div>
                    <div><span>4</span> กำลังตรวจสอบ</div>
                </div>
                <div class="line2"></div>
                
                <div class="form-content">
                
                    <form action="regis.php" method="POST" enctype="multipart/form-data">
                        <h2>เรซูเม่</h2>
                        <div class="upload-section">
                            <div class="form-group">
                                <label for="resume" class="upload-box">
                                    <span id="file-name-resume">อัปโหลด</span>
                                </label>
                                <input type="file" id="resume" name="resume" accept=".png, .pdf" required hidden onchange="updateFileName('resume', 'file-name-resume')">
                            </div>
                        </div>

                        <div class="example-section">
                            <h2>*ตัวอย่าง ใบประกอบวิชาชีพ / ใบรับรอง</h2>
                            <div class="examples">
                                <img src="ex1.jpg" alt="Example Certificate 1">
                                <img src="ex2.jpg" alt="Example Certificate 2">
                                <img src="ex3.jpg" alt="Example Certificate 3">
                            </div>
                        </div>

                        <h2>ใบประกอบวิชาชีพ / ใบรับรอง</h2>
                        <div class="upload-section">
                            <div class="form-group">
                                <label for="certificate1" class="upload-box">
                                    <span id="file-name-cert1">อัปโหลด</span>
                                </label>
                                <input type="file" id="certificate1" name="certificate1" accept=".png, .pdf" required hidden onchange="updateFileName('certificate1', 'file-name-cert1')">
                            </div>
                        </div>

                        <div class="upload-section">
                            <div class="form-group">
                                <label for="certificate2" class="upload-box">
                                    <span id="file-name-cert2">อัปโหลด</span>
                                </label>
                                <input type="file" id="certificate2" name="certificate2" accept=".png, .pdf"  hidden onchange="updateFileName('certificate2', 'file-name-cert2')">
                            </div>
                        </div>

                        <div class="upload-section">
                            <div class="form-group">
                                <label for="certificate3" class="upload-box">
                                    <span id="file-name-cert3">อัปโหลด</span>
                                </label>
                                <input type="file" id="certificate3" name="certificate3" accept=".png, .pdf"  hidden onchange="updateFileName('certificate3', 'file-name-cert3')">
                            </div>
                        </div>

                    <div class="submit-button">
                    <button type="submit" >ส่ง</button>
                    </div>

                        <div class="submit-button-back">
                            <button type="button" onclick="window.location.href='care_giver2.php'">กลับไป</button>
                        </div>
                    </form>    
                </div>

                <script>
                    function updateFileName(inputId, labelId) {
                        const input = document.getElementById(inputId);
                        const label = document.getElementById(labelId);
                        if (input.files.length > 0) {
                            label.textContent = input.files[0].name; // แสดงชื่อไฟล์ที่เลือก
                        } else {
                            label.textContent = "แนบไฟล์ (PNG หรือ PDF)";
                        }
                    }

       
                </script>

            </div>
        </div>
    </div>
</body>
</html>
