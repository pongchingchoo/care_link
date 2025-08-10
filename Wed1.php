<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>สมัครเป็นผู้ใช้งาน</title>
    <link rel="stylesheet" href="wed1.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
<style>
body {
    margin: 0;
    font-family: 'Arial', sans-serif;
    background-color: #ffffff;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}

.submit-btn {
    background-color: #4CAF50; /* สีเขียว */
    color: white;
    padding: 10px 20px;
    font-size: 16px;
    border: none;
    cursor: pointer;
    border-radius: 5px;
}

.submit-btn:hover {
    background-color: #45a049;
}



.back {
    position: relative;
    width: 100%;
    height: 360vh; /* ครอบคลุมความสูงของหน้าจอ */
    background-image: url('back2.png'); /* ใส่ภาพพื้นหลัง */
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
  }


.container {
    margin-top: 450px;
    position: relative;
    width: 80%;
    max-width: 800px;
    background-color: #ffffff;
    border-radius: 10px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    padding: 40px;

    position: absolute;
      top: 50%; /* จัดตำแหน่งให้อยู่ตรงกลางแนวตั้ง */
      left: 50%; /* จัดตำแหน่งให้อยู่ตรงกลางแนวนอน */
      transform: translate(-50%, -50%); /* ปรับให้อยู่กึ่งกลางพอดี */
}

.form-card {
    width: 100%;
}

.title {
    font-size: 2rem;
    margin: 0;
    margin-bottom: 10px;
}

.highlight {
    color: #ff9800;
}

.divider {
    border: none;
    border-top: 3px solid #ddd;
    margin-bottom: 20px;
    margin-left: 0;
    width: 600px;
    
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    font-size: 1rem;
    margin-bottom: 5px;
    color: #333;
    
}

.form-group input {
    width: 50%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 5px;
    font-size: 1rem;
}

.form-group select {
    width: 52.5%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 5px;
    font-size: 1rem;
}

.submit-btn {
    background-color: #2ac530;
    color: #fff;
    border: none;
    padding: 10px 35px;
    font-size: 1rem;
    border-radius: 5px;
    cursor: pointer;
    display: block;
    margin: 0 auto;
    position: relative;
    left: 300px;
    border-radius: 20px;
}

.submit-btn:hover {
    background-color: #45a049;
}

/* ขอโทษผมหาไอคอนไม่เจออันนี้ เป็น โปรไฟล์ */
.profile-icon {
    position: absolute;
    top: 30px;
    right: 20px;
}

.icon-circle {
    width: 100px;
    height: 100px;
    background-color: #ddd;
    border-radius: 50%;
    display: flex;
    justify-content: center;
    align-items: center;
    position: relative;
    right: 70px;
    bottom: 10px;
}

.profile-icon i {
    font-size: 40px;
}
#fullname{
    width: 400px;
    border-radius: 10px;
}
#email{
    width: 400px;
    border-radius: 10px;
}
#password{
    width: 400px;
    border-radius: 10px;
}


.free{
margin-top: 500px;
font-size: 500px;
}
</style>

</head>
<!-- http://localhost/care_link/Wed1.php -->
<body>
    <div class="back">
        <div class="container">
            <div class="form-card">
                <h2 onclick="location.href='LOG-IN.php'">กลับ</h2>
                <h1 class="title">
                    <span class="highlight">สมัคร</span>เป็นผู้ใช้งาน
                </h1>
                <hr class="divider">
                <form action="view_parent.php" method="POST">
    
                    <div class="form-group">
                        <label for="first-name">ชื่อ</label>
                        <input type="text" id="first-name" placeholder="ชื่อ" name="first_name" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="last-name">นามสกุล</label>
                        <input type="text" id="last-name" placeholder="นามสกุล" name="last_name" required>
                    </div>
                
                    <div class="form-group">
                        <label for="guardian_status">สถานะผู้ปกครอง</label>
                        <input type="text" id="guardian_status" placeholder="สถานะ" name="guardian_status">
                    </div>
                
                    <div class="form-group">
                        <label for="family_members">จำนวนคนในครอบครัว</label>
                        <select id="family_members" name="family_members">
                            <option value="" disabled selected>-- จำนวนคนในครอบครัว --</option>
                            <option value="2-3">2-3 คน</option>
                            <option value="4-5">4-5 คน</option>
                            <option value="5+">5 คนขึ้นไป</option>
                        </select>
                    </div>

                    <div class="form-group">
                    <label for="province">จังหวัด</label>
                    <select id="province" name="province" required>
                        <option value="กรุงเทพฯ">กรุงเทพฯ</option>
                    </select>
                    </div>

                    <div class="form-group">
    <label for="district">เขต/อำเภอ</label>
    <select id="district" name="district" onchange="updateSubdistricts()">
        <option value="" disabled selected>-- เลือกเขต/อำเภอ --</option>
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
        <option value="บางกอกใหญ่">เขตบางกอกใหญ่</option>
        <option value="ดินแดง">เขตดินแดง</option>
        <option value="บางซื่อ">เขตบางซื่อ</option>
        <option value="จตุจักร">เขตจตุจักร</option>
        <option value="บางคอแหลม">เขตบางคอแหลม</option>
        <option value="คลองเตย">เขตคลองเตย</option>
        <option value="สวนหลวง">เขตสวนหลวง</option>
        <option value="จอมทอง">เขตจอมทอง</option>
        <option value="ราชเทวี">เขตราชเทวี</option>
        <option value="วัฒนา">เขตวัฒนา</option>
        <option value="บางแค">เขตบางแค</option>
        <option value="หลักสี่">เขตหลักสี่</option>
        <option value="บางนา">เขตบางนา</option>
        <option value="ทวีวัฒนา">เขตทวีวัฒนา</option>
        <option value="ทุ่งครุ">เขตทุ่งครุ</option>
        <option value="บางบอน">เขตบางบอน</option>
    </select>
</div>

<div class="form-group">
    <label for="subdistrict">แขวง/ตำบล</label>
    <select id="subdistrict" name="sub_district">
        <option value="" disabled selected>-- เลือกแขวง/ตำบล --</option>
    </select>
</div>

                    <script>
const subdistricts = {
    "พระนคร": ["พระบรมมหาราชวัง", "วังบูรพาภิรมย์", "พระราชวัง", "บวรนิเวศ", "ศาลเจ้าพ่อเสือ", "สำราญราษฎร์", "ชนะสงคราม", "ตลาดยอด", "สนามเจ้าม้า", "บรมมหาราชวัง"],
    "ดุสิต": ["วชิรพยาบาล", "ดุสิต", "สวนจิตรลดา", "สี่แยกมหานาค"],
    "หนองจอก": ["หนองจอก", "สองคลอง", "กระทุ่มราย", "คลองสิบ"],
    "บางรัก": ["มหาพฤฒาราม", "สีลม", "สุริยวงศ์", "บางรัก"],
    "บางเขน": ["ท่าแร้ง", "อนุสาวรีย์", "รามอินทรา", "คลองถนน", "สายไหม", "ออเงิน", "ทุ่งสองห้อง"],
    "บางกะปิ": ["คลองจั่น", "หัวหมาก", "คลองกุ่ม"],
    "ปทุมวัน": ["ลุมพินี", "ปทุมวัน", "รองเมือง", "วังใหม่"],
    "ป้อมปราบศัตรูพ่าย": ["วัดเทพศิรินทร์", "วัดโสมนัส", "ป้อมปราบ", "บ้านบาตร", "คลองมหานาค"],
    "พระโขนง": ["บางจาก", "พระโขนง", "บางนา"],
    "มีนบุรี": ["มีนบุรี", "แสนแสบ", "ทรายกองดิน", "คลองสิบสอง", "บางชัน"],
    "ลาดกระบัง": ["ลาดกระบัง", "คลองสองต้นนุ่น", "คลองสามประเวศ", "คลองสิบห้า", "ทับยาว"],
    "ยานนาวา": ["ช่องนนทรี", "ทุ่งมหาเมฆ", "บางโพงพาง"],
    "สัมพันธวงศ์": ["สัมพันธวงศ์", "ตลาดน้อย"],
    "สวนหลวง": ["สวนหลวง", "หนองบอน"],
    "จอมทอง": ["บางขุนเทียน", "บางบอน", "จอมทอง"],
    "ดินแดง": ["ดินแดง", "สามเสนใน"],
    "บางกอกใหญ่": ["ศิริราช", "บางขุนนนท์", "อรุณอมรินทร์", "บางกอกใหญ่", "บางยี่เรือ", "คลองสาน", "บางพลัด"],
    "บางซื่อ": ["บางซื่อ"],
    "บางคอแหลม": ["วัดพระยาไกร", "บางคอแหลม", "บางโคล่"],
    "บางแค": ["บางแค", "บางไผ่", "หลักสอง"],
    "คลองเตย": ["คลองตัน", "พระโขนง", "คลองเตย"],
    "หลักสี่": ["ตลาดบางเขน", "ทุ่งสองห้อง"],
    "จตุจักร": ["จอมพล", "จตุจักร", "ลาดยาว", "เสนานิคม", "จันทรเกษม"],
    "พญาไท": ["พญาไท", "ถนนพญาไท", "สามเสนใน"],
    "ราชเทวี": ["ถนนเพชรบุรี", "ถนนพญาไท", "มักกะสัน"],
    "วัฒนา": ["คลองเตย", "คลองตันเหนือ"],
    "บางนา": ["บางนา", "บางแก้ว"],
    "ทวีวัฒนา": ["ศาลาธรรมสพน์", "บางด้วน", "บางกระดี่"],
    "ทุ่งครุ": ["ทุ่งครุ", "บางครุ"],
    "บางบอน": ["บางบอน", "บางขุนเทียน"]
};

function updateSubdistricts() {
    const district = document.getElementById("district").value;
    const subdistrictSelect = document.getElementById("subdistrict");

    // ล้างข้อมูลเดิมและเพิ่ม default option
    subdistrictSelect.innerHTML = '<option value="" disabled selected>-- เลือกแขวง/ตำบล --</option>';

    if (district && Array.isArray(subdistricts[district])) {
        subdistricts[district].forEach(sub => {
            const option = document.createElement("option");
            option.value = sub;
            option.textContent = sub;
            subdistrictSelect.appendChild(option);
        });
    } else {
        const option = document.createElement("option");
        option.value = "";
        option.textContent = "-- ไม่มีข้อมูล --";
        subdistrictSelect.appendChild(option);
    }
}
</script>
                
                    <div class="form-group">
                        <label for="children-count">จำนวนบุตรทั้งหมด</label>
                        <input type="text" id="children-count" placeholder="จำนวนบุตรทั้งหมด" name="total_children">
                    </div>
                
                    <!-- 🏡 ประเภทที่อยู่อาศัย -->
                    <div class="form-group">
                        <label for="housing-type">ประเภทที่อยู่อาศัย</label>
                        <select id="housing-type" name="housing_type">
                            <option value="" disabled selected>-- ประเภทที่อยู่อาศัย --</option>
                            <option value="บ้านเดี่ยว">บ้านเดี่ยว</option>
                            <option value="บ้านแฝด">บ้านแฝด</option>
                            <option value="แฟลตหรืออาพาร์ตเม้นต์">แฟลตหรืออาพาร์ตเม้นต์</option>
                            <option value="คอนโดมิเนียม">คอนโดมิเนียม</option>
                        </select>
                    </div>
                
                    <!-- 🏡 รายละเอียดที่อยู่อาศัย -->
                    <div class="form-group">
                        <label for="housing-detail">ลักษณะเพิ่มเติมที่อยู่อาศัย</label>
                        <input type="text" id="housing-detail" placeholder="เช่น มีสระน้ำ สวนให้วิ่งเล่น กว้างใหญ่" name="housing_detail">
                    </div>
                
                    <div class="form-group">
                        <label for="pets">สัตว์เลี้ยงในบ้าน</label>
                        <select id="pets" name="pets_in_home">
                            <option value="" disabled selected>-- สัตว์เลี้ยงในบ้าน --</option>
                            <option value="มี">มี</option>
                            <option value="ไม่มี">ไม่มี</option>
                        </select>
                    </div>
                
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" placeholder="อีเมลของท่าน" name="email" required>
                    </div>

                    <div class="form-group">
                        <label for="phone_number">phone number</label>
                        <input type="phone_number" id="phone_number" placeholder="กรอกเบอร์ติดต่อ" name="phone_number" required>
                    </div>
                
                    <div class="form-group">
                        <label for="password">รหัสผ่าน</label>
                        <input type="password" id="password" placeholder="กรุณากรอก password" name="password" required>
                    </div>
                    
                    <div class="form-group">
            <label for="confirm_password">ยืนยันรหัสผ่าน</label>
            <input type="password" id="confirm_password" placeholder="กรุณายืนยัน password" name="confirm_password" required>
          </div>
                    
                

                    <button type="submit" class="submit-btn" name="create">ส่ง</button>
                </form>
                
                
            </div>
            <div class="profile-icon">
                <div class="icon-circle">
                    <i class="fas fa-user"></i>
                </div>
            </div>
        </div>
    </div>







<script>
  // ฟังก์ชันตรวจสอบฟอร์มก่อน submit
  document.querySelector('form').addEventListener('submit', function(e) {
    // ดึงค่าต่างๆ
    const firstName = document.getElementById('first-name').value.trim();
    const lastName = document.getElementById('last-name').value.trim();
    const familyMembers = document.getElementById('family_members').value;
    const totalChildren = document.getElementById('children-count').value.trim();
    const phoneNumber = document.getElementById('phone_number').value.trim();
    const email = document.getElementById('email').value.trim();
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirm_password').value;

    // กำหนด regex สำหรับตรวจสอบ
    const nameRegex = /^[ก-๙a-zA-Z\s]+$/; // ตัวอักษรไทย, อังกฤษ, เว้นวรรค
    const phoneRegex = /^[0-9]{9,10}$/; // เบอร์โทร 9-10 ตัวเลข
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    // ตรวจชื่อ
    if (!nameRegex.test(firstName)) {
      alert('กรุณากรอกชื่อด้วยตัวอักษรภาษาไทย');
      e.preventDefault();
      return;
    }

    // ตรวจนามสกุล
    if (!nameRegex.test(lastName)) {
      alert('กรุณากรอกนามสกุลด้วยตัวอักษรภาษาไทย');
      e.preventDefault();
      return;
    }

    // ตรวจจำนวนคนในครอบครัว (ต้องเลือก)
    if (!familyMembers) {
      alert('กรุณาเลือกจำนวนคนในครอบครัว');
      e.preventDefault();
      return;
    }

    // ตรวจจำนวนบุตร ถ้ามีค่า ต้องเป็นตัวเลข
    if (totalChildren !== '' && isNaN(totalChildren)) {
      alert('กรุณากรอกจำนวนบุตรเป็นตัวเลข');
      e.preventDefault();
      return;
    }

    // ตรวจเบอร์โทร
    if (!phoneRegex.test(phoneNumber)) {
      alert('กรุณากรอกเบอร์โทรศัพท์ให้ถูกต้อง (9-10 ตัวเลข)');
      e.preventDefault();
      return;
    }

    // ตรวจอีเมล
    if (!emailRegex.test(email)) {
      alert('กรุณากรอกอีเมลให้ถูกต้อง');
      e.preventDefault();
      return;
    }

    // ตรวจรหัสผ่านขั้นต่ำ 6 ตัวอักษร
    if (password.length < 6) {
      alert('รหัสผ่านต้องมีอย่างน้อย 6 ตัวอักษร');
      e.preventDefault();
      return;
    }

    // ตรวจรหัสผ่านตรงกัน
    if (password !== confirmPassword) {
      alert('รหัสผ่านและยืนยันรหัสผ่านไม่ตรงกัน');
      e.preventDefault();
      return;
    }

    // ถ้าผ่านหมด จะ submit form ตามปกติ
  });
</script>


</body>

</html>