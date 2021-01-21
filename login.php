<?php
    include "dbconfig.inc.php";
    session_start();

    if ( isset( $_SESSION['user_id'] ) ) {
        // Redirect them to the home page if logged in
        header("Location: http://matthewheck.me/yoohoo/home.php?conversation=1");
    }
    
    $incorrectLogin = false;
    
    
    if (!empty( $_POST)) {
        if (isset( $_POST['username']) && isset( $_POST['password'])) {
            // Getting user data from database for submitted username
            $conn = openConnection();
            $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
            $stmt->bind_param('s', $_POST['username']);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_object();

            // Verify user password and set $_SESSION
            if (password_verify($_POST['password'], $user->password)) {
                $_SESSION['user_id'] = $user->user_id;
                $_SESSION['username'] = $user->username;
                $_SESSION['user_image'] = $user->imageFileName;
                header("Location: http://matthewheck.me/yoohoo/home.php?conversation=1");
                die();
            } else {
                $incorrectLogin = true;
            }
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
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
                <div id="account-title">SIGN IN HERE<i class="fas fa-level-down-alt"></i></div>
                <div id="account-form">
                    <form action="" method="post">
                        <input class="account-input" type="text" name="username" placeholder="Username" required>
                        <input class="account-input" type="password" name="password" placeholder="Password" required>
                        <input id="account-button" class="account-input-button" type="submit" value="LOGIN">
                    </form>
                    <?php
                        if ($incorrectLogin){
                            echo '<p id="incorrect-input">* username or password is incorrect</p>';   
                        }
                    ?>
                </div>
                <div id="create-account-text">Don't have an account? <a href="./createAccount.php">Create one</a></div>
            </div>
        </div>
    </div>
</body>
</html>