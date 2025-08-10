<?php
include 'config.php'; // เชื่อมต่อฐานข้อมูล

if (isset($_GET['id'])) {
    $parent_id = $_GET['id'];

    try {
        // เริ่ม transaction
        $conn->begin_transaction();

        // ลบเด็กที่อยู่ภายใต้ parent นี้ก่อน
        $sql1 = "DELETE FROM child WHERE parent_id = ?";
        $stmt1 = $conn->prepare($sql1);
        if (!$stmt1) throw new Exception("Error preparing SQL 1: " . $conn->error);
        $stmt1->bind_param("i", $parent_id);
        $stmt1->execute();
        $stmt1->close();

        // ลบการจองที่เกี่ยวข้องกับ parent
        $sql2 = "DELETE FROM caregiver_booking WHERE parent_id = ?";
        $stmt2 = $conn->prepare($sql2);
        if (!$stmt2) throw new Exception("Error preparing SQL 2: " . $conn->error);
        $stmt2->bind_param("i", $parent_id);
        $stmt2->execute();
        $stmt2->close();

        // ลบข้อมูลแบบสอบถาม
        $sql3 = "DELETE FROM parent_questionnaire_result WHERE parent_id = ?";
        $stmt3 = $conn->prepare($sql3);
        if (!$stmt3) throw new Exception("Error preparing SQL 3: " . $conn->error);
        $stmt3->bind_param("i", $parent_id);
        $stmt3->execute();
        $stmt3->close();

        // ลบผู้ปกครอง
        $sql4 = "DELETE FROM parent WHERE parent_id = ?";
        $stmt4 = $conn->prepare($sql4);
        if (!$stmt4) throw new Exception("Error preparing SQL 4: " . $conn->error);
        $stmt4->bind_param("i", $parent_id);
        $stmt4->execute();
        $stmt4->close();

        // ยืนยันการลบ
        $conn->commit();

        echo "<script>alert('ลบข้อมูลผู้ปกครองและข้อมูลที่เกี่ยวข้องสำเร็จ!'); window.location.href='view_parent.php';</script>";

    } catch (Exception $e) {
        $conn->rollback(); // ยกเลิกถ้ามีข้อผิดพลาด
        echo "<script>alert('เกิดข้อผิดพลาด: {$e->getMessage()}'); window.location.href='view_parent.php';</script>";
    }
} else {
    echo "<script>alert('ไม่พบรหัสผู้ปกครอง'); window.location.href='view_parent.php';</script>";
}
?>


