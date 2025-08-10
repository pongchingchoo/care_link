<?php
include 'config.php';

if (empty($_GET['id']) || empty($_GET['f'])) {
    http_response_code(400); exit('Missing parameters');
}
$contractId = (int)$_GET['id'];
$field      = $_GET['f']; // 'pay' | 'contract_image'

if (!in_array($field, ['pay', 'contract_image'], true)) {
    http_response_code(400); exit('Invalid file field');
}

$stmt = $conn->prepare("SELECT $field FROM caregiver_contract WHERE contract_id=?");
$stmt->bind_param('i', $contractId);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows === 0) { http_response_code(404); exit('File not found'); }

$stmt->bind_result($blob);
$stmt->fetch();
$stmt->close();
$conn->close();

if ($blob === null || $blob === '') { http_response_code(404); exit('No file data'); }

$finfo = new finfo(FILEINFO_MIME_TYPE);
$mime  = $finfo->buffer($blob);
if (!$mime || !in_array($mime, ['image/png', 'image/jpeg', 'application/pdf'], true)) {
    $mime = 'application/octet-stream';
}

// กรณี pay คือรูปภาพ (jpg/png) เราจะส่งหน้า HTML แสดงภาพ base64
if ($field === 'pay' && in_array($mime, ['image/png', 'image/jpeg'])) {
    $base64 = base64_encode($blob);
    echo "<!DOCTYPE html>
<html lang='th'>
<head>
    <meta charset='UTF-8'>
    <title>สลิปจ่าย</title>
    <style>
        body {
            margin: 0;
            background: #222;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            overflow: hidden;
            flex-direction: column;
            color: white;
            font-family: Arial, sans-serif;
        }
        img {
            max-width: 90vw;
            max-height: 80vh;
            border-radius: 8px;
            box-shadow: 0 0 20px rgba(0,0,0,0.7);
        }
        .close-btn {
            margin-top: 20px;
            background: #f44336;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }
        .close-btn:hover {
            background: #d32f2f;
        }
    </style>
</head>
<body>
    <img src='data:$mime;base64,$base64' alt='สลิปจ่าย' />
    
</body>
</html>";
    exit;
}

// กรณีอื่น ๆ (เช่น PDF) ส่งแบบ raw binary ตามเดิม
header('Content-Type: ' . $mime);
header('Content-Length: ' . strlen($blob));
header('Content-Disposition: inline; filename="' . $field . '_' . $contractId . '"');
echo $blob;
?>
