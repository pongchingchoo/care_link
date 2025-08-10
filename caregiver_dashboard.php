<?php
session_start();

// ตรวจสอบว่า caregiver ได้ล็อกอินแล้วหรือไม่
if (!isset($_SESSION["user_id"])) {
    // หากไม่พบ session ให้ redirect ไปที่หน้า login (เปลี่ยนเป็น URL ที่คุณต้องการ)
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>Caregiver Dashboard</title>
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



.testimonial-text {
        max-width: 800px;
        margin: 30px 20px 30px;
        padding: 40px;
        background: #f9f9f9;
        border-radius: 20px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        text-align: center;
        font-family: 'Segoe UI', sans-serif;
        color: #333;
    }

    .testimonial-text h2 {
        font-size: 2rem;
        margin-bottom: 20px;
        color: #2c3e50;
    }

    .testimonial-text p {
        font-size: 1.1rem;
        line-height: 1.8;
        color: #555;
    }






  /* General Styles */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: Arial, sans-serif;
}

body {
    background-color: #f9f9f9;
}



.hero-section {
    display: flex;
    align-items: center;
    justify-content: space-between;
    max-width: 1200px;
    margin: 0 auto;
}

.hero-content {
    width: 50%;
}

.hero-content h1 {
    font-size: 36px;
    color: #333;
}

.hero-content p {
    font-size: 18px;
    color: #666;
    margin-top: 20px;
}

.cta-button {
    display: inline-block;
    padding: 15px 30px;
    background-color: #00b89c;
    color: white;
    text-decoration: none;
    border-radius: 5px;
    font-size: 18px;
    margin-top: 20px;
}

.hero-image {
    width: 50%;
    object-fit: cover;
}

/* Features Section */
.features {
    background-color: #fff;
    padding: 50px 0;
    display: flex;
    justify-content: space-around;
    max-width: 1200px;
    margin: 0 auto;
    margin-top: 100px;
}

.feature-item {
    text-align: center;
    padding: 20px;
    border: 1px solid #ddd;
    border-radius: 10px;
    transition: transform 0.3s ease;
    margin: 20px;
}

.feature-item:hover {
    transform: scale(1.05);
}

.feature-item img {
    width: 60px;
    height: 60px;
    margin-bottom: 15px;
}

.feature-item h3 {
    font-size: 20px;
    margin-bottom: 10px;
}

.feature-item p {
    font-size: 16px;
    color: #666;
}

/* Testimonials Section */
.testimonials {
    background-color: #e6fff7;
    padding: 50px 0;
    display: flex;
    justify-content: space-between;
    max-width: 1200px;
    margin: 0 auto;
}

.testimonial-image {
    width: 50%;
    position: relative;
}

.testimonial-image img {
    width: 100%;
    object-fit: cover;
}

.stats {
    display: flex;
    justify-content: space-around;
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(60%, -50%);
    background-color: rgba(255, 255, 255, 0.8);
    padding: 20px;
    border-radius: 10px;
}

.stat-item {
    text-align: center;
}

.stat-item span {
    font-size: 24px;
    font-weight: bold;
    color: #00b89c;
}

.stat-item p {
    font-size: 16px;
    color: #666;
}

.testimonial-text {
    width: 50%;
}

.testimonial-text h2 {
    font-size: 30px;
    color: #333;
    margin-bottom: 20px;
}

.testimonial-text p {
    font-size: 18px;
    color: #666;
}

/* Footer Section */
footer {
    background-color: #00b89c;
    color: white;
    padding: 30px 0;
}

.footer-section {
    max-width: 1200px;
    margin: 0 auto;
    display: flex;
    justify-content: space-between;
}

.footer-col {
    width: 25%;
}

.footer-logo {
    width: 60px;
    height: 60px;
    margin-bottom: 10px;
}

.footer-col h4 {
    font-size: 20px;
    margin-bottom: 15px;
}

.footer-col ul li {
    list-style: none;
    margin-bottom: 10px;
}

.footer-col ul li a {
    color: white;
    text-decoration: none;
}
    </style>
</head>
<body>
<header>
        <div class="head">
        <div class="logo">
            <img src="logo2.png" alt="Logo" height="60px" style="">
        </div>
        <nav>
            <a href="#" onclick="location.href='caregiver_dashboard.php'" style="color: #ff9100;">หน้าหลัก</a>
            <a href="#" onclick="location.href='contract.php'">การทำสัญญา</a>
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

    <section class="features">
        <div class="feature-item">
            <img src="icon1.jpg" alt="กรอกข้อมูลสมัคร">
            <h3>กรอกข้อมูลสมัคร</h3>
            <p>ใส่ข้อมูลส่วนตัว เช่น ชื่อ-นามสกุล อีเมล หมายเลขโทรศัพท์ ที่อยู่ และข้อมูลเพิ่มเติมที่จำเป็น</p>
        </div>
        <div class="feature-item">
            <img src="icon1.jpg" alt="สมัครสมาชิก + ข้อสอบ">
            <h3>สมัครสมาชิก + ข้อสอบ</h3>
            <p>การดูแลเด็กออทิสติกเน้นเรื่องความรู้เข้าใจพัฒนาการเด็กพิเศษและการจัดการสถานการณ์</p>
        </div>
        <div class="feature-item">
            <img src="icon1.jpg" alt="เสร็จสิ้น">
            <h3>เสร็จสิ้น</h3>
            <p>การผ่านการทดสอบเพื่อการเทรนและอบรมในการดูแลเด็กออทิสติก</p>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="testimonials">
        <div class="testimonial-image">
            <img src="01.png" alt="แม่และเด็ก">
            <div class="">
                <div class="stat-item">
                    
                </div>
                <div class="stat-item">
                    
                </div>
            </div>
        </div>
        <div class="testimonial-text" >
            <h2>สมัครเป็นผู้เลี้ยงสร้างคุณภาพชีวิตที่ดีกว่าเดิม</h2>
            <p>การสมัครเป็นผู้เลี้ยง (Caregiver) เป็นการเข้าร่วมในกระบวนการสร้างคุณภาพชีวิตที่ดีกว่าเดิมให้กับผู้ที่ต้องการการดูแล โดยผู้เลี้ยงจะได้รับการฝึกอบรมและสนับสนุนในการดูแลผู้ป่วยหรือผู้สูงอายุ ให้มีความสะดวกสบาย ปลอดภัย และมีคุณภาพชีวิตที่ดีขึ้น การเป็นผู้เลี้ยงไม่ได้เป็นเพียงแค่การทำงาน แต่ยังเป็นการทำหน้าที่สำคัญในการส่งเสริมการใช้ชีวิตที่มีความสุขและสมบูรณ์ยิ่งขึ้นสำหรับผู้ที่พวกเขาดูแล โดยการให้การสนับสนุนทั้งด้านร่างกาย จิตใจ และอารมณ์</p>
        </div>
    </section>

    <!-- Footer Section -->
    <footer>
        <div class="footer-section">
            <div class="footer-col">
                <img src="logo.png" alt="Logo" class="footer-logo">
                <p>Care Link</p>
                <p>Lorem ipsum dolor sit amet consectetur. Ullamcorper donec justo eget a odio.</p>
            </div>
            <div class="footer-col">
                <h4>Quick Links</h4>
                <ul>
                    <li><a href="#">Home</a></li>
                    <li><a href="#">Course</a></li>
                    <li><a href="#">Blog</a></li>
                    <li><a href="#">Contact</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h4>Legal Information</h4>
                <ul>
                    <li><a href="#">Privacy Policy</a></li>
                    <li><a href="#">Terms of Service</a></li>
                    <li><a href="#">Cookie Policy</a></li>
                    <li><a href="#">Refund Policy</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h4>Follow Us</h4>
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

