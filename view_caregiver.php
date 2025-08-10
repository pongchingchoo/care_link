<?php
include 'config.php'; // เชื่อมต่อฐานข้อมูล

// ตรวจสอบว่ามีการเปลี่ยนสถานะหรือไม่
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["caregiver_id"]) && isset($_POST["status"])) {
    $caregiver_id = $_POST["caregiver_id"];
    $status = $_POST["status"];

    // อัปเดตสถานะลงในฐานข้อมูล
    $sql = "UPDATE caregiver SET status = ? WHERE caregiver_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $status, $caregiver_id);

    if ($stmt->execute()) {
        echo "success"; // แจ้งว่าอัปเดตสำเร็จ
    } else {
        echo "error"; // แจ้งว่ามีข้อผิดพลาด
    }

    $stmt->close();
    exit(); // จบการทำงานตรงนี้
}

$search = isset($_GET['search']) ? $_GET['search'] : '';

$sql = "SELECT c.caregiver_id, c.first_name, c.last_name, c.gender, c.email, c.birthdate, c.sub_district, c.district, c.province, 
               c.working_days, c.working_hours, c.bio, c.status, c.type, c.phone, c.price, 
               h.id_card_front, h.id_card_back, h.resume, h.certificate1, h.certificate2, h.certificate3
        FROM caregiver c
        LEFT JOIN h_caregiver h ON c.caregiver_id = h.caregiver_id";

if (!empty($search)) {
    $search = $conn->real_escape_string($search);
    $sql .= " WHERE c.first_name LIKE '%$search%' OR c.last_name LIKE '%$search%'";
}

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="th">
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการข้อมูลผู้ดูแล</title>
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
            white-space: nowrap;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #007bff;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        select, a {
            padding: 5px;
            border-radius: 5px;
            text-decoration: none;
        }
        select {
            border: 1px solid #ccc;
        }
        .table-container a {
            color: white;
            padding: 5px 10px;
            background-color: #28a745;
            display: inline-block;
        }
        .table-container a:hover {
            background-color: #218838;
        }
        .delete-btn {
            background-color: #dc3545;
        }
        .delete-btn:hover {
            background-color: #c82333;
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
    <script>
        function updateStatus(caregiver_id, status) {
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "view_caregiver.php", true);
            xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function () {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    if (xhr.responseText.trim() === "success") {
                        alert("อัปเดตสถานะสำเร็จ!");
                    } else {
                        alert("เกิดข้อผิดพลาดในการอัปเดตสถานะ");
                    }
                }
            };
            xhr.send("caregiver_id=" + caregiver_id + "&status=" + status);
        }
    </script>
</head>
<body>
<header>
    <div class="head">
        <div class="logo">
            <img src="logo2.png" alt="Logo" height="60px">
        </div>
        <nav>
        <a href="#" onclick="location.href='admin.php'">หน้าหลัก</a>
            <a href="#" onclick="location.href='view_parent.php'">การจัดการผู้ปกครอง</a>
            <!-- <a href="#">ระดับการดูแล</a> -->
            <a href="#" style="color: #ff9100;">การจัดการผู้ดูแล</a>
            <a href="#" onclick="location.href='view_booking.php'">การจัดการจอง</a>
            <a href="#" onclick="location.href='view_contract.php'">การจัดการทำสัญญา</a>
            <a href="#" onclick="location.href='view_payment.php'">การจัดการชำระเงิน</a>
        </nav>

        <button class="cta-button" onclick="location.href='login_admin.php'">ลงชื่อออก</button>
    
    </div>

</header>

    <h2>ข้อมูลผู้ดูแลทั้งหมด</h2>
    <form method="GET" class="mb-4" style="text-align: center; margin-bottom: 20px;">
    <input type="text" name="search" placeholder="ค้นหาชื่อหรือนามสกุลผู้ดูแล" 
           value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>" 
           style="padding: 10px; width: 300px; border-radius: 5px; border: 1px solid #ccc;">
    <button type="submit" style="padding: 10px 20px; border-radius: 5px; background-color: #007bff; color: white; border: none;">ค้นหา</button>
</form>
    <div class="table-container">
    <table>
            <tr>
                <th>รหัส</th>
                <th>ชื่อ</th>
                <th>นามสกุล</th>
                <th>เพศ</th>
                <th>อีเมล</th>
                <th>อายุ</th>
                <th>วันเกิด</th>
                <th>ตำบล</th>
                <th>อำเภอ</th>
                <th>จังหวัด</th>
                <th>วันทำงาน</th>
                <th>เวลาทำงาน</th>
                <th>สถานะ</th>
                <th>ประเภท</th>
                <th>เบอร์โทร</th>
                <th>ค่าจ้าง</th>
                <th>ไฟล์แนบ</th>
                <th>จัดการ</th>
                <th>ประวัติ</th>
            </tr>

            <?php while ($row = $result->fetch_assoc()) { 
                // คำนวณอายุจากวันเกิด
                $birthdate = new DateTime($row['birthdate']);
                $today = new DateTime();
                $age = $today->diff($birthdate)->y;
            ?>
                <tr>
                    <td><?php echo $row["caregiver_id"]; ?></td>
                    <td><?php echo $row["first_name"]; ?></td>
                    <td><?php echo $row["last_name"]; ?></td>
                    <td><?php echo $row["gender"]; ?></td>
                    <td><?php echo $row["email"]; ?></td>
                    <td><?php echo $age; ?> ปี</td>
                    <td><?php echo $row["birthdate"]; ?></td>
                    <td><?php echo $row["sub_district"]; ?></td>
                    <td><?php echo $row["district"]; ?></td>
                    <td><?php echo $row["province"]; ?></td>
                    <td><?php echo $row["working_days"]; ?></td>
                    <td><?php echo $row["working_hours"]; ?></td>
                    <td>
                        <select onchange="updateStatus(<?php echo $row['caregiver_id']; ?>, this.value)">
                            <option value="รอการอนุมัติ" <?php if ($row["status"] == "รอการอนุมัติ") echo "selected"; ?>>รอการอนุมัติ</option>
                            <option value="อนุมัติ" <?php if ($row["status"] == "อนุมัติ") echo "selected"; ?>>อนุมัติ</option>
                            <option value="แบน" <?php if ($row["status"] == "แบน") echo "selected"; ?>>แบน</option>
                        </select>
                    </td>
                    <td><?php echo $row["type"]; ?></td>
                    <td><?php echo $row["phone"]; ?></td>
                    <td><?php echo $row["price"]; ?></td>
                    <td>
                        <a href="<?php echo $row["id_card_front"]; ?>" target="_blank">บัตรหน้า</a> |
                        <a href="<?php echo $row["id_card_back"]; ?>" target="_blank">บัตรหลัง</a> |
                        <a href="<?php echo $row["resume"]; ?>" target="_blank">เรซูเม่</a> |
                        <a href="<?php echo $row["certificate1"]; ?>" target="_blank">ใบรับรอง1</a> |
                        <a href="<?php echo $row["certificate2"]; ?>" target="_blank">ใบรับรอง2</a> |
                        <a href="<?php echo $row["certificate3"]; ?>" target="_blank">ใบรับรอง3</a>
                    </td>
                    <td>
                        <a href="edit_caregiver.php?id=<?php echo $row['caregiver_id']; ?>">แก้ไข</a>
                        
                    </td>
                    <td><?php echo $row["bio"]; ?></td>
                </tr>
            <?php } ?>
        </table>
    </div>
</body>
</html>

<?php $conn->close(); ?>

<!-- <a href="delete_caregiver.php?id=<?php echo $row['caregiver_id']; ?>" class="delete-btn" onclick="return confirm('คุณแน่ใจหรือไม่ว่าต้องการลบข้อมูลนี้?');">ลบ</a> -->