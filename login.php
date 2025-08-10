<?php
session_start();
include 'config.php'; // เชื่อมต่อฐานข้อมูล

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // กำจัดช่องว่างที่ไม่จำเป็น
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    // ตรวจสอบข้อมูลเบื้องต้น
    if (empty($email) || empty($password)) {
        echo "<script>alert('กรุณากรอกอีเมลและรหัสผ่าน!'); window.history.back();</script>";
        exit();
    }

    // ใช้ Prepared Statement เพื่อป้องกัน SQL Injection
    $sql = "SELECT * FROM parent WHERE email = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        // ตรวจสอบรหัสผ่านด้วย password_verify()
        if ($user && password_verify($password, $user["password"])) {
            // regenerate session ID เพื่อความปลอดภัย (ป้องกัน session fixation)
            session_regenerate_id(true);

            // กำหนด session key เป็น parent_id เพื่อให้หน้า profile_parent.php ดึงข้อมูลได้ตรงกัน
            $_SESSION["parent_id"] = $user["parent_id"];
            $_SESSION["user_name"] = $user["first_name"] . " " . $user["last_name"];
            $_SESSION["user_email"] = $user["email"];

            // สามารถเปลี่ยนเป็น header redirect ได้ หากไม่จำเป็นต้อง alert ก็สามารถใช้ header("Location: home.php");
            echo "<script>alert('เข้าสู่ระบบสำเร็จ!'); window.location.href='home.php';</script>";
            exit();
        } else {
            echo "<script>alert('อีเมลหรือรหัสผ่านไม่ถูกต้อง!'); window.history.back();</script>";
            exit();
        }
    }
    $stmt->close();
}
$conn->close();
?>

