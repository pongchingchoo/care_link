<?php

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>สมัครเป็นผู้ดูแล</title>
    <link rel="stylesheet" href="care_giver5.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>

<body>

    <!-- Navbar -->
    <div class="container">



        <!-- Form Section -->
        <div class="form-section">

            <h1>สมัครเป็นผู้ดูแล</h1>
            <div class="line"></div>






        <div class="avatar">
            <img id="profileImage" src="get_profile_image_parent.png" alt="Avatar" style="width: 100px; height: 100px;">
            <input type="file" id="img" accept="image/*" style="display: none;">
            <span class="edit-icon" onclick="document.getElementById('img').click();">&#9998;</span>
        </div>



        <div class="section">
        <div class="steps">
        <div class="active"><span><i class="fa-solid fa-check"></i></span> ประวัติส่วนตัว</div>
                    <div class="active"><span><i class="fa-solid fa-check"></i></span> ยืนยันตัวตน+ผลงาน</div>
                    <div class="active"><span>3</span> รอการตรวจสอบ</div>
        </div>
        <div class="line2"></div>
            <div class="form-content">


            <div class="text-head" style="text-align: center;">
            <i class="fa-solid fa-envelope" style="color: #3AA18F; font-size: 100px;"></i>
            <h1 style="font-size: 40px; color: #3AA18F;">กำลังตรวจสอบ</h1>
            <h2 style="font-size: 20px; color: #3AA18F;" >หากท่านได้รับการคัดเลือกสัมภาษณ์จะติดต่อไปทางอีเมลของท่าน</h2>
            <button onclick="downloadFile()" style="font-size: 20px; color: #3AA18F;">ดาวน์โหลด</button>

<script>
    function downloadFile() {
        const link = document.createElement("a");
        link.href = "http://localhost/care_link/sample.pdf"; // ไฟล์อยู่ในโฟลเดอร์ "files"
        link.download = "เอกสารสัญญาจ้าง.pdf"; // ตั้งชื่อไฟล์เมื่อดาวน์โหลด
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
</script>
            </div>
    <br><br>










        <div class="submit-button">
    <button type="submit" onclick="window.location.href='LOG-IN.php'">กลับไปหน้า LOGIN</button>
        </div>



            </div>
        </div>
    </div>
</body>

</html>