<?php
session_start();  // เริ่มต้นเซสชัน

// ตรวจสอบว่าเซสชัน 'caregiver_data1' มีค่าหรือไม่
if (isset($_SESSION['caregiver_data1'])) {
    // ดึงข้อมูลจาก $_SESSION['caregiver_data1']
    $caregiver_data = $_SESSION['caregiver_data1'];
}

// ob_start(); // Start output buffering

// if ($_SERVER["REQUEST_METHOD"] == "POST") {
//     // Define a directory to store uploaded files
//     $uploadDir = 'uploads/';
//     if (!is_dir($uploadDir)) {
//         mkdir($uploadDir, 0777, true); // Create directory if not exists
//     }

//     // Handle file uploads
//     $uploadedFiles = [];
    
//     // Check each file upload and process
//     $fileKeys = ['id_card_front', 'id_card_back', 'resume', 'certificate1', 'certificate2', 'certificate3'];
//     foreach ($fileKeys as $key) {
//         if (isset($_FILES[$key]) && $_FILES[$key]['error'] == UPLOAD_ERR_OK) {
//             $fileName = basename($_FILES[$key]['name']);
//             $targetFilePath = $uploadDir . $fileName;

//             // Move the uploaded file to the server's directory
//             if (move_uploaded_file($_FILES[$key]['tmp_name'], $targetFilePath)) {
//                 $uploadedFiles[$key] = $targetFilePath;
//             } else {
//                 $uploadedFiles[$key] = ''; // If file move fails, set to empty
//             }
//         } else {
//             $uploadedFiles[$key] = ''; // If no file is uploaded, set to empty
//         }
//     }

//     // Store form data and file paths in session
//     $_SESSION['caregiver_data2'] = [
//         'id_card_front'  => $uploadedFiles['id_card_front'] ?? '',
//         'id_card_back'   => $uploadedFiles['id_card_back'] ?? '',
//         'resume'         => $uploadedFiles['resume'] ?? '',
//         'certificate1'   => $uploadedFiles['certificate1'] ?? '',
//         'certificate2'   => $uploadedFiles['certificate2'] ?? '',
//         'certificate3'   => $uploadedFiles['certificate3'] ?? ''
//     ];

//     session_write_close();
//     header("Location: care_giver5.php"); // Redirect to next page
//     exit();
// }


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>สมัครเป็นผู้ดูแล</title>
    <link rel="stylesheet" href="care_giver4.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

    <style>




.upload-title {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    font-size: 18px;
    font-weight: 600;
    margin-bottom: 16px;
    color: #333;
}

.upload-title img {
    width: 40px;
    height: auto;
}



#file-name-front {
    display: inline-block;
}

.exp{
                position: relative;

            font-size: 18px;
            bottom: 90px;
            margin-right: 600px;
        }


        .submit-button,
.submit-button-back {
    text-align: left;  /* ให้เนื้อหาชิดซ้ายใน div */
    margin-bottom: 10px; /* ระยะห่างระหว่างปุ่ม */
}

.submit-button button,
.submit-button-back button {
    margin-left: 500px; /* ไม่มีระยะขอบซ้าย */
}

    </style>
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
                    <div class="active"><span>2</span> ยืนยันตัวตน+ผลงาน</div>
                    <div><span>3</span> รอการตรวจสอบ</div>
                </div>
                <div class="line2"></div>
                <div class="form-content">

                    
<!-- ✅ ต้องใช้ method="POST" และ enctype="multipart/form-data" -->
<form action="register_caregiver2.php" method="POST" enctype="multipart/form-data">
                        <!-- ✅ ถ้าหน้านี้เป็น Step 2 ต้องส่งค่าจาก Step 1 มาด้วย -->
                        
                        <div class="upload-section-card">
                            <!-- อัปโหลดบัตรประชาชน ด้านหน้า -->
                            <div class="exp"><img src="card-f.jpg" alt="Icon"> ด้านหน้า</div>
                            <label for="id_card_front" class="upload-box">
                                <span id="file-name-front">แนบไฟล์</span>
                            </label>
                            <input type="file" id="id_card_front" name="id_card_front" accept=".png, .pdf" required hidden onchange="updateFileName('id_card_front', 'file-name-front')">

                            <!-- อัปโหลดบัตรประชาชน ด้านหลัง -->
                            <!-- <div class="exp"><img src="card-b.jpg" alt="Icon"> ด้านหลัง</div>
                            <label for="id_card_back" class="upload-box">
                                <span id="file-name-back">แนบไฟล์</span>
                            </label>
                            <input type="file" id="id_card_back" name="id_card_back" accept=".png, .pdf" required hidden onchange="updateFileName('id_card_back', 'file-name-back')"> -->
                        </div>

                        <h2>เรซูเม่</h2>
                        <div class="upload-section">
                            <div class="form-group">
                                <label for="resume" class="upload-box-resume">
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
                                <label for="certificate1" class="upload-box-resume">
                                    <span id="file-name-cert1">อัปโหลด</span>
                                </label>
                                <input type="file" id="certificate1" name="certificate1" accept=".png, .pdf" required hidden onchange="updateFileName('certificate1', 'file-name-cert1')">
                            </div>
                        </div>

                        <div class="upload-section">
                            <div class="form-group">
                                <label for="certificate2" class="upload-box-resume">
                                    <span id="file-name-cert2">อัปโหลด</span>
                                </label>
                                <input type="file" id="certificate2" name="certificate2" accept=".png, .pdf"  hidden onchange="updateFileName('certificate2', 'file-name-cert2')">
                            </div>
                        </div>

                        <div class="upload-section">
                            <div class="form-group">
                                <label for="certificate3" class="upload-box-resume">
                                    <span id="file-name-cert3">อัปโหลด</span>
                                </label>
                                <input type="file" id="certificate3" name="certificate3" accept=".png, .pdf"  hidden onchange="updateFileName('certificate3', 'file-name-cert3')">
                            </div>
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