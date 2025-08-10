<?php
include 'config.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT * FROM parent WHERE parent_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
}

if (isset($_POST['update'])) {
    $first_name = $_POST["first_name"];
    $last_name = $_POST["last_name"];
    $sub_district = $_POST["sub_district"];
    $district = $_POST["district"];
    $province = $_POST['province'];
    $family_members = $_POST["family_members"];
    $guardian_status = $_POST["guardian_status"];
    $total_children = $_POST["total_children"];
    $pets_in_home = $_POST["pets_in_home"];
    $housing_type = $_POST["housing_type"];
    $housing_detail = $_POST["housing_detail"];
    $email = $_POST["email"];

    $sql = "UPDATE parent SET first_name=?, last_name=?, sub_district=?, district=?,province=?, family_members=?, guardian_status=?, total_children=?, pets_in_home=?, housing_type=?, housing_detail=?, email=? WHERE parent_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssssssi", $first_name, $last_name, $sub_district, $district,$province, $family_members, $guardian_status, $total_children, $pets_in_home, $housing_type, $housing_detail, $email, $id);

    if ($stmt->execute()) {
        echo "<script>alert('อัปเดตข้อมูลสำเร็จ!'); window.location.href='view_parent.php';</script>";
    } else {
        echo "เกิดข้อผิดพลาด: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>แก้ไขข้อมูล</title>
</head>
<body>
    <h2>แก้ไขข้อมูล</h2>
    <form method="post">
        ชื่อ: <input type="text" name="first_name" value="<?= $row['first_name'] ?>" required><br>
        นามสกุล: <input type="text" name="last_name" value="<?= $row['last_name'] ?>" required><br>
        จังหวัด: <input type="text" name="province" value="<?= $row['province'] ?>" required><br>
        ตำบล: <input type="text" name="sub_district" value="<?= $row['sub_district'] ?>" required><br>
        อำเภอ: <input type="text" name="district" value="<?= $row['district'] ?>" required><br>
        สมาชิกในครอบครัว: <input type="text" name="family_members" value="<?= $row['family_members'] ?>" required><br>
        สถานะผู้ปกครอง: <input type="text" name="guardian_status" value="<?= $row['guardian_status'] ?>" required><br>
        จำนวนเด็ก: <input type="text" name="total_children" value="<?= $row['total_children'] ?>" required><br>
        สัตว์เลี้ยง: <input type="text" name="pets_in_home" value="<?= $row['pets_in_home'] ?>" required><br>
        ประเภทที่พัก: <input type="text" name="housing_type" value="<?= $row['housing_type'] ?>" required><br>
        รายละเอียดที่พัก: <input type="text" name="housing_detail" value="<?= $row['housing_detail'] ?>" required><br>
        อีเมล: <input type="email" name="email" value="<?= $row['email'] ?>" required><br>
        <button type="submit" name="update">อัปเดต</button>
    </form>
</body>
</html>
