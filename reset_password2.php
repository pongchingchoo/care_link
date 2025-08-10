<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    require 'config.php';

    $token = $_POST['token'];
    $new_password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // ตรวจสอบโทเค็นในฐานข้อมูล
    $sql = "SELECT email FROM password_resets WHERE token = ? AND expires_at > NOW()";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $stmt->bind_result($email);
    $stmt->fetch();
    $stmt->close(); // ปิด statement ก่อนใช้งานใหม่

    if ($email) {
        // อัปเดตรหัสผ่านใหม่ในตาราง parent
        $sql = "UPDATE caregiver SET password = ? WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $new_password, $email);
        $stmt->execute();
        $stmt->close();

        // ลบโทเค็นออกจากฐานข้อมูล
        $sql = "DELETE FROM password_resets WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->close();

        echo "Your password has been reset. <a href='LOG-IN.php'>Login now</a>";
    } else {
        echo "Invalid or expired token.";
    }

    $conn->close(); // ปิดการเชื่อมต่อ MySQL
}
?>


<form method="POST">
    <input type="hidden" name="token" value="<?php echo $_GET['token']; ?>">
    <input type="password" name="password" placeholder="New password" required>
    <button type="submit">Reset Password</button>
</form>
