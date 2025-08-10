<?php
session_start(); 
ob_start(); 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // ตรวจสอบว่ามีการป้อนรหัสผ่านทั้งสองช่องหรือไม่
    if (!isset($_POST['password']) || !isset($_POST['confirm_password'])) {
        echo "กรุณากรอกรหัสผ่านและยืนยันรหัสผ่าน";
        exit();
    }

    // ตรวจสอบว่ารหัสผ่านและการยืนยันตรงกันหรือไม่
    if ($_POST['password'] !== $_POST['confirm_password']) {
        echo "รหัสผ่านและการยืนยันรหัสผ่านไม่ตรงกัน!";
        exit();
    }

    // บันทึกข้อมูลลงในตัวแปรเซสชัน
    $_SESSION['caregiver_data1'] = [
        'first_name'    => $_POST['first_name'] ?? '',
        'last_name'     => $_POST['last_name'] ?? '',
        'gender'        => $_POST['gender'] ?? '',
        'email'         => $_POST['email'] ?? '',
        'birthdate'     => $_POST['birthdate'] ?? '', // เพิ่มวันเกิด
        'age'           => $_POST['age'] ?? '',
        'sub_district'  => $_POST['sub_district'] ?? '',
        'district'      => $_POST['district'] ?? '',
        'province'      => $_POST['province'] ?? '',
        'working_days'  => isset($_POST['working_days']) ? implode(", ", (array) $_POST['working_days']) : '',
        'working_hours' => $_POST['working_hours'] ?? '',
        'bio'           => $_POST['bio'] ?? '',
        'status'        => $_POST['status'] ?? null,
        'type'          => $_POST['type'] ?? '',
        'phone'         => $_POST['phone'] ?? '',
        'price'         => $_POST['price'] ?? '',
        'password'      => password_hash($_POST['password'], PASSWORD_BCRYPT) // เข้ารหัสรหัสผ่าน
    ];

    session_write_close(); 
    header("Location: care_giver4.php"); // ไปยังหน้าถัดไป
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>สมัครเป็นผู้ดูแล</title>
    <link rel="stylesheet" href="care_giver.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>

<body>

    <!-- Navbar -->
    <div class="container">



        <!-- Form Section -->
        <div class="form-section">

            <h1>สมัครเป็นผู้ดูแล</h1>
            <div class="line"></div>
            <!-- <div class="avatar">
            <img id="profileImage" src="default-avatar.png" alt="Avatar">
            <input type="file" id="imageUpload" accept="image/*" style="display: none;">
            <span class="edit-icon" onclick="document.getElementById('imageUpload').click();">&#9998;</span>
        </div> -->





        <div class="avatar">
            <img id="profileImage" src="get_profile_image_parent.png" alt="avatar" style="width: 100px; height: 100px;">
            <input type="file" id="img" accept="image/*" style="display: none;">
            <span class="edit-icon" onclick="document.getElementById('img').click();">&#9998;</span>
        </div>



        <div class="section">
        <div class="steps">
            <div class="active"><span>1</span> ประวัติส่วนตัว</div>
            <div><span>2</span> ยืนยันตัวตน+ผลงาน</div>
            <div><span>3</span> รอการตรวจสอบ</div>
        </div>
        <div class="line2"></div>
            <div class="form-content">







                <form   method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="firstName">ชื่อ</label>
                        <input type="text" id="firstName" name="first_name" required placeholder="กรอกชื่อ">
                    </div>

                    <div class="form-group">
                        <label for="lastName">นามสกุล</label>
                        <input type="text" id="lastName" name="last_name" required placeholder="กรอกนามสกุล">
                    </div>

                    <div class="form-group">
                        <label for="gender">เพศ</label>
                        <select id="gender" name="gender" required>
                            <option value="ชาย">ชาย</option>
                            <option value="หญิง">หญิง</option>
                            <option value="other">อื่นๆ</option>
                        </select>
                    </div>

                    <div class="form-group">
                    <label for="birthdate">วัน/เดือน/ปีเกิด:</label>
                    <input type="date" id="birthdate" name="birthdate" required>
                    </div>

                    <div class="form-group">
                        <label for="province">จังหวัด</label>
                        <select id="province" name="province">
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
                        <label for="phone">เบอร์โทร</label>
                        <input type="text" id="phone" name="phone" required placeholder="088-xxx-xxxx">
                    </div>

                    <div class="form-group">
                        <label for="type">ประเภทของผู้ดูแล</label>
                        <select id="type" name="type" required>
                            <option value="" disabled selected>-- เลือกประเภท --</option>
                            <option value="เด็กปกติ">เด็กปกติ</option>
                            <option value="เด็กออทิสติก">เด็กออทิสติก</option>
                            <!-- <option value="Normal">เด็กปกติ</option>
                            <option value="Autism">เด็กออทิสติก</option> -->
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="bio">เกี่ยวกับฉัน <button type="button" onclick="togglePopup()">โชว์ตัวอย่าง</button></label>
                        <textarea id="bio" name="bio" contenteditable="true" class="editable-input placeholder" required>อธิบายตนเองแบบคร่าวๆ</textarea>
                    </div>
                    <style>
.editable-input {
    width: 100%;
    min-height: 100px;
    border: 1px solid #ccc;
    padding: 8px;
    border-radius: 4px;
    white-space: pre-wrap;
    word-break: break-word;
}
.placeholder {
    color: gray;
}
</style>
<script>
document.addEventListener("DOMContentLoaded", function() {
    const bioDiv = document.getElementById("bio");
    const placeholderText = "อธิบายตนเองแบบคร่าวๆ";

    // ตั้งค่าเริ่มต้น
    if (bioDiv.innerHTML.trim() === "") {
        bioDiv.innerHTML = placeholderText;
        bioDiv.classList.add("placeholder");
    }

    bioDiv.addEventListener("focus", function() {
        if (bioDiv.innerHTML === placeholderText) {
            bioDiv.innerHTML = "";
            bioDiv.classList.remove("placeholder");
        }
    });

    bioDiv.addEventListener("blur", function() {
        if (bioDiv.innerHTML.trim() === "") {
            bioDiv.innerHTML = placeholderText;
            bioDiv.classList.add("placeholder");
        }
    });

    bioDiv.addEventListener("input", function() {
        bioDiv.setAttribute("data-value", bioDiv.innerHTML);
    });
});
</script>

                    <div id="popup" class="popup">
    <div class="popup-content">
        <span class="close" onclick="togglePopup()">&times;</span>
        <p id="sample-text"></p>
    </div>
</div>

<style>
.popup {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    justify-content: center;
    align-items: center;
}
.popup-content {
    background: white;
    padding: 20px;
    border-radius: 10px;
    text-align: left;
    width: 50%;
    line-height: 1.6;
}
.close {
    float: right;
    font-size: 24px;
    cursor: pointer;
}
</style>



<div class="form-group">
    <label for="working_days">วันที่ทำงาน</label>
    <div id="working_days" class="working-days-group">
        <button type="button" class="working-day" data-value="Monday">จันทร์</button>
        <button type="button" class="working-day" data-value="Tuesday">อังคาร</button>
        <button type="button" class="working-day" data-value="Wednesday">พุธ</button>
        <button type="button" class="working-day" data-value="Thursday">พฤหัสบดี</button>
        <button type="button" class="working-day" data-value="Friday">ศุกร์</button>
        <button type="button" class="working-day" data-value="Saturday">เสาร์</button>
        <button type="button" class="working-day" data-value="Sunday">อาทิตย์</button>
    </div>
    <input type="hidden" id="selected_working_days" name="working_days">
</div>

<style>
.working-days-group {
    display: flex;
    flex-wrap: wrap;
    gap: 5px;
}
.working-day {
    padding: 8px 12px;
    border: 1px solid #ccc;
    border-radius: 4px;
    background-color: white;
    cursor: pointer;
}
.working-day.selected {
    background-color: #007bff;
    color: white;
    border-color: #007bff;
}
</style>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const buttons = document.querySelectorAll(".working-day");
    const hiddenInput = document.getElementById("selected_working_days");

    buttons.forEach(button => {
        button.addEventListener("click", function() {
            this.classList.toggle("selected");
            updateSelectedDays();
        });
    });

    function updateSelectedDays() {
        const selectedValues = Array.from(document.querySelectorAll(".working-day.selected"))
            .map(btn => btn.getAttribute("data-value"));
        hiddenInput.value = selectedValues.join(",");
    }
});
</script>


                    <div class="form-group">
                        <label for="working_hours_start">เวลาเริ่มงาน</label>
                        <input type="time" id="working_hours_start" required>
                    </div>

                    <div class="form-group">
                        <label for="working_hours">เวลาทำงาน (อัตโนมัติ)</label>
                        <input type="text" id="working_hours" name="working_hours" readonly required>
                    </div>
                    
                    <script>
// การคำนวณเวลาเริ่มต้นและสิ้นสุด
document.getElementById('working_hours_start').addEventListener('change', function () {
    let startTime = this.value;
    if (startTime) {
        let [hours, minutes] = startTime.split(":").map(Number);
        hours = (hours + 8) % 24; // บวก 8 ชั่วโมง และจัดการกรณีข้ามเที่ยงคืน
        let endTime = (hours < 10 ? "0" : "") + hours + ":" + (minutes < 10 ? "0" : "") + minutes;
        document.getElementById('working_hours').value = startTime + " - " + endTime;
    }
});



// การจัดการการอัปโหลดรูปภาพ
document.getElementById("img").addEventListener("change", function(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const imgElement = document.getElementById("profileImage");
            imgElement.src = e.target.result;
            imgElement.onload = function() {
                const canvas = document.createElement("canvas");
                const ctx = canvas.getContext("2d");

                const size = Math.min(imgElement.naturalWidth, imgElement.naturalHeight);
                canvas.width = 120; 
                canvas.height = 120; 

                ctx.drawImage(
                    imgElement,
                    (imgElement.naturalWidth - size) / 2, 
                    (imgElement.naturalHeight - size) / 2,
                    size, size,
                    0, 0,
                    canvas.width, canvas.height
                );

                // ตั้งค่า src ใหม่จาก canvas
                imgElement.setAttribute("src", canvas.toDataURL("image/png"));
            };
        };
        reader.readAsDataURL(file);
    }
});

// เพิ่ม event listener สำหรับการเปลี่ยนแปลงใน "district"
document.getElementById("district").addEventListener("change", updateSubdistricts);


function togglePopup() {
    var popup = document.getElementById("popup");
    var sampleText = "สวัสดีค่ะ\n\nฉันชื่อหนึ่ง เป็นคุณแม่ กำลังมองหางานพี่เลี้ยงเด็กและแม่บ้านแบบอยู่ประจำเต็มเวลา\n\nฉันมีประสบการณ์มากกว่า 10 ปี สามารถทำความสะอาด ซักผ้า รีดผ้า ล้างจาน และทำอาหารให้เด็ก ๆ และครอบครัวได้\n\nฉันมีประสบการณ์ดูแลเด็กแรกเกิด ทารก วัยหัดเดิน และเด็กวัยเรียนได้\n\nฉันสามารถพูดภาษาอังกฤษได้บ้าง (อ่านและเขียนได้) ภาษาไทยได้ดี (อ่านได้) และภาษาพม่าเป็นภาษาแม่\n\nฉันว่ายน้ำกับเด็ก ๆ ได้ และโอเคกับแมว\n\nสามารถทำงานได้ตั้งแต่วันจันทร์ถึงวันเสาร์\n\nกรุณาติดต่อฉันผ่าน FamBear ขอบคุณค่ะ";
    
    if (popup.style.display === "none" || popup.style.display === "") {
        document.getElementById("sample-text").innerText = sampleText;
        popup.style.display = "flex";
    } else {
        popup.style.display = "none";
    }
}
</script>

                    <div class="form-group">
    <label for="price"> ราคา <button type="button" onclick="pop()">แนะนำการตั้งราคา</button></label>
    <input type="number" id="price" name="price" required placeholder="กรอกราคา" step="0.01" min="0">
    
</div>

<!-- Popup -->
<div id="popup" class="popup">
    <div class="popup-content">
        <span class="close" onclick="pop()">&times;</span>
        <p id="sample-text"></p>
    </div>
</div>

<!-- CSS -->
<style>
.popup {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    justify-content: center;
    align-items: center;
}

.popup-content {
    background: white;
    padding: 20px;
    border-radius: 10px;
    text-align: left;
    width: 50%;
    line-height: 1.6;
}

.close {
    float: right;
    font-size: 24px;
    cursor: pointer;
    color: red;
}

.close:hover {
    color: darkred;
}
</style>

<!-- JavaScript -->
<script>
function pop() {
    var popup = document.getElementById("popup");
    var sampleText = 
        "📌 **แนะนำการตั้งราคา** 💰\n\n" +
        "✅ **ศึกษาตลาด**: เช็คราคาของผู้ดูแลที่มีประสบการณ์และความสามารถใกล้เคียงกัน\n" +
        "✅ **คิดจากชั่วโมงทำงาน**: เช่น 100 - 300 บาท/ชั่วโมง หรือ 10,000 - 25,000 บาท/เดือน\n" +
        "✅ **รวมค่าใช้จ่ายเพิ่มเติม**: เช่น ค่าเดินทาง อาหาร ที่พัก (ถ้ามี)\n" +
        "✅ **กำหนดช่วงราคา**: เพื่อให้ลูกค้าสามารถต่อรองได้ เช่น 'เริ่มต้นที่ 12,000 บาท/เดือน'\n" +
        "✅ **โปรโมชัน/ส่วนลด**: สำหรับลูกค้าประจำหรืองานระยะยาว\n\n" +
        "💡 **Tip**: ควรตั้งราคาที่คุ้มค่าและสมเหตุสมผล เพื่อให้ได้งานที่เหมาะสม 🎯";

    if (popup.style.display === "none" || popup.style.display === "") {
        document.getElementById("sample-text").innerText = sampleText;
        popup.style.display = "flex";
    } else {
        popup.style.display = "none";
    }
}
</script>


                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" placeholder="อีเมลของท่าน" name="email" required>
                    </div>
                
                    <div class="form-group">
                        <label for="password">รหัสผ่าน</label>
                        <input type="password" id="password" placeholder="กรุณากรอก password" name="password" required>
                    </div>

                    <div class="form-group">
            <label for="confirm_password">ยืนยันรหัสผ่าน</label>
            <input type="password" id="confirm_password" placeholder="กรุณายืนยัน password" name="confirm_password" required>
          </div>

                    <div class="submit-button">
    <button type="submit">ต่อไป</button>
</div>
                </form>



            </div>
        </div>
    </div>

    <script src="care_giver.js"></script>










<!-- โค้ดดักการกรอก -->
    <script>
document.addEventListener("DOMContentLoaded", function () {
    const form = document.querySelector("form");

    form.addEventListener("submit", function (e) {
        const firstName = document.getElementById("firstName").value.trim();
        const lastName = document.getElementById("lastName").value.trim();
        const gender = document.getElementById("gender").value;
        const birthdate = document.getElementById("birthdate").value;
        const province = document.getElementById("province").value;
        const district = document.getElementById("district").value;
        const subdistrict = document.getElementById("subdistrict").value;
        const phone = document.getElementById("phone").value.trim();
        const type = document.getElementById("type").value;
        const bio = document.getElementById("bio").value.trim();
        const workingDays = document.getElementById("selected_working_days").value;
        const workingHours = document.getElementById("working_hours").value.trim();
        const price = parseFloat(document.getElementById("price").value);
        const email = document.getElementById("email").value.trim();
        const password = document.getElementById("password").value;
        const confirmPassword = document.getElementById("confirm_password").value;

        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        const phonePattern = /^0[689]\d{8}$/; // เบอร์มือถือไทยพื้นฐาน
        const today = new Date();
        const birth = new Date(birthdate);
        const age = today.getFullYear() - birth.getFullYear();

        // เริ่มตรวจสอบ
        if (!firstName.match(/^[ก-๏a-zA-Z\s]+$/)) {
            alert("กรุณากรอกชื่อให้ถูกต้อง (เฉพาะตัวอักษร)");
            e.preventDefault();
            return;
        }

        if (!lastName.match(/^[ก-๏a-zA-Z\s]+$/)) {
            alert("กรุณากรอกนามสกุลให้ถูกต้อง (เฉพาะตัวอักษร)");
            e.preventDefault();
            return;
        }

        if (!gender) {
            alert("กรุณาเลือกเพศ");
            e.preventDefault();
            return;
        }

        if (!birthdate || age < 25 || age > 45) {
    alert("กรุณากรอกวันเกิดให้ถูกต้อง และอายุต้องอยู่ระหว่าง 25 ถึง 45 ปี");
    e.preventDefault();
    return;
}


        if (!province || !district || !subdistrict) {
            alert("กรุณากรอกจังหวัด เขต และแขวงให้ครบ");
            e.preventDefault();
            return;
        }

        if (!phonePattern.test(phone)) {
            alert("กรุณากรอกเบอร์โทรให้ถูกต้อง เช่น 088xxxxxxx");
            e.preventDefault();
            return;
        }

        if (!type) {
            alert("กรุณาเลือกประเภทของผู้ดูแล");
            e.preventDefault();
            return;
        }

        if (bio.length < 10 || bio === "อธิบายตนเองแบบคร่าวๆ") {
            alert("กรุณากรอกเกี่ยวกับตัวคุณอย่างเหมาะสม");
            e.preventDefault();
            return;
        }

        if (!workingDays) {
            alert("กรุณาเลือกวันที่ทำงานอย่างน้อย 1 วัน");
            e.preventDefault();
            return;
        }

        if (!workingHours) {
            alert("กรุณากรอกเวลาทำงาน");
            e.preventDefault();
            return;
        }

        if (isNaN(price) || price <= 0) {
            alert("กรุณากรอกราคาให้ถูกต้อง และต้องมากกว่า 0");
            e.preventDefault();
            return;
        }

        if (!emailPattern.test(email)) {
            alert("กรุณากรอกอีเมลให้ถูกต้อง เช่น name@email.com");
            e.preventDefault();
            return;
        }

        if (password.length < 6) {
            alert("รหัสผ่านต้องมีอย่างน้อย 6 ตัวอักษร");
            e.preventDefault();
            return;
        }

        if (password !== confirmPassword) {
            alert("รหัสผ่านและการยืนยันรหัสผ่านไม่ตรงกัน");
            e.preventDefault();
            return;
        }
    });
});
</script>
</body>

</html>