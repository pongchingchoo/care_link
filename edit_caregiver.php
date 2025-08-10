<?php
include 'config.php';

if (isset($_GET["id"])) {
    $caregiver_id = $_GET["id"];

    // ดึงข้อมูลปัจจุบันของผู้ดูแล
    $sql = "SELECT c.*, h.id_card_front, h.id_card_back, h.resume, h.certificate1, h.certificate2, h.certificate3
            FROM caregiver c
            LEFT JOIN h_caregiver h ON c.caregiver_id = h.caregiver_id
            WHERE c.caregiver_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $caregiver_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $caregiver = $result->fetch_assoc();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $caregiver_id = $_POST["caregiver_id"];
    $first_name = $_POST["first_name"];
    $last_name = $_POST["last_name"];
    $gender = $_POST["gender"];
    $email = $_POST["email"];
    $birthdate = $_POST["birthdate"];  // รับค่า birthdate จากฟอร์ม
    $sub_district = $_POST["sub_district"];
    $district = $_POST["district"];
    $province = $_POST["province"];
    $working_days = $_POST["working_days"];
    $working_hours = $_POST["working_hours"];
    $bio = $_POST["bio"];
    $status = $_POST["status"];
    $type = $_POST["type"];
    $phone = $_POST["phone"];
    $price = $_POST["price"];

    // อัปเดตข้อมูลหลักของ caregiver โดยเพิ่ม price และ birthdate เข้าไปด้วย
    $sql = "UPDATE caregiver SET 
                first_name = ?, last_name = ?, gender = ?, email = ?, birthdate = ?, 
                sub_district = ?, district = ?, province = ?, working_days = ?, 
                working_hours = ?, bio = ?, status = ?, type = ?, phone = ?, price = ? 
            WHERE caregiver_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssssssssssd", 
        $first_name, $last_name, $gender, $email, $birthdate, 
        $sub_district, $district, $province, $working_days, 
        $working_hours, $bio, $status, $type, $phone, $price, $caregiver_id
    );

    if ($stmt->execute()) {
        // อัปโหลดไฟล์
        $upload_dir = "uploads/";
        $file_fields = ["id_card_front", "id_card_back", "resume", "certificate1", "certificate2", "certificate3"];

        foreach ($file_fields as $field) {
            if (!empty($_FILES[$field]["name"])) {
                $file_name = basename($_FILES[$field]["name"]);
                $target_file = $upload_dir . $file_name;
                move_uploaded_file($_FILES[$field]["tmp_name"], $target_file);

                // อัปเดตข้อมูลไฟล์ในฐานข้อมูล
                $update_file_sql = "UPDATE h_caregiver SET $field = ? WHERE caregiver_id = ?";
                $update_stmt = $conn->prepare($update_file_sql);
                $update_stmt->bind_param("si", $target_file, $caregiver_id);
                $update_stmt->execute();
            }
        }

        echo "<script>alert('อัปเดตข้อมูลสำเร็จ!'); window.location.href='view_caregiver.php';</script>";
    } else {
        echo "เกิดข้อผิดพลาดในการอัปเดตข้อมูล";
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>แก้ไขข้อมูลผู้ดูแล</title>
</head>
<body>
    <h2>แก้ไขข้อมูลผู้ดูแล</h2>
    <form method="post" enctype="multipart/form-data">
        <input type="hidden" name="caregiver_id" value="<?php echo $caregiver['caregiver_id']; ?>">

        <label>ชื่อ:</label>
        <input type="text" name="first_name" value="<?php echo $caregiver['first_name']; ?>"><br>

        <label>นามสกุล:</label>
        <input type="text" name="last_name" value="<?php echo $caregiver['last_name']; ?>"><br>

        <label>เพศ:</label>
        <select name="gender">
            <option value="ชาย" <?php if ($caregiver['gender'] == "ชาย") echo "selected"; ?>>ชาย</option>
            <option value="หญิง" <?php if ($caregiver['gender'] == "หญิง") echo "selected"; ?>>หญิง</option>
        </select><br>

        <label>อีเมล:</label>
        <input type="email" name="email" value="<?php echo $caregiver['email']; ?>"><br>

        <label>วันเกิด:</label>
        <input type="date" name="birthdate" value="<?php echo $caregiver['birthdate']; ?>"><br>

        <label>อายุ:</label>
        <input type="number" name="age" value="<?php echo $caregiver['age']; ?>"><br>

        <label>ตำบล:</label>
        <input type="text" name="sub_district" value="<?php echo $caregiver['sub_district']; ?>"><br>

        <label>อำเภอ:</label>
        <input type="text" name="district" value="<?php echo $caregiver['district']; ?>"><br>

        <label>จังหวัด:</label>
        <input type="text" name="province" value="<?php echo $caregiver['province']; ?>"><br>

        <label>วันทำงาน:</label>
        <input type="text" name="working_days" value="<?php echo $caregiver['working_days']; ?>"><br>

        <label>เวลาทำงาน:</label>
        <input type="text" name="working_hours" value="<?php echo $caregiver['working_hours']; ?>"><br>

        <label>ประวัติ:</label>
        <textarea name="bio"><?php echo $caregiver['bio']; ?></textarea><br>

        <label>สถานะ:</label>
        <select name="status">
            <option value="รอการอนุมัติ" <?php if ($caregiver['status'] == "รอการอนุมัติ") echo "selected"; ?>>รอการอนุมัติ</option>
            <option value="อนุมัติ" <?php if ($caregiver['status'] == "อนุมัติ") echo "selected"; ?>>อนุมัติ</option>
            <option value="แบน" <?php if ($caregiver['status'] == "แบน") echo "selected"; ?>>แบน</option>
        </select><br>

        <label>ประเภท:</label>
        <input type="text" name="type" value="<?php echo $caregiver['type']; ?>"><br>

        <label>เบอร์โทร:</label>
        <input type="text" name="phone" value="<?php echo $caregiver['phone']; ?>"><br>

        <label>ราคา:</label>
        <input type="number" name="price" value="<?php echo $caregiver['price']; ?>" placeholder="กรอกราคา" step="0.01" min="0"><br>

        <h3>อัปโหลดไฟล์ใหม่ (ถ้าต้องการเปลี่ยน)</h3>
        <label>บัตรประชาชนหน้า:</label>
        <input type="file" name="id_card_front"><br>

        <label>บัตรประชาชนหลัง:</label>
        <input type="file" name="id_card_back"><br>

        <label>เรซูเม่:</label>
        <input type="file" name="resume"><br>

        <label>ใบรับรอง 1:</label>
        <input type="file" name="certificate1"><br>

        <label>ใบรับรอง 2:</label>
        <input type="file" name="certificate2"><br>

        <label>ใบรับรอง 3:</label>
        <input type="file" name="certificate3"><br>

        <button type="submit">บันทึก</button>
    </form>
</body>
</html>
