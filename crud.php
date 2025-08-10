<?php
require_once 'config.php';

// ✅ CREATE (เพิ่มข้อมูล)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["create"])) {
    $first_name = $_POST["first_name"];
    $last_name = $_POST["last_name"];
    $sub_district = $_POST["sub_district"];
    $district = $_POST["district"];
    $family_members = $_POST["family_members"];
    $guardian_status = $_POST["guardian_status"];
    $total_children = $_POST["total_children"];
    $pets_in_home = $_POST["pets_in_home"];
    $email = $_POST["email"];
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT); // เข้ารหัสรหัสผ่าน

    // ใช้ Prepared Statements เพื่อป้องกัน SQL Injection
    $stmt = $conn->prepare("INSERT INTO parent (first_name, last_name, sub_district, district, family_members, guardian_status, total_children, pets_in_home, email, password) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssisssss", $first_name, $last_name, $sub_district, $district, $family_members, $guardian_status, $total_children, $pets_in_home, $email, $password);
    
    if ($stmt->execute()) {
        echo "<script>alert('บันทึกข้อมูลสำเร็จ!'); window.location.href='LOG-IN1.html';</script>";
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}

// ✅ READ (ดึงข้อมูล)
if (isset($_GET["read"])) {
    $sql = "SELECT * FROM parent";
    $result = $conn->query($sql);
    $data = [];
    while ($row = $result->fetch_assoc()) {
        unset($row['password']); // ไม่ส่งรหัสผ่านกลับไป
        $data[] = $row;
    }
    echo json_encode($data);
}

// ✅ UPDATE (แก้ไขข้อมูล)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["update"])) {
    $parent_id = $_POST["parent_id"];
    $first_name = $_POST["first_name"];
    $last_name = $_POST["last_name"];
    $sub_district = $_POST["sub_district"];
    $district = $_POST["district"];
    $family_members = $_POST["family_members"];
    $guardian_status = $_POST["guardian_status"];
    $total_children = $_POST["total_children"];
    $pets_in_home = $_POST["pets_in_home"];
    $email = $_POST["email"];

    if (!empty($_POST["password"])) {
        $password = password_hash($_POST["password"], PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE parent SET first_name=?, last_name=?, sub_district=?, district=?, family_members=?, guardian_status=?, total_children=?, pets_in_home=?, email=?, password=? WHERE parent_id=?");
        $stmt->bind_param("ssssisssssi", $first_name, $last_name, $sub_district, $district, $family_members, $guardian_status, $total_children, $pets_in_home, $email, $password, $parent_id);
    } else {
        $stmt = $conn->prepare("UPDATE parent SET first_name=?, last_name=?, sub_district=?, district=?, family_members=?, guardian_status=?, total_children=?, pets_in_home=?, email=? WHERE parent_id=?");
        $stmt->bind_param("ssssissssi", $first_name, $last_name, $sub_district, $district, $family_members, $guardian_status, $total_children, $pets_in_home, $email, $parent_id);
    }

    if ($stmt->execute()) {
        echo "อัปเดตข้อมูลสำเร็จ!";
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}

// ✅ DELETE (ลบข้อมูล)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["delete"])) {
    $parent_id = $_POST["parent_id"];

    $stmt = $conn->prepare("DELETE FROM parent WHERE parent_id=?");
    $stmt->bind_param("i", $parent_id);

    if ($stmt->execute()) {
        echo "ลบข้อมูลสำเร็จ!";
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}

// ปิดการเชื่อมต่อฐานข้อมูล
$conn->close();
?>
