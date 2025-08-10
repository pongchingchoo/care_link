<!-- 
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

include 'config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $booking_id      = $_POST['booking_id'] ?? '';
    $payment_status  = $_POST['payment_status'] ?? '';
    $caregiver_email = $_POST['caregiver_email'] ?? '';
    $caregiver_name  = $_POST['caregiver_name'] ?? '';

    $update_sql = "UPDATE caregiver_booking SET payment_status = ? WHERE booking_id = ?";
    $stmt = $conn->prepare($update_sql);
    if (!$stmt) {
        $_SESSION['error'] = "Prepare failed: " . $conn->error;
        header("Location: view_payment.php");
        exit();
    }

    $stmt->bind_param("ss", $payment_status, $booking_id);
    $stmt->execute();

    if ($stmt->affected_rows > 0 && $payment_status === 'paid') {
        $enableDebug = true; // เปิด debug ชั่วคราว

        $mail = new PHPMailer(true);
        try {
            $mail->SMTPDebug = $enableDebug ? 2 : 0;
            $mail->Debugoutput = 'html';

            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'pongchingchoo@gmail.com';
            $mail->Password   = 'toef utmr taou vctx';  // เปลี่ยนเป็น App Password จริง
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            $mail->CharSet = "UTF-8";
            $mail->SMTPOptions = [
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                ]
            ];

            $mail->setFrom('pongchingchoo@gmail.com', 'CareLink');
            $mail->addAddress($caregiver_email, $caregiver_name);

            $mail->isHTML(true);
            $mail->Subject = 'แจ้งเตือน: มีการชำระเงินแล้ว';
            $mail->Body    = "สวัสดีคุณ $caregiver_name,<br><br>"
                           . "มีการชำระเงินสำหรับรหัสการจอง <strong>$booking_id</strong> แล้ว<br>"
                           . "โปรดเข้าสู่ระบบเพื่อดูรายละเอียดเพิ่มเติม<br><br>"
                           . "ขอบคุณที่ใช้บริการ CareLink";

            $mail->send();
            $_SESSION['message'] = "ส่งอีเมลแจ้งชำระเงินเรียบร้อยแล้ว";
        } catch (Exception $e) {
            $_SESSION['error'] = "ส่งอีเมลล้มเหลว: {$mail->ErrorInfo}";
        }
    } else {
        $_SESSION['message'] = "อัปเดตสถานะการชำระเงินเรียบร้อยแล้ว";
    }

    header("Location: view_payment.php");
    exit();
}
 -->
<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

include 'config.php';  // เชื่อมต่อฐานข้อมูล
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $booking_id      = $_POST['booking_id'] ?? '';
    $payment_status  = $_POST['payment_status'] ?? '';
    $caregiver_email = $_POST['caregiver_email'] ?? '';
    $caregiver_name  = $_POST['caregiver_name'] ?? '';

    // ตรวจสอบอีเมลผู้รับ
    if (empty($caregiver_email) || !filter_var($caregiver_email, FILTER_VALIDATE_EMAIL)) {
        echo "อีเมลผู้รับไม่ถูกต้อง";
        exit;
    }

    // อัปเดตสถานะการชำระเงินในฐานข้อมูล
    $update_sql = "UPDATE caregiver_booking SET payment_status = ? WHERE booking_id = ?";
    $stmt = $conn->prepare($update_sql);
    if (!$stmt) {
        echo "Prepare failed: " . $conn->error;
        exit();
    }
    $stmt->bind_param("ss", $payment_status, $booking_id);

    if ($stmt->execute()) {
        if ($payment_status === 'paid') {
            $mail = new PHPMailer(true);
            try {
                // เปิด debug (0 = ปิด, 2 = เปิดดูขั้นตอน)
                $mail->SMTPDebug = 0;
                $mail->Debugoutput = 'html';

                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'pongchingchoo@gmail.com';  // อีเมลจริงของคุณ
                $mail->Password = 'toef utmr taou vctx';      // App Password จริง ๆ ของคุณ
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                // ตั้ง Charset UTF-8 รองรับภาษาไทย
                $mail->CharSet = "UTF-8";

                $mail->setFrom('pongchingchoo@gmail.com', 'CareLink');
                $mail->addAddress($caregiver_email, $caregiver_name);

                $mail->isHTML(true);
                $mail->Subject = 'แจ้งเตือน: มีการชำระเงินแล้ว';
                $mail->Body    = "สวัสดีคุณ $caregiver_name,<br><br>"
                               . "มีการชำระเงินสำหรับรหัสการจอง <strong>$booking_id</strong> แล้ว<br>"
                               . "โปรดเข้าสู่ระบบเพื่อดูรายละเอียดเพิ่มเติม<br><br>"
                               . "ขอบคุณที่ใช้บริการ CareLink";

                $mail->send();
                $_SESSION['message'] = 'ส่งอีเมลแจ้งชำระเงินเรียบร้อยแล้ว';
            header('Location: view_payment.php');
            exit();
        } catch (Exception $e) {
            $_SESSION['error'] = "ไม่สามารถส่งอีเมลได้: {$mail->ErrorInfo}";
            header('Location: view_payment.php');
            exit();
        }
    } else {
        $_SESSION['message'] = "อัปเดตสถานะเรียบร้อย แต่สถานะไม่ใช่ paid";
        header('Location: view_payment.php');
        exit();
    }
} else {
    $_SESSION['error'] = "อัปเดตฐานข้อมูลไม่สำเร็จ";
    header('Location: view_payment.php');
    exit();
}
}
?>