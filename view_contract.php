<?php
include 'config.php';        // $conn

// รับคำค้น
$search = isset($_GET['search']) ? $_GET['search'] : '';

$sql = "SELECT c.caregiver_id, c.first_name, c.last_name, c.gender,
               c.email, c.birthdate, c.sub_district, c.district, c.province,
               c.working_days, c.working_hours, c.bio, c.status, c.type,
               c.phone, c.price, c.contract_type, c.contract_status,
               cc.start_date, cc.end_date, cc.amount, cc.pay, cc.contract_image,cc.contract_id
        FROM caregiver c
        LEFT JOIN caregiver_contract cc ON c.caregiver_id = cc.caregiver_id
        WHERE (c.first_name LIKE ? OR c.last_name LIKE ?)
          AND c.contract_type <> ''";

$stmt = $conn->prepare($sql) or die("Prepare failed: ".$conn->error);
$searchTerm = "%$search%";
$stmt->bind_param('ss', $searchTerm, $searchTerm);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการข้อมูลผู้ดูแล</title>
    <style>


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
  body {
            font-family: Arial, sans-serif;
            margin: 0;
            background-color: #f8f9fa;
        }

        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }

        .table-container {
            width: 100%;
            overflow-x: auto;
            display: flex;
            justify-content: center;
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
            padding: 12px;
        }

        th {
            background-color: #007bff;
            color: white;
            font-weight: bold;
            text-transform: uppercase;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        /* ปรับปรุงปุ่มและลิงก์ */
        .table-container a {
            color: white;
            padding: 8px 12px;
            background-color: #28a745;
            border-radius: 5px;
            text-decoration: none;
            font-size: 14px;
            font-weight: bold;
            transition: 0.3s;
        }

        .table-container a:hover {
            background-color: #218838;
        }

        .delete-btn {
            background-color: #dc3545;
            padding: 8px 12px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            transition: 0.3s;
        }

        .delete-btn:hover {
            background-color: #c82333;
        }

        /* ปรับปรุง select dropdown */
        select {
            padding: 8px;
            border-radius: 5px;
            border: 1px solid #ccc;
            background-color: #fff;
            font-size: 14px;
            cursor: pointer;
        }

        select:focus {
            outline: none;
            border-color: #007bff;
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
            <a href="#" onclick="location.href='view_parent.php'">การจัดการผู้ปกครอง</a>
            <a href="#" onclick="location.href='view_caregiver.php'">การจัดการผู้ดูแล</a>
            <a href="#" onclick="location.href='view_booking.php'">การจัดการจอง</a>
            <a href="#" style="color: #ff9100;">การจัดการทำสัญญา</a>
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
            <th>ประเภท</th>

            <th>สัญญา</th>

            <th>สถานะ</th>
            <th>วันที่เริ่มสัญญา</th>
            <th>วันที่จบสัญญา</th>
            <th>รูปการชำระเงิน</th>
            <th>รูปหนังสือสัญญา</th>
            <!-- <th>จัดการ</th> -->
        </tr>
        <?php 


$current_date = date('Y-m-d'); // วันที่ปัจจุบัน

while ($row = $result->fetch_assoc()) {
    // แปลง end_date เป็น timestamp เพื่อเปรียบเทียบ
    if (strtotime($row["end_date"]) <= strtotime($current_date)) {

        $new_status = "รออนุมัติ"; // สถานะใหม่
        $caregiver_id = (int)$row['caregiver_id'];

        // อัปเดตสถานะในฐานข้อมูล caregiver
        $update_sql = "UPDATE caregiver SET contract_status = ? WHERE caregiver_id = ?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param('si', $new_status, $caregiver_id);
        $stmt->execute();
        $stmt->close();

        $display_status = $new_status;

    } else {
        // ถ้ายังไม่หมดอายุ ใช้สถานะปัจจุบัน
        $display_status = $row["contract_status"];
    }




    // ส่วนที่แสดงผลในตาราง
    echo "<tr>";
    echo "<td>" . $row["caregiver_id"] . "</td>";
    echo "<td>" . $row["first_name"] . "</td>";
    echo "<td>" . $row["last_name"] . "</td>";
    echo "<td>" . $row["gender"] . "</td>";
    echo "<td>" . $row["email"] . "</td>";
    echo "<td>" . $row["type"] . "</td>";

    echo "<td>";
switch ($row["amount"]) {
    case 500:
        echo "1 เดือน";
        break;
    case 1500:
        echo "3 เดือน";
        break;
    case 2500:
        echo "6 เดือน";
        break;
    case 4000:
        echo "1 ปี";
        break;
    default:
        echo $row["amount"]; // กรณีอื่นๆ แสดงค่าตามจริง
        break;
}
echo "</td>";

    echo "<td>" . $row["status"] . "</td>"; // แสดงวันที่เริ่มต้น
    echo "<td>" . $row["start_date"] . "</td>"; // แสดงวันที่เริ่มต้น
    echo "<td>" . $row["end_date"] . "</td>";   // แสดงวันที่สิ้นสุด
    


    

/* ---------- ปุ่มเปิดสลิปการชำระเงิน (pay) ---------- */
if (!empty($row['pay'])) {
    $payUrl = 'file_blob.php?id=' . $row['contract_id'] . '&f=pay';
    echo '<td>
            <a href="' . $payUrl . '" target="_blank"
               style="
                 display:inline-block;
                 padding:6px 14px;
                 background:#28a745;
                 color:#fff;
                 border-radius:4px;
                 font-size:14px;
                 text-decoration:none;
                 cursor:pointer;
               ">
               ดูสลิป
            </a>
          </td>';
} else {
    echo '<td>-</td>';
}



/* ---------- ปุ่มเปิดหนังสือสัญญา (contract_image) ---------- */
if (!empty($row['contract_image'])) {
    $contractUrl = 'file_blob.php?id=' . $row['contract_id'] . '&f=contract_image';
    echo '<td>
            <a href="' . $contractUrl . '" target="_blank"
               style="
                 display:inline-block;
                 padding:6px 14px;
                 background:#17a2b8;
                 color:#fff;
                 border-radius:4px;
                 font-size:14px;
                 text-decoration:none;"
            >ดูสัญญา</a>
          </td>';
} else {
    echo '<td>-</td>';
}












    echo "</tr>";
}
?>

    </table>
</div>

</body>
</html>

<?php $conn->close(); ?>


    <!-- echo "<td>
        
        <a href='edit_contract.php?id=" . $row['caregiver_id'] . "'>แก้ไข</a> | 
 <a href='delete_contract.php?id=" . $row['caregiver_id'] . "' >ยกเลิก</a>
      </td>"; -->