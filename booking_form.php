<?php
include('config.php'); // เชื่อมต่อฐานข้อมูล

session_start(); // เปิดใช้งาน session

// ตรวจสอบว่า parent ได้ล็อกอินแล้วหรือไม่
if (!isset($_SESSION["parent_id"])) {
    header("Location: login.php");
    exit();
}




$parent_id = $_SESSION['parent_id']; // ID ของผู้ปกครองที่ล็อกอิน

// ดึงข้อมูลราคา (price) ของ caregiver ตาม caregiver_id
$caregiver_id = $_GET['caregiver_id'] ?? ''; // รับ caregiver_id ผ่าน GET
$caregiver_price = 0; // ค่าใช้จ่ายเริ่มต้น

if ($caregiver_id) {
    $sql = "SELECT price FROM caregiver WHERE caregiver_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $caregiver_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $caregiver_price = $row['price'];
    }
}



$existingBookings = [];
if ($caregiver_id) {
    $sql = "SELECT booking_type, start_date, end_date, start_month, end_month, address FROM caregiver_booking WHERE caregiver_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $caregiver_id);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $existingBookings[] = $row;
    }
}

// ส่งข้อมูลการจองไปฝั่ง JavaScript
$bookings_json = json_encode($existingBookings);
?>

<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>จองผู้ดูแล</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
  <div class="container mt-5">
    <h2 class="text-center">จองผู้ดูแลเด็ก</h2>
    <form action="book_caregiver.php" method="POST">
      
      <div class="mb-3">
        <label for="children_count" class="form-label">จำนวนเด็ก</label>
        <input type="number" class="form-control" id="children_count" name="children_count" required min="1">
      </div>
      <div class="mb-3">
        <label for="address" class="form-label">ที่อยู่สำหรับการดูแล</label>
        <input type="text" class="form-control" id="address" name="address" placeholder="กรอกที่อยู่ที่ต้องการให้ผู้ดูแลไปดูแล" required>
      </div>
      <div class="mb-3">
        <label class="form-label">ประเภทการจ้าง</label>
        <select class="form-select" id="booking_type" name="booking_type" required>
          <option value="" disabled selected>เลือกประเภทการจ้าง</option>
          <option value="daily">รายวัน</option>
          <option value="monthly">รายเดือน</option>
        </select>
      </div>
      <div class="mb-3" id="date_range" style="display: none;">
        <label for="start_date" class="form-label">วันที่เริ่มต้น</label>
        <input type="date" class="form-control" id="start_date" name="start_date" min=""/>
        <label for="end_date" class="form-label mt-2">วันที่สิ้นสุด</label>
        <input type="date" class="form-control" id="end_date" name="end_date" min=""/>
        <label for="start_time" class="form-label mt-2">เวลาเริ่มงาน</label>
        <input type="time" class="form-control" id="start_time" name="start_time">
        <label for="end_time" class="form-label mt-2">เวลาเลิกงาน</label>
        <input type="time" class="form-control" id="end_time" name="end_time" readonly>
      </div>
      <div class="mb-3" id="month_selection" style="display: none;">
        <label for="start_month" class="form-label">เดือนที่เริ่มงาน</label>
        <input type="month" class="form-control" id="start_month" name="start_month" min=""/>
        <label for="end_month" class="form-label mt-2">เดือนที่สิ้นสุดงาน</label>
        <input type="month" class="form-control" id="end_month" name="end_month" min=""/>
      </div>

      <!-- แสดงค่าใช้จ่าย -->
      <div class="mb-3">
        <p id="total_price">ค่าใช้จ่าย: 0 บาท</p>
      </div>

      <!-- ส่งข้อมูล parent_id และ caregiver_id ผ่าน hidden input -->
      <input type="hidden" name="parent_id" value="<?php echo $_SESSION['parent_id'] ?? ''; ?>">
      <input type="hidden" name="caregiver_id" value="<?php echo $_GET['caregiver_id'] ?? ''; ?>">
      <button type="submit" class="submit btn btn-primary w-100">ยืนยันการจอง</button>
    </form>
  </div>

  <script>
    // แสดง/ซ่อนส่วนของฟอร์มตามประเภทการจ้างที่เลือก
    document.getElementById("booking_type").addEventListener("change", function() {
      let type = this.value;
      document.getElementById("date_range").style.display = (type === "daily") ? "block" : "none";
      document.getElementById("month_selection").style.display = (type === "monthly") ? "block" : "none";
    });

    document.addEventListener("DOMContentLoaded", function() {
      // สำหรับการจองรายวัน: ไม่ให้เลือกวันปัจจุบัน
      let today = new Date();
      today.setDate(today.getDate() + 1);
      let minDate = today.toISOString().split("T")[0];
      document.getElementById("start_date").setAttribute("min", minDate);
      document.getElementById("end_date").setAttribute("min", minDate);

      // สำหรับการจองรายเดือน: ไม่ให้เลือกเดือนปัจจุบันหรือก่อนหน้า
      let currentMonth = new Date();
      currentMonth.setMonth(currentMonth.getMonth() + 1);
      let minMonth = currentMonth.toISOString().slice(0, 7);
      document.getElementById("start_month").setAttribute("min", minMonth);
      document.getElementById("end_month").setAttribute("min", minMonth);
    });

    // คำนวณเวลาเลิกงานโดยเพิ่ม 8 ชั่วโมงจากเวลาเริ่มงาน
    document.getElementById("start_time").addEventListener("change", function() {
      let startTime = this.value;
      if (startTime) {
        let [hours, minutes] = startTime.split(":");
        let endHours = parseInt(hours) + 8;
        if (endHours >= 24) endHours -= 24;
        let endTime = `${endHours.toString().padStart(2, '0')}:${minutes}`;
        document.getElementById("end_time").value = endTime;
      }
    });

    // คำนวณค่าใช้จ่ายเมื่อจำนวนเด็กเปลี่ยนแปลง
    document.getElementById("children_count").addEventListener("input", function() {
      let childrenCount = this.value;
      let pricePerChild = <?php echo $caregiver_price; ?>;
      let totalAmount = pricePerChild;

      // ถ้ามีจำนวนเด็กมากกว่าหนึ่งคน เพิ่มค่าใช้จ่ายคนละ 100 บาท
      if (childrenCount > 1) {
        totalAmount += (childrenCount - 1) * 100;
      }

      // แสดงค่าใช้จ่าย
      document.getElementById("total_price").innerText = "ค่าบริการ: " + totalAmount + " บาทต่อวัน";
    });

    // คำนวณค่าใช้จ่ายครั้งแรกเมื่อโหลดหน้า
    document.addEventListener("DOMContentLoaded", function() {
      let childrenCount = document.getElementById("children_count").value;
      let pricePerChild = <?php echo $caregiver_price; ?>;
      let totalAmount = pricePerChild;

      if (childrenCount > 1) {
        totalAmount += (childrenCount - 1) * 100;
      }

      document.getElementById("total_price").innerText = "ค่าบริการ: " + totalAmount + " บาทต่อวัน";
    });

    // คำนวณค่าใช้จ่ายแบบรายวันและรายเดือน
    document.getElementById("start_date").addEventListener("change", calculateAmount);
    document.getElementById("end_date").addEventListener("change", calculateAmount);
    document.getElementById("start_month").addEventListener("change", calculateAmount);
    document.getElementById("end_month").addEventListener("change", calculateAmount);

    function calculateAmount() {
      let bookingType = document.getElementById("booking_type").value;
      let childrenCount = document.getElementById("children_count").value;
      let pricePerChild = <?php echo $caregiver_price; ?>;
      let totalAmount = 0;

      if (bookingType === "daily") {
        let startDate = new Date(document.getElementById("start_date").value);
        let endDate = new Date(document.getElementById("end_date").value);
        let timeDiff = endDate - startDate;
        let days = timeDiff / (1000 * 3600 * 24) + 1;

        totalAmount = pricePerChild * days;

        if (childrenCount > 1) {
          totalAmount += (childrenCount - 1) * 100 * days;
        }

      
      
      
      // ------------------------------------------------------------------------------------------------------ รอแก้
      } else if (bookingType === "monthly") {
        let startMonth = new Date(document.getElementById("start_month").value);
        let endMonth = new Date(document.getElementById("end_month").value);
        let monthDiff = endMonth.getMonth() - startMonth.getMonth() + 1;
        let daysInMonth = new Date(startMonth.getFullYear(), startMonth.getMonth() + 1, 0).getDate();
        
        totalAmount = pricePerChild * daysInMonth * monthDiff * 0.8;

        if (childrenCount > 1) {
          totalAmount += (childrenCount - 1) * 100 * monthDiff * daysInMonth * 0.8;
        }
      }

      document.getElementById("total_price").innerText = "ค่าบริการ: " + totalAmount + " บาทต่อเดือน";
    }



// ตรวจสอบช่วงเวลาซ้ำ

    const existingBookings = <?php echo $bookings_json; ?>;

  function isOverlapping(start1, end1, start2, end2) {
    return (start1 <= end2) && (end1 >= start2);
  }

  function validateBooking() {
    let type = document.getElementById("booking_type").value;

    if (type === "daily") {
      let startDate = new Date(document.getElementById("start_date").value);
      let endDate = new Date(document.getElementById("end_date").value);

      for (let booking of existingBookings) {
        if (booking.booking_type === 'daily') {
          let bookedStart = new Date(booking.start_date);
          let bookedEnd = new Date(booking.end_date);
          if (isOverlapping(startDate, endDate, bookedStart, bookedEnd)) {
            alert("ช่วงเวลานี้ถูกจองแล้ว กรุณาเลือกวันใหม่");
            return false;
          }
        }
      }

    } else if (type === "monthly") {
      let startMonth = new Date(document.getElementById("start_month").value + "-01");
      let endMonth = new Date(document.getElementById("end_month").value + "-01");
      endMonth.setMonth(endMonth.getMonth() + 1); // ให้จบที่ปลายเดือน

      for (let booking of existingBookings) {
        if (booking.booking_type === 'monthly') {
          let bookedStart = new Date(booking.start_month);
          let bookedEnd = new Date(booking.end_month);
          bookedEnd.setMonth(bookedEnd.getMonth() + 1);
          if (isOverlapping(startMonth, endMonth, bookedStart, bookedEnd)) {
            alert("ช่วงเดือนนี้ถูกจองแล้ว กรุณาเลือกเดือนใหม่");
            return false;
          }
        }
      }
    }

    return true;
  }

  // ผูก validateBooking กับ submit form
  document.querySelector("form").addEventListener("submit", function(e) {
    if (!validateBooking()) {
      e.preventDefault(); // ยกเลิกการ submit ถ้าเวลาซ้ำ
    }
  });
  </script>
</body>
</html>
