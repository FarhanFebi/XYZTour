<?php
session_start();
if(isset($_SESSION['admin'])){
    header("Location: admin_dashboard.php"); exit;
}
$USER = "admin";
$PASS = "admin123";
if(isset($_POST['login'])){
    $user = $_POST['user'];
    $pass = $_POST['pass'];
    if($user == $USER && $pass == $PASS){
        $_SESSION['admin'] = true;
        header("Location: admin_dashboard.php"); exit;
    } else {
        $msg = "Username/password salah!";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login Admin XYZTour</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body { background: linear-gradient(135deg, #2979FF, #B3E5FC 80%); font-family: 'Segoe UI', Arial, sans-serif; min-height:100vh; }
        .login-container {
            background: #fff; max-width:340px; margin:60px auto 0 auto; padding:30px 24px 18px 24px;
            border-radius:16px; box-shadow: 0 6px 24px rgba(41,121,255,0.16);
        }
        h2 { color: #2979FF; text-align:center; }
        label { font-weight:500; color:#1565C0; }
        input[type="text"], input[type="password"] {
            width: 100%; padding: 10px; margin:10px 0 22px 0;
            border:1px solid #90CAF9; border-radius:7px;
        }
        button {
            width: 100%; background: #2979FF; color: #fff; 
            border: none; border-radius: 7px; padding: 10px 0; 
            font-size: 1.07em; font-weight: bold; cursor: pointer;
        }
        button:hover { background: #1565C0; }
        .msg { text-align:center; color:#B71C1C; margin-bottom:10px;}
    </style>
</head>
<body>
<div class="login-container">
    <h2>Login Admin</h2>
    <?php if(isset($msg)) echo "<div class='msg'>$msg</div>"; ?>
    <form method="POST">
        <label>Username:</label>
        <input type="text" name="user" required>
        <label>Password:</label>
        <input type="password" name="pass" required>
        <button type="submit" name="login">Login</button>
    </form>
</div>
</body>
</html>
