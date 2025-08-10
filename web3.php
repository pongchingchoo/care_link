<?php
session_start();
if (!isset($_SESSION['parent_id'])) {
    header("Location: login.php");
    exit();
}

$parent_id = $_SESSION['parent_id'];

// เชื่อมต่อฐานข้อมูล
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "care_link";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ดึงคะแนนของ Parent จากตาราง parent_questionnaire_result
$sql = "SELECT P_score FROM parent_questionnaire_result WHERE parent_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $parent_id);
$stmt->execute();
$result = $stmt->get_result();
$parent_score = $result->fetch_assoc()['P_score'] ?? null;



//ดึงข้อมูล caregiver ที่มีคะแนนใกล้เคียงกับ parent_score

$sql = "
    SELECT c.*, q.score 
    FROM caregiver c 
    JOIN questionnaire_result q ON c.caregiver_id = q.caregiver_id 
    ORDER BY ABS(q.score - ?) ASC
";



$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $parent_score);
$stmt->execute();
$caregivers = $stmt->get_result();





$conn->close();
?>








<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Caregiver Search</title>
    <link rel="stylesheet" href="web3.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

    <style>


/* ===== รีเซ็ตเล็กน้อย ===== */
*,
*::before,
*::after {
  box-sizing: border-box;
}
body {
  font-family: "Noto Sans Thai", Arial, sans-serif;
  color: #333;
  margin: 0;
  line-height: 1.4;
}

/* ========= สีหลักธีมเขียวมิ้น ========= */
/* ========= สีหลักธีมเขียวมิ้น ========= */
:root {
  --mint:        #09B3B0;          /* สีหลัก */
  --mint-dark:   #06817F;          /* เข้ม ≈ –20 % */
  --mint-light:  #09b3b026;        /* อ่อน โปร่ง 15 % */
  --mint-shadow: rgba(9,179,176,.3);  /* เงา / ไฮไลต์ */
}

/* ---------- เลย์เอาต์การ์ด ---------- */
.cards {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 24px;
  max-width: 1400px;
  margin: 0 auto;
  padding: 20px;
}

/* ---------- การ์ดผู้ดูแล ---------- */
.card {
  background: #fff !important;
  border-radius: 16px !important;
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08) !important;
  padding: 24px 20px 28px !important;
  width: 100% !important;
  position: relative;
  overflow: hidden;
  border: 1px solid rgba(255, 255, 255, 0.8);
  transition: all .3s ease !important;
}
.card::before {
  content: "";
  position: absolute;
  top: 0; left: 0; right: 0; height: 4px;
  background: linear-gradient(90deg, var(--mint), var(--mint-dark));
  opacity: 0; transition: opacity .3s ease;
}
.card:hover {
  transform: translateY(-8px) !important;
  box-shadow: 0 16px 40px rgba(0, 0, 0, 0.15) !important;
  border-color: var(--mint-light);
}
.card:hover::before { opacity: 1; }

/* ---------- รูปโปรไฟล์ ---------- */
.caregiver-img {
  width: 100px !important; height: 100px !important;
  border-radius: 50% !important; object-fit: cover !important;
  border: 4px solid #e9ecef !important; margin-bottom: 16px !important;
  transition: all .3s ease;
}
.card:hover .caregiver-img {
  border-color: var(--mint);
  transform: scale(1.05);
}

/* ---------- Bullet & ข้อความ ---------- */
.card p:before     { content: "•";  color: var(--mint); font-weight: 700; font-size: 1.2em; }
.card p:first-of-type:before  { content: "👤"; }
.card p:nth-of-type(2):before { content: "⚥"; }
.card p:nth-of-type(3):before { content: "📍"; }
.card p:nth-of-type(4):before { content: "💰"; }
.card p:nth-of-type(5):before { content: "🏷️"; }

/* ---------- ป้ายประเภท (Tag) ---------- */
.card p:nth-of-type(5){
  display:inline-block;margin:16px 0 20px!important;padding:8px 16px;
  background:linear-gradient(135deg,#FFF6E0 0%,#FFE2B3 100%);
  color:var(--accent-dark);font-size:.85rem;font-weight:600;
  border-radius:20px;border:1px solid var(--accent-light);justify-content:center;
}

/* ---------- ปุ่มดูรายละเอียด ---------- */
.card .btn {
  display: inline-block !important;
  background: linear-gradient(135deg, var(--mint) 0%, var(--mint-dark) 100%) !important;
  color: #fff !important;
  padding: 12px 24px !important;
  border-radius: 25px !important;
  margin-top: 16px !important;
  font-size: .9rem !important; font-weight: 600 !important;
  transition: all .3s ease !important;
  box-shadow: 0 4px 12px var(--mint-shadow);
  border: none; cursor: pointer;
  position: relative; overflow: hidden;
}
.card .btn::before {
  content: "";
  position: absolute; top: 0; left: -100%;
  width: 100%; height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255,255,255,.3), transparent);
  transition: left .6s;
}
.card .btn:hover {
  transform: translateY(-2px) !important;
  box-shadow: 0 6px 20px var(--mint-shadow) !important;
  background: linear-gradient(135deg, var(--mint-dark) 0%, #036865 100%) !important;
}
.card .btn:hover::before { left: 100%; }


/* ------------ ปุ่มดูรายละเอียด ------------ */
.btn{
  display:inline-block;
  background:#007bff !important;
  color:#fff !important;
  padding:10px 18px !important;
  border-radius:10px;
  margin-top:16px !important;
  font-size:.95rem !important;
  text-decoration:none !important;
  transition:background .18s;
}
.btn:hover{background:#0066d4 !important;}




/* ------------ ปุ่มดูรายละเอียด ------------ */
.btn{
  display:inline-block;
  background:#007bff !important;
  color:#fff !important;
  padding:10px 18px !important;
  border-radius:10px;
  margin-top:16px !important;
  font-size:.95rem !important;
  text-decoration:none !important;
  transition:background .18s;
}
.btn:hover{background:#0066d4 !important;}

    </style>
</head>
<body>
    <header>
        <div class="head">
        <div class="logo">
            <img src="logo2.png" alt="Logo" height="60px">
        </div>
        <nav>
            <a href="#" onclick="location.href='home.php'">หน้าหลัก</a>
            <a href="#" onclick="location.href='web2.php'">ค้นหาผู้ดูแล</a>
            <!-- <a href="#">ระดับการดูแล</a> -->
            <a href="#" style="color: #ff9100;">การหาที่ตรงใจ</a>
            <a href="#" onclick="location.href='web4.php'">การจ้าง</a>
                        <a href="web5.php" >ประวัติการจอง</a>

            <!-- <a href="#">ติดต่อเรา</a> -->
        </nav>
        <div class="user-info">
                <?php 
                    if (isset($_SESSION['user_name'])) {
                        echo "สวัสดี, ผู้ปกครอง " . htmlspecialchars($_SESSION['user_name']);
                    }
                ?>
            </div>
        
    
        </div>

    <div class="icon-circle">
    <img src="get_profile_image_parent.png" onclick="location.href='profile.php'" style="width:50px; height:50px; border-radius:50%; cursor:pointer;" alt="โปรไฟล์">
    </div>

    </header>



<div class="banner">
    <img src="logo.png" alt="Logo" height="200px">
</div>


<button class="open-popup-btn" onclick="location.href='web3b.php'">ทำแบบสอบถาม</button>






<div class="cards">
<?php if ($caregivers && $caregivers->num_rows > 0): ?>
        <?php while ($row = $caregivers->fetch_assoc()): ?>
            <div class="card" >
                
                <!-- รูปภาพผู้ดูแล -->
                <?php if (!empty($row['img'])): ?>
                    <img src="data:image/jpeg;base64,<?php echo base64_encode($row['img']); ?>" alt="Profile" class="caregiver-img" style="
                        width: 100px; 
                        height: 100px; 
                        border-radius: 50%; 
                        object-fit: cover; 
                        border: 3px solid #ddd; 
                        margin-bottom: 15px;">
                <?php else: ?>
                    <img src="default-avatar.png" alt="Default Profile" class="caregiver-img" style="
                        width: 100px; 
                        height: 100px; 
                        border-radius: 50%; 
                        object-fit: cover; 
                        border: 3px solid #ddd; 
                        margin-bottom: 15px;">
                <?php endif; ?>

                <h3 style="margin: 10px 0; font-size: 20px; color: #333;">
                    <?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?>
                </h3>
                
                <p style="margin: 5px 0; font-size: 16px; color: #666;">
    อายุ: 
    <?php 
        if (!empty($row['birthdate']) && $row['birthdate'] != '0000-00-00') {
            $birthdate = new DateTime($row['birthdate']); // แปลงวันเกิดเป็น DateTime
            $today = new DateTime(); // วันที่ปัจจุบัน
            $age = $today->diff($birthdate)->y; // คำนวณอายุ
            echo htmlspecialchars($age) . " ปี";
        } else {
            echo "ไม่ระบุ";
        }
    ?>
</p>




                <p style="margin: 5px 0; font-size: 16px; color: #666;">
                    เพศ: <?php echo htmlspecialchars($row['gender'] ?? 'ไม่ระบุ'); ?>
                </p>
                <p style="margin: 5px 0; font-size: 16px; color: #666;">
                    ที่อยู่: 
                    <?php 
                        $address = trim(($row['district'] ?? '') . ', ' . ($row['province'] ?? ''));
                        echo htmlspecialchars(!empty($address) ? $address : 'ไม่ระบุ');
                    ?>
                </p>
                

                <!-- <p style="margin: 5px 0; font-size: 16px; color: #666;">
                เบอร์ติดต่อ: <?php echo htmlspecialchars($row['phone'] ?? 'ไม่ระบุ'); ?>
                </p> -->
                <!-- <p style="margin: 5px 0; font-size: 16px; color: #666; font-weight: bold;">
                    คะแนน: <?php echo htmlspecialchars($row['score'] ?? 'ไม่ระบุ'); ?>
                </p> -->

                <p style="margin: 5px 0; font-size: 16px; color: #666;">
อัตราค่าบริการ: <?php echo number_format($row['price'] ?? 0, 0); ?> บาท / วัน
                </p>
                
                <p style="margin: 5px 0; font-size: 16px; color: #666;">
                    ประเภท: <?php echo htmlspecialchars($row['type'] ?? 'ไม่ระบุ'); ?>
                </p>
<br>
                <!-- ปุ่มดูรายละเอียด -->
                <a href="caregiver_profile_show2.php?id=<?php echo urlencode($row['caregiver_id']); ?>" class="btn" style="
                    display: inline-block; 
                    text-decoration: none; 
                    background: #007bff; 
                    color: white; 
                    padding: 10px 15px; 
                    border-radius: 5px; 
                    margin-top: 10px; 
                    font-size: 16px; 
                    transition: background 0.3s ease;">
                    ดูรายละเอียด
                </a>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p style="text-align: center; color: red;">ไม่พบผู้ดูแลที่ตรงกับคะแนนของคุณ</p>
    <?php endif; ?>



</div>




    <footer>
        <div class="footer-content">
            <div>
                <h3>Care Link</h3>
                <p>Lorem ipsum dolor sit amet consectetur. Ullamcorper donec justo eget a odio.</p>
            </div>
            <div>
                <h3>Quick Links</h3>
                <ul>
                    <li><a href="#">Home</a></li>
                    <li><a href="#">Course</a></li>
                    <li><a href="#">Blog</a></li>
                    <li><a href="#">Contact</a></li>
                </ul>
            </div>
            <div>
                <h3>Legal Information</h3>
                <ul>
                    <li><a href="#">Privacy Policy</a></li>
                    <li><a href="#">Terms of Service</a></li>
                    <li><a href="#">Cookie Policy</a></li>
                    <li><a href="#">Refund Policy</a></li>
                </ul>
            </div>
            <div>
                <h3>Follow Us</h3>
                <ul>
                    <li><a href="#">Facebook</a></li>
                    <li><a href="#">Instagram</a></li>
                    <li><a href="#">YouTube</a></li>
                </ul>
            </div>
        </div>
    </footer>
</body>
</html>
