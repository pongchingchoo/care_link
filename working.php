<?php
include('config.php'); // เชื่อมต่อฐานข้อมูล PDO

session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$caregiver_id = $_SESSION['user_id'];

$sql = "SELECT cb.*, p.first_name, p.last_name, p.phone_number, p.email, cb.status,
               pay.payments_id, pay.paymentslip, cb.address
        FROM caregiver_booking cb
        LEFT JOIN parent p ON cb.parent_id = p.parent_id
        LEFT JOIN payments pay ON cb.booking_id = pay.booking_id
        WHERE cb.caregiver_id = ?
        ORDER BY cb.booking_id DESC";


$stmt = $pdo->prepare($sql);
$stmt->execute([$caregiver_id]);
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);




$today          = date('Y-m-d');
$activeBookings = array_filter($result, function ($row) use ($today) {

    // ข้ามรายการที่ถูก reject
    if (($row['status'] ?? '') === 'rejected') {
        return false;
    }

    // รายวัน: ให้แสดงเฉพาะที่ end_date ยังไม่ผ่าน
    if ($row['booking_type'] === 'daily' && !empty($row['end_date'])) {
        return $today <= $row['end_date'];
    }

    // รายเดือน: ให้แสดงเฉพาะที่ปลายเดือนยังไม่ผ่าน
    if ($row['booking_type'] === 'monthly' && !empty($row['end_month'])) {
        $endMonthDate = date('Y-m-t', strtotime($row['end_month'] . '-01'));
        return $today <= $endMonthDate;
    }

    // งานอื่น ๆ (เผื่ออนาคต) ให้ถือว่ายังทำอยู่
    return true;
});

$activeCount = count($activeBookings);







$incomePerMonth = [];
$jobsPerMonth = [];

// สมมติปีนี้
$year = date('Y');

// เริ่มเดือนตั้งแต่ 1 ถึง 12 (หรือช่วงเดือนที่สนใจ)
for ($m=1; $m<=12; $m++) {
    $monthKey = sprintf('%04d-%02d', $year, $m);
    $incomePerMonth[$monthKey] = 0;
    $jobsPerMonth[$monthKey] = 0;
}

// รวมข้อมูลจาก $result
foreach ($result as $row) {
    // กรองสถานะที่ไม่ต้องการด้วยถ้ายังไม่ได้
    if (($row['status'] ?? '') === 'rejected') continue;

    // กำหนดเดือนของรายการ
    if ($row['booking_type'] === 'daily') {
        // ใช้เดือนจาก start_date
        $monthKey = date('Y-m', strtotime($row['start_date']));
    } else {
        // ใช้เดือนเริ่มจาก start_month เช่น "2025-06"
        $monthKey = $row['start_month'];
    }

    // ตรวจสอบ key ว่ามีใน array หรือยัง
    if (!isset($incomePerMonth[$monthKey])) {
        $incomePerMonth[$monthKey] = 0;
        $jobsPerMonth[$monthKey] = 0;
    }

    // รวมยอดรายได้
    $incomePerMonth[$monthKey] += floatval($row['total_price'] ?? 0);
    // รวมจำนวนงาน
    $jobsPerMonth[$monthKey] += 1;
}

// จัดเรียงเดือนตามลำดับ (ถ้าจำเป็น)
ksort($incomePerMonth);
ksort($jobsPerMonth);

// ส่งข้อมูลเป็น JSON ให้ JavaScript
$months = array_keys($incomePerMonth);
$incomes = array_values($incomePerMonth);
$jobs = array_values($jobsPerMonth);




$years = [];          // [2023, 2024, 2025, ...]
foreach ($result as $row) {
    $y = ($row['booking_type'] === 'daily')
          ? date('Y', strtotime($row['start_date']))
          : substr($row['start_month'], 0, 4);   // "2025-06" → "2025"
    $years[$y] = true;
}
$years = array_keys($years);
sort($years);                         // เรียงปีจากน้อย→มาก


$incomePerMonth = $jobsPerMonth = [];
for ($m = 1; $m <= 12; $m++) {
    $monthKey = sprintf('%04d-%02d', $year, $m);
    $incomePerMonth[$monthKey] = 0;
    $jobsPerMonth[$monthKey]   = 0;
}

foreach ($result as $row) {
    if (($row['status'] ?? '') === 'rejected') continue;

    $monthKey = ($row['booking_type'] === 'daily')
        ? date('Y-m', strtotime($row['start_date']))
        : $row['start_month'];

    // เก็บเฉพาะรายการที่ตรงกับปีที่เลือก
    if (strpos($monthKey, $year . '-') === 0) {
        $incomePerMonth[$monthKey] += floatval($row['total_price'] ?? 0);
        $jobsPerMonth[$monthKey]   += 1;
    }
}
ksort($incomePerMonth);
ksort($jobsPerMonth);


$months  = array_keys($incomePerMonth);
$incomes = array_values($incomePerMonth);
$jobs    = array_values($jobsPerMonth);
$totalIncome = array_sum($incomes);



$years = [];
foreach ($result as $r) {
    $y = ($r['booking_type'] === 'daily')
            ? date('Y', strtotime($r['start_date']))
            : substr($r['start_month'], 0, 4);
    $years[$y] = true;
}
$years = array_keys($years);
sort($years);

if (isset($_GET['ajax_year'])) {
    $year = (int)$_GET['ajax_year'];

    $income = $jobs = [];
    for ($m=1;$m<=12;$m++){
        $k=sprintf('%04d-%02d',$year,$m);
        $income[$k]=0; $jobs[$k]=0;
    }

    foreach ($result as $r){
        if (($r['status']??'')==='rejected') continue;
        $k = ($r['booking_type']==='daily')
                ? date('Y-m',strtotime($r['start_date']))
                : $r['start_month'];

        if (strpos($k,$year.'-')===0){
            $income[$k] += (float)($r['total_price']??0);
            $jobs[$k]   += 1;
        }
    }
    ksort($income); ksort($jobs);

    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'months'  => array_keys($income),
        'incomes' => array_values($income),
        'jobs'    => array_values($jobs),
        'total'   => array_sum($income)
    ]);
    exit;
}



$selectedYear = isset($_GET['year']) && ctype_digit($_GET['year'])
                ? $_GET['year']
                : date('Y');
$years = range(date('Y'), 2020);     // [2025, 2024, ... 2020]         ไว้เลือกปี---------------------------------//////////////////
sort($years);   
?>

<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>การจ้างงานของคุณ</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />

<style>
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

  .box{
    margin: 0 200px;
    margin-top: 20px;
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
            <a href="#" onclick="location.href='caregiver_dashboard.php'" >หน้าหลัก</a>
            <a href="#" onclick="location.href='contract.php'">การทำสัญญา</a>
            <a class="test_caregiver" href="test_caregiver.php">ทำแบบทดสอบ</a>
            <!-- <a href="#">ระดับการดูแล</a> -->
            <a class="test_caregiver" href="booking.php">แจ้งเตือนการจ้าง</a>
            <a class="test_caregiver" href="working.php" style="color: #ff9100;">การทำงาน</a>
            <!-- <a href="#">ติดต่อเรา</a> -->
        </nav>
        <div class="user-info">
        
            <?php
           
            if (isset($_SESSION['user_name'])) {
                echo "<p>สวัสดี, ผู้ดูแล " . htmlspecialchars($_SESSION['user_name']) . "</p>";
            }
            ?>

            </div>
        
    
        </div>

    <div class="icon-circle">
    <img src="get_profile_image_parent.png" onclick="location.href='caregiver_profile.php'" style="width:50px; height:50px; border-radius:50%; cursor:pointer;" alt="โปรไฟล์">
    </div>


    </header>    




    <style>
  /* ลดขนาด container ของกราฟให้เล็กลง */
  #chart-container {
    height: 600px;
    width: 1000px;  /* กว้าง 600px */
    max-width: 500vw; /* กรณีหน้าจอเล็กจะยืดตาม */
    margin: 20px auto;
  }
  .chart-header {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 20px;
        gap: 12px;
    }

    /* ฟอร์มเลือกปี */
    #yearForm {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 0.95rem;
    }
    #yearSelect {
        appearance: none;         /* ซ่อนลูกศร default บางเบราว์เซอร์ */
        border: 1px solid #d0d7e2;
        border-radius: 6px;
        padding: 6px 26px 6px 12px;
        font-size: 0.95rem;
        background: #fff url("data:image/svg+xml,%3Csvg height='12' width='12' xmlns='http://www.w3.org/2000/svg'%3E%3Cpolyline points='2,4 6,8 10,4' stroke='%23555' stroke-width='1.5' fill='none' stroke-linecap='round'/%3E%3C/svg%3E") no-repeat right 8px center/10px;
        cursor: pointer;
    }

    /* ข้อความสรุปรายได้ */
    .total-income {
        font-size: 1rem;
        font-weight: 600;
        color: #1c6ed2;
        white-space: nowrap;
    }

    /* Canvas ให้รักษาอัตราส่วน 2:1 ด้วย aspect-ratio */
    #bookingChart {
        width: 100%;
        aspect-ratio: 2 / 1;
    }

    /* Responsive เพิ่มเติม */
    @media (max-width: 480px) {
        #chart-container {
            padding: 18px 18px 24px;
        }
        #yearForm label { display: none; } /* จอเล็กซ่อน label "เลือกปี" ประหยัดที่ */
    }
</style>




<div id="chart-container">
  <div class="chart-header">
<form id="yearForm" method="get" style="display:inline-block;">
  <label for="yearSelect">เลือกปี</label>
  <select id="yearSelect" name="year" onchange="this.form.submit()">
      <?php foreach ($years as $y): ?>
          <option value="<?= $y ?>" <?= $y == $selectedYear ? 'selected' : '' ?>>
              <?= $y ?>
          </option>
      <?php endforeach; ?>
  </select>
</form>

  <span id="totalIncome" class="total-income"></span>
</div>
  <canvas id="bookingChart"></canvas>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('bookingChart').getContext('2d');
let chart;

/* ---- สร้าง/อัปเดตกราฟ ---- */
async function loadYear(year){
    const res = await fetch('?ajax_year='+year);  // เรียก endpoint ด้านบน
    const data = await res.json();

    // อัปเดตข้อความรวม
    document.getElementById('totalIncome').textContent =
        `ยอดรายได้ปี ${year} : ` +
        new Intl.NumberFormat('th-TH',{minimumFractionDigits:2}).format(data.total) +
        ' บาท';

    if(!chart){
        chart = new Chart(ctx, makeConfig(data));
    }else{
        chart.data.labels = data.months;
        chart.data.datasets[0].data = data.incomes;
        chart.data.datasets[1].data = data.jobs;
        chart.update();
    }
}

/* ---- config Chart.js ---- */
function makeConfig(d) {
  return {
    type: 'bar',            // ชนิดกราฟหลัก
    data: {
      labels: d.months,
      datasets: [
        {
          label: 'รายได้ (บาท)',
          data: d.incomes,
          backgroundColor: 'rgba(54,162,235,.65)',
          borderColor:   'rgba(54,162,235,.9)',
          borderWidth: 1,
          borderRadius: 4,
          yAxisID: 'y'
        },
        {
          label: 'จำนวนงาน',
          data: d.jobs,
          backgroundColor: 'rgba(255,99,132,.65)',
          borderColor:   'rgba(255,99,132,.9)',
          borderWidth: 1,
          borderRadius: 4,
          yAxisID: 'y1'     // จะให้แกนขวาหรือรวมแกนเดียวก็ได้
        }
      ]
    },
    options: {
      maintainAspectRatio: false,
      responsive: true,
      scales: {
        x: { grid: { display: false } },
        y: {
          beginAtZero: true,
          title: { display: true, text: 'รายได้ (บาท)' }
        },
        y1: {
          beginAtZero: true,
          position: 'right',
          grid: { drawOnChartArea: false },
          title: { display: true, text: 'จำนวนงาน' }
        }
      },
      plugins: {
        legend: { position: 'top' }
      }
    }
  };
}


/* ---- เมื่อผู้ใช้เลือกปีใหม่ ---- */
document.getElementById('yearSelect').addEventListener('change',e=>{
    loadYear(e.target.value);
});

/* ---- โหลดกราฟปีปัจจุบันเมื่อ page เปิด ---- */
loadYear(document.getElementById('yearSelect').value);
</script>




<h2 class="mb-3" style="margin: 0 150px; margin-top: 60px;">
    🔔 การทำงาน (<?= $activeCount ?> งาน)
</h2>
<style>
    .booking-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 15px;
        font-family: Arial, sans-serif;
    }
    .booking-table th, .booking-table td {
        border: 1px solid #ccc;
        padding: 8px 10px;
        vertical-align: middle;
        text-align: left;
    }
    .booking-table th {
        background-color: #f9f9f9;
        border: 1px solid #ccc;
        padding: 10px;
        vertical-align: top;
        font-size: 16px;
        text-align: center;
    }
    .booking-table tr:nth-child(even) {
        background-color: #fafafa;
    }
    .booking-table a {
        color: #0072ff;
        text-decoration: none;
    }
    .booking-table a:hover {
        text-decoration: underline;
    }
</style>

<div class="box">
<?php if (!empty($result)): ?>
    <table class="booking-table">
        <thead>
            <tr>
                <th>ผู้ปกครอง</th>
                <th>ที่อยู่สำหรับไป</th>
                <th>ประเภท</th>
                <th>ช่วงเวลา</th>
                <th>เวลา (รายวัน)</th>
                <th>จำนวนเงิน (บาท)</th>
                <th>จำนวนเด็ก</th>
                <th>ติดต่อ</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($result as $row):
            if (($row['status'] ?? '') === 'rejected') continue;

            $today = date('Y-m-d');

            if ($row['booking_type'] === 'daily' && !empty($row['end_date'])) {
                if ($today > $row['end_date']) continue;
            }

            if ($row['booking_type'] === 'monthly' && !empty($row['end_month'])) {
                $endMonthDate = date('Y-m-t', strtotime($row['end_month'] . '-01'));
                if ($today > $endMonthDate) continue;
            }

            // กำหนดช่วงเวลา
            if ($row['booking_type'] === 'daily') {
                $timeRange = 
                    htmlspecialchars(($row['start_date'] ?? 'ไม่ระบุ')) 
                    . " - " . 
                    htmlspecialchars(($row['end_date'] ?? 'ไม่ระบุ'));
                $timeOfDay = 
                    htmlspecialchars(($row['start_time'] ?? 'ไม่ระบุ')) 
                    . " - " . 
                    htmlspecialchars(($row['end_time'] ?? 'ไม่ระบุ'));
            } else {
                $timeRange = 
                    htmlspecialchars(($row['start_month'] ?? 'ไม่ระบุ')) 
                    . " - " . 
                    htmlspecialchars(($row['end_month'] ?? 'ไม่ระบุ'));
                $timeOfDay = '-';
            }
        ?>
            <tr>
                <td>
                    <a href="profile_parent.php?id=<?= urlencode($row['parent_id']) ?>">
                        <?= htmlspecialchars(($row['first_name'] ?? '') . " " . ($row['last_name'] ?? 'ไม่ระบุ')) ?>
                    </a>
                </td>
                <td><?= htmlspecialchars($row['address'] ?? 'ไม่มีข้อมูล') ?></td>
                <td><?= ($row['booking_type'] === 'daily') ? 'รายวัน' : 'รายเดือน' ?></td>
                <td><?= $timeRange ?></td>
                <td><?= $timeOfDay ?></td>
                <td><?= number_format($row['total_price'] ?? 0) ?></td>
                <td><?= htmlspecialchars($row['children_count'] ?? '0') ?></td>
                <td>
                    <?= htmlspecialchars($row['phone_number'] ?? 'ไม่มีข้อมูล') ?><br>
                    อีเมล: <?= htmlspecialchars($row['email'] ?? 'ไม่มีข้อมูล') ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>ไม่พบรายการการจอง</p>
<?php endif; ?>
</div>

</body>
</html>
