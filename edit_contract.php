<?php
include 'config.php'; // เชื่อมต่อฐานข้อมูล

// ตรวจสอบว่ามีการส่งค่า id มาหรือไม่
if (isset($_GET['id'])) {
    $caregiver_id = $_GET['id'];

    // ดึงข้อมูลของผู้ดูแลจากฐานข้อมูล
    $sql = "SELECT c.caregiver_id, c.first_name, c.last_name, c.gender, c.email, c.birthdate, c.type, c.price, c.contract_type, c.start_date, c.end_date FROM caregiver c WHERE caregiver_id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $caregiver_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
        } else {
            echo "ไม่พบข้อมูลผู้ดูแล";
            exit();
        }
        $stmt->close();
    }
} else {
    echo "ไม่พบรหัสผู้ดูแล";
    exit();
}

// เมื่อมีการส่งข้อมูลฟอร์ม (POST) ให้ทำการอัปเดตข้อมูล
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $contract_type = $_POST['contract_type'];

    $sql = "UPDATE caregiver SET contract_type=? WHERE caregiver_id=?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("si", $contract_type, $caregiver_id);
        if ($stmt->execute()) {
            echo "<script>alert('อัปเดตข้อมูลเรียบร้อย'); window.location.href='view_contract.php';</script>";
        } else {
            echo "<script>alert('เกิดข้อผิดพลาดในการอัปเดตข้อมูล');</script>";
        }
        $stmt->close();
    }
}

?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แก้ไขข้อมูลผู้ดูแล</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f8f9fa;
        }
        h2 {
            text-align: center;
            color: #333;
        }
        form {
            
            width: 800px;
            margin: auto;
            background: #fff;
            padding: 20px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        form input[type="text"],
        form input[type="email"],
        form input[type="date"] {
            width: 100%;
            padding: 8px;
            margin: 8px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
            
        }
        form input[type="submit"] {
            background-color: #00a99d;
            color: white;
            border: none;
            padding: 10px 20px;
            margin-top: 10px;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
        }
        form input[type="submit"]:hover {
            background-color: #008f82;
        }
        label {
            font-weight: bold;
           
        }

    </style>
</head>
<body>
    <h2>แก้ไขข้อมูลผู้ดูแล</h2>
    <form method="POST">
        <label>ชื่อ:</label>
        <input type="text" name="first_name" value="<?php echo $row['first_name']; ?>" required>

        <label>นามสกุล:</label>
        <input type="text" name="last_name" value="<?php echo $row['last_name']; ?>" required>

        <label>เพศ:</label>
        <input type="text" name="gender" value="<?php echo $row['gender']; ?>" required>

        <label>อีเมล:</label>
        <input type="email" name="email" value="<?php echo $row['email']; ?>" required>

        <label>ประเภท:</label>
        <input type="text" name="type" value="<?php echo $row['type']; ?>" required>

        <label>ค่าจ้าง:</label>
        <input type="text" name="price" value="<?php echo $row['price']; ?>" required>

        <label>ประเภทสัญญา:</label>
      <select name="contract_type" id="contract_type">
          <option value="1 เดือน" <?php if ($row['contract_type'] == "1 เดือน") echo "selected"; ?>>1 เดือน</option>
          <option value="3 เดือน" <?php if ($row['contract_type'] == "3 เดือน") echo "selected"; ?>>3 เดือน</option>
          <option value="6 เดือน" <?php if ($row['contract_type'] == "6 เดือน") echo "selected"; ?>>6 เดือน</option>
          <option value="1 ปี" <?php if ($row['contract_type'] == "1 ปี") echo "selected"; ?>>1 ปี</option>
      </select>

      <label>วันที่เริ่มต้น:</label>
      <input type="date" name="start_date" id="start_date" value="<?php echo $row['start_date']; ?>" required>

      <label>วันที่สิ้นสุด:</label>
      <input type="date" name="end_date" id="end_date" value="<?php echo $row['end_date']; ?>" required>

      

      <input type="submit" value="บันทึก">
  </form>

  <script>
    // ฟังก์ชันสำหรับเซ็ตค่า start_date เป็นวันปัจจุบันและคำนวณ end_date
    function setStartAndCalculateEnd(){
        var today = new Date();
        // แปลงวันที่ให้เป็นรูปแบบ yyyy-mm-dd
        var dd = String(today.getDate()).padStart(2, '0');
        var mm = String(today.getMonth() + 1).padStart(2, '0');
        var yyyy = today.getFullYear();
        var todayStr = yyyy + '-' + mm + '-' + dd;
        document.getElementById("start_date").value = todayStr;
        calculateEndDate(new Date(todayStr));
    }

    // ฟังก์ชันคำนวณ end_date โดยเพิ่มจำนวนตามประเภทสัญญา
    function calculateEndDate(startDate){
        var contractType = document.getElementById("contract_type").value;
        var endDate = new Date(startDate);
        if(contractType === "1 เดือน"){
            endDate.setMonth(endDate.getMonth() + 1);
        } else if(contractType === "3 เดือน"){
            endDate.setMonth(endDate.getMonth() + 3);
        } else if(contractType === "6 เดือน"){
            endDate.setMonth(endDate.getMonth() + 6);
        } else if(contractType === "1 ปี"){
            endDate.setFullYear(endDate.getFullYear() + 1);
        }
        var dd = String(endDate.getDate()).padStart(2, '0');
        var mm = String(endDate.getMonth() + 1).padStart(2, '0');
        var yyyy = endDate.getFullYear();
        document.getElementById("end_date").value = yyyy + '-' + mm + '-' + dd;
    }

    // เมื่อเปลี่ยนประเภทสัญญา ให้เซ็ต start_date เป็นวันปัจจุบัน และคำนวณ end_date
    document.getElementById("contract_type").addEventListener("change", function(){
        setStartAndCalculateEnd();
    });
  </script>
</body>
</html>

<?php $conn->close(); ?>

