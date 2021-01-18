<?php
    
    include "dbconfig.inc.php";
    
    // Always start this first
    session_start();

    // Put this code at the top of any protected page to redirect
    if (!isset( $_SESSION['user_id'])) {
        // Redirect them to the login page if not logged in
        header("Location: http://matthewheck.me/yoohoo/login.php");
    }
    
    if (isset( $_POST['logout'])) {
        session_destroy();

        header("Location: http://matthewheck.me/yoohoo/login.php");
    }
    
    $conn = openConnection();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Account</title>
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
            <div id="account-title" class="account-settings">ACCOUNT SETTINGS<i class="fas fa-level-down-alt"></i></div>
            <div id="account-forms-container">
                <form id="upload-photo" action="" enctype="multipart/form-data" method="post">
                    <div class="form-title">Upload a new profile photo:</div>
                    <label for="toUpload" id="file-upload" class="account-input-button">
                        CHOOSE FILE...
                    </label>
                    <label for="toSubmit" id="file-submit" class="account-input-button">
                        SUBMIT FILE
                    </label>
                    <div id="file-name"></div>
                    <input id="toUpload" type="file" name="toUpload">
                    <input id="toSubmit" type="submit" value="Upload" name="submit">
                    <?php
                        if($_SERVER["REQUEST_METHOD"] == "POST"){
                            if(isset($_FILES["toUpload"]) && $_FILES["toUpload"]["error"] == 0){
                                if(move_uploaded_file($_FILES["toUpload"]["tmp_name"], "profile_imgs/".$_SESSION['username'].".png")){
                                    $stmt=$conn->prepare("UPDATE users SET imageFileName = '".$_SESSION['username'].".png' WHERE user_id = ?");
                                    $stmt->bind_param('s', $_SESSION['user_id']);
                                    $stmt->execute(); 
                                    $_SESSION['user_image'] = $_SESSION['username'].".png";
                                    echo '<div class="success">Profile photo has been updated. You may have to clear your cache for the change to reflect in your browser.</div>';
                                }
                                else{
                                    echo "<div>There was an error updating your profile photo.</div>";
                                }
                            }
                        }
                    ?>
                </form>  
                <script src="./js/uploadPhoto.js"></script>
                <form action="" method="post">
                    <div class="form-title">Enter a new password:</div>
                    <input type="password" name="password" id="password" placeholder="Enter new password..." required>
                    <label for="change-password" id="change-password-button" class="account-input-button">
                        UPDATE PASSWORD
                    </label>
                    <input type="submit" value="changePassword" name="submit" id="change-password">
                </form>  
                <?php
                    if(isset($_POST['password'])){
                        $stmt=$conn->prepare("UPDATE users SET password = '".$_POST['password']."' WHERE user_id = ?");
                        $stmt->bind_param('s', $_SESSION['user_id']);
                        $stmt->execute(); 
                        echo '<div class="success">Password has been updated.</div>';
                    }
            
                ?>
            </div>
            <!-- user profile area -->
            <div id="profile-area">
                <div id="image-cropper">
                    <?php
                        echo '<img id="user-pic" src="./profile_imgs/' . $_SESSION["user_image"] . '" alt="User Icon">';
                    ?>
                </div>
                <div id="profile-options">
                    <form id="home-button" action="http://matthewheck.me/yoohoo/home.php?conversation=1" method="post"><input class="user-button" type="submit" value="HOME"></form>
                    <form id="logout-button" action="" method="post"><input type="hidden" name="logout"><input class= "user-button" type="submit" value="LOGOUT"></form>
                </div>
                <!-- Custom JavaScript -->
                <script src="./js/userProfile.js"></script>
            </div>
        </div>
    </div>
</body>
</html>