<?php
session_start(); // เปิดใช้งาน session

// เชื่อมต่อฐานข้อมูล
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "care_link";
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ตรวจสอบว่ามีการล็อกอินหรือไม่
if (!isset($_SESSION['user_id'])) {
    die("กรุณาเข้าสู่ระบบก่อน");
}
$caregiver_id = $_SESSION['user_id']; // ดึง caregiver_id จาก session

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $Q1 = $_POST['Q1'];
    $Q2 = $_POST['Q2'];
    $Q3 = $_POST['Q3'];
    $Q4 = $_POST['Q4'];
    $Q5 = $_POST['Q5'];
    $Q6 = $_POST['Q6'];
    $Q7 = $_POST['Q7'];
    $Q8 = $_POST['Q8'];
    $Q9 = $_POST['Q9'];
    $Q10 = $_POST['Q10'];
    $Q11 = $_POST['Q11'];
    $Q12 = $_POST['Q12'];
    $Q13 = $_POST['Q13'];
    $Q14 = $_POST['Q14'];
    $Q15 = $_POST['Q15'];
    $Q16 = $_POST['Q16'];
    $Q17 = $_POST['Q17'];
    
    // คะแนนข้อ (5) × คะแนนตัวเลือก (1,2,3,4)
    $Q1 = 12 * intval($Q1);
    $Q2 = 6 * intval($Q2);
    $Q3 = 6 * intval($Q3);
    $Q4 = 3 * intval($Q4);
    $Q5 = 8 * intval($Q5);
    $Q6 = 10 * intval($Q6);
    $Q7 = 12 * intval($Q7);
    $Q8 = 8 * intval($Q8);
    $Q9 = 10 * intval($Q9);
    $Q10 = 2 * intval($Q10);
    $Q11 = 6 * intval($Q11);
    $Q12 = 3 * intval($Q12);
    $Q13 = 4 * intval($Q13);
    $Q14 = 4 * intval($Q14);
    $Q15 = 6 * intval($Q15);
    $Q16 = 4 * intval($Q16);
    $Q17 = 4 * intval($Q17);
    
    // คำนวณผลรวมของคะแนน
    $score = $Q1 + $Q2 + $Q3 + $Q4 + $Q5 + $Q6 + $Q7 + $Q8 + $Q9 + $Q10 + $Q11 + $Q12 + $Q13 + $Q14 + $Q15 + $Q16 + $Q17 ;
    
    // ตรวจสอบว่ามีข้อมูลใน questionnaire_result หรือไม่
    $check_sql = "SELECT * FROM questionnaire_result WHERE caregiver_id = '$caregiver_id'";
    $result = $conn->query($check_sql);
    
    if ($result->num_rows > 0) {
        // ถ้ามีข้อมูลอยู่แล้วให้ทำการอัปเดต
        $update_sql = "UPDATE questionnaire_result SET Q1 = '$Q1', Q2 = '$Q2',Q3 = '$Q3',Q4 = '$Q4',Q5 = '$Q5',Q6 = '$Q6',Q7 = '$Q7',Q8 = '$Q8',Q9 = '$Q9',Q10 = '$Q10',Q11 = '$Q11',Q12 = '$Q12',Q13 = '$Q13',Q14 = '$Q14',Q15 = '$Q15',Q16 = '$Q16',Q17 = '$Q17',  score = '$score' WHERE caregiver_id = '$caregiver_id'";
        
        if ($conn->query($update_sql) === TRUE) {
            echo "<script>alert('อัปเดตข้อมูลเรียบร้อย'); window.location.href='caregiver_dashboard.php';</script>";
        } else {
            echo "Error: " . $conn->error;
        }
    } else {
        // ถ้ายังไม่มีข้อมูลให้เพิ่มข้อมูลใหม่
        $insert_sql = "INSERT INTO questionnaire_result (caregiver_id, Q1, Q2, Q3, Q4, Q5, Q6, Q7, Q8, Q9, Q10, Q11, Q12, Q13, Q14, Q15, Q16, Q17, score) VALUES ('$caregiver_id', '$Q1', '$Q2', '$Q3', '$Q4', '$Q5', '$Q6', '$Q7', '$Q8', '$Q9', '$Q10', '$Q11', '$Q12', '$Q13', '$Q14', '$Q15', '$Q16', '$Q17', '$score')";
        
        if ($conn->query($insert_sql) === TRUE) {
            echo "<script>alert('บันทึกข้อมูลเรียบร้อย'); window.location.href='caregiver_dashboard.php';</script>";
        } else {
            echo "Error: " . $conn->error;
        }
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แบบสอบถาม</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex justify-center items-center min-h-screen">
    <form action="" method="POST" class="bg-white p-6 rounded-lg shadow-lg max-w-2xl">
        <h2 class="text-lg font-bold mb-4">แบบทดสอบการดูแลเด็ก</h2>
        
        <label class="block mb-2">1. คุณสามารถดูแลเด็กที่ไม่พูดได้หรือไม่?</label>
       <div class="space-y-2">
            <input type="radio" name="Q1" value="3" class="mr-2"> ได้<br>
            <input type="radio" name="Q1" value="2" class="mr-2"> ได้ แต่ต้องมีแนวทางช่วยเหลือเพิ่มเติม<br>
            <input type="radio" name="Q1" value="1" class="mr-2"> ได้ในบางกรณี แต่ต้องการคำแนะนำมากขึ้น<br>
            <input type="radio" name="Q1" value="0" class="mr-2"> ไม่สามารถดูแลได้
        </div> 
        <br>

        <label class="block mb-2">2. คุณสามารถช่วยเหลือเด็กที่ต้องการช่วยเหลือในการกินอาหารได้หรือไม่?</label>
        <div class="space-y-2">
            <input type="radio" name="Q2" value="3" class="mr-2"> ได้ และเคยมีประสบการณ์มาก่อน<br>
            <input type="radio" name="Q2" value="2" class="mr-2"> ได้ แต่ต้องการแนวทางเพิ่มเติม<br>
            <input type="radio" name="Q2" value="1" class="mr-2"> ได้ในบางกรณี แต่ต้องการคำแนะนำมากขึ้น<br>
            <input type="radio" name="Q2" value="0" class="mr-2"> ไม่สามารถดูแลได้
        </div>
        <br>

        <label class="block mb-2">3. คุณสามารถช่วยเหลือเด็กที่ยังใช้ห้องน้ำเองไม่ได้หรือไม่?</label>
        <div class="space-y-2">
            <input type="radio" name="Q3" value="1" class="mr-2"> ได้<br>
            <input type="radio" name="Q3" value="0" class="mr-2"> ไม่สามารถดูแลได้
        </div>
        <br>

        <label class="block mb-2">4. คุณสามารถดูแลสัตว์เลี้ยงที่มีขนได้หรือไม่?</label>
        <div class="space-y-2">
            <input type="radio" name="Q4" value="1" class="mr-2"> ได้<br>
            <input type="radio" name="Q4" value="0" class="mr-2"> ไม่สามารถดูแลได้
        </div>
        <br>

        <label class="block mb-2">5. คุณมีประสบการณ์รับมือกับเด็กที่อารมณ์แปรปรวนหรืออาละวาดรุนแรงหรือไม่?</label>
        <div class="space-y-2">
            <input type="radio" name="Q5" value="3" class="mr-2"> มี และสามารถรับมือได้ดี<br>
            <input type="radio" name="Q5" value="2" class="mr-2"> ได้ แต่ต้องมีแนวทางช่วยเหลือเพิ่มเติม<br>
            <input type="radio" name="Q5" value="1" class="mr-2"> ได้ในบางกรณี แต่ต้องการคำแนะนำมากขึ้น<br>
            <input type="radio" name="Q5" value="0" class="mr-2"> ไม่สามารถดูแลได้
        </div>
        <br>

        <label class="block mb-2">6. คุณสามารถดูแลเด็กที่มีแนวโน้มทำร้ายตัวเองได้หรือไม่?</label>
        <div class="space-y-2">
            <input type="radio" name="Q6" value="3" class="mr-2"> ได้ และเคยมีประสบการณ์มาก่อน<br>
            <input type="radio" name="Q6" value="2" class="mr-2"> ได้ แต่ต้องการแนวทางช่วยเหลือเพิ่มเติม<br>
            <input type="radio" name="Q6" value="1" class="mr-2"> ได้ในบางกรณี แต่ต้องการคำแนะนำมากขึ้น<br>
            <input type="radio" name="Q6" value="0" class="mr-2"> ไม่สามารถดูแลได้
        </div>
        <br>

        <label class="block mb-2">7. คุณสามารถดูแลเด็กที่ไม่พูดหรือสื่อสารด้วยภาษามือได้หรือไม่?</label>
        <div class="space-y-2">
            <input type="radio" name="Q7" value="3" class="mr-2"> มี และสามารถรับมือได้ดี<br>
            <input type="radio" name="Q7" value="2" class="mr-2"> ได้ แต่ต้องมีแนวทางช่วยเหลือเพิ่มเติม<br>
            <input type="radio" name="Q7" value="1" class="mr-2"> ได้ในบางกรณี แต่ต้องการคำแนะนำมากขึ้น<br>
            <input type="radio" name="Q7" value="0" class="mr-2"> ไม่สามารถดูแลได้
        </div>
        <br>

        <label class="block mb-2">8. คุณสามารถช่วยพัฒนาและฝึกการสื่อสารให้เด็กที่มีปัญหาด้านการเข้าใจคำสั่งได้หรือไม่?</label>
        <div class="space-y-2">
            <input type="radio" name="Q8" value="3" class="mr-2"> ได้ และเคยมีประสบการณ์มาก่อน<br>
            <input type="radio" name="Q8" value="2" class="mr-2"> ได้ แต่ต้องการแนวทางช่วยเหลือเพิ่มเติม<br>
            <input type="radio" name="Q8" value="1" class="mr-2"> ได้ในบางกรณี แต่ต้องการคำแนะนำมากขึ้น<br>
            <input type="radio" name="Q8" value="0" class="mr-2"> ไม่สามารถดูแลได้
        </div>
        <br>

        <label class="block mb-2">9. คุณสามารถดูแลเด็กที่มีปัญหาในการเข้าสังคมได้หรือไม่?</label>
        <div class="space-y-2">
            <input type="radio" name="Q9" value="3" class="mr-2"> ได้ และมีวิธีช่วยส่งเสริมพฤติกรรม<br>
            <input type="radio" name="Q9" value="2" class="mr-2"> ได้ แต่ต้องมีแนวทางช่วยเหลือเพิ่มเติม<br>
            <input type="radio" name="Q9" value="1" class="mr-2"> ได้ในบางกรณี แต่ต้องการคำแนะนำมากขึ้น<br>
            <input type="radio" name="Q9" value="0" class="mr-2"> ไม่สามารถดูแลได้
        </div>
        <br>

        <label class="block mb-2">10.คุณมีประสบการณ์ในการส่งเสริมพัฒนาการของเด็ก (เช่น การพัฒนาทักษะทางอารมณ์หรือสังคม) หรือไม</label>
        <div class="space-y-2">
            <input type="radio" name="Q10" value="1" class="mr-2"> มีประสบการณ์<br>
            <input type="radio" name="Q10" value="0" class="mr-2"> ไม่มีประสบการณ์
        </div>
        <br>

        <label class="block mb-2">11. คุณสามารถพาเด็กออกไปทำกิจกรรมกลางแจ้ง (เช่น เล่นกีฬา, ปั่นจักรยาน) ได้หรือไม่?</label>
        <div class="space-y-2">

            <input type="radio" name="Q11" value="1" class="mr-2"> ได้<br>
            <input type="radio" name="Q11" value="0" class="mr-2"> ไม่ได้
        </div>
        <br>

        <label class="block mb-2">12. คุณสามารถทำความสะอาดเล็กๆ น้อยๆ เช่น ปัด กวาด เช็ด ถู ได้หรือไม่?</label>
        <div class="space-y-2">
            <input type="radio" name="Q12" value="1" class="mr-2"> มีประสบการณ์<br>
            <input type="radio" name="Q12" value="0" class="mr-2"> ไม่มีประสบการณ์
        </div>
        <br>

        <label class="block mb-2">13. คุณสามารถทำอาหารหรือเตรียมอาหารให้เด็กได้หรือไม่?</label>
        <div class="space-y-2">
            <input type="radio" name="Q13" value="1" class="mr-2"> ได้<br>
            <input type="radio" name="Q13" value="0" class="mr-2"> ไม่ได้
        </div>
        <br>

        <label class="block mb-2">14. คุณสามารถช่วยเหลือเรื่องทั่วไปของเด็ก เช่น การแต่งตัว การทำการบ้าน ได้หรือไม่?</label>
        <div class="space-y-2">
            <input type="radio" name="Q14" value="1" class="mr-2"> ได้<br>
            <input type="radio" name="Q14" value="0" class="mr-2"> ไม่ได้
        </div>
        <br>

        <label class="block mb-2">15. คุณมีทักษะในการปฐมพยาบาลเบื้องต้นหรือไม่?</label>
        <div class="space-y-2">
            <input type="radio" name="Q15" value="1" class="mr-2"> มี<br>
            <input type="radio" name="Q15" value="0" class="mr-2"> ไม่ได้
        </div>
        <br>

        <label class="block mb-2">16. คุณดื่มแอลกอฮอล์หรือไม่?</label>
        <div class="space-y-2">
            <input type="radio" name="Q16" value="1" class="mr-2"> ไม่ดื่ม<br>
            <input type="radio" name="Q16" value="0" class="mr-2"> ดื่ม
        </div>
        <br>

        <label class="block mb-2">17. คุณสูบบุหรี่หรือไม่?</label>
        <div class="space-y-2">
            <input type="radio" name="Q17" value="1" class="mr-2"> ไม่สูบ<br>
            <input type="radio" name="Q17" value="0" class="mr-2"> สูบ
        </div>
        <br>

        
        <button type="submit" class="w-full bg-green-500 text-white p-2 rounded">ส่ง</button>
    </form>
</body>
</html>
