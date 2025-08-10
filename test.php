<?php
include 'config.php';  // ตรวจสอบการเชื่อมต่อฐานข้อมูล

session_start(); // เริ่มต้นเซสชัน

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ตรวจสอบการอัพโหลดไฟล์จาก $_FILES หรือจาก $_SESSION
    if (isset($_FILES['id_card_front']) && isset($_FILES['id_card_back']) && isset($_FILES['resume']) && isset($_FILES['certificate1']) && isset($_FILES['certificate2']) && isset($_FILES['certificate3'])) {
        
        // กำหนดตำแหน่งที่เก็บไฟล์ที่อัพโหลด
        $target_dir = "uploads/";

        // กำหนดชื่อไฟล์ที่ถูกอัพโหลด
        $id_card_front = $target_dir . basename($_FILES['id_card_front']['name']);
        $id_card_back = $target_dir . basename($_FILES['id_card_back']['name']);
        $resume = $target_dir . basename($_FILES['resume']['name']);
        $certificate1 = $target_dir . basename($_FILES['certificate1']['name']);
        $certificate2 = $target_dir . basename($_FILES['certificate2']['name']);
        $certificate3 = $target_dir . basename($_FILES['certificate3']['name']);

        // ตรวจสอบว่าไฟล์สามารถอัพโหลดได้หรือไม่
        if (move_uploaded_file($_FILES['id_card_front']['tmp_name'], $id_card_front) &&
            move_uploaded_file($_FILES['id_card_back']['tmp_name'], $id_card_back) &&
            move_uploaded_file($_FILES['resume']['tmp_name'], $resume) &&
            move_uploaded_file($_FILES['certificate1']['tmp_name'], $certificate1) &&
            move_uploaded_file($_FILES['certificate2']['tmp_name'], $certificate2) &&
            move_uploaded_file($_FILES['certificate3']['tmp_name'], $certificate3)) {

            // บันทึกไฟล์ใน session
            $_SESSION['caregiver_data2'] = [
                'id_card_front'  => $id_card_front,
                'id_card_back'   => $id_card_back,
                'resume'         => $resume,
                'certificate1'   => $certificate1,
                'certificate2'   => $certificate2,
                'certificate3'   => $certificate3
            ];
        } else {
            echo "การอัพโหลดไฟล์ล้มเหลว!";
            exit();
        }
    }

    // ดึงข้อมูลจาก session
    $first_name = $_SESSION['caregiver_data1']['first_name'] ?? '';
    $last_name = $_SESSION['caregiver_data1']['last_name'] ?? '';
    $gender = $_SESSION['caregiver_data1']['gender'] ?? '';
    $email = $_SESSION['caregiver_data1']['email'] ?? '';
    $age = $_SESSION['caregiver_data1']['age'] ?? '';
    $sub_district = $_SESSION['caregiver_data1']['sub_district'] ?? '';
    $district = $_SESSION['caregiver_data1']['district'] ?? '';
    $province = $_SESSION['caregiver_data1']['province'] ?? '';
    $working_days = $_SESSION['caregiver_data1']['working_days'] ?? '';
    $working_hours = $_SESSION['caregiver_data1']['working_hours'] ?? '';
    $bio = $_SESSION['caregiver_data1']['bio'] ?? '';
    $status = $_SESSION['caregiver_data1']['status'] ?? null; // ถ้าไม่มีค่าให้กำหนดเป็น null
    $type = $_SESSION['caregiver_data1']['type'] ?? '';
    $phone = $_SESSION['caregiver_data1']['phone'] ?? '';
    $password = $_SESSION['caregiver_data1']['password'] ?? '';

    // การเชื่อมต่อกับฐานข้อมูล
    $conn = new mysqli('localhost', 'root', '', 'care_link');
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // บันทึกข้อมูลลงในตาราง caregiver
    $stmt_caregiver = $conn->prepare("INSERT INTO caregiver (first_name, last_name, gender, email, age, sub_district, district, province, working_days, working_hours, bio, status, type, phone, password) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    // ตรวจสอบให้มั่นใจว่า status ถูกกำหนดเป็น NULL หากไม่มีข้อมูล
    if ($status === null) {
        $status = NULL;  // ถ้า status เป็น NULL ต้องใช้ NULL
    }

    // ผูกพารามิเตอร์
    $stmt_caregiver->bind_param('sssssssssssssss', 
        $first_name, 
        $last_name, 
        $gender, 
        $email, 
        $age, 
        $sub_district, 
        $district, 
        $province, 
        $working_days, 
        $working_hours, 
        $bio, 
        $status, 
        $type, 
        $phone, 
        $password
    );

    // Execute statement
    if ($stmt_caregiver->execute()) {
        // ดึง caregiver_id ที่ถูกสร้างขึ้น
        $caregiver_id = $stmt_caregiver->insert_id;

        // คำสั่ง SQL สำหรับการบันทึกข้อมูลไฟล์
        $sql = "INSERT INTO h_caregiver (caregiver_id, id_card_front, id_card_back, resume, certificate1, certificate2, certificate3) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";

        // เตรียมคำสั่ง SQL สำหรับไฟล์
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('issssss', 
            $caregiver_id, 
            $_SESSION['caregiver_data2']['id_card_front'], 
            $_SESSION['caregiver_data2']['id_card_back'], 
            $_SESSION['caregiver_data2']['resume'], 
            $_SESSION['caregiver_data2']['certificate1'], 
            $_SESSION['caregiver_data2']['certificate2'], 
            $_SESSION['caregiver_data2']['certificate3']
        );

        // Execute statement สำหรับไฟล์
        if ($stmt->execute()) {
            header("Location: LOG-IN1.php"); // ไปหน้า LOG-IN1.php
        } else {
            echo "เกิดข้อผิดพลาดในการบันทึกข้อมูล: " . $stmt->error;
        }

        // ปิดการเชื่อมต่อฐานข้อมูล
        $stmt->close();
        $conn->close();
    } else {
        echo "เกิดข้อผิดพลาดในการบันทึกข้อมูลในตาราง caregiver: " . $stmt_caregiver->error;
    }

    // ปิดการเชื่อมต่อกับฐานข้อมูล
    $stmt_caregiver->close();
}
?>
