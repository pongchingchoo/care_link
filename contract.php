<?php
include('config.php'); // เชื่อมต่อฐานข้อมูล PDO

session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>แพ็คเกจสมัครสมาชิก</title>
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



  .package-container {
    display: grid;
    grid-template-columns: repeat(auto-fit,minmax(250px,1fr));
    gap: 20px;
    max-width: 1000px;
    width: 100%;
    padding: 40px 20px;
    position: relative;
    left: 17%;
        display: flex;
    justify-content: center;

  }
  .package-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.1);
    padding: 30px 25px;
    text-align: center;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
  }
  .package-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 15px 35px rgba(0,0,0,0.15);
  }
  .package-title {
    font-size: 24px;
    font-weight: 700;
    color: #00a99d;
    margin-bottom: 12px;
  }
  .package-price {
    font-size: 36px;
    font-weight: 900;
    margin: 15px 0;
    color: #333;
  }
  .package-desc {
    font-size: 16px;
    color: #666;
    margin-bottom: 25px;
  }
  .package-btn {
    background-color: #00a99d;
    color: white;
    border: none;
    border-radius: 8px;
    padding: 12px 0;
    font-weight: 700;
    font-size: 16px;
    cursor: pointer;
    width: 100%;
    transition: background-color 0.3s ease;
    text-decoration: none;
    display: inline-block;
  }
  .package-btn:hover {
    background-color: #008f82;
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
            <a href="#" onclick="location.href='contract.php'" style="color: #ff9100;">การทำสัญญา</a>
            <a class="test_caregiver" href="test_caregiver.php">ทำแบบทดสอบ</a>
            <!-- <a href="#">ระดับการดูแล</a> -->
            <a class="test_caregiver" href="booking.php">แจ้งเตือนการจ้าง</a>
            <a class="test_caregiver" href="working.php">การทำงาน</a>
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



<div style="max-width: 1000px; margin: 20px auto; padding: 20px; background-color: #fffbe6; border: 1px solid #ffe58f; border-radius: 10px; color: #614700;">
    <h3 style="margin-top: 0; color: #d48806;">📢 ประกาศสำคัญก่อนสมัครแพ็คเกจ</h3>
    <ul style="line-height: 1.8;">
        <li>แพ็คเกจการสมัครสมาชิกมีอายุการใช้งานตามระยะเวลาที่เลือก และไม่สามารถขอคืนเงินได้ภายหลังจากสมัครแล้ว</li>
        <li>ระบบจะทำการตัดเงินจากจำนวนเงินที่คุณจะได้รับจากงาน ตามราคาที่ระบุบนแพ็คเกจ</li>
        <li>ระบบจะเริ่มนับวันใช้งานตั้งแต่วันที่คุณสมัคร</li>
        <li>เมื่อแพ็คเกจหมดอายุ ท่านจะไม่สามารถใช้งานฟีเจอร์บางอย่างได้ จนกว่าจะสมัครแพ็คเกจใหม่</li>
        <li>หากพบปัญหาในการใช้งาน สามารถติดต่อทีมงานได้ที่อีเมล care_link@gmail.com</li>
    </ul>
</div>



<div class="package-container">

  <!-- 1 เดือน -->
<div class="package-card">
  <div class="package-title">1 เดือน</div>
  <div class="package-price">฿500</div>
  <div class="package-desc"> สมัครสมาชิก<br>แพ็คเกจ 1 เดือน </div>

  <button
      class="package-btn"
      onclick="openPaymentPopup('1 เดือน', 500)">
      สมัครเลย
  </button>
</div>

<!-- 3 เดือน -->
<div class="package-card">
  <div class="package-title">3 เดือน</div>
  <div class="package-price">฿1,500</div>
  <div class="package-desc"> สมัครสมาชิก<br>แพ็คเกจ 3 เดือน </div>

  <button
      class="package-btn"
      onclick="openPaymentPopup('3 เดือน', 1500)">
      สมัครเลย
  </button>
</div>

<!-- 6 เดือน -->
<div class="package-card">
  <div class="package-title">6 เดือน</div>
  <div class="package-price">฿2,500</div>
  <div class="package-desc"> สมัครสมาชิก<br>แพ็คเกจ 6 เดือน </div>

  <button
      class="package-btn"
      onclick="openPaymentPopup('6 เดือน', 2500)">
      สมัครเลย
  </button>
</div>

<!-- 1 ปี -->
<div class="package-card">
  <div class="package-title">1 ปี</div>
  <div class="package-price">฿4,000</div>
  <div class="package-desc"> สมัครสมาชิก <br> แพ็คเกจ 1 ปี </div>

  <button
      class="package-btn"
      onclick="openPaymentPopup('1 ปี', 4000)">
      สมัครเลย
  </button>
</div>



<script>
/**
 * เปิด pop‑up ชำระเงิน
 * @param {string} pkg    – ชื่อแพ็กเกจ (เช่น ‘1 เดือน’)
 * @param {number} price  – ราคา (เช่น 500)
 */
function openPaymentPopup(pkg, price) {
  if (!confirm(`โปรดอ่านประกาศสำคัญก่อนสมัคร\n\nยืนยันสมัครแพ็กเกจ ${pkg} ใช่หรือไม่?`)) {
    return false;               // ยกเลิกถ้าผู้ใช้กด Cancel
  }

  // สร้าง URL ส่งค่าผ่าน query string ไปยังหน้า pop‑up
  const url = `subscribe_package.php?package=${encodeURIComponent(pkg)}&price=${price}`;

  // เปิดหน้าต่างใหม่ (ปรับขนาด/option ได้ตามต้องการ)
  window.open(
    url,
    'paymentPopup',
    'width=600,height=800,scrollbars=yes,resizable=yes'
  );

  return false;                 // กันไม่ให้ <form> รีเฟรชหน้านี้
}
</script>
</div>




</body>
</html>
