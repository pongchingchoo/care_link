<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เข้าสู่ระบบ</title>
    <style>
body {
    margin: 0;
    font-family: 'Arial', sans-serif;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    background-color: #fff;
}

.back {
    position: relative;
    width: 100%;
    height: 100vh; /* ครอบคลุมความสูงของหน้าจอ */
    background-image: url('back.jpg'); /* ใส่ภาพพื้นหลัง */
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
  }

.container {
    display: flex;
    width: 80%;
    max-width: 1200px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    border-radius: 10px;
    overflow: hidden;

    position: absolute;
      top: 50%; /* จัดตำแหน่งให้อยู่ตรงกลางแนวตั้ง */
      left: 50%; /* จัดตำแหน่งให้อยู่ตรงกลางแนวนอน */
      transform: translate(-50%, -50%); /* ปรับให้อยู่กึ่งกลางพอดี */

    
}

/* Left Section */
.left-section {
    flex: 1;
    background-color: #ffffff;
    padding: 50px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: flex-start;
}

.left-section h1 {
    font-size: 2.5rem;
    margin: 0;
}

.left-section .highlight {
    color: #ff9800;
}

.left-section .subtitle {
    font-size: 1.2rem;
    margin-top: 10px;
    color: #555;
}

/*ฝั่งขวา*/
.right-section {
    flex: 1;
    background-color: #f8f9fa;
    padding: 50px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    position: relative;
}

.language-switch {
    position: absolute;
    top: 20px;
    right: 20px;
    font-size: 0.9rem;
    color: #555;
}

.form-container {
    text-align: center;
    width: 100%;
    max-width: 400px;
}

.logo {
    width: 60px;
    margin-bottom: 20px;
}

.form-container h2 {
    font-size: 1.5rem;
    margin-bottom: 20px;
}

.input-field {
    width: 100%;
    padding: 10px;
    margin-bottom: 15px;
    border: 1px solid #ddd;
    border-radius: 5px;
    font-size: 1rem;
}

.recover-link {
    font-size: 0.9rem;
    color: #00bfa5;
    text-decoration: none;
    display: block;
    margin-bottom: 20px;
}

.recover-link:hover {
    text-decoration: underline;
}

/* ปุ่มทั้งหมมด */
.btn {
    width: 100%;
    padding: 10px;
    border: none;
    border-radius: 5px;
    font-size: 1rem;
    cursor: pointer;
}

.sign-in-btn {
    background-color: #00bfa5;
    color: #fff;
    margin-bottom: 15px;
}

.register-btn {
    background-color: #ff9800;
    color: #fff;
}

.sign-in-btn:hover {
    background-color: #009c8a;
}

.register-btn:hover {
    background-color: #e68900;
}

    </style>
</head>
<body>
<div class="back">
        <div class="container">
            <div class="left-section">
                <h1>Welcome to <span class="highlight">Admin Panel</span></h1>
                <p class="subtitle">"ระบบจัดการสำหรับ<span style="color: #00bfa5;">ผู้ดูแลระบบ</span>"</p>
            </div>
            <div class="right-section">
                <div class="form-container">
                    <img src="LOGO.png" alt="Admin Logo" class="logo">
                    <h2>เข้าสู่ระบบ <br> สำหรับผู้ดูแลระบบ</h2>
                    <form onsubmit="return checkLogin(event)">
                    <input type="text" id="username" class="input-field" placeholder="Username" required>
                    <input type="password" id="password" class="input-field" placeholder="Password" required>
                        <button type="submit" class="btn sign-in-btn">เข้าสู่ระบบ</button>
                    </form>
                </div>
            </div>
        </div>
        <script>
        function checkLogin(event) {
            event.preventDefault();
            const username = document.getElementById('username').value;
            const password = document.getElementById('password').value;
            
            if (username === 'admin1' && password === 'admin1') {
                alert('เข้าสู่ระบบสำเร็จ!');
                window.location.href = 'admin.php'; // เปลี่ยนเส้นทางไปหน้าหลักของแอดมิน
            } else {
                document.getElementById('error-message').style.display = 'block';
            }
        }
    </script>
    </div>
</body>
</html>
