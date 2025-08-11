<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Care Link - Login</title>
    <link rel="stylesheet" href="LOG IN1.css">
</head>
<body>
    <div class="back">
        <div class="container">
            <div class="left-section">
                <h1>Welcome to <span class="highlight">Care Link</span></h1>
                <p class="subtitle">"เชื่อมโยง<span style="color: #00bfa5;">ผู้ดูแล</span>กับความต้องการของคุณ"</p>
            </div>
            <div class="right-section">
                <div class="form-container">
                    <img src="logo.png" alt="Care Link Logo" style="width: 60px;" class="logo">
                    <h2>Sign in <br> สำหรับผู้ปกครอง</h2>
                    <form action="login.php" method="post">
                        <input type="email" name="email" placeholder="Enter Email" class="input-field" required>
                        <input type="password" name="password" placeholder="Password" class="input-field" required>
                        <a href="forgot_password.php" class="recover-link">Recover Password ?</a>
                        <button type="submit" class="btn sign-in-btn">Sign in</button>
                    </form>
                    <button class="btn register-btn" onclick="location.href='Wed1.php'">ยังไม่มีบัญชีใช่ไหม สมัครเลย</button>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

