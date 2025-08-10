<?php
session_start();
if (!isset($_SESSION['parent_id'])) {
    header("Location: login.php");
    exit();
}
include 'config.php';

// ฟิลเตอร์ต่างๆ
$age = $_GET['age'] ?? '';
$gender = $_GET['gender'] ?? '';
$price = $_GET['price'] ?? '';
$province = $_GET['province'] ?? '';
$district = $_GET['district'] ?? '';
$sub_district = $_GET['sub_district'] ?? '';
$type = $_GET['type'] ?? '';
$search = $_GET['search'] ?? '';

// SQL เบื้องต้น
$sql = "SELECT caregiver_id, first_name, last_name, gender, birthdate, district, province, sub_district, type, img, phone, price 
        FROM caregiver 
        WHERE status = 'อนุมัติ'";

// ค้นหาชื่อ/นามสกุล
if ($search !== '') {
    $search_esc = $conn->real_escape_string($search);
    $sql .= " AND (first_name LIKE '%$search_esc%' OR last_name LIKE '%$search_esc%')";
}

// กรองตามอายุ
if ($age !== '') {
    if ($age == '25-30') {
        $from = (new DateTime())->modify('-30 years')->format('Y-m-d');
        $to = (new DateTime())->modify('-25 years')->format('Y-m-d');
        $sql .= " AND birthdate BETWEEN '$from' AND '$to'";
    } elseif ($age == '31-40') {
        $from = (new DateTime())->modify('-40 years')->format('Y-m-d');
        $to = (new DateTime())->modify('-31 years')->format('Y-m-d');
        $sql .= " AND birthdate BETWEEN '$from' AND '$to'";
    } elseif ($age == '41+') {
        $from = (new DateTime())->modify('-150 years')->format('Y-m-d');
        $to = (new DateTime())->modify('-41 years')->format('Y-m-d');
        $sql .= " AND birthdate BETWEEN '$from' AND '$to'";
    }
}

// เงื่อนไขอื่นๆ
if ($gender !== '') $sql .= " AND gender = '" . $conn->real_escape_string($gender) . "'";
if ($price === 'low') $sql .= " AND price < 400";
elseif ($price === 'medium') $sql .= " AND price BETWEEN 400 AND 700";
elseif ($price === 'high') $sql .= " AND price > 700";
if ($province !== '') $sql .= " AND province = '" . $conn->real_escape_string($province) . "'";
if ($district !== '') $sql .= " AND district = '" . $conn->real_escape_string($district) . "'";
if ($sub_district !== '') $sql .= " AND sub_district = '" . $conn->real_escape_string($sub_district) . "'";
if ($type !== '') $sql .= " AND type = '" . $conn->real_escape_string($type) . "'";

// ดึงข้อมูล
$result = $conn->query($sql);

?>







<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Caregiver Search</title>
    <link rel="stylesheet" href="web2.css">
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

/* ===== container หลักของฟอร์ม ===== */
.filter-container {
  background: #fff;
  border-radius: 16px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
  padding: 24px;
  max-width: 980px;
  margin: 24px auto;
}

/* ===== อินพุตค้นหา ===== */
.search-section {
  margin-bottom: 20px;
}
.search-input {
  width: 100%;
  padding: 12px 44px 12px 16px;
  border: 1.5px solid #d6d6d6;
  border-radius: 12px;
  font-size: 1rem;
  transition: border-color 0.2s;
  background: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='%23999' height='20' viewBox='0 0 24 24' width='20'%3E%3Cpath d='M15.5 14h-.79l-.28-.27a6.471 6.471 0 0 0 1.48-5.34C15.05 5.67 12.4 3 9.13 3 5.86 3 3.21 5.67 3.21 9c0 3.33 2.65 6 5.92 6 1.61 0 3.09-.59 4.23-1.56l.27.28v.79l4.25 4.25c.39.39 1.03.39 1.42 0 .4-.39.4-1.03.01-1.42L15.5 14zm-6.37 0C6.34 14 4 11.66 4 9s2.34-5 5.13-5S14.25 6.34 14.25 9s-2.34 5-5.12 5z'/%3E%3C/svg%3E")
    no-repeat right 14px center / 20px 20px;
}
.search-input:focus {
  outline: none;
  border-color: #ff9100;
}



/* ===== กลุ่มฟิลเตอร์หลัก ===== */
.filter-container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .filter-wrapper {
            padding: 30px;
        }

        .filter-form {
            width: 100%;
        }

        /* Search Section */
        .search-section {
            margin-bottom: 30px;
        }

        .search-input {
            width: 100%;
            padding: 8px 12px;
            font-size: 16px;
            border: 2px solid #e1e8ed;
            border-radius: 8px;
            outline: none;
            transition: all 0.3s ease;
            background: #f8fafc;
        }

        .search-input:focus {
            border-color: #3b82f6;
            background: white;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .search-input::placeholder {
            color: #9ca3af;
        }

        /* Filter Main Section */
        .filter-main {
            display: grid;
            grid-template-columns: repeat(6, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
        }

        .filter-label {
            font-size: 14px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 8px;
            text-align: left;
        }

        .filter-select {
            padding: 12px 16px;
            border: 2px solid #e1e8ed;
            border-radius: 8px;
            font-size: 14px;
            background: white;
            cursor: pointer;
            outline: none;
            transition: all 0.3s ease;
            appearance: none;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
            background-position: right 12px center;
            background-repeat: no-repeat;
            background-size: 16px;
            padding-right: 40px;
        }

        .filter-select:hover {
            border-color: #3b82f6;
        }

        .filter-select:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        /* Search Button */
        .search-button {
            margin-left: 40%;
            width: 20%;
            padding: 15px 30px;
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .search-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(59, 130, 246, 0.3);
        }

        .search-button:active {
            transform: translateY(0);
        }

        .search-button::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .search-button:hover::before {
            left: 100%;
        }






/* ===== mobile layout tweaks ===== */
@media (max-width: 576px) {
  .filter-main {
    grid-template-columns: 1fr;
  }
  .search-button {
    width: 100%;
  }
}

.demo-content {
  max-width: 1040px;
  margin: 40px auto;
  padding: 0 16px;
}
.demo-content h2 {
  font-size: 1.8rem;
  font-weight: 700;
  margin-bottom: 6px;
  color: #00a99d;
}
.demo-content > p {
  margin-bottom: 24px;
  color: #666;
}


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

    </style>
</head>
<body>
    <header>
        <div class="head">
        <div class="logo">
            <img src="logo2.png" alt="Logo" height="60px" >
        </div>
        <nav>
            <a href="#" onclick="location.href='home.php'">หน้าหลัก</a>
            <a href="#" style="color: #ff9100;">ค้นหาผู้ดูแล</a>
            <!-- <a href="#">ระดับการดูแล</a> -->
            <a href="#" onclick="location.href='web3.php'">การหาที่ตรงใจ</a>
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










    <!-- -------------------------------------------------------------------------------ตัวค้นหา------------------------------------------------------------------------------------------------------------------ -->
    <form method="GET" action="web2.php">

    <div class="filter-container">
        <div class="filter-wrapper">
            <form method="GET" action="web2.php" class="filter-form">
                <!-- Search Section -->
                <div class="search-section">
                    <input
                        type="text"
                        name="search"
                        class="search-input"
                        placeholder="🔍 ค้นหาชื่อหรือนามสกุลผู้ดูแลเด็ก..."
                        value=""
                    >
                </div>

                <!-- Filter Section -->
                <div class="filter-main">
                    <!-- Care Type -->
                    <div class="filter-group">
                        <div class="filter-label">ประเภทการดูแล</div>
                        <select name="type" class="filter-select">
                            <option value="">ทุกประเภท</option>
                            <option value="เด็กปกติ">เด็กปกติ</option>
                            <option value="เด็กออทิสติก">เด็กออทิสติก</option>
                        </select>
                    </div>

                    <!-- Age -->
                    <div class="filter-group">
                        <div class="filter-label">อายุ</div>
                        <select name="age" class="filter-select">
                            <option value="">ทุกช่วงอายุ</option>
                            <option value="25-30">25-30 ปี</option>
                            <option value="31-40">31-40 ปี</option>
                            <option value="41+">41+ ปี</option>
                        </select>
                    </div>

                    <!-- Gender -->
                    <div class="filter-group">
                        <div class="filter-label">เพศ</div>
                        <select name="gender" class="filter-select">
                            <option value="">ทุกเพศ</option>
                            <option value="ชาย">ชาย</option>
                            <option value="หญิง">หญิง</option>
                        </select>
                    </div>

                    <!-- Price -->
                    <div class="filter-group">
                        <div class="filter-label">ราคา</div>
                        <select name="price" class="filter-select">
                            <option value="">ทุกราคา</option>
                            <option value="low">ต่ำกว่า 400 บาท</option>
                            <option value="medium">400 - 700 บาท</option>
                            <option value="high">มากกว่า 700 บาท</option>
                        </select>
                    </div>

                    <!-- Province -->
                    <div class="filter-group">
                        <div class="filter-label">จังหวัด</div>
                        <select name="province" class="filter-select">
                            <option value="">ทุกจังหวัด</option>
                            <option value="กรุงเทพฯ">กรุงเทพฯ</option>
                        </select>
                    </div>

                    <!-- District -->
                    <div class="filter-group">
                        <div class="filter-label">เขต/อำเภอ</div>
                        <select name="district" id="district" class="filter-select" onchange="updateSubdistricts()">
                            <option value="">ทุกเขต/อำเภอ</option>
                            <option value="พระนคร">เขตพระนคร</option>
                            <option value="ดุสิต">เขตดุสิต</option>
                            <option value="หนองจอก">เขตหนองจอก</option>
                            <option value="บางรัก">เขตบางรัก</option>
                            <option value="บางเขน">เขตบางเขน</option>
                            <option value="บางกะปิ">เขตบางกะปิ</option>
                            <option value="ปทุมวัน">เขตปทุมวัน</option>
                            <option value="ป้อมปราบศัตรูพ่าย">เขตป้อมปราบศัตรูพ่าย</option>
                            <option value="พระโขนง">เขตพระโขนง</option>
                            <option value="มีนบุรี">เขตมีนบุรี</option>
                            <option value="ลาดกระบัง">เขตลาดกระบัง</option>
                            <option value="ยานนาวา">เขตยานนาวา</option>
                            <option value="สัมพันธวงศ์">เขตสัมพันธวงศ์</option>
                            <option value="พญาไท">เขตพญาไท</option>
                            <option value="ธนบุรี">เขตธนบุรี</option>
                            <option value="บางกอกใหญ่">เขตบางกอกใหญ่</option>
                            <option value="ห้วยขวาง">เขตห้วยขวาง</option>
                            <option value="คลองสาน">เขตคลองสาน</option>
                            <option value="ตลิ่งชัน">เขตตลิ่งชัน</option>
                            <option value="บางกอกน้อย">เขตบางกอกน้อย</option>
                            <option value="บางขุนเทียน">เขตบางขุนเทียน</option>
                            <option value="ภาษีเจริญ">เขตภาษีเจริญ</option>
                            <option value="หนองแขม">เขตหนองแขม</option>
                            <option value="ราษฎร์บูรณะ">เขตราษฎร์บูรณะ</option>
                            <option value="บางพลัด">เขตบางพลัด</option>
                            <option value="ดินแดง">เขตดินแดง</option>
                            <option value="บึงกุ่ม">เขตบึงกุ่ม</option>
                            <option value="สาทร">เขตสาทร</option>
                            <option value="บางซื่อ">เขตบางซื่อ</option>
                            <option value="จตุจักร">เขตจตุจักร</option>
                            <option value="บางคอแหลม">เขตบางคอแหลม</option>
                            <option value="ประเวศ">เขตประเวศ</option>
                            <option value="คลองเตย">เขตคลองเตย</option>
                            <option value="สวนหลวง">เขตสวนหลวง</option>
                            <option value="จอมทอง">เขตจอมทอง</option>
                            <option value="ดอนเมือง">เขตดอนเมือง</option>
                            <option value="ราชเทวี">เขตราชเทวี</option>
                            <option value="ลาดพร้าว">เขตลาดพร้าว</option>
                            <option value="วัฒนา">เขตวัฒนา</option>
                            <option value="บางแค">เขตบางแค</option>
                            <option value="หลักสี่">เขตหลักสี่</option>
                            <option value="สายไหม">เขตสายไหม</option>
                            <option value="คันนายาว">เขตคันนายาว</option>
                            <option value="สะพานพุทธ">เขตสะพานพุทธ</option>
                            <option value="วังทองหลาง">เขตวังทองหลาง</option>
                            <option value="คลองสามวา">เขตคลองสามวา</option>
                            <option value="บางนา">เขตบางนา</option>
                            <option value="ทวีวัฒนา">เขตทวีวัฒนา</option>
                            <option value="ทุ่งครุ">เขตทุ่งครุ</option>
                            <option value="บางบอน">เขตบางบอน</option>
                        </select>
                    </div>
                </div>

                <!-- Search Button -->
                <button type="submit" class="search-button">
                    <span>ค้นหา</span>
                </button>
            </form>
        </div>
    </div>
    <!-- Demo content to show the filter in context -->
    <div class="demo-content">
        <h2>ผลการค้นหาผู้ดูแลเด็ก</h2>
        <p>แถบค้นหาด้านบนจะช่วยให้คุณค้นหาผู้ดูแลเด็กที่เหมาะสมได้อย่างรวดเร็วและแม่นยำ</p>
    </div>


    <script>
        function updateSubdistricts() {
            // Function to update subdistricts based on selected district
            console.log('District changed');
        }

        // Enhanced form submission with loading state
        document.querySelector('form').addEventListener('submit', function(e) {
            const button = this.querySelector('.search-button');
            const span = button.querySelector('span');
            
            // Add loading state
            button.classList.add('loading');
            span.style.opacity = '0';
            button.disabled = true;
            
            // For demo purposes, remove loading state after 2 seconds
            setTimeout(() => {
                button.classList.remove('loading');
                span.style.opacity = '1';
                button.disabled = false;
            }, 2000);
        });

        // Auto-submit on filter change (optional)
        document.querySelectorAll('.filter-select').forEach(select => {
            select.addEventListener('change', function() {
                // Uncomment the line below to enable auto-submit
                // this.form.submit();
            });
        });

        // Enhanced search input with debounce
        let searchTimeout;
        document.querySelector('.search-input').addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                // Uncomment to enable live search
                // this.form.submit();
            }, 500);
        });
    </script>
</form>



    


    <div class="cards">
    <?php while ($row = $result->fetch_assoc()): ?>
    
        <div class="card" style="
            background: white; 
            border-radius: 10px; 
            padding: 20px; 
            width: 280px; 
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1); 

            transition: transform 0.3s ease, box-shadow 0.3s ease;">

            <!-- แสดงภาพโปรไฟล์ -->
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

            <!-- ข้อมูลผู้ดูแล -->
            <h3 style="margin: 10px 0; font-size: 20px; color: #333;">
                <?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?>
            </h3>
            
            <p style="margin: 5px 0; font-size: 16px; color: #666;">
    อายุ:
    <?php 
        $birth_raw = trim($row['birthdate'] ?? '');
        if (!empty($birth_raw) && strtotime($birth_raw) && $birth_raw != '0000-00-00') {
            $birthdate = new DateTime($birth_raw);
            $today = new DateTime();
            $age = $today->diff($birthdate)->y;
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

            <p style="margin: 5px 0; font-size: 16px; color: #666;">
อัตราค่าบริการ: <?php echo number_format($row['price'] ?? 0, 0); ?> บาท / วัน
                </p>

<p style="margin: 5px 0; font-size: 16px; color: #666;">
                ประเภท: <?php echo htmlspecialchars($row['type'] ?? 'ไม่ระบุ'); ?>
            </p>
            <!-- ปุ่มดูรายละเอียด -->
            <a href="caregiver_profile_show.php?id=<?php echo urlencode($row['caregiver_id']); ?>" class="btn" style="
                display: inline-block; 
                text-decoration: none; 
                background: #007bff; 
                color: white; 
                padding: 10px 15px; 
                border-radius: 5px; 
                font-size: 16px; 
                transition: background 0.3s ease;">
                ดูรายละเอียด
            </a>
        </div>
    <?php endwhile; ?>
</div>


    <!-- <div class="more">
        <h1>ดูเพิ่มเติม</h1>
    </div> -->

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
