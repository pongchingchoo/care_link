<?php
include('config.php'); // เชื่อมต่อฐานข้อมูล

// ตรวจสอบว่ามีการส่ง booking_id หรือไม่
if (isset($_GET['booking_id'])) {
    $booking_id = $_GET['booking_id'];

    // ดึงข้อมูลการจองจากฐานข้อมูล
    $sql = "SELECT b.booking_id, 
                   b.parent_id, 
                   b.caregiver_id, 
                   b.booking_type,
                   b.Start_Date, 
                   b.End_Date, 
                   b.start_month,
                   b.end_month,
                   b.total_price,
                   b.children_count,
                   b.Status 
            FROM caregiver_booking b 
            WHERE b.booking_id = ?";
    
    // เตรียมคำสั่ง
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $booking_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if (!$row) {
        die('ไม่พบการจองที่ต้องการแก้ไข');
    }
} else {
    die('ไม่พบข้อมูลการจอง');
}

// การอัพเดตข้อมูลเมื่อส่งฟอร์ม
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $parent_id = $_POST['parent_id'];
    $caregiver_id = $_POST['caregiver_id'];
    $booking_type = $_POST['booking_type'];
    $start_date = $_POST['Start_Date'];
    $end_date = $_POST['End_Date'];
    $start_month = $_POST['start_month'];
    $end_month = $_POST['end_month'];
    $children_count = $_POST['children_count'];
    $total_price = $_POST['total_price'];
    $status = $_POST['Status'];

    $update_sql = "UPDATE caregiver_booking 
                   SET parent_id = ?, caregiver_id = ?, booking_type = ?, Start_Date = ?, End_Date = ?, start_month = ?, end_month = ?, children_count = ?, total_price = ?, Status = ? 
                   WHERE booking_id = ?";
    
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("iissssssdis", $parent_id, $caregiver_id, $booking_type, $start_date, $end_date, $start_month, $end_month, $children_count, $total_price, $status, $booking_id);

    if ($update_stmt->execute()) {
        header("Location: view_booking.php"); // เปลี่ยนเส้นทางไปยังหน้ารายการจอง
        exit();
    } else {
        echo "เกิดข้อผิดพลาดในการอัพเดตข้อมูล";
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แก้ไขการจอง</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>


<div class="container mt-5">
    <h2 class="text-center">แก้ไขการจอง</h2>

    <form action="edit_booking.php?booking_id=<?php echo $booking_id; ?>" method="post">
        <div class="mb-3">
            <label for="parent_id" class="form-label">ผู้ปกครอง</label>
            <input type="text" class="form-control" name="parent_id" value="<?php echo $row['parent_id']; ?>" required>
        </div>

        <div class="mb-3">
            <label for="caregiver_id" class="form-label">ผู้ดูแล</label>
            <input type="text" class="form-control" name="caregiver_id" value="<?php echo $row['caregiver_id']; ?>" required>
        </div>

        <div class="mb-3">
            <label for="booking_type" class="form-label">ประเภทการจอง</label>
            <input type="text" class="form-control" name="booking_type" value="<?php echo $row['booking_type']; ?>" required>
        </div>

        <div class="mb-3">
            <label for="Start_Date" class="form-label">วันที่เริ่ม</label>
            <input type="date" class="form-control" name="Start_Date" value="<?php echo $row['Start_Date']; ?>" required>
        </div>

        <div class="mb-3">
            <label for="End_Date" class="form-label">วันที่สิ้นสุด</label>
            <input type="date" class="form-control" name="End_Date" value="<?php echo $row['End_Date']; ?>" required>
        </div>

        <div class="mb-3">
            <label for="start_month" class="form-label">เดือนเริ่ม</label>
            <input type="text" class="form-control" name="start_month" value="<?php echo $row['start_month']; ?>" required>
        </div>

        <div class="mb-3">
            <label for="end_month" class="form-label">เดือนสิ้นสุด</label>
            <input type="text" class="form-control" name="end_month" value="<?php echo $row['end_month']; ?>" required>
        </div>

        <div class="mb-3">
            <label for="children_count" class="form-label">จำนวนเด็ก</label>
            <input type="number" class="form-control" name="children_count" value="<?php echo $row['children_count']; ?>" required>
        </div>

        <div class="mb-3">
            <label for="total_price" class="form-label">ราคารวม</label>
            <input type="number" class="form-control" name="total_price" value="<?php echo $row['total_price']; ?>" required>
        </div>

        <div class="mb-3">
            <label for="Status" class="form-label">สถานะ</label>
            <select class="form-select" name="Status" required>
                <option value="Confirmed" <?php echo ($row['Status'] == 'Confirmed') ? 'selected' : ''; ?>>Confirmed</option>
                <option value="Pending" <?php echo ($row['Status'] == 'Pending') ? 'selected' : ''; ?>>Pending</option>
                <option value="Canceled" <?php echo ($row['Status'] == 'Canceled') ? 'selected' : ''; ?>>Canceled</option>
            </select>
        </div>

        <button type="submit" class="btn btn-success">บันทึกการแก้ไข</button>
        <a href="view_booking.php" class="btn btn-secondary">ยกเลิก</a>
    </form>
</div>

</body>
</html>
