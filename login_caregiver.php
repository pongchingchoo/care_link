<?php
session_start();
include 'config.php'; // เชื่อมต่อฐานข้อมูล

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $password = $_POST["password"];

    // ตรวจสอบว่า Email มีอยู่ในระบบหรือไม่ (ตาราง caregiver)
    $sql = "SELECT * FROM caregiver WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user["password"])) {
        $_SESSION["user_id"] = $user["caregiver_id"];
        $_SESSION["user_name"] = $user["first_name"] . " " . $user["last_name"];
        $_SESSION["user_email"] = $user["email"];
        echo "<script>alert('เข้าสู่ระบบสำเร็จ!'); window.location.href='caregiver_dashboard.php';</script>";
    } else {
        echo "<script>alert('อีเมลหรือรหัสผ่านไม่ถูกต้อง!'); window.history.back();</script>";
    }
}
?>
