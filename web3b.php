<?php
session_start();
if (!isset($_SESSION['parent_id'])) {
    header("Location: login.php");
    exit();
}

// กำหนดค่า parent_id จาก session
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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // รับค่าจากแบบสอบถามและคูณด้วย 5 ตามที่กำหนด
    $PQ1  = 12 * intval($_POST['PQ1']);
    $PQ2  = 6 * intval($_POST['PQ2']);
    $PQ3  = 6 * intval($_POST['PQ3']);
    $PQ4  = 3 * intval($_POST['PQ4']);
    $PQ5  = 8 * intval($_POST['PQ5']);
    $PQ6  = 10 * intval($_POST['PQ6']);
    $PQ7  = 12 * intval($_POST['PQ7']);
    $PQ8  = 8 * intval($_POST['PQ8']);
    $PQ9  = 10 * intval($_POST['PQ9']);
    $PQ10 = 2 * intval($_POST['PQ10']);
    $PQ11 = 6 * intval($_POST['PQ11']);
    $PQ12 = 3 * intval($_POST['PQ12']);
    $PQ13 = 4 * intval($_POST['PQ13']);
    $PQ14 = 4 * intval($_POST['PQ14']);
    $PQ15 = 6 * intval($_POST['PQ15']);
    $PQ16 = 4 * intval($_POST['PQ16']);
    $PQ17 = 4 * intval($_POST['PQ17']);
    
    // คำนวณผลรวมของคะแนน (P_score)
    $P_score = $PQ1 + $PQ2 + $PQ3 + $PQ4 + $PQ5 + $PQ6 + $PQ7 + $PQ8 + $PQ9 + $PQ10 + $PQ11 + $PQ12 + $PQ13 + $PQ14 + $PQ15 + $PQ16 + $PQ17;
    
    // ตรวจสอบว่ามีข้อมูลของ parent_id นี้อยู่แล้วหรือไม่
$check_sql = "SELECT * FROM parent_questionnaire_result WHERE parent_id = '$parent_id'";
$result = $conn->query($check_sql);

if ($result) { // ตรวจสอบว่าคำสั่ง SQL ถูกต้อง
    if ($result->num_rows > 0) {
        // ถ้ามีข้อมูลอยู่แล้วให้ทำการอัปเดต
        $update_sql = "UPDATE parent_questionnaire_result SET 
                        PQ1 = '$PQ1', PQ2 = '$PQ2', PQ3 = '$PQ3', PQ4 = '$PQ4', PQ5 = '$PQ5', 
                        PQ6 = '$PQ6', PQ7 = '$PQ7', PQ8 = '$PQ8', PQ9 = '$PQ9', PQ10 = '$PQ10', 
                        PQ11 = '$PQ11', PQ12 = '$PQ12', PQ13 = '$PQ13', PQ14 = '$PQ14', 
                        PQ15 = '$PQ15', PQ16 = '$PQ16', PQ17 = '$PQ17', P_score = '$P_score' 
                        WHERE parent_id = '$parent_id'";

        if ($conn->query($update_sql) === TRUE) {
            echo "<script>alert('อัปเดตข้อมูลเรียบร้อย'); window.location.href='web3.php';</script>";
        } else {
            echo "Error: " . $conn->error;
        }
    } else {
        // ถ้ายังไม่มีข้อมูลให้เพิ่มข้อมูลใหม่
        $insert_sql = "INSERT INTO parent_questionnaire_result 
                       (parent_id, PQ1, PQ2, PQ3, PQ4, PQ5, PQ6, PQ7, PQ8, PQ9, PQ10, PQ11, PQ12, PQ13, PQ14, PQ15, PQ16, PQ17, P_score) 
                       VALUES 
                       ('$parent_id', '$PQ1', '$PQ2', '$PQ3', '$PQ4', '$PQ5', '$PQ6', '$PQ7', '$PQ8', '$PQ9', '$PQ10', '$PQ11', '$PQ12', '$PQ13', '$PQ14', '$PQ15', '$PQ16', '$PQ17', '$P_score')";

        if ($conn->query($insert_sql) === TRUE) {
            echo "<script>alert('บันทึกข้อมูลเรียบร้อย'); window.location.href='web3.php';</script>";
        } else {
            echo "Error: " . $conn->error;
        }
    }
} else {
    echo "Error: " . $conn->error; // แจ้งเตือนหากเกิดข้อผิดพลาดในคำสั่ง SQL
}
$conn->close();
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Caregiver Search</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex justify-center items-center min-h-screen">
        <form action="" method="POST" class="bg-white p-6 rounded-lg shadow-lg max-w-2xl">
        <h2 class="text-lg font-bold mb-4">แบบทดสอบการหาผู้เด็ก</h2>
        
        <label class="block mb-2">1. เด็กสามารถพูดสื่อสารได้หรือไม่? (เช่น พูดโต้ตอบ สนทนา แสดงความต้องการผ่านคำพูด ฯลฯ)</label>
       <div class="space-y-2">
            <input type="radio" name="PQ1" value="3" class="mr-2"> ไม่พูด ใช้วิธีอื่นสื่อสาร<br>
            <input type="radio" name="PQ1" value="2" class="mr-2"> พูดได้บ้างแต่ไม่เป็นรูปประโยคไม่สมบูรณ์<br>
            <input type="radio" name="PQ1" value="1" class="mr-2"> พูดได้บ้างแต่เป็นคำ<br>
            <input type="radio" name="PQ1" value="0" class="mr-2"> พูดคุยได้ปกติ
        </div> 
        <br>

        <label class="block mb-2">2. เด็กสามารถช่วยเหลือตัวเองในการกินข้าวได้หรือไม่?</label>
        <div class="space-y-2">
            <input type="radio" name="PQ2" value="3" class="mr-2"> ต้องการความช่วยเหลือทั้งหมด<br>
            <input type="radio" name="PQ2" value="2" class="mr-2"> ต้องการความช่วยเหลือปานกลาง<br>
            <input type="radio" name="PQ2" value="1" class="mr-2"> ต้องการความช่วยเหลือเล็กน้อย<br>
            <input type="radio" name="PQ2" value="0" class="mr-2"> ได้เอง
        </div>
        <br>

        <label class="block mb-2">3. เด็กสามารถเข้าห้องน้ำได้เองหรือไม่?</label>
        <div class="space-y-2">
            <input type="radio" name="PQ3" value="1" class="mr-2"> ไม่สามารถเข้าเองได้<br>
            <input type="radio" name="PQ3" value="0" class="mr-2"> สามารถเข้าเองได้
        </div>
        <br>

        <label class="block mb-2">4. คุณมีสัตว์เลี้ยงมีขนในบ้าน (เช่น หมา , แมว) หรือไม่?</label>
        <div class="space-y-2">
            <input type="radio" name="PQ4" value="1" class="mr-2"> มี<br>
            <input type="radio" name="PQ4" value="0" class="mr-2"> ไม่มี
        </div>
        <br>

        <label class="block mb-2">5. เด็กมีอาการอาละวาดบ่อยแค่ไหน? (ตัวอย่าง: เด็กอาจอาละวาดเมื่อไม่ได้ของเล่นที่ต้องการ)?</label>
        <div class="space-y-2">
            <input type="radio" name="PQ5" value="3" class="mr-2"> เป็นประจำ<br>
            <input type="radio" name="PQ5" value="2" class="mr-2"> บ่อย<br>
            <input type="radio" name="PQ5" value="1" class="mr-2"> บางครั้ง<br>
            <input type="radio" name="PQ5" value="0" class="mr-2"> แทบไม่มี
        </div>
        <br>

        <label class="block mb-2">6. เด็กมีแนวโน้มทำร้ายตนเองหรือไม่ (เช่น กัดตัวเอง, ข่วนตัวเอง,ทุบศีรษะ, ใช้ของมีคมกรีดผิวหนัง ฯลฯ)</label>
        <div class="space-y-2">
            <input type="radio" name="PQ6" value="3" class="mr-2"> เป็นประจำ (เกิดขึ้นทุกวันหรือแทบทุกวัน)<br>
            <input type="radio" name="PQ6" value="2" class="mr-2"> บ่อย (เกิดขึ้นหลายครั้งต่อสัปดาห์ และต้องได้รับการดูแลใกล้ชิด)<br>
            <input type="radio" name="PQ6" value="1" class="mr-2"> บางครั้ง (เกิดขึ้นเป็นครั้งคราว เช่น เมื่อรู้สึกเครียดหรือหงุดหงิด)<br>
            <input type="radio" name="PQ6" value="0" class="mr-2"> ไม่มี (เด็กไม่มีพฤติกรรมทำร้ายตนเองเลย)
        </div>
        <br>

        <label class="block mb-2">7. เด็กสามารถพูดสื่อสารได้หรือไม่? (เช่น พูดโต้ตอบ สนทนา แสดงความต้องการผ่านคำพูด ฯลฯ)</label>
        <div class="space-y-2">
            <input type="radio" name="PQ7" value="3" class="mr-2"> ไม่พูด ใช้วิธีอื่นสื่อสาร<br>
            <input type="radio" name="PQ7" value="2" class="mr-2"> พูดได้บ้างแต่ไม่เป็นรูปประโยคไม่สมบูรณ์<br>
            <input type="radio" name="PQ7" value="1" class="mr-2"> พูดได้บ้างแต่เป็นคำ<br>
            <input type="radio" name="PQ7" value="0" class="mr-2"> พูดคุยได้ปกติ
        </div>
        <br>

        <label class="block mb-2">8. เด็กสามารถเข้าใจและปฏิบัติตามคำสั่งง่าย ๆ ได้หรือไม่? (เช่น “หยิบของมาให้หน่อย”, “นั่งลง”, “ยกมือขึ้น” ฯลฯ)</label>
        <div class="space-y-2">
            <input type="radio" name="PQ8" value="3" class="mr-2"> ไม่เข้าใจคำสั่งเลย (ไม่สามารถเข้าใจหรือทำตามคำสั่งได้เลย)<br>
            <input type="radio" name="PQ8" value="2" class="mr-2"> เข้าใจเพียงบางคำสั่งง่าย ๆ และมักต้องมีการช่วยเหลือ<br>
            <input type="radio" name="PQ8" value="1" class="mr-2"> เข้าใจบางคำสั่ง แต่ต้องมีการทวนหรือชี้นำเพิ่มเติม <br>
            <input type="radio" name="PQ8" value="0" class="mr-2"> สามารถเข้าใจและทำตามคำสั่งง่าย ๆ ได้อย่างถูกต้องและสม่ำเสมอ
        </div>
        <br>

        <label class="block mb-2">9. เด็กสามารถอยู่ร่วมกับเด็กคนอื่นได้หรือไม่? (เช่น เล่นหรือทำกิจกรรมร่วมกับเด็กคนอื่น แบ่งปันของเล่น ฯลฯ)</label>
        <div class="space-y-2">
            <input type="radio" name="PQ9" value="3" class="mr-2">  มีปัญหากับการอยู่ร่วมกับผู้อื่น (มักแยกตัว ไม่สนใจผู้อื่น หรือมีพฤติกรรมก้าวร้าว/ต่อต้านเมื่ออยู่กับเด็กคนอื่น)<br>
            <input type="radio" name="PQ9" value="2" class="mr-2"> ได้บ้าง (สามารถอยู่ร่วมกับเด็กคนอื่นได้ แต่มีปัญหาบ่อย เช่น แย่งของ ไม่ค่อยโต้ตอบ)<br>
            <input type="radio" name="PQ9" value="1" class="mr-2"> ได้ดี (สามารถอยู่ร่วมกับเด็กคนอื่น แต่บางครั้งอาจมีปัญหาเล็กน้อย)<br>
            <input type="radio" name="PQ9" value="0" class="mr-2"> ได้ดีมาก (สามารถเล่นและทำกิจกรรมร่วมคนอื่นอย่างราบรื่น)
        </div>
        <br>

        <label class="block mb-2">10.เด็กมีปัญหาด้านการเสริมพัฒนากา (เช่น การพัฒนาทักษะทางอารมณ์หรือสังคม) หรือไม่?</label>
        <div class="space-y-2">
            <input type="radio" name="PQ10" value="1" class="mr-2"> มีปัญหา<br>
            <input type="radio" name="PQ10" value="0" class="mr-2"> ไม่มีปัญหา
        </div>
        <br>

        <label class="block mb-2">11. คุณต้องการให้ผู้ดูแลพาเด็กออกไปทำกิจกรรมที่ส่งเสริมการเรียนรู้และพัฒนาการของเด็กได้หรือไม่?</label>
        <div class="space-y-2">

            <input type="radio" name="PQ11" value="1" class="mr-2"> ต้องการ<br>
            <input type="radio" name="PQ11" value="0" class="mr-2"> ไม่ต้องการ
        </div>
        <br>

        <label class="block mb-2">12. คุณต้องการให้ผู้ดูแลสามารถทำความสะอาดเล็กๆ น้อยๆ เช่น ปัด กวาด เช็ด ถู เพื่อช่วยดูแลความเรียบร้อยของบ้านได้หรือไม่?</label>
        <div class="space-y-2">
            <input type="radio" name="PQ12" value="1" class="mr-2"> ต้องการ<br>
            <input type="radio" name="PQ12" value="0" class="mr-2"> ไม่ต้องการ
        </div>
        <br>

        <label class="block mb-2">13. คุณต้องการให้ผู้ดูแลสามารถทำอาหารให้กับเด็กได้หรือไม่?</label>
        <div class="space-y-2">
            <input type="radio" name="PQ13" value="1" class="mr-2"> ได้<br>
            <input type="radio" name="PQ13" value="0" class="mr-2"> ไม่ได้
        </div>
        <br>

        <label class="block mb-2">14. คุณต้องการให้ผู้ดูแลช่วยเหลือในเรื่องทั่วไปของเด็ก (เช่น การแต่งตัว การทำการบ้าน ฯลฯ) ?</label>
        <div class="space-y-2">
            <input type="radio" name="PQ14" value="1" class="mr-2"> ต้องการ<br>
            <input type="radio" name="PQ14" value="0" class="mr-2"> ไม่ต้องการ
        </div>
        <br>

        <label class="block mb-2">15. .คุณต้องการให้ผู้ดูแลมีทักษะการปฐมพยาบาลเบื้องต้นเพื่อดูแลเด็กในกรณีฉุกเฉินได้หรือไม่??</label>
        <div class="space-y-2">
            <input type="radio" name="PQ15" value="1" class="mr-2"> ต้องการ<br>
            <input type="radio" name="PQ15" value="0" class="mr-2"> ไม่ต้องการ
        </div>
        <br>

        <label class="block mb-2">16. คุณต้องการให้ผู้ดูแลไม่ดื่มแอลกอฮอล์หรือไม่??</label>
        <div class="space-y-2">
            <input type="radio" name="PQ16" value="1" class="mr-2"> ต้องการ<br>
            <input type="radio" name="PQ16" value="0" class="mr-2"> ไม่ต้องการ
        </div>
        <br>

        <label class="block mb-2">17. คุณต้องการให้ผู้ดูแลไม่สูบหรี่หรือไม่?</label>
        <div class="space-y-2">
            <input type="radio" name="PQ17" value="1" class="mr-2"> ต้องการ<br>
            <input type="radio" name="PQ17" value="0" class="mr-2"> ไม่ต้องการ
        </div>
        <br>

        
        <button type="submit" class="w-full bg-green-500 text-white p-2 rounded">ส่ง</button>
    </form>









</body>
</html>




