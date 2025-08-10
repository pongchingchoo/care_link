<?php
include 'config.php';

if (isset($_GET["id"])) {
    $caregiver_id = $_GET["id"];

    // เริ่ม transaction
    $conn->begin_transaction();

    try {
        // ลบจาก booking
        $stmt = $conn->prepare("DELETE FROM booking WHERE caregiver_id = ?");
        $stmt->bind_param("i", $caregiver_id);
        $stmt->execute();
        $stmt->close();

        // ลบจาก caregiver_booking
        $stmt = $conn->prepare("DELETE FROM caregiver_booking WHERE caregiver_id = ?");
        $stmt->bind_param("i", $caregiver_id);
        $stmt->execute();
        $stmt->close();

        // ลบจาก h_caregiver
        $stmt = $conn->prepare("DELETE FROM h_caregiver WHERE caregiver_id = ?");
        $stmt->bind_param("i", $caregiver_id);
        $stmt->execute();
        $stmt->close();

        $stmt = $conn->prepare("DELETE FROM questionnaire_result WHERE caregiver_id = ?");
        $stmt->bind_param("i", $caregiver_id);
        $stmt->execute();
        $stmt->close();
        

        // ลบจาก caregiver
        $stmt = $conn->prepare("DELETE FROM caregiver WHERE caregiver_id = ?");
        $stmt->bind_param("i", $caregiver_id);
        $stmt->execute();
        $stmt->close();

        $conn->commit();

        echo "<script> window.location.href='view_contract.php';</script>";
    } catch (Exception $e) {
        $conn->rollback();
        echo "เกิดข้อผิดพลาด: " . $e->getMessage();
    }
}
?>

