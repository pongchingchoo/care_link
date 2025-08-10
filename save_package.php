<?php
include 'config.php';
session_start();
if (!isset($_SESSION['user_id'])) exit('กรุณาเข้าสู่ระบบ');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Method not allowed');
    }

    $amount       = isset($_POST['amount']) ? (float)$_POST['amount'] : 0;
    $pay_date     = $_POST['pay_date'] ?? '';
    $pay_time     = $_POST['pay_time'] ?? '';
    $caregiver_id = $_POST['user_id'] ?? ($_SESSION['user_id'] ?? null);

    if (!$amount || !$pay_date || !$pay_time || !$caregiver_id) {
        throw new Exception('ข้อมูลไม่ครบ');
    }

    // ตรวจสอบขนาดไฟล์ (เช่นไม่เกิน 10MB)
    $maxFileSize = 10 * 1024 * 1024; // 10MB

    if ($_FILES['slip']['error'] !== UPLOAD_ERR_OK) throw new Exception('อัปโหลดสลิปไม่สำเร็จ');
    if ($_FILES['contract_file']['error'] !== UPLOAD_ERR_OK) throw new Exception('อัปโหลดสัญญาไม่สำเร็จ');

    if ($_FILES['slip']['size'] > $maxFileSize) throw new Exception('ไฟล์สลิปมีขนาดใหญ่เกินไป');
    if ($_FILES['contract_file']['size'] > $maxFileSize) throw new Exception('ไฟล์สัญญามีขนาดใหญ่เกินไป');

    $slip_data     = file_get_contents($_FILES['slip']['tmp_name']);
    $contract_data = file_get_contents($_FILES['contract_file']['tmp_name']);

    $period_map = [
        500  => '+1 month',
        1500 => '+3 months',
        2500 => '+6 months',
        4000 => '+1 year',
    ];

    $contract_type_map = [
        500  => '1 เดือน',
        1500 => '3 เดือน',
        2500 => '6 เดือน',
        4000 => '1 ปี',
    ];
    if (!isset($period_map[$amount])) throw new Exception('จำนวนเงินไม่ถูกต้อง');

    $start = new DateTime("$pay_date $pay_time");
    $end   = (clone $start)->modify($period_map[$amount]);
    $start_date = $start->format('Y-m-d');
    $end_date   = $end->format('Y-m-d');
    $contract_type = $contract_type_map[$amount];

    // เริ่ม Transaction
    $pdo->beginTransaction();

    // Insert contract
    $stmt = $pdo->prepare(
        "INSERT INTO caregiver_contract
         (caregiver_id, amount, start_date, end_date, contract_image, pay)
         VALUES (:caregiver_id, :amount, :start_date, :end_date, :contract_image, :pay)"
    );
    $stmt->execute([
        ':caregiver_id'   => $caregiver_id,
        ':amount'         => $amount,
        ':start_date'     => $start_date,
        ':end_date'       => $end_date,
        ':contract_image' => $contract_data,
        ':pay'            => $slip_data,
    ]);

    // Update contract_type
    $stmt2 = $pdo->prepare(
        "UPDATE caregiver
         SET contract_type = :ctype
         WHERE caregiver_id = :caregiver_id"
    );
    $stmt2->execute([
        ':ctype'        => $contract_type,
        ':caregiver_id' => $caregiver_id,
    ]);

    // Update status
    if (!empty($contract_type)) {
        $stmt3 = $pdo->prepare(
            "UPDATE caregiver
             SET status = 'รออนุมัติ'
             WHERE caregiver_id = :caregiver_id"
        );
        $stmt3->execute([
            ':caregiver_id' => $caregiver_id,
        ]);
    }

    $pdo->commit();

    echo "บันทึกข้อมูลเรียบร้อย<br>สัญญาจะสิ้นสุดวันที่ " . htmlspecialchars($end_date);

} catch (Exception $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    echo "เกิดข้อผิดพลาด: " . htmlspecialchars($e->getMessage());
}
?>
