<?php
session_start();
require_once 'config.php';
$data = get_data();

$registerError = '';

if (isset($_POST['register'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role     = $_POST['role'];

    $alreadyUsed = false;
    foreach ($data['users'] as $u) {
        if ($u['username'] === $username) {
            $alreadyUsed = true;
            break;
        }
    }

    // Check if username already exists
    $users = db_select("SELECT * FROM users WHERE username = '$username'");
    
    if (count($users) > 0) {
        $registerError = "Email already registered. Please use a different email.";
    } else {
        // Insert new user
        $query = "INSERT INTO users (username, password, role) VALUES ('$username', '$password', '$role')";
        db_query($query);
    }
}

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    foreach ($data['users'] as $user) {
        if ($user['username'] === $username && $user['password'] === $password) {
            $_SESSION['role'] = $user['role'];
            $_SESSION['user_id'] = $user['id'];
            if ($user['role'] === 'teacher') {
                header("Location: teacher.php");
            } elseif ($user['role'] === 'student') {
                header("Location: student.php");
            } else {
                header("Location: admin.php");
            }
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Portal Login & Registration</title>
    <style>
    @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&display=swap');
    body {
        font-family: 'Roboto', sans-serif;
        background: #eef2f3;
        margin: 0;
        padding: 0;
        font-size: 1.1rem;
    }
    .top-bar {
        padding: 30px; 
        text-align: center;
        background: linear-gradient(90deg, #0078d7, #00a1e4);
        color: #fff;
        font-size: 1.5rem;
    }
    .tabs {
        margin: 30px auto 0;
        width: 500px;
        display: flex;
        justify-content: space-between;
    }
    .tab {
        flex: 1;
        padding: 20px;
        text-align: center;
        background: #f1f5f9;
        cursor: pointer;
        border-top-left-radius: 5px;
        border-top-right-radius: 5px;
        transition: background 0.3s, box-shadow 0.3s;
    }
    .tab:hover {
        background: #e2ebf5;
    }
    .active-tab {
        background: #fff;
        border-bottom: 2px solid #fff;
        font-weight: 700;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .form-container {
        background: #fff;
        margin: 20px auto;
        width: 500px;
        padding: 30px 40px;
        border-radius: 5px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    h2 {
        margin-top: 0;
        font-weight: 400;
        color: #0078d7;
        font-size: 2rem;
    }
    input, select, button {
        width: 100%;
        margin: 15px 0;
        padding: 15px;
        box-sizing: border-box;
        font-size: 1.1rem;
        border-radius: 5px;
        border: 1px solid #ccc;
    }
    button {
        background: #0078d7;
        color: #fff;
        border: none;
        cursor: pointer;
        font-size: 1.1rem;
        transition: background 0.3s;
    }
    button:hover {
        background: #005a9e;
    }
    .error {
        color: #e74c3c;
        font-size: 1rem;
        margin-top: -10px;
        margin-bottom: 10px;
    }
</style>
    <script>
        function showTab(tabName) {
            document.getElementById('registerTab').style.display = (tabName === 'register') ? 'block' : 'none';
            document.getElementById('loginTab').style.display = (tabName === 'login') ? 'block' : 'none';
            document.getElementById('tab-register').classList.toggle('active-tab', tabName === 'register');
            document.getElementById('tab-login').classList.toggle('active-tab', tabName === 'login');
        }
        window.onload = function() {
            showTab('register'); // default open the 'register' tab
        }
    </script>
</head>
<body>
    <div class="top-bar">
        <h1>Welcome to the Portal</h1>
    </div>

    <div class="tabs">
        <div id="tab-register" class="tab" onclick="showTab('register')">Register</div>
        <div id="tab-login" class="tab" onclick="showTab('login')">Login</div>
    </div>

    <div class="form-container">
        <!-- Register Form -->
        <div id="registerTab">
            <h2>Create Account</h2>
            <form method="post">
                <?php if (!empty($registerError)) : ?>
                    <div class="error"><?php echo $registerError; ?></div>
                <?php endif; ?>
                <input type="text" name="username" placeholder="Username (email)" required>
                <input type="password" name="password" placeholder="Password" required>
                <select name="role" required>
                    <option value="teacher">Teacher</option>
                    <option value="student">Student</option>
                    <option value="admin">Admin</option>
                </select>
                <button type="submit" name="register">Register</button>
            </form>
        </div>

        <!-- Login Form -->
        <div id="loginTab" style="display:none;">
            <h2>Sign In</h2>
            <form method="post">
                <input type="text" name="username" placeholder="Username (email)" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit" name="login">Login</button>
            </form>
        </div>
    </div>
</body>
</html>