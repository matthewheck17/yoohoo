<?php
    ob_start();
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

    function convertTime($sendTime){
        
        return str_replace("-", "/", substr($sendTime, 5, -3));
    }
    

    $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>yoohoo - Home</title>
    <link rel="icon" type="image/png" href="./imgs/logo.png">
    <link rel="stylesheet" href="./css/home.css">
    <link rel="stylesheet" href="./../node_modules/@fortawesome/fontawesome-free/css/all.css">
    <script>
        window.onload=function () {
            var objDiv = document.getElementById("chat");
            objDiv.scrollTop = objDiv.scrollHeight;
        }
    </script>
</head>
<body>
    <!-- Master Container -->
    <div id="master">

        <!-- Side Menu -->
        <div id="side-menu">

            <!-- Conversation List -->
            <div id="list-title">CONVOS</div>
            <img id="logo" src="./imgs/logo.png"/>
            <div id="list-items">
                <?php
                    $stmt = $conn->prepare("SELECT * FROM users WHERE NOT user_id = ?"); //get each user excluding the logged in user
                    $stmt->bind_param('i', $_SESSION['user_id']);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    while($row = $result->fetch_assoc()) {
                        echo '<a class="conversation-link" href="home.php?conversation=' . $row["user_id"] . '">';
                        if ($row["user_id"] == $_GET['conversation']){
                            echo '<div class="list-item selected">';
                            echo '<div class="image-cropper-left">';
                            echo '<img class="profile-pic" src="./profile_imgs/' . $row["imageFileName"] . '" alt="User Icon">';
                            echo '</div>';
                            echo '<span class="username">' . $row["username"] . '<i class="fas fa-sync-alt"></i></span>';
                            echo '</div></a>';
                        } else {
                            echo '<div class="list-item">';
                            echo '<div class="image-cropper-left">';
                            echo '<img class="profile-pic" src="./profile_imgs/' . $row["imageFileName"] . '" alt="User Icon">';
                            echo '</div>';
                            echo '<span class="username">' . $row["username"] . '</span>';
                            echo '</div></a>';
                        }
                    }
                ?>
            </div>
        </div>

        <!-- Chat & Message Type -->
        <div id="conversation">

            <!-- Chat Message Area -->
            <div id="chat">
                <?php
                    if (isset($_GET['conversation'])) {
                      if ($_GET['conversation'] == "1"){ //get global convo
                        $stmt = $conn->prepare("SELECT sendTime, content, sender_id  FROM messages WHERE recipient_id='1' ORDER BY sendTime ASC"); //get each message from sender to user
                        $stmt->execute();
                        $result = $stmt->get_result();
                        while($row = $result->fetch_assoc()) {
                            if ($row["sender_id"] == $_SESSION['user_id']){
                              $sendTime = convertTime($row["sendTime"]);
                              echo '<div class="message sender">';
                              echo '<div class="message-content message-sender-content"><span class="send-time">' . $sendTime . '</span><br>' . $row["content"] . '</div>';
                              echo '</div>';
                            } else {
                              $stmt2 = $conn->prepare("SELECT username, imageFileName FROM users WHERE user_id = ?"); //get the username of the sender
                              $stmt2->bind_param('i', $row['sender_id']);
                              $stmt2->execute();
                              $result2 = $stmt2->get_result();
                              $sender = $result2->fetch_object();
                              $sendTime = convertTime($row["sendTime"]);
                              echo '<div class="message receiver global">';
                              echo '<img class="sender-pic" src="./profile_imgs/' . $sender->imageFileName . '" alt="User Icon">';
                              echo '<div class="message-content message-receiver-content"><span class="send-time">' . $sendTime . '</span><span class="sender-username"> - ' . $sender->username . '</span><br>' . $row["content"] . '</div>';
                              echo '</div>';
                            }
                        }
                      } else { //get any other convo
                        $stmt = $conn->prepare("SELECT sendTime, content, sender_id  FROM messages WHERE (recipient_id = ? AND sender_id = ?) OR (recipient_id = ? AND sender_id = ?) ORDER BY sendTime ASC"); //get each message from sender to user
                        $stmt->bind_param('issi', $_SESSION['user_id'], $_GET['conversation'], $_GET['conversation'], $_SESSION['user_id']);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        if (mysqli_num_rows($result)==0){
                          echo '<div id="no-messages">no prior messages found</div>';
                          echo '<div id="start-convo">START THE CONVERSATION    <i class="fas fa-hand-point-down"></i></div>';
                        } else {
                          while($row = $result->fetch_assoc()) {
                              if ($row["sender_id"] == $_SESSION['user_id']){
                                  $sendTime = convertTime($row["sendTime"]);
                                  echo '<div class="message sender">';
                                  echo '<div class="message-content message-sender-content"><span class="send-time">' . $sendTime . '</span><br>' . $row["content"] . '</div>';
                                  echo '</div>';
                              } else {
                                  $sendTime = convertTime($row["sendTime"]);
                                  echo '<div class="message receiver">';
                                  echo '<div class="message-content message-receiver-content"><span class="send-time">' . $sendTime . '</span><br>' . $row["content"] . '</div>';
                                  echo '</div>';
                              }
                          }
                        }
                      }
                    }

                ?>

            </div>

            <!-- Message Typing Area -->
            <div id="typing-area">
                <div id="message-container">
                    <form id="message-form" action="" method="post">
                        <input id="text-input" type="text" name="message" placeholder="Type in your message..." max="500">
                        <input id="send" type="image" src="./imgs/send.png" alt="submit">
                    </form>
                    <?php
                        if (isset($_POST['message']) && $_POST['message'] != "") {
                            date_default_timezone_set('America/New_York');
                            date("Y-m-d H:i:s");
                            $stmt = $conn->prepare("INSERT INTO messages (sendTime, content, sender_id, recipient_id) VALUES (?, ?, ?, ?)");
                            $stmt->bind_param('ssii', date("Y-m-d H:i:s"), $_POST['message'], $_SESSION['user_id'], $_GET['conversation']);
                            $stmt->execute();
                            header("Refresh:0");
                        }
                    ?>
                </div>
            </div>
        </div>

        <!-- user profile area -->
        <div id="profile-area">
            <div id="image-cropper">
                <?php
                    echo '<img id="user-pic" src="./profile_imgs/' . $_SESSION["user_image"] . '" alt="User Icon">';
                ?>
            </div>
            <div id="profile-options">
                <form id="account-button" action="myAccount.php" method="post"><input class="user-button" type="submit" value="MY ACCOUNT"></form>
                <form id="logout-button" action="" method="post"><input type="hidden" name="logout"><input class= "user-button" type="submit" value="LOGOUT"></form>
            </div>
            <!-- Custom JavaScript -->
            <script src="./js/userProfile.js"></script>
        </div>
    </div>
</body>
</html>