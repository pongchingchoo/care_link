<?php
include 'config.php'; // เชื่อมต่อฐานข้อมูล

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // รับข้อมูลจากฟอร์ม
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $gender = $_POST['gender'];
    $email = $_POST['email'];
    $age = $_POST['age'];
    $sub_district = $_POST['sub_district'];
    $district = $_POST['district'];
    $province = $_POST['province'];
    $working_days = isset($_POST['working_days']) ? implode(", ", $_POST['working_days']) : '';  // แปลงเป็นสตริง
    $working_hours = $_POST['working_hours'];
    $bio = $_POST['bio'];
    $status = isset($_POST['status']) ? $_POST['status'] : null;
    $type = $_POST['type'];
    $phone = $_POST['phone'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    // ตรวจสอบข้อมูลที่จำเป็น
    if (empty($first_name) || empty($last_name) || empty($email) || empty($password) || empty($phone)) {
        echo "กรุณากรอกข้อมูลให้ครบถ้วน";
        exit();
    }

    // ตรวจสอบอีเมลที่ถูกต้อง
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "อีเมลไม่ถูกต้อง";
        exit();
    }

    // ตรวจสอบเบอร์โทรศัพท์
    if (!preg_match("/^[0-9]{10}$/", $phone)) {
        echo "เบอร์โทรศัพท์ต้องมี 10 หลัก";
        exit();
    }

    // ตรวจสอบว่าอีเมลซ้ำหรือไม่
    $stmt_check = $conn->prepare("SELECT caregiver_id FROM caregiver WHERE email = ?");
    $stmt_check->bind_param("s", $email);
    $stmt_check->execute();
    $stmt_check->store_result();

    if ($stmt_check->num_rows > 0) {
        echo "อีเมลนี้มีผู้ใช้งานแล้ว";
        exit();
    }

    // จัดการอัปโหลดรูปภาพ
    function uploadFile($file) {
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
        $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $uploadDir = 'uploads/';
        
        if ($file['error'] == 0 && in_array($fileExtension, $allowedExtensions)) {
            $fileName = uniqid() . '.' . $fileExtension;
            if (move_uploaded_file($file['tmp_name'], $uploadDir . $fileName)) {
                return $fileName;
            } else {
                return null; // กรณีการอัปโหลดล้มเหลว
            }
        }
        return null;
    }

    // ตรวจสอบการอัปโหลดไฟล์
    $img = isset($_FILES['img']) ? uploadFile($_FILES['img']) : null;

    // บันทึกข้อมูลลงฐานข้อมูล
    $stmt = $conn->prepare("INSERT INTO caregiver (first_name, last_name, gender, email, age, sub_district, district, province, working_days, working_hours, bio, status, type, img, password, phone) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssissssssssbss", $first_name, $last_name, $gender, $email, $age, $sub_district, $district, $province, $working_days, $working_hours, $bio, $status, $type, $img, $password, $phone);



    

    if ($stmt->execute()) {
        header("Location: care_giver4.php"); // ไปหน้า care_giver3.php
    } else {
        echo "เกิดข้อผิดพลาดในการบันทึกข้อมูล";
    }

    $stmt->close();
    $conn->close();
}
?>
