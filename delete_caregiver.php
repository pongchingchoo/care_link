<?php
include 'config.php';

if (isset($_GET["id"])) {
    $caregiver_id = $_GET["id"];

    $conn->begin_transaction();

    try {
        // รายการ SQL ที่จะพยายามลบ
        $tables = [
            'booking',
            'caregiver_booking',
            'h_caregiver',
            'questionnaire_result',
            'caregiver'
        ];

        foreach ($tables as $table) {
            $sql = "DELETE FROM $table WHERE caregiver_id = ?";
            $stmt = $conn->prepare($sql);

            if ($stmt) {
                $stmt->bind_param("i", $caregiver_id);
                $stmt->execute();
                $stmt->close();
            } else {
                // แค่แจ้งเตือนว่า table ไม่มี ไม่หยุดโปรแกรม
                error_log("ข้าม: prepare ล้มเหลวในตาราง '$table': " . $conn->error);
            }
        }

        $conn->commit();

        echo "<script>alert('ลบข้อมูลสำเร็จ!'); window.location.href='view_caregiver.php';</script>";
    } catch (Exception $e) {
        $conn->rollback();
        echo "เกิดข้อผิดพลาด: " . $e->getMessage();
    }
}
?>

