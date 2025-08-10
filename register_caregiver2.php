<?php
include 'config.php';  
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['id_card_front'])  && isset($_FILES['resume']) && isset($_FILES['certificate1'])) {
        
        $target_dir = "uploads/";

        $id_card_front = $target_dir . basename($_FILES['id_card_front']['name']);
        
        $resume = $target_dir . basename($_FILES['resume']['name']);
        $certificate1 = $target_dir . basename($_FILES['certificate1']['name']);

        $certificate2 = isset($_FILES['certificate2']) && $_FILES['certificate2']['error'] == UPLOAD_ERR_OK 
            ? $target_dir . basename($_FILES['certificate2']['name']) 
            : NULL;

        $certificate3 = isset($_FILES['certificate3']) && $_FILES['certificate3']['error'] == UPLOAD_ERR_OK 
            ? $target_dir . basename($_FILES['certificate3']['name']) 
            : NULL;

        // ดึงข้อมูลจาก session
        $first_name   = $_SESSION['caregiver_data1']['first_name'] ?? '';
        $last_name    = $_SESSION['caregiver_data1']['last_name'] ?? '';
        $gender       = $_SESSION['caregiver_data1']['gender'] ?? '';
        $email        = $_SESSION['caregiver_data1']['email'] ?? '';
        $birthdate    = $_SESSION['caregiver_data1']['birthdate'] ?? ''; // เพิ่ม birthdate
        $age          = $_SESSION['caregiver_data1']['age'] ?? '';
        $sub_district = $_SESSION['caregiver_data1']['sub_district'] ?? '';
        $district     = $_SESSION['caregiver_data1']['district'] ?? '';
        $province     = $_SESSION['caregiver_data1']['province'] ?? '';
        $working_days = $_SESSION['caregiver_data1']['working_days'] ?? '';
        $working_hours= $_SESSION['caregiver_data1']['working_hours'] ?? '';
        $bio          = $_SESSION['caregiver_data1']['bio'] ?? '';
        $status       = $_SESSION['caregiver_data1']['status'] ?? null;
        $type         = $_SESSION['caregiver_data1']['type'] ?? '';
        $phone        = $_SESSION['caregiver_data1']['phone'] ?? '';
        $price        = $_SESSION['caregiver_data1']['price'] ?? '';
        $password     = $_SESSION['caregiver_data1']['password'] ?? '';

        if (move_uploaded_file($_FILES['id_card_front']['tmp_name'], $id_card_front) &&
            move_uploaded_file($_FILES['resume']['tmp_name'], $resume) &&
            move_uploaded_file($_FILES['certificate1']['tmp_name'], $certificate1)) {

            if ($certificate2) move_uploaded_file($_FILES['certificate2']['tmp_name'], $certificate2);
            if ($certificate3) move_uploaded_file($_FILES['certificate3']['tmp_name'], $certificate3);

            $conn = new mysqli('localhost', 'root', '', 'care_link');
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            $check_sql = "SELECT * FROM caregiver WHERE email = ? ";
            $check_stmt = $conn->prepare($check_sql);
            $check_stmt->bind_param("s", $email);
            $check_stmt->execute();
            $check_result = $check_stmt->get_result();
            if ($check_result->num_rows > 0) {
                echo "อีเมลซ้ำ กรุณาใช้ข้อมูลอื่น";
                $check_stmt->close();
                $conn->close();
                exit();
            }
            $check_stmt->close();

            $stmt_caregiver = $conn->prepare("INSERT INTO caregiver 
            (first_name, last_name, gender, email, birthdate, age, sub_district, district, province, working_days, working_hours, bio, status, type, phone, price, password) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

            if ($status === null) {
                $status = NULL;
            }

            $price = (double)$price;

            $stmt_caregiver->bind_param('ssssssssssssssdds', 
                $first_name, 
                $last_name, 
                $gender, 
                $email, 
                $birthdate, // เพิ่ม birthdate
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
                $price, 
                $password
            );

            if ($stmt_caregiver->execute()) {
                $caregiver_id = $stmt_caregiver->insert_id;

                $sql = "INSERT INTO h_caregiver (caregiver_id, id_card_front, resume, certificate1, certificate2, certificate3) 
            VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('isssss', 
        $caregiver_id, 
        $id_card_front, 
        $resume, 
        $certificate1, 
        $certificate2, 
        $certificate3
    );

                if ($stmt->execute()) {
                    header("Location: care_giver5.php");
                    exit();
                } else {
                    echo "เกิดข้อผิดพลาดในการบันทึกข้อมูล: " . $stmt->error;
                }

                $stmt->close();
                $conn->close();
            } else {
                echo "เกิดข้อผิดพลาดในการบันทึกข้อมูลในตาราง caregiver: " . $stmt_caregiver->error;
            }

            $stmt_caregiver->close();
        } else {
            echo "การอัพโหลดไฟล์ล้มเหลว!";
        }
    } else {
        echo "กรุณาเลือกไฟล์ที่จำเป็น (ID Card และ Resume) เพื่ออัพโหลด!";
    }
}
?>



