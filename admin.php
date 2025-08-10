<?php
include 'config.php'; // เชื่อมต่อฐานข้อมูล






$current_year = isset($_GET['year']) && is_numeric($_GET['year']) ? (int)$_GET['year'] : (int)date('Y');


// ดึงข้อมูลรายเดือน เฉพาะที่จองแล้ว (confirmed) และจ่ายเงินแล้ว (paid)
$sql_monthly_data = "
    SELECT 
        MONTH(
            CASE 
                WHEN booking_type = 'daily' THEN start_date
                WHEN booking_type = 'monthly' THEN STR_TO_DATE(CONCAT(start_month, '-01'), '%Y-%m-%d')
            END
        ) AS month,
        COUNT(*) AS bookings,
        SUM(total_price) AS revenue
    FROM caregiver_booking
    WHERE 
        YEAR(
            CASE 
                WHEN booking_type = 'daily' THEN start_date
                WHEN booking_type = 'monthly' THEN STR_TO_DATE(CONCAT(start_month, '-01'), '%Y-%m-%d')
            END
        ) = $current_year
      AND status = 'confirmed'
      AND payment_status = 'paid'
    GROUP BY month
    ORDER BY month
";

$monthly_data = array_fill(0, 12, ['bookings' => 0, 'revenue' => 0]);

$result_monthly_data = $conn->query($sql_monthly_data);

while ($row = $result_monthly_data->fetch_assoc()) {
    $monthIndex = (int)$row['month'] - 1; // เปลี่ยนจาก 1-12 เป็น 0-11
    $monthly_data[$monthIndex]['bookings'] = (int)$row['bookings'];
    $monthly_data[$monthIndex]['revenue'] = (float)$row['revenue'];
}





$today = date('Y-m-d');   // วันที่ปัจจุบัน (2025-06-27)

$activeCondition = "
    (
        booking_type = 'daily'   AND end_date              >= '$today'
    )
    OR
    (
        booking_type = 'monthly' AND CONCAT(end_month,'-01') >= '$today'
    )
";

// จำนวนการจองทั้งหมด (ที่ยังไม่หมดอายุ)
$sql_all = "
    SELECT COUNT(*) AS total_booking
    FROM caregiver_booking
    WHERE $activeCondition
";
$result_all = $conn->query($sql_all);
$total_booking = $result_all->fetch_assoc()['total_booking'] ?? 0;

// จำนวนการจองที่ยืนยันแล้ว (ยังไม่หมดอายุ)
$sql_confirmed = "
    SELECT COUNT(*) AS confirmed_booking
    FROM caregiver_booking
    WHERE status = 'confirmed'
      AND $activeCondition
";
$result_confirmed = $conn->query($sql_confirmed);
$confirmed_booking = $result_confirmed->fetch_assoc()['confirmed_booking'] ?? 0;

// จำนวนการจองที่ชำระเงินแล้ว (ยังไม่หมดอายุ)
$sql_paid = "
    SELECT COUNT(*) AS paid_booking
    FROM caregiver_booking
    WHERE payment_status = 'paid'
      AND $activeCondition
";
$result_paid = $conn->query($sql_paid);
$paid_booking = $result_paid->fetch_assoc()['paid_booking'] ?? 0;

// จำนวนผู้ปกครอง (ไม่ได้เกี่ยวกับเวลา)
$sql_parent = "SELECT COUNT(*) AS total_parent FROM parent";
$result_parent = $conn->query($sql_parent);
$total_parent = $result_parent->fetch_assoc()['total_parent'] ?? 0;




// จำนวนผู้ดูแล (caregiver)
$sql_caregiver = "SELECT COUNT(*) AS total_caregiver FROM caregiver";
$result_caregiver = $conn->query($sql_caregiver);
$total_caregiver = $result_caregiver->fetch_assoc()['total_caregiver'] ?? 0;

$sql_contract = "SELECT COUNT(*) AS total_contract FROM caregiver WHERE contract_type";
$result_contract = $conn->query($sql_contract);
$total_contract = $result_contract->fetch_assoc()['total_contract'] ?? 0;


$month_names = ['มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน',
                'กรกฎาคม', 'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม'];


$contract_counts = [
    '1 เดือน' => 0,
    '3 เดือน' => 0,
    '6 เดือน' => 0,
    '1 ปี'   => 0
];

$sql = "SELECT contract_type, COUNT(*) as total FROM caregiver GROUP BY contract_type";
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
    $type = $row['contract_type'];
    $count = $row['total'];

    if ($type == '1 เดือน') $contract_counts['1 เดือน'] = $count;
    elseif ($type == '3 เดือน') $contract_counts['3 เดือน'] = $count;
    elseif ($type == '6 เดือน') $contract_counts['6 เดือน'] = $count;
    elseif ($type == '1 ปี') $contract_counts['1 ปี'] = $count;
}

$age_groups = [
    'ต่ำกว่า 20' => 0,
    '21-30' => 0,
    '31-40' => 0,
    '41-50' => 0,
    'มากกว่า 50' => 0,
];

$sql_age = "SELECT birthdate FROM caregiver WHERE birthdate IS NOT NULL";
$result_age = $conn->query($sql_age);
while ($row = $result_age->fetch_assoc()) {
    $birthdate = $row['birthdate'];
    $birth_year = (int)date('Y', strtotime($birthdate));
    $this_year = (int)date('Y');
$age = $this_year - $birth_year;

    if ($age < 20) $age_groups['ต่ำกว่า 20']++;
    elseif ($age <= 30) $age_groups['21-30']++;
    elseif ($age <= 40) $age_groups['31-40']++;
    elseif ($age <= 50) $age_groups['41-50']++;
    else $age_groups['มากกว่า 50']++;
}



$caregiver_gender = [
    'ชาย' => 0,
    'หญิง' => 0,
    'อื่น ๆ' => 0,
];

$sql_gender_cg = "SELECT gender, COUNT(*) as total FROM caregiver GROUP BY gender";
$result_cg = $conn->query($sql_gender_cg);
while ($row = $result_cg->fetch_assoc()) {
    $gender = $row['gender'];
    if ($gender === 'ชาย') $caregiver_gender['ชาย'] = $row['total'];
    elseif ($gender === 'หญิง') $caregiver_gender['หญิง'] = $row['total'];
    else $caregiver_gender['อื่น ๆ'] += $row['total'];
}




$amountLabels = [
    500  => '1 เดือน (500)',
    1500 => '3 เดือน (1500)',
    2500 => '6 เดือน (2500)',
    4000 => '1 ปี (4000)'
];

$sql = "SELECT amount, COUNT(*) AS total FROM caregiver_contract GROUP BY amount";
$result = $conn->query($sql);

$labels = [];
$counts = [];

while ($row = $result->fetch_assoc()) {
    $amount = (int)$row['amount'];
    $labels[] = $amountLabels[$amount] ?? $amount . ' บาท'; // ถ้าไม่เจอในแมป ให้แสดง amount เป็นบาท
    $counts[] = (int)$row['total'];
}


// ปิด connection ถ้าต้องการ
$conn->close();


?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CareLink - ระบบจัดการผู้ดูแลเด็ก</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
background: linear-gradient(135deg,rgb(55, 204, 172) 0%,rgb(1, 158, 182) 100%);

            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .header {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 20px 0;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }

        .header-content {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 0 20px;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .logo-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
            font-weight: bold;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }

        .logo-text {
            color: #333;
        }

        .logo-text h1 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .logo-text p {
            font-size: 14px;
            color: #666;
            font-weight: 400;
        }

        .main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 40px 20px;
        }

        .welcome-section {
            text-align: center;
            margin-bottom: 50px;
            color: white;
        }

        .welcome-section h2 {
            font-size: 36px;
            font-weight: 300;
            margin-bottom: 15px;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }

        .welcome-section p {
            font-size: 18px;
            opacity: 0.9;
            max-width: 600px;
            line-height: 1.6;
        }

        .menu-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 30px;
            max-width: 1000px;
            width: 100%;
        }

        .menu-item {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 20px;
            padding: 30px 25px;
            text-decoration: none;
            color: #333;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            position: relative;
            overflow: hidden;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }

        .menu-item::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
            transition: left 0.5s;
        }

        .menu-item:hover::before {
            left: 100%;
        }

        .menu-item:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
            border-color: rgba(102, 126, 234, 0.3);
        }

        .menu-icon {
            width: 60px;
            height: 60px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin-bottom: 20px;
            transition: all 0.3s ease;
        }

        .menu-item:hover .menu-icon {
            transform: scale(1.1);
        }

        .menu-item:nth-child(1) .menu-icon {
            background: linear-gradient(135deg, #ff6b6b, #ff8e8e);
            color: white;
        }

        .menu-item:nth-child(2) .menu-icon {
            background: linear-gradient(135deg, #4ecdc4, #44a08d);
            color: white;
        }

        .menu-item:nth-child(3) .menu-icon {
            background: linear-gradient(135deg, #45b7d1, #96c93d);
            color: white;
        }

        .menu-item:nth-child(4) .menu-icon {
            background: linear-gradient(135deg, #f093fb, #f5576c);
            color: white;
        }

        .menu-item:nth-child(5) .menu-icon {
            background: linear-gradient(135deg, #4facfe, #00f2fe);
            color: white;
        }

        .menu-title {
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 10px;
            color: #333;
        }

        .menu-description {
            font-size: 14px;
            color: #666;
            line-height: 1.5;
        }

        .stats-bar {
            display: flex;
            justify-content: center;
            gap: 40px;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }

        .stat-item {
            background: rgba(255, 255, 255, 0.2);
            padding: 15px 25px;
            border-radius: 15px;
            text-align: center;
            color: white;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .stat-number {
            font-size: 24px;
            font-weight: bold;
            display: block;
        }

        .stat-label {
            font-size: 12px;
            opacity: 0.8;
            margin-top: 5px;
        }

        @media (max-width: 768px) {
            .menu-container {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .welcome-section h2 {
                font-size: 28px;
            }

            .stats-bar {
                gap: 20px;
            }

            .stat-item {
                padding: 10px 15px;
            }
        }

        /* Animation for page load */
        .menu-item {
            opacity: 0;
            transform: translateY(30px);
            animation: fadeInUp 0.6s ease forwards;
        }

        .menu-item:nth-child(1) { animation-delay: 0.1s; }
        .menu-item:nth-child(2) { animation-delay: 0.2s; }
        .menu-item:nth-child(3) { animation-delay: 0.3s; }
        .menu-item:nth-child(4) { animation-delay: 0.4s; }
        .menu-item:nth-child(5) { animation-delay: 0.5s; }

        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        h1, h2 {
            text-align: center;
        }
        form {
            text-align: center;
            margin-bottom: 30px;
        }
        select {
            font-size: 1.1rem;
            padding: 5px 10px;
        }
        canvas {
            background: #fff;
            border: 1px solid #ddd;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 40px;
            display: block;
            margin-left: auto;
            margin-right: auto;
        }


        form#yearForm {
    margin: 30px auto 50px;
    text-align: center;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

/* ป้าย label */
form#yearForm label {
    font-size: 1.2rem;
    color: #000;
    font-weight: 600;
    margin-right: 12px;
    user-select: none;
    
}

form#yearForm select#yearSelect {
    font-size: 1.15rem;
    padding: 10px 40px 10px 18px; /* เพิ่ม padding ด้านขวาให้มีที่วางลูกศร */
    border-radius: 12px;
    border: none;
    min-width: 140px;
    background: linear-gradient(135deg, #00c6ff, #0072ff);
    color: white;
    font-weight: 600;
    cursor: pointer;
    box-shadow: 0 4px 15px rgba(0, 114, 255, 0.5);
    transition: all 0.3s ease;
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;

    /* ใส่ลูกศรเป็นรูปสามเหลี่ยมเล็ก ๆ สีขาว */
    background-image: url("data:image/svg+xml,%3Csvg width='12' height='7' viewBox='0 0 12 7' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M1 1L6 6L11 1' stroke='white' stroke-width='2'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 15px center;
    background-size: 12px 7px;
}

form#yearForm select#yearSelect,
form#yearForm select#yearSelect:focus {
    background: linear-gradient(135deg, #0072ff, #00c6ff);
    box-shadow: 0 6px 20px rgba(0, 198, 255, 0.7);
    outline: none;
    color: rgb(0, 0, 0);
}


    </style>
</head>
<body>

<header class="header">
    <div class="header-content">
        <div class="logo">
            
            <img src="logo.png" alt="Logo" height="60px">
        
            <div class="logo-text">
                <h1>CareLink</h1>
                <p>ระบบจัดการผู้ดูแลเด็ก</p>
            </div>
        </div>
    </div>
</header>

<div class="main-content">
    <div class="welcome-section">
        <h2>ยินดีต้อนรับสู่ระบบจัดการ</h2>
        <p>เลือกเมนูที่ต้องการจัดการจากตัวเลือกด้านล่าง เพื่อเริ่มต้นการใช้งานระบบ</p>
    </div>

    <div class="stats-bar">
    <div class="stat-item">
        <span class="stat-number"><?= $total_booking ?></span>
        <div class="stat-label">การจองทั้งหมด</div>
    </div>
    <div class="stat-item">
        <span class="stat-number"><?= $confirmed_booking ?></span>
        <div class="stat-label">การจองที่ยืนยัน</div>
    </div>
    <div class="stat-item">
        <span class="stat-number"><?= $paid_booking ?></span>
        <div class="stat-label">การจองที่ชำระเงินแล้ว</div>
    </div>
        <div class="stat-item">
        <span class="stat-number"><?= $total_parent ?></span>
        <div class="stat-label">จำนวนผู้ปกครอง</div>
    </div>
    <div class="stat-item">
        <span class="stat-number"><?= $total_caregiver ?></span>
        <div class="stat-label">จำนวนผู้ดูแล</div>
    </div>
    <div class="stat-item">
    <span class="stat-number"><?= $total_contract ?></span>
    <div class="stat-label">จำนวนการทำสัญญา</div>

</div>
</div>


<div class="menu-container">
        <a href="view_parent.php" class="menu-item">
            <div class="menu-icon">👨‍👩‍👧‍👦</div>
            <div class="menu-title">การจัดการผู้ปกครอง</div>
            <div class="menu-description">จัดการข้อมูลผู้ปกครอง เพิ่ม แก้ไข และดูรายละเอียด</div>
        </a>

        <a href="view_caregiver.php" class="menu-item">
            <div class="menu-icon">👩‍⚕️</div>
            <div class="menu-title">การจัดการผู้ดูแล</div>
            <div class="menu-description">จัดการข้อมูลผู้ดูแลเด็ก เพิ่ม แก้ไข และดูรายละเอียด</div>
        </a>

        <a href="view_booking.php" class="menu-item">
            <div class="menu-icon">📅</div>
            <div class="menu-title">การจัดการการจอง</div>
            <div class="menu-description">จัดการการจองผู้ดูแล ยืนยัน ปฏิเสธ และติดตาม</div>
        </a>

        <a href="view_contract.php" class="menu-item">
            <div class="menu-icon">📋</div>
            <div class="menu-title">การจัดการสัญญา</div>
            <div class="menu-description">จัดการสัญญาการจ้างงาน สร้าง แก้ไข และอนุมัติ</div>
        </a>

        <a href="view_payment.php" class="menu-item">
            <div class="menu-icon">💳</div>
            <div class="menu-title">การจัดการชำระเงิน</div>
            <div class="menu-description">จัดการการชำระเงิน ตรวจสอบ และออกใบเสร็จ</div>
        </a>
    </div>
<br>
<br>

<script>
const ageData = <?= json_encode(array_values($age_groups)) ?>;
const caregiverGenderData = <?= json_encode(array_values($caregiver_gender)) ?>;
</script>


<div class="stats-bar" style="margin-top: 20px;">
    <div class="stat-item" style="background: rgba(255,255,255,0.95); color: #333;">
        <span class="stat-number">สถิติเกี่ยวกับผู้ดูแล</span>
    </div>
</div>

<div style="display: flex; gap: 40px; justify-content: center; flex-wrap: wrap;">
    <div style="text-align: center;">
        <div class="stat-item" style="background: rgba(255,255,255,0.95); color: #333; margin-bottom: 20px;">
        <span class="stat-number">ช่วงอายุของผู้ดูแล</span>
    </div>
        <canvas id="agePieChart" width="300" height="300"></canvas>
    </div>

    <div style="text-align: center;">
        <div class="stat-item" style="background: rgba(255,255,255,0.95); color: #333; margin-bottom: 20px;">
        <span class="stat-number">เพศของผู้ดูแล</span>
    </div>
        <canvas id="caregiverGenderPieChart" width="300" height="300"></canvas>
    </div>
</div>



<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>



<script>
document.addEventListener("DOMContentLoaded", function () {
    const colors = ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF'];

    new Chart(document.getElementById('agePieChart'), {
        type: 'pie',
        data: {
            labels: ['ต่ำกว่า 20', '21-30', '31-40', '41-50', 'มากกว่า 50'],
            datasets: [{
                data: ageData,
                backgroundColor: colors,
                borderColor: '#fff',
                borderWidth: 2
            }]
        }
    });

    new Chart(document.getElementById('caregiverGenderPieChart'), {
        type: 'pie',
        data: {
            labels: ['ชาย', 'หญิง', 'อื่น ๆ'],
            datasets: [{
                data: caregiverGenderData,
                backgroundColor: ['#FF6384', '#36A2EB', '#CCCCCC'],
                borderColor: '#fff',
                borderWidth: 2
            }]
        }
    });


});
</script>











<div class="stats-bar" style="margin-top: 20px;">
    <div class="stat-item" style="background: rgba(255,255,255,0.95); color: #333;">
        <span class="stat-number">กราฟแสดงประเภทการทำสัญญาของผู้ดูแล</span>
    </div>
</div>

<canvas id="contractPieChart" width="400" height="400"></canvas>

<script>
const contractData = <?= json_encode(array_values($contract_counts)) ?>;
</script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const ctx = document.getElementById('contractPieChart').getContext('2d');
    new Chart(ctx, {
        type: 'pie',
        data: {
            labels: ['1 เดือน', '3 เดือน', '6 เดือน', '1 ปี'],
            datasets: [{
                data: contractData,
                backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0'],
                borderColor: '#fff',
                borderWidth: 2,
                hoverOffset: 10
            }]
        },
        options: {
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
});
</script>






<br>






<div class="stat-item" style="background: rgba(255,255,255,0.95); color: #333;">
        <span class="stat-number"><h3>จำนวนผู้สมัครแต่ละแพ็คเกจ</h3><h3 id="totalRevenue" style="margin-top:12px;"></h3></span>
</div><br>


<canvas id="packageChart" width="600" height="400"></canvas>


<script>
    
    const ctx = document.getElementById('packageChart').getContext('2d');

    // ข้อมูลจำนวนผู้สมัคร
    const labels = <?= json_encode($labels, JSON_UNESCAPED_UNICODE) ?>;
const counts = <?= json_encode($counts) ?>;

const priceMap = {
    '1 ปี (4000)': 4000,
    '1 เดือน (500)': 500,
    '3 เดือน (1500)': 1500,
    '6 เดือน (2500)': 2500
    
};


// คำนวณรายได้ = จำนวน × ราคา
const revenues = labels.map(label => (priceMap[label] || 0) * (counts[labels.indexOf(label)] || 0));

const packageChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: labels,
        datasets: [
            {
                label: 'จำนวนผู้สมัคร',
                data: counts,
                backgroundColor: '#4e73df',
                borderColor: '#fff',
                borderWidth: 1,
                yAxisID: 'y-left',
            },
            {
                label: 'ราคา (บาท)',
                data: labels.map(label => priceMap[label] || 0),
                type: 'line',
                borderColor: '#f6c23e',
                backgroundColor: '#f6c23e',
                yAxisID: 'y-right',
                tension: 0.3,
                pointRadius: 5,
                hidden: true
            },
            {
                label: 'รายได้ (บาท)',
                data: revenues,
                type: 'bar',
                backgroundColor: '#36b9cc',
                borderColor: '#fff',
                borderWidth: 1,
                yAxisID: 'y-right',
            }
        ]
    },
    options: {
        responsive: true,
        scales: {
            'y-left': {
                position: 'left',
                beginAtZero: true,
                title: {
                    display: true,
                    text: 'จำนวนผู้สมัคร'
                },
                ticks: {
                    stepSize: 1
                }
            },
            'y-right': {
                position: 'right',
                beginAtZero: true,
                grid: {
                    drawOnChartArea: false
                },
                title: {
                    display: true,
                    text: 'ราคา / รายได้ (บาท)'
                }
            }
        },
        plugins: {
            tooltip: {
                mode: 'index',
                intersect: false
            },
            legend: {
                position: 'top'
                
            }
        }
    }
});
const totalRevenue = revenues.reduce((sum, val) => sum + val, 0);

// แสดงผลในหน้าเว็บ
document.getElementById('totalRevenue').textContent =
    'ยอดรวมรายได้: ' + totalRevenue.toLocaleString('th-TH') + ' บาท';
</script>







<br>










<!-- แสดงเงินต่อเดือน -->

<div class="stats-bar" style="margin-top: 20px;">
    <div class="stat-item" style="background: rgba(255,255,255,0.95); color: #333;">
        <span class="stat-number">สรุปรายเดือน</span>
        <div class="stat-label">ปี <?= $current_year ?> — เฉพาะการจองที่ยืนยันและจ่ายเงินแล้ว</div>
    </div>
</div>

<div style="overflow-x: auto; max-width: 1000px; margin: 0 auto 40px; background: white; border-radius: 15px; padding: 20px;">



<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


<h1>สถิติการจองรายเดือน ปี <?= htmlspecialchars($current_year) ?></h1>

<form method="GET" id="yearForm" action="">
    <label for="yearSelect">เลือกปี: </label>
    <select id="yearSelect" name="year" onchange="this.form.submit()">
        <?php
        $nowYear = (int)date('Y');
        for ($y = $nowYear; $y >= $nowYear - 10; $y--) {
            $selected = ($y == $current_year) ? 'selected' : '';
            echo "<option value='$y' $selected>$y</option>";
        }
        ?>
    </select>
</form>

    
    <canvas id="bookingsChart" width="900" height="400"></canvas>

    
    <canvas id="revenueChart" width="900" height="400"></canvas>

    <script>
        const monthNames = <?= json_encode($month_names) ?>;
        const monthlyData = <?= json_encode($monthly_data) ?>;

        // ข้อมูลแยก
        const bookingsData = monthlyData.map(m => m.bookings);
        const revenueData = monthlyData.map(m => m.revenue);

        // กราฟจำนวนการจอง
        const ctxBookings = document.getElementById('bookingsChart').getContext('2d');
        new Chart(ctxBookings, {
            type: 'bar',
            data: {
                labels: monthNames,
                datasets: [{
                    label: 'จำนวนการจอง',
                    data: bookingsData,
                    backgroundColor: 'rgba(75, 192, 192, 0.7)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    x: { type: 'category' },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1,
                            callback: v => v.toLocaleString()
                        }
                    }
                },
                plugins: {
                    title: {
                        display: true,
                        text: 'จำนวนการจองรายเดือน ปี <?= htmlspecialchars($current_year) ?>',
                        font: { size: 18 }
                    },
                    legend: { display: false },
                    tooltip: { mode: 'index', intersect: false }
                }
            }
        });

        // กราฟรายได้
        const ctxRevenue = document.getElementById('revenueChart').getContext('2d');
        new Chart(ctxRevenue, {
            type: 'bar',
            data: {
                labels: monthNames,
                datasets: [{
                    label: 'รายได้ (บาท)',
                    data: revenueData,
                    backgroundColor: 'rgba(255, 159, 64, 0.7)',
                    borderColor: 'rgba(255, 159, 64, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    x: { type: 'category' },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: v => v.toLocaleString() + ' บาท'
                        }
                    }
                },
                plugins: {
                    title: {
                        display: true,
                        text: 'รายได้รายเดือน ปี <?= htmlspecialchars($current_year) ?>',
                        font: { size: 18 }
                    },
                    legend: { display: false },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        callbacks: {
                            label: ctx => ctx.dataset.label + ': ' + ctx.parsed.y.toLocaleString() + ' บาท'
                        }
                    }
                }
            }
        });
    </script>

</div>

<br>









</div>

</body>
</html>
