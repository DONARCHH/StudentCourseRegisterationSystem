<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Motion University</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: #e9f3ff;
        }

        .right-panel {
            width: 100%;
            max-width: 400px;
            background: #fff;
            padding: 40px 30px;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        }

        .login-box .logo-area {
            text-align: center;
            margin-bottom: 30px;
        }

        .login-box .logo-area img.crest {
            width: 80px;
            margin-bottom: 10px;
        }

        .login-box .logo-area h2 {
            color: #1c2b4a;
            margin-bottom: 5px;
            font-size: 22px;
        }

        .login-box .logo-area .panel-title {
            font-size: 14px;
            color: #555;
        }

        .input-group {
            margin-bottom: 20px;
        }

        .input-group label {
            display: block;
            margin-bottom: 5px;
            font-size: 14px;
            color: #333;
        }

        .input-group input {
            width: 100%;
            padding: 12px 15px;
            border-radius: 8px;
            border: 1px solid #ccc;
            font-size: 14px;
        }

        .options-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .options-row .forgot {
            text-decoration: none;
            color: #1c2b4a;
        }

        .options-row .forgot:hover {
            text-decoration: underline;
        }

        .login-btn {
            width: 100%;
            padding: 12px;
            background: #1c2b4a;
            color: #fff;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            transition: background 0.3s ease;
        }

        .login-btn:hover {
            background: #0f1a33;
        }

        .error-msg {
            background: #ffdddd;
            color: #d8000c;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
        }
    </style>
</head>

<body>

    <div class="right-panel">

        <div class="login-box">

            <div class="logo-area">
                <img src="logo.jpg" class="crest">
                <h2>MOTION UNIVERSITY</h2>
                <p class="panel-title">ADMIN PANEL</p>
            </div>

            <?php 
            if(isset($_SESSION['admin_error'])) {
                echo "<div class='error-msg'>".$_SESSION['admin_error']."</div>";
                unset($_SESSION['admin_error']);
            }
            ?>

            <form method="POST" action="login_process.php">

                <div class="input-group">
                    <label>Admin Name</label>
                    <input type="text" name="name" required>
                </div>

                <div class="input-group">
                    <label>Password</label>
                    <input type="password" name="password" required>
                </div>

                <div class="options-row">
                    <div>
                        <input type="checkbox" id="remember">
                        <label for="remember">Remember me</label>
                    </div>
                    <a href="#" class="forgot">Forgot Password?</a>
                </div>

                <button class="login-btn">Sign In</button>

            </form>

        </div>

    </div>

</body>
</html>
