<?php
// เริ่มต้น session เพื่อเข้าถึงข้อมูลของผู้ใช้ที่ล็อกอิน
session_start();

// ตรวจสอบว่า parent_id อยู่ใน session หรือไม่
if (isset($_SESSION['parent_id'])) {
    $parent_id = $_SESSION['parent_id']; // ใช้ parent_id จาก session เป็น id ของผู้ที่ล็อกอิน
} else {
    die("User not logged in.");
}

// เชื่อมต่อกับฐานข้อมูล
$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "care_link";

$conn = new mysqli($servername, $username, $password, $dbname);

// เช็คการเชื่อมต่อ
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ดึงข้อมูลรูปโปรไฟล์จากฐานข้อมูล
$sql = "SELECT img FROM parent WHERE parent_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $parent_id); // เชื่อมโยงค่า id ของผู้ที่ล็อกอิน
$stmt->execute();
$stmt->bind_result($img_data); // รับค่าผลลัพธ์จากฐานข้อมูล

if ($stmt->fetch()) {
    // ถ้ามีข้อมูลรูปโปรไฟล์
    $img_info = getimagesizefromstring($img_data);
    if ($img_info) {
        $mime = isset($img_info['mime']) ? $img_info['mime'] : 'image/jpeg';
        $profile_pic = 'data:' . $mime . ';base64,' . base64_encode($img_data);
        echo $profile_pic; // ส่งค่ารูปกลับไปที่ src ของ <img>
    } else {
        // หากไม่ได้รับข้อมูลที่เป็นรูปภาพ ให้ใช้ placeholder
        echo 'https://via.placeholder.com/150';
    }
} else {
    // ถ้าไม่มีรูปโปรไฟล์ ให้ใช้ placeholder
    echo 'https://via.placeholder.com/150';
}

$stmt->close();
$conn->close();
?>
