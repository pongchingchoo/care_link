<?php
session_start();

// ตรวจสอบว่าผู้ใช้ล็อกอินหรือไม่
if (!isset($_SESSION['parent_id'])) {
    echo "<script>alert('กรุณาเข้าสู่ระบบก่อน!'); window.location.href='LOG-IN.php';</script>";
    exit();
}

// เชื่อมต่อฐานข้อมูล
$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "care_link";

// เชื่อมต่อฐานข้อมูล
$conn = new mysqli($servername, $username, $password, $dbname);
$conn->set_charset("utf8mb4");

// ตรวจสอบว่าได้รับ child_id หรือไม่
if (isset($_POST['child_id'])) {
    $child_id = $_POST['child_id'];
    $parent_id = $_SESSION['parent_id'];

    // ตรวจสอบว่าเด็กอยู่ภายใต้ผู้ปกครองคนนี้
    $stmt = $conn->prepare("SELECT * FROM child WHERE child_id = ? AND parent_id = ?");
    $stmt->bind_param("ii", $child_id, $parent_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // ลบข้อมูลเด็ก
        $stmt_delete = $conn->prepare("DELETE FROM child WHERE child_id = ?");
        $stmt_delete->bind_param("i", $child_id);
        if ($stmt_delete->execute()) {
            echo "success";  // ลบสำเร็จ
        } else {
            echo "error";    // เกิดข้อผิดพลาด
        }
        $stmt_delete->close();
    } else {
        echo "not_found"; // ไม่พบข้อมูลเด็ก
    }
    $stmt->close();
} else {
    echo "no_child_id";  // ไม่มี child_id ที่จะลบ
}

$conn->close();
?>
