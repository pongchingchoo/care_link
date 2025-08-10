<?php
include('config.php'); // เชื่อมต่อฐานข้อมูล

$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';

$today = date('Y-m-d');      // 2025-06-27

$activeCondition = "
    (booking_type = 'daily'   AND End_Date            >= '$today')
 OR (booking_type = 'monthly' AND CONCAT(end_month,'-01') >= '$today')
";


$sql = "SELECT b.booking_id, 
               CONCAT(p.first_name, ' ', p.last_name) AS parent_name, 
               CONCAT(c.first_name, ' ', c.last_name) AS caregiver_name, 
               b.booking_type,
               b.Start_Date, 
               b.End_Date, 
               b.start_month,
               b.end_month,
               b.total_price,
               b.children_count,
               b.Status 
        FROM caregiver_booking b 
        JOIN parent p ON b.parent_id = p.parent_id 
        JOIN caregiver c ON b.caregiver_id = c.caregiver_id";

if (!empty($search)) {
    $sql .= " WHERE 
        b.booking_id LIKE '%$search%' OR
        p.first_name LIKE '%$search%' OR
        p.last_name LIKE '%$search%' OR
        c.first_name LIKE '%$search%' OR
        b.Start_Date LIKE '%$search%' OR
        b.start_month LIKE '%$search%' OR
        c.last_name LIKE '%$search%'";
}

$sql .= " ORDER BY b.booking_id DESC";

$result = $conn->query($sql);
?>



<style>
body {
            background-color: #f8f9fa;
        }
        .container {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            margin-bottom: 20px;
            font-weight: bold;
            color: #343a40;
        }
        .table th {
            background-color: #007bff;
            color: white;
        }
        .btn {
            margin-right: 5px;
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

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการการจอง</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
            <a href="#" style="color: #ff9100;">การจัดการจอง</a>
            <a href="#" onclick="location.href='view_contract.php'">การจัดการทำสัญญา</a>
            <a href="#" onclick="location.href='view_payment.php'">การจัดการชำระเงิน</a>

        </nav>

        <button class="cta-button" onclick="location.href='login_admin.php'">ลงชื่อออก</button>
    
    </div>

</header>
<div class="container mt-5">
        <h2 class="text-center">รายการจอง</h2>
        <!-- ฟอร์มค้นหา -->
        <form method="GET" class="mb-4" style="text-align: center; margin-bottom: 20px;">
    <input type="text" name="search" placeholder="ค้นหาชื่อหรือนามสกุลผู้ดูแล" 
           value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>" 
           style="padding: 10px; width: 300px; border-radius: 5px; border: 1px solid #ccc;">
    <button type="submit" style="padding: 10px 20px; border-radius: 5px; background-color: #007bff; color: white; border: none;">ค้นหา</button>
</form>
        <table class="table table-bordered table-hover text-center">
            <thead>
                <tr>
                    <th>รหัสการจอง</th>
                    <th>ชื่อผู้ปกครอง</th>
                    <th>ชื่อผู้ดูแล</th>
                    <th>ประเภทการจอง</th>
                    <th>วันที่เริ่ม</th>
                    <th>วันที่สิ้นสุด</th>
                    <th>เดือนเริ่ม</th>
                    <th>เดือนสิ้นสุด</th>
                    <th>จำนวนเด็ก</th>
                    <th>ราคารวม</th>
                    <th>สถานะ</th>
                    <th>การจัดการ</th>
                </tr>
            </thead>
            <tbody>
                
                <?php 
                
                
                while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo $row['booking_id']; ?></td>
                        <td><?php echo $row['parent_name']; ?></td>
                        <td><?php echo $row['caregiver_name']; ?></td>
                        <td><?php echo ucfirst($row['booking_type']); ?></td>
                        <td><?php echo $row['Start_Date']; ?></td>
                        <td><?php echo $row['End_Date']; ?></td>
                        <td><?php echo $row['start_month']; ?></td>
                        <td><?php echo $row['end_month']; ?></td>
                        <td><?php echo $row['children_count']; ?></td>
                        <td><?php echo number_format($row['total_price'], 2); ?> บาท</td>
                        <td>
                            <span class="badge bg-<?php echo ($row['Status'] == 'Confirmed') ? 'success' : 'warning'; ?>">
                                <?php echo $row['Status']; ?>
                            </span>
                        </td>
                        <td>
                            <a href="edit_booking.php?booking_id=<?php echo $row['booking_id']; ?>" class="btn btn-warning btn-sm">แก้ไข</a>
                            <a href="delete_booking1.php?booking_id=<?= $row['booking_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('คุณแน่ใจหรือไม่ว่าต้องการลบรายการนี้?');">ยกเลิก</a>

                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</body>
</html>