<!-- http://localhost/care_link/view_parent.php -->
<?php
// เรียกใช้ไฟล์ config.php
include 'config.php';

// ตรวจสอบว่ามีการส่งฟอร์มหรือไม่
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["create"])) {
    // รับข้อมูลจากฟอร์ม
    $first_name      = $_POST["first_name"];
    $last_name       = $_POST["last_name"];
    $sub_district    = $_POST["sub_district"];
    $district        = $_POST["district"];
    $family_members  = $_POST["family_members"];
    $guardian_status = $_POST["guardian_status"];
    $total_children  = $_POST["total_children"];
    $pets_in_home    = $_POST["pets_in_home"];
    $housing_type    = $_POST["housing_type"];
    $housing_detail  = $_POST["housing_detail"];
    $email           = $_POST["email"];
    $password        = $_POST["password"];
    $confirm_password= $_POST["confirm_password"];
    $phone_number = $_POST["phone_number"];

    // ตรวจสอบว่ามีช่องใดว่างหรือไม่
    if (empty($first_name) || empty($last_name) || empty($sub_district) || empty($district) || 
        empty($family_members) || empty($guardian_status) || empty($total_children) || 
        empty($pets_in_home) || empty($housing_type) || empty($housing_detail) || 
        empty($email) || empty($password) || empty($confirm_password) || empty($phone_number)) {
        
        echo "<script>alert('สมัครไม่สำเร็จ! กรุณากรอกข้อมูลให้ครบถ้วน'); window.history.back();</script>";
        exit();
    }
    
    // ตรวจสอบว่ารหัสผ่านและยืนยันรหัสผ่านตรงกันหรือไม่
    if ($password !== $confirm_password) {
        echo "<script>alert('รหัสผ่านและยืนยันรหัสผ่านไม่ตรงกัน กรุณาลองใหม่อีกครั้ง'); window.history.back();</script>";
        exit();
    }
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

if (!empty($search)) {
    // ค้นหาด้วย first_name หรือ last_name ที่ตรงกับข้อความค้นหา
    $sql = "SELECT * FROM parent 
            WHERE first_name LIKE ? OR last_name LIKE ?";
    $stmt = $conn->prepare($sql);
    $searchParam = "%$search%";
    $stmt->bind_param("ss", $searchParam, $searchParam);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $sql = "SELECT * FROM parent";
    $result = $conn->query($sql);
}

    // ตรวจสอบว่า Email หรือ Password มีอยู่ในฐานข้อมูลแล้วหรือไม่
    $check_sql = "SELECT * FROM parent WHERE email = ? OR password = ?";
    $stmt = $conn->prepare($check_sql);
    $stmt->bind_param("ss", $email, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<script>alert('อีเมลหรือรหัสผ่านนี้ถูกใช้งานแล้ว! กรุณาใช้ข้อมูลใหม่'); window.history.back();</script>";
        exit();
    }
    
    // เข้ารหัสรหัสผ่าน
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // คำสั่ง SQL สำหรับเพิ่มข้อมูล
    $sql = "INSERT INTO parent (
        first_name, last_name, sub_district, district, family_members, guardian_status, 
        total_children, pets_in_home, housing_type, housing_detail, email, password, phone_number
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    
    $stmt = $conn->prepare($sql);
$stmt->bind_param(
    "sssssssssssss",
    $first_name, $last_name, $sub_district, $district, $family_members, 
    $guardian_status, $total_children, $pets_in_home, $housing_type, 
    $housing_detail, $email, $hashed_password, $phone
);


    if ($stmt->execute()) {
        echo "<script>alert('สมัครสำเร็จ!'); window.location.href='http://localhost/care_link/LOG-IN.php';</script>";
    } else {
        echo "<script>alert('สมัครไม่สำเร็จ! กรุณาลองอีกครั้ง');</script>";
    }
}

?>


<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แสดงข้อมูลผู้ปกครอง</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f8f9fa;
        }
        h2 {
            text-align: center;
            color: #333;
        }
        .table-container {
            width: 100%;
            overflow-x: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }
        th {
            background-color: #007bff;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        a {
            color: black;
            padding: 5px 10px;
            border-radius: 5px;
            text-decoration: none;
        }
        .edit-btn {
            background-color: #ffc107;
        }
        .edit-btn:hover {
            background-color: #e0a800;
        }
        .delete-btn {
            background-color: #dc3545;
        }
        .delete-btn:hover {
            background-color: #c82333;
        }
        .action-links {
            display: flex;
            justify-content: center;
            gap: 10px;
        }
        body {
    padding: 0;
    margin: 0;
    width: 100%;
    height: 100%;
    font-family: 'Arial', sans-serif;
    background-color: #fff;
    position: absolute;
}



header{
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 20px;
    margin: 0 10% ;

}
.head {
    box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    background-color: white;
    padding: 10px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid #e0e0e0;
    width: 100%;
    border-radius: 50px;

}

.head nav a {
    text-decoration: none;
    color: #333;
    margin: 0 15px;
    font-size: 16px;
}



.icon-circle {
    margin-left: 50px;
    width: 54px;
    height: 50px;
    background-color: #ddd;
    border-radius: 100%;
    display: flex;
    justify-content: center;
    align-items: center;

}
.icon-circle{
    font-size: 20px;
}
header .cta-button {
    background-color: #00a99d;
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 20px;
    font-size: 16px;
    cursor: pointer;
}

.hero-section {
    text-align: center;
    background-color: #fff;
    display: flex;
    justify-content: center;
    align-items: center;
}
.hero-section .banner img{
    padding-top: 7px;
    width: 570px;
    height: 570px;
}
.hero-section h1 {
    font-size: 100px;
    color: #ff9100;
    margin-bottom: 20px;
}

.hero-section .cta {
    background-color: #00a99d;
    color: white;
    border: none;
    padding: 15px 30px;
    border-radius: 5px;
    font-size: 18px;
    cursor: pointer;
}


.features {
    position: relative;
    background-color: #fff;
    box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.1);
    display: flex;
    justify-content: space-around;
    align-items: center;
    padding: 40px 20px;
    margin: 0 60px;
    margin-bottom: 20px;
    bottom: 40px;
}

.feature {
    text-align: center;
    max-width: 250px;
    
}

.feature img {

    max-width: 80px;
    margin-bottom: 15px;
}

.feature h3 {
    font-size: 18px;
    color: #333;
    margin-bottom: 10px;
}
.question{
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin: 0 50px;
}
.question img{
    width: 750px;
    height: 500px;
    padding-bottom: 30px;
    position: relative;
    top: 50px;
    object-fit: cover; /* ปรับให้รูปพอดีในกรอบ */
}
.faq {
    padding-bottom: 20px;
    background-color: #fff;

}

.faq h2 {
    font-size: 40px;
    text-align: center;
    color: #333;
    margin-bottom: 30px;
}


  .faq-item {
    width: 600px;
    background-color: #ff9100;
    color: white;
    padding: 20px;
    border-radius: 10px;
    margin: 10px 0px;
    font-size: 25px;
    cursor: pointer;
    border: 1px solid #ddd;
    border-radius: 5px;
    margin: 10px 0;
    overflow: hidden;
  }
  .faq-question {

    cursor: pointer;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  .faq-answer {
    display: none;
    background-color: #ff9100;
    border-top: 1px solid #ddd;
  }
  .faq-icon {
    font-weight: bold;
    color: #555;
  }
    </style>
</head>
<body>
<header>
    <div class="head">
        <div class="logo">
            <img src="logo2.png" alt="Logo" height="60px">
        </div>
        <nav>
        <a href="#" onclick="location.href='admin.php'">หน้าหลัก</a>
            <a href="#" style="color: #ff9100;">การจัดการผู้ปกครอง</a>
            <a href="#" onclick="location.href='view_caregiver.php'">การจัดการผู้ดูแล</a>
            <a href="#" onclick="location.href='view_booking.php'">การจัดการจอง</a>
            <a href="#" onclick="location.href='view_contract.php'">การจัดการทำสัญญา</a>
            <a href="#" onclick="location.href='view_payment.php'">การจัดการชำระเงิน</a>
        </nav>

        <button class="cta-button" onclick="location.href='login_admin.php'">ลงชื่อออก</button>
    
    </div>

</header>

    <h2>รายการข้อมูลผู้ปกครอง</h2>
    <form method="GET" class="mb-4" style="text-align: center; margin-bottom: 20px;">
    <input type="text" name="search" placeholder="ค้นหาชื่อหรือนามสกุลผู้ปกครอง" 
           value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>" 
           style="padding: 10px; width: 300px; border-radius: 5px; border: 1px solid #ccc;">
    <button type="submit" style="padding: 10px 20px; border-radius: 5px; background-color: #007bff; color: white; border: none;">ค้นหา</button>
</form>

    <table>
        <tr>
            <th>ID</th>
            <th>ชื่อ</th>
            <th>นามสกุล</th>
            <th>ตำบล</th>
            <th>อำเภอ</th>
            <th>สมาชิกครอบครัว</th>
            <th>สถานะผู้ปกครอง</th>
            <th>จำนวนเด็ก</th>
            <th>สัตว์เลี้ยง</th>
            <th>ประเภทที่พัก</th>
            <th>รายละเอียดที่พัก</th>
            <th>อีเมล</th>
            <!-- <th>เบอร์ติดต่อ</th> -->
            <th>การจัดการ</th>
        </tr>
        <?php

        // เชื่อมต่อกับฐานข้อมูล
        include 'config.php';
    
        $search = isset($_GET['search']) ? trim($_GET['search']) : '';
    
        // คำสั่ง SQL สำหรับค้นหาข้อมูลผู้ปกครอง
        if (!empty($search)) {
            $sql = "SELECT parent_id, first_name, last_name, sub_district, district, family_members, guardian_status, total_children, pets_in_home, housing_type, housing_detail, email, phone_number
                    FROM parent
                    WHERE first_name LIKE ? OR last_name LIKE ?";
            $stmt = $conn->prepare($sql);
            $searchParam = "%$search%";
            $stmt->bind_param("ss", $searchParam, $searchParam);
            $stmt->execute();
            $result = $stmt->get_result();
        } else {
            $sql = "SELECT parent_id, first_name, last_name, sub_district, district, family_members, guardian_status, total_children, pets_in_home, housing_type, housing_detail, email, phone_number FROM parent";
            $result = $conn->query($sql);
        }


        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>{$row['parent_id']}</td>
                        <td>{$row['first_name']}</td>
                        <td>{$row['last_name']}</td>
                        <td>{$row['sub_district']}</td>
                        <td>{$row['district']}</td>
                        <td>{$row['family_members']}</td>
                        <td>{$row['guardian_status']}</td>
                        <td>{$row['total_children']}</td>
                        <td>{$row['pets_in_home']}</td>
                        <td>{$row['housing_type']}</td>
                        <td>{$row['housing_detail']}</td>
                        <td>{$row['email']}</td>

                        <td class='action-links'>
                                <a href='edit_parent.php?id={$row['parent_id']}' class='edit-btn'>แก้ไข</a>
                                
                        </td>
                      </tr>";
            }
        } else {
            echo "<tr><td colspan='12'>ไม่มีข้อมูล</td></tr>";
        }
        ?>
    </table>

</body>
</html>

<?php
$conn->close();
?>
<!-- <td>{$row['phone_number']}</td> 
 <a href='delete_parent.php?id={$row['parent_id']}' class='delete-btn' onclick='return confirm(\"คุณแน่ใจหรือไม่ว่าต้องการลบ?\")'>ลบ</a> -->