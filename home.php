<?php
session_start();
if (!isset($_SESSION['parent_id'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Find Caregiver</title>
    <link rel="stylesheet" href="home.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>
<body>

<div class="container">
    <header>
        <div class="head">
        <div class="logo">
            <img src="logo2.png" alt="Logo" height="60px">
        </div>
        <nav>
            <a href="#" style="color: #ff9100;">หน้าหลัก</a>
            <a href="#" onclick="location.href='web2.php'">ค้นหาผู้ดูแล</a>
            <!-- <a href="#">ระดับการดูแล</a> -->
            <a href="#" onclick="location.href='web3.php'">การหาที่ตรงใจ</a>
            <a href="#" onclick="location.href='web4.php'">การจ้าง</a>
                        <a href="web5.php" >ประวัติการจอง</a>

            <!-- <a href="#">ติดต่อเรา</a> -->
        </nav>
        <div class="user-info">
        <?php
            // ตรวจสอบว่า session มีค่า parent_name หรือไม่
            if (isset($_SESSION['user_name'])) {
                echo "<p>สวัสดี, ผู้ปกครอง " . htmlspecialchars($_SESSION['user_name']) . "</p>";
            }
            ?>

            </div>

    
        </div>

    <div class="icon-circle">
    <img src="get_profile_image_parent.png" 
     onclick="location.href='profile.php'" 
     style="width:50px; height:50px; border-radius:50%; cursor:pointer;" 
     alt="โปรไฟล์">

    </div>


    </header>


    <section class="hero-section">
        <div class="text">
        <h1>Find<br>Caregiver</h1>
        <button class="cta" onclick="location.href='web3.php'">เริ่มหาเลย !</button>
        </div>
        <div class="banner"><img src="banner.jpg"></div>
    </section>

    <section class="features">
        <div class="feature">
            <img src="icon2.jpg" alt="Search">
            <h3>ค้นหาผู้ดูแลที่เหมาะสม</h3>
            <p>ผู้ปกครองสามารถกำหนดความต้องการเพื่อค้นหาผู้ดูแล</p>
        </div>
        <div class="feature">
            <img src="icon3.jpg" alt="Profile">
            <h3>โปรไฟล์/ประเมินผู้ดูแล</h3>
            <p>ผู้ดูแลมีโปรไฟล์แสดงประสบการณ์และใบรับรอง</p>
        </div>
        <div class="feature">
            <img src="icon1.jpg" alt="Track">
            <h3>จองและติดตามผล</h3>
            <p>สามารถจัดการการจองและติดตามสถานะได้</p>
        </div>
    </section>

    <div class="question">
    <section class="faq">
        <h2>คำถามที่พบบ่อย</h2>
        <div class="faq-item">
            <div class="faq-question">
            <span>คอร์สการดูแลมีระดับใดบ้าง?</span>
            <span class="faq-icon">+</span>
          </div>
          <div class="faq-answer">
            <p>คอร์สการดูแลมีระดับเริ่มต้น ระดับกลาง และระดับสูงให้เลือกตามความต้องการของคุณ</p>
          </div>
        </div>
        <div class="faq-item">
            <div class="faq-question">
            <span>ฉันจะจองผู้ดูแลได้อย่างไร?</span>
            <span class="faq-icon">+</span>
          </div>
          <div class="faq-answer">
            <p>คุณสามารถจองผู้ดูแลผ่านระบบออนไลน์ โดยเลือกวันและเวลาที่สะดวก</p>
          </div>
        </div>
        <div class="faq-item">
            <div class="faq-question">
            <span>มีการให้ใบรับรองหลังจากสำเร็จการเรียนหรือไม่?</span>
            <span class="faq-icon">+</span>
          </div>
          <div class="faq-answer">
            <p>มีบริการสอนเพิ่มเติมหลังการเรียนเสร็จสิ้นเพื่อความเข้าใจที่ลึกซึ้ง</p>
          </div>
        </div>
        <div class="faq-item"><div class="faq-question">
            <span>มีการสอนเป็นภาษาไทยหรือไม่?</span>
            <span class="faq-icon">+</span>
          </div>
          <div class="faq-answer">
            <p>คุณสามารถจองผู้ดูแลผ่านระบบออนไลน์ โดยเลือกวันและเวลาที่สะดวก</p>
          </div></div>
        <div class="faq-item"><div class="faq-question">
            <span>หากมีปัญหาในการเข้าใช้งาน ควรทำอย่างไร?</span>
            <span class="faq-icon">+</span>
          </div>
          <div class="faq-answer">
            <p>คุณสามารถจองผู้ดูแลผ่านระบบออนไลน์ โดยเลือกวันและเวลาที่สะดวก</p>
          </div></div>
        <div class="faq-item"><div class="faq-question">
            <span>จะสามารถเปลี่ยนคอร์สได้หรือไม่หลังจากลงทะเบียน?</span>
            <span class="faq-icon">+</span>
          </div>
          <div class="faq-answer">
            <p>คุณสามารถจองผู้ดูแลผ่านระบบออนไลน์ โดยเลือกวันและเวลาที่สะดวก</p>
          </div></div>
    </section>
    <img src="pic-Qu.png">
    </div>

    <script>
        // Add event listeners to FAQ items
        document.querySelectorAll('.faq-question').forEach(question => {
          question.addEventListener('click', () => {
            const answer = question.nextElementSibling;
            const icon = question.querySelector('.faq-icon');
    
            // Toggle the answer
            if (answer.style.display === 'block') {
              answer.style.display = 'none';
              icon.textContent = '+';
            } else {
              answer.style.display = 'block';
              icon.textContent = '-';
            }
          });
        });
      </script>

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
    </div>

</body>
</html>
