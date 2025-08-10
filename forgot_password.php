<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    require 'config.php'; // ไฟล์เชื่อมต่อฐานข้อมูล

    $email = $_POST['email'];
    $token = bin2hex(random_bytes(50)); // สร้างโทเค็นรีเซ็ตรหัสผ่าน
    $expires = date("Y-m-d H:i:s", strtotime("+10 hour")); // กำหนดหมดอายุ 1 ชม.

    // บันทึกโทเค็นลงฐานข้อมูล
    $sql = "INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $email, $token, $expires);
    $stmt->execute();}

$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'pongchingchoo@gmail.com'; // เปลี่ยนเป็นอีเมลของคุณ
    $mail->Password   = 'hsfotnvvwmozvsch';   // ใช้ App Password แทนรหัส Gmail
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // หรือ PHPMailer::ENCRYPTION_SMTPS ถ้าใช้ port 465
    $mail->Port       = 587; // 465 ถ้าใช้ SSL

    $mail->setFrom('your-email@gmail.com', 'Care Link');
    $mail->addAddress($email);

    $mail->isHTML(true);
    $mail->Subject = 'Password Reset';
    $mail->Body    = "Click <a href='http://localhost/care_link/reset_password.php?token=$token'>here</a> to reset your password.";

    $mail->send();
    echo "Please check your email for a password reset link.";
} catch (Exception $e) {
    echo "Email could not be sent. Mailer Error: {$mail->ErrorInfo}";
}
?>


<form method="POST">
    <input type="email" name="email" placeholder="Enter your email" required>
    <button type="submit">Send Reset Link</button>
</form>
