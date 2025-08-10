<?php
// เปิด error reporting เพื่อช่วยดีบัค
error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = "localhost"; // หรือ IP ของเซิร์ฟเวอร์
$username = "root"; // ชื่อผู้ใช้ MySQL
$password = ""; // รหัสผ่าน MySQL
$database = "care_link"; // ชื่อฐานข้อมูล

// สร้างการเชื่อมต่อฐานข้อมูลด้วย MySQLi
$conn = new mysqli($servername, $username, $password, $database);

// ตรวจสอบการเชื่อมต่อ MySQLi
if ($conn->connect_error) {
    die("เชื่อมต่อฐานข้อมูลล้มเหลว: " . $conn->connect_error);
}

try {
    // สร้างการเชื่อมต่อฐานข้อมูลด้วย PDO โดยใช้ตัวแปรที่ถูกต้อง
    $pdo = new PDO("mysql:host=$servername;dbname=$database;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "เชื่อมต่อฐานข้อมูลสำเร็จ";
} catch (PDOException $e) {
    die("ไม่สามารถเชื่อมต่อฐานข้อมูล $database ได้: " . $e->getMessage());
}

// ตั้งค่าภาษาให้รองรับ UTF-8 สำหรับการเชื่อมต่อ MySQLi
$conn->set_charset("utf8");
?>


