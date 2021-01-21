<?php
    include "dbconfig.inc.php";
    session_start();

    if ( isset( $_SESSION['user_id'] ) ) {
        // Redirect them to the home page if logged in
        header("Location: http://matthewheck.me/yoohoo/home.php?conversation=1");
    }
    
    $accountCreated = false;
    $accountCreationFail = false;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account</title>
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Titillium+Web:wght@300&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="./../node_modules/@fortawesome/fontawesome-free/css/all.css">
    <link rel="stylesheet" href="./css/accounts.css">
    <link rel="icon" type="image/png" href="./imgs/logo.png">
</head>
<body>

    <!-- Master Container -->
    <div id="master">

        <!-- Sub-container -->
        <div id="sub-container">
            <div id="sub-content">
                <img id="logo" src="./imgs/logo.png"/>
                <div id="mission">Simplify the process of communication</div><br>
                <div id="description">yoohoo is an online messaging platform designed to be used in a simple, clear way.</div>
            </div>
        </div>

        <!-- Main Container -->
        <div id="main-container">
            <div id="main-content">
                <div id="account-title">CREATE ACCOUNT<i class="fas fa-level-down-alt"></i></div>
                <div id="account-form">
                    <form action="" method="post">
                        <input class="account-input" type="text" name="username" placeholder="Username" max="20" required>
                        <input class="account-input" type="password" name="password" placeholder="Password" max="20" required>
                        <input id="account-button" class="account-input-button" type="submit" value="CREATE ACCOUNT">
                    </form>
                    <?php
                        if (!empty($_POST)) {
                            if ( isset( $_POST['username'] ) && isset( $_POST['password'] ) ) {
                                $conn = openConnection();
                                $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
                                $stmt->bind_param('s', $_POST['username']);
                                $stmt->execute();
                                $result = $stmt->get_result();
                                $user = $result->fetch_object();
        
                                if(isset($user)){
                                    $accountCreationFail = true;
                                }else{
                                    $stmt = $conn->prepare("INSERT INTO users (username, password, imageFileName) VALUES (?, ?, ?)");
                                    $stmt->bind_param('sss', $_POST['username'], password_hash($_POST['password'], PASSWORD_DEFAULT), $file = "default.png");
                                    $stmt->execute();
                                    $accountCreated = true;
                                    $accountCreationFail = false;
                                }
                            }
                        }
                    ?>
                </div>
                <?php
                    if ($accountCreationFail){
                        echo '<div id="incorrect-input">The requested username already exists.</div><br>';
                    }
                    if (!$accountCreated){
                        echo '<div id="create-account-text">Already have an account? <a href="./login.php">Log In</a></div>';
                    } else {
                        echo '<div id="success-text">Account successfully created</div>';
                        echo '<div id="login-button"><a href="./login.php">Login <i class="fas fa-sign-in-alt"></i></a></div>';
                    }
                ?>
            </div>
        </div>
    </div>
</body>
</html>