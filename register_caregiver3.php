<?php
session_start();
include 'config.php'; // เชื่อมต่อฐานข้อมูล

// เช็คว่ามี session หรือไม่
// if (!isset($_SESSION['caregiver_data'])) {
//     die("Error: ไม่มีข้อมูล session กรุณากรอกข้อมูลใน care_giver2.php ก่อน");
// }

$data = $_SESSION['caregiver_data']; // ดึงข้อมูลจาก session
$data1 = $_SESSION['caregiver_data1'];
 // รายการประเภทไฟล์ที่รองรับ



if ($_SERVER["REQUEST_METHOD"] == "POST") {
 // ฟังก์ชันตรวจสอบประเภทไฟล์
 $allowed_types = ['image/png', 'image/jpeg', 'application/pdf'];

 function validate_file($base64_string, $allowed_types) {
    $file_data = base64_decode($base64_string);
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime_type = $finfo->buffer($file_data);
    return in_array($mime_type, $allowed_types);
}

// ตรวจสอบประเภทไฟล์ก่อนบันทึก
if (!validate_file($data['id_card_front'], $allowed_types) || 
    !validate_file($data['id_card_back'], $allowed_types) ||
    !validate_file($data1['certificate1'], $allowed_types) ||
    !validate_file($data1['certificate2'], $allowed_types) ||
    !validate_file($data1['certificate3'], $allowed_types) ||
    !validate_file($data1['resume'], $allowed_types)) {
    die("Error: ไฟล์ที่อัปโหลดต้องเป็น PNG, JPG หรือ PDF เท่านั้น");
}


    // แปลง base64 เป็น binary
    $id_card_front = isset($data['id_card_front']) ? base64_decode($data['id_card_front']) : null;
    $id_card_back = isset($data['id_card_back']) ? base64_decode($data['id_card_back']) : null;
    $certificate1 = isset($data1['certificate1']) ? base64_decode($data1['certificate1']) : null;
    $certificate2 = isset($data1['certificate2']) ? base64_decode($data1['certificate2']) : null;
    $certificate3 = isset($data1['certificate3']) ? base64_decode($data1['certificate3']) : null;
    $resume = isset($data1['resume']) ? base64_decode($data1['resume']) : null;

    // เช็คข้อมูลก่อน INSERT
    if (!$id_card_front || !$id_card_back) {
        die("Error: ข้อมูลไม่ครบ กรุณากรอกให้ครบใน care_giver2.php");
    }
    
    // $bum=$_SESSION['caregiver_data']['id_card_front'];

    $sql = "INSERT INTO h_caregiver (id_card_front, id_card_back, certificate1, certificate2, certificate3, resume) 
            VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("bbbbbb", $id_card_front, $id_card_back, $certificate1, $certificate2, $certificate3, $resume);

    if ($stmt->execute()) {
        echo "บันทึกข้อมูลสำเร็จ";
        // unset($_SESSION['caregiver_data']);
        header("Location: LOG-IN1.php"); // ไปหน้า care_giver3.php
    } else {
        echo "เกิดข้อผิดพลาด: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>

