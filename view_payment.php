<?php
include('config.php');
session_start();
if (isset($_SESSION['message'])) {
    echo '<div class="alert alert-success">'.htmlspecialchars($_SESSION['message']).'</div>';
    unset($_SESSION['message']);
}
if (isset($_SESSION['error'])) {
    echo '<div class="alert alert-danger">'.htmlspecialchars($_SESSION['error']).'</div>';
    unset($_SESSION['error']);
}


// รับค่าการค้นหา
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';

// สร้าง SQL ตามเงื่อนไขการค้นหา
$sql = "SELECT p.payments_id, p.booking_id, p.paymentslip, p.payment_date,p.payment_time,
               cb.parent_id, cb.caregiver_id, cb.booking_type, cb.total_price, cb.start_date, cb.end_date, cb.start_month, cb.end_month, cb.payment_status,
               cg.first_name AS caregiver_fname, cg.last_name AS caregiver_lname,
               pr.first_name AS parent_fname,cg.email AS caregiver_email, pr.last_name AS parent_lname
        FROM caregiver_booking cb
        LEFT JOIN payments p ON cb.booking_id = p.booking_id
        JOIN caregiver cg ON cb.caregiver_id = cg.caregiver_id
        JOIN parent pr ON cb.parent_id = pr.parent_id";

// เพิ่มเงื่อนไขค้นหา ถ้ามีการกรอกคำค้น
if (!empty($search)) {
    $sql .= " WHERE 
        cb.booking_id LIKE '%$search%' OR 
        cg.first_name LIKE '%$search%' OR 
        cg.last_name LIKE '%$search%' OR 
        pr.first_name LIKE '%$search%' OR 
        pr.last_name LIKE '%$search%'";
}

$sql .= " ORDER BY cb.booking_id DESC";

$result = $conn->query($sql);

if (!$result) {
    die("Query Error: " . $conn->error);
}




?>


<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>แดชบอร์ดการชำระเงิน</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        img.receipt {
            max-width: 150px;
            height: auto;
        }
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
    padding: 10px 15px;
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
    margin: 0 10px;
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
            <a href="#" onclick="location.href='view_parent.php'">การจัดการผู้ปกครอง</a>
            <a href="#" onclick="location.href='view_caregiver.php'">การจัดการผู้ดูแล</a>
            <a href="#" onclick="location.href='view_booking.php'">การจัดการจอง</a>
            <a href="#" onclick="location.href='view_contract.php'">การจัดการทำสัญญา</a>
            <a href="#" style="color: #ff9100;">การจัดการชำระเงิน</a>
        </nav>

        <button class="cta-button" onclick="location.href='login_admin.php'">ลงชื่อออก</button>
    
    </div>

</header>
<div class="container mt-5">
    <h2>📄 รายการชำระเงินทั้งหมด</h2>
    <form method="GET" class="mb-4" style="text-align: center; margin-bottom: 20px;">
    <input type="text" name="search" placeholder="ค้นหาชื่อหรือนามสกุลผู้ดูแล" 
           value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>" 
           style="padding: 10px; width: 300px; border-radius: 5px; border: 1px solid #ccc;">
    <button type="submit" style="padding: 10px 20px; border-radius: 5px; background-color: #007bff; color: white; border: none;">ค้นหา</button>
</form>
    <?php if ($result->num_rows > 0): ?>
        <table class="table table-bordered mt-3">
            <thead class="table-dark">
                <tr>
                    <th>รหัสการจอง</th>
                    <th>ชื่อผู้ปกครอง</th>
                    <th>ชื่อผู้ดูแล</th>
                    <th>ประเภท</th>
                    <th>ช่วงเวลา(รายวัน)</th>
                    <th>ช่วงเวลา(รายเดือน)</th>
                    <th>ราคา</th>
                    <th>สลิปการโอน</th>
                    <th>วันที่โอน</th>
                    <th>เวลาที่โอน</th>
                    <!-- <th>เงินรวม</th> -->
                    <th>สถานะการชำระเงิน</th>
                </tr>
            </thead>
            <tbody>
    <?php while($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['booking_id']) ?></td>
            <td><?= htmlspecialchars($row['parent_fname'] . ' ' . $row['parent_lname']) ?></td>
            <td><?= htmlspecialchars($row['caregiver_fname'] . ' ' . $row['caregiver_lname']) ?></td>
            <td><?= $row['booking_type'] === 'daily' ? 'รายวัน' : 'รายเดือน' ?></td>
            <td><?= htmlspecialchars($row['start_date']) ?> - <?= htmlspecialchars($row['end_date']) ?></td>
            <td><?= htmlspecialchars($row['start_month']) ?> - <?= htmlspecialchars($row['end_month']) ?></td>
            <td><?= number_format($row['total_price'], 2) ?> บาท</td>
            
            <td>
                <?php if (!empty($row['paymentslip'])): ?>
                    <img class="receipt"
                         src="data:image/jpeg;base64,<?= base64_encode($row['paymentslip']) ?>"
                         alt="สลิป" style="max-width:100px;">
                <?php else: ?>
                    <span class="text-muted">ไม่มีสลิป</span>
                <?php endif; ?>
            </td>
            <!-- เพิ่มแสดงวันที่ชำระเงิน -->
    <td><?= !empty($row['payment_date']) ? htmlspecialchars($row['payment_date']) : '-' ?></td>
    <td><?= !empty($row['payment_time']) ? htmlspecialchars($row['payment_time']) . ' ' : '-' ?></td>
<!-- <td><?= !empty($row['amount']) ? number_format($row['amount'], 2) . ' บาท' : '-' ?></td> -->

            <td class="d-flex align-items-center gap-2">
                <!-- ฟอร์มอัปเดตสถานะการชำระเงิน -->
                <form method="post" action="update_payment_status.php" class="m-0 p-0 d-flex align-items-center gap-1">
    <input type="hidden" name="booking_id" value="<?= htmlspecialchars($row['booking_id']) ?>">
<input type="hidden" name="caregiver_email" value="<?= htmlspecialchars($row['caregiver_email'] ?? '') ?>">
<input type="hidden" name="caregiver_name" value="<?= htmlspecialchars($row['caregiver_fname'] . ' ' . $row['caregiver_lname']) ?>">
    <select name="payment_status" class="form-select form-select-sm w-auto">
        <option value="pending" <?= $row['payment_status'] === 'pending' ? 'selected' : '' ?>>รอตรวจสอบ</option>
        <option value="paid"    <?= $row['payment_status'] === 'paid'    ? 'selected' : '' ?>>ชำระแล้ว</option>
    </select>
    <button type="submit" class="btn btn-sm btn-primary">✔</button>
</form>

                <!-- ฟอร์มลบรายการการชำระเงิน / Booking -->
                <form method="post" action="delete_payment.php" class="m-0 p-0">
    <input type="hidden" name="booking_id" value="<?= htmlspecialchars($row['booking_id']) ?>">
    <!-- <button type="submit"
            class="btn btn-sm btn-danger"
            onclick="return confirm('คุณแน่ใจหรือไม่ที่จะลบรายการนี้?');">
        ยกเลิก
    </button> -->
</form>
            </td>
        </tr>
    <?php endwhile; ?>
</tbody>

        </table>
    <?php else: ?>
        <div class="alert alert-warning">ไม่มีรายการชำระเงิน</div>
    <?php endif; ?>
</div>
</body>
</html>

<?php
$conn->close();
?>
