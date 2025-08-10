<?php
// payment_popup.php
include 'config.php';
session_start();

if (!isset($_SESSION['user_id'])) exit('กรุณาเข้าสู่ระบบ');

// กำหนดช่วงเวลาสัญญาให้เลือกได้พร้อมราคาตามที่บอก
$packages = [
    500 => '1 เดือน',
    1500 => '3 เดือน',
    2500 => '6 เดือน',
    4000 => '1 ปี',
];

// ค่าเริ่มต้น
$default_price = 500;

?>
<!doctype html>
<html lang="th">
<head>
<meta charset="utf-8">
<title>ชำระเงินแพ็กเกจ</title>
<style>
body{font-family:Arial;padding:20px;max-width:450px;margin:auto;}
img{max-width:250px;margin:20px 0;}
input, select, button{margin:8px 0;padding:8px;width:100%;}
button{background:#28a745;color:#fff;border:none;cursor:pointer;border-radius:6px;}
button:hover{background:#218838;}
a.download-link {
  display:inline-block;
  margin: 10px 0;
  padding: 10px 15px;
  background: #007bff;
  color: #fff;
  text-decoration: none;
  border-radius: 6px;
}
a.download-link:hover {
  background: #0056b3;
}
</style>
</head>
<body>

<h2>สแกน QR เพื่อชำระเงิน</h2>
<img src="QR.jpg" alt="QR Code">

<h3>ดาวน์โหลดสัญญา</h3>
<a href="sample1.pdf" class="download-link" download>ดาวน์โหลดไฟล์สัญญา</a>

<h3>กรอกข้อมูลการชำระเงิน</h3>
<form action="save_package.php" method="post" enctype="multipart/form-data" onsubmit="return validateAmount()">
  <label for="amount">เลือกจำนวนเงินที่ชำระ</label>
  <select name="amount" id="amount" required>
    <?php foreach ($packages as $price => $period): ?>
        <option value="<?= $price ?>"><?= number_format($price) ?> บาท — <?= $period ?></option>
    <?php endforeach; ?>
  </select>
  <br><br>

  <label for="entered_amount">จำนวนเงินที่ชำระจริง (บาท):</label>
  <input type="number" name="entered_amount" id="entered_amount" required>

  <script>
function validateAmount() {
  const selectedAmount = parseFloat(document.getElementById('amount').value);
  const enteredAmount = parseFloat(document.getElementById('entered_amount').value);

  if (enteredAmount < selectedAmount) {
    alert("จำนวนเงินที่กรอกน้อยกว่าราคาที่เลือก กรุณาตรวจสอบอีกครั้ง");
    return false;
  }
  return true;
}
</script>

  <label>วันที่โอน</label>
  <input type="date" name="pay_date" required>

  <label>เวลาโอน</label>
  <input type="time" name="pay_time" required>

  <label>หลักฐานสลิปโอนเงิน (.jpg/.png)</label>
  <input type="file" name="slip" accept="image/jpeg,image/png" required>

  <label>หนังสือสัญญา (.pdf หรือ .docx)</label>
  <input type="file" name="contract_file" accept=".pdf,.docx" required>
  <small>อัปโหลดไฟล์สัญญาที่เซ็นแล้ว</small>

  <button type="submit">ยืนยันการชำระเงิน</button>
</form>

<script>
  // (ถ้าต้องการแสดงราคาอื่น ๆ หรือคำนวณเพิ่มเติม สามารถใส่ได้)
  function updatePrice() {
    // ตัวอย่าง ถ้ามีการแสดงราคาอื่นๆในอนาคต
  }
</script>

</body>
</html>

