<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include('../assets/connection/connection.php');
$bakerid=$_SESSION['bid'];
if(isset($_POST['user'])){
  $userid = $_POST['user'];}else{$userid=NULL;}
if(isset($_SESSION['userid'])){
  $userid=$_SESSION['userid'];
}else{$userid=NULL;}
$sql = "SELECT c.chatroom_id, c.userid, c.bakerid, MAX(m.created_at) as last_message_time
            FROM chatroom c
            LEFT JOIN messages m ON c.chatroom_id = m.chatroom_id
            WHERE c.bakerid = $bakerid   
            GROUP BY c.chatroom_id
            ORDER BY last_message_time DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>baker messages</title>
    <style>
        .container{
            display: grid;
            grid-template-columns: 1fr 1fr 4fr;
            grid-template-areas: 
            "nav messageList body";
        }
        .navbar{
            grid-area: nav;
        }
        .all{
            grid-area: body;
            margin-top: 88px;
            width: 800px;
            height: 800px;
            box-shadow: #ce3434 0px 20px 30px -10px;
            background-color: #171717;
            border-radius: 25px;
            margin-left: 100px;
            margin-top: 100px;
            overflow-y: auto;
        }
        body{
            padding: 0px;
            margin: 0px;
            background-color: #1e1e1e;
        }
        .messageList{
            width: 350px;
            height: 750px;
            margin-top: 100px;
            box-shadow: #ce3434 0px 20px 30px -10px;
            background-color: #171717;
            border-radius: 25px;
            overflow-y: scroll;
            padding-inline: 5px;
            padding-block: 2px;
        }
        .messageList h1{
            margin-top: 0px;
            margin-bottom: 20px;
            margin-left: 5px;
            font-size: 40px;
            font-family: fantasy;
            color: #fff;
        }
        .usrimg{
          width: 40px;
          height: 40px;
          border-radius: 45%;
          background-size: cover;
          background-repeat: no-repeat;
          background-position: center;
        }
        .user{
          display: flex;
          align-items: center;
          border-bottom: solid 1px #4a4a4a;
        }
        .user p{
          position: relative;
          left: 20px;
          color: #fff;
        }
        .notification{
          position: relative;
          left:230px;
          color: #fff;
          background-color: #980000;
          padding-inline: 5px;
          border-radius: 50%;

        }


        .chat {
      width: 800px;
      height: 800px;
      background-color: #dbd6cf;
    }

    .nochat {
      display: flex;
      justify-content: center;
      align-items: center;
      font-size: 10px;
      color: gray;
      height: 800px;
      font-family: monospace;
    }

    .chatHead {
      width: 800px;
      height: 100px;
      display: flex;
      border-bottom: wheat solid 0.5px;
      background-color: #fff;
    }

    .chatHead img {
      width: 75px;
      height: 75px;
      border-radius: 50%;
      margin-top: 8px;
    }

    .chatHead p {
      font-size: 25px;
      font-weight: 700;
      margin-left: 25px;
    }

    .chatHead i {
      color: #1e1e1e;
      font-size: 15px;
      position: relative;
      top: 60px;
      right: 50px;
    }

    .messageplace {
      padding-top: 10px;
      overflow-y: scroll;
      width: 800px;
      height: 620px;
      background-color: #dbd6cf;
      max-width: 780px;
      margin: 0 auto;
      display: flex;
      flex-direction: column;
      gap: 10px;
    }

    .textbox form {
      width: 800px;
      height: 80px;
      display: flex;
      padding-top: 20px;
    }

    .sendMessage {
      width: 500px;
      height: 35px;
      background-color: #fff;
      border-radius: 45px;
      padding-left: 50px;
      margin-left: 50px;
      border: none;
    }

    .sendButton {
      height: 35px;
      width: 70px;
      border-radius: 45px;
      background-color: #980000;
      margin-left: 35px;
    }

    button {
      font-family: inherit;
      margin-left: 5px;
      height: 35px;
      width: 100px;
      font-size: 14px;
      background: #980000;
      color: white;
      padding: 0.7em 1em;
      padding-left: 0.9em;
      display: flex;
      align-items: center;
      border: none;
      border-radius: 16px;
      overflow: hidden;
      transition: all 0.2s;
      cursor: pointer;
    }

    button span {
      display: block;
      margin-left: 0.3em;
      transition: all 0.3s ease-in-out;
    }

    button svg {
      display: block;
      transform-origin: center center;
      transition: transform 0.3s ease-in-out;
    }

    button:hover .svg-wrapper {
      animation: fly-1 0.6s ease-in-out infinite alternate;
    }

    button:hover svg {
      transform: translateX(1.2em) rotate(45deg) scale(1.1);
    }

    button:hover span {
      transform: translateX(5em);
    }

    button:active {
      transform: scale(0.95);
    }

    @keyframes fly-1 {
      from {
        transform: translateY(0.1em);
      }

      to {
        transform: translateY(-0.1em);
      }
    }

    .chat-container {
      max-width: 600px;
      margin: 0 auto;
      display: flex;
      flex-direction: column;
      gap: 10px;
    }

    .message {
      width: fit-content;
      max-width: 70%;
      min-width: 100px;
      padding: 10px 15px;
      border-radius: 20px;
      position: relative;
      font-size: 16px;
      line-height: 1.5;
    }

    .receiver {
      background-color: #f8c6c6;
      align-self: flex-end;
      border-top-right-radius: 0;
    }

    .sender {
      background-color: #fff;
      align-self: flex-start;
      border-top-left-radius: 0;
    }

    .time {
      font-size: 12px;
      color: #999;
      position: absolute;
      bottom: 5px;
      right: 10px;
    }
    .searchResults{
      width: 350px;
      height: 690px;
      overflow-y: scroll;
      background-color: #fff;
      display: none;
    }
        </style>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
  $(document).ready(function () {
    var scrollThreshold = 50; // Distance in pixels from the bottom to trigger scroll

// Check mouse pointer position within the .messageplace div
$('.messageplace').on('mousemove', function(event) {
    var messagePlace = $(this);
    var messagePlaceHeight = messagePlace.height();
    var scrollTop = messagePlace.scrollTop();
    var scrollHeight = messagePlace[0].scrollHeight;
    var mousePositionY = event.clientY - messagePlace.offset().top; // Mouse Y position within the div

    // If mouse is near the bottom, scroll to bottom
    if (mousePositionY >= (messagePlaceHeight - scrollThreshold)) {
        scrollToBottom();
    }
});

function scrollToBottom() {
    var messagePlace = $('.messageplace');
    messagePlace.scrollTop(messagePlace[0].scrollHeight);
}

// Periodically fetch messages every 5 seconds
setInterval(fetchMessages, 5000);

function fetchMessages() {
    var userid = <?php echo json_encode($userid); ?>;
    var bakerid = <?php echo json_encode($bakerid); ?>;

    $.ajax({
        url: '../assets/component/load_chat.php',
        type: 'POST',
        data: {
            userid: userid,
            bakerid: bakerid
        },
        success: function (response) {
            var messagePlace = $('.messageplace');
            messagePlace.html(response);  // Update the chat messages
        },
        error: function (xhr, status, error) {
            console.error('Error fetching messages:', error);
        }
    });
}
    // Send a message using AJAX
    $('.textbox form').on('submit', function (e) {
      e.preventDefault();
      var message = $('.sendMessage').val();

      $.ajax({
        url: '../assets/component/baker_send_messages.php', // PHP file to handle sending message
        type: 'POST',
        data: {
          userid: <?php echo json_encode($userid); ?>,
          bakerid: <?php echo json_encode($bakerid); ?>,
          message: message
        },
        success: function (response) {
          if (response === 'Message sent') {
            $('.sendMessage').val(''); // Clear the message input
            fetchMessages(); // Reload chat messages
          } else {
            console.error('Message not sent:', response);
          }
        },
        error: function(xhr, status, error) {
          console.error('AJAX Error:', status, error);
        }
      });
    });



    //to refresh with updated baker id
$('.user').on('click', function () {
      var newUserId = $(this).data('userid');
  //  var newBakerId = //$(this).data('bakerid'); // Get the bakerid from the clicked user div

    // Update the bakerid in PHP using an AJAX request
    $.ajax({
      url: '../assets/component/update_userid.php', // PHP file to update bakerid
      type: 'POST',
      data: {
        userid: newUserId
      },
      success: function (response) {
        if (response.trim() === 'Userid updated') {
          location.reload(); // Refresh the page to update chathead and messageplace
        } else {
          console.error('Failed to update userid:', response);
        }
      },
      error: function(xhr, status, error) {
        console.error('AJAX Error:', status, error);
      }
    });
  });

    // Initial fetch of messages
    fetchMessages();
  });

</script>

</head>
<body>
<?php 
include('../assets/component/header.php');
?>
<div class="container">
     <div class="navbar">
      <?php include('../assets/component/baker_navbar.php'); ?>
      <style>#messages{ color:red;}</style>
    </div>
    <div class="messageList">
        <h1>Sugared Chat</h1>
        <?php while ($row = $result->fetch_assoc()): ?>
          <?php
          // Fetch user details for chat list
          $chatroom_id = $row['chatroom_id'];
          $chatlist_query = "SELECT `name`,`profile_pic`
                             FROM users
                             WHERE userid = ?";
          $unread_query = "SELECT COUNT(*) as unread_count 
                           FROM messages 
                           WHERE chatroom_id = ? AND is_read = 0 AND sender_type='user'";
          $chatlist_stmt = $conn->prepare($chatlist_query);
          $chatlist_stmt->bind_param("i", $row['userid']);
          $chatlist_stmt->execute();
          $chatlist_result = $chatlist_stmt->get_result();
          $chatlist_details = $chatlist_result->fetch_assoc();

          $unread_stmt = $conn->prepare($unread_query);
          $unread_stmt->bind_param("i", $chatroom_id);
          $unread_stmt->execute();
          $unread_result = $unread_stmt->get_result();
          $unread_count = $unread_result->fetch_assoc()['unread_count'];

          ?>
        <div class="user" data-userid="<?php echo $row['userid'] ?>">
            <div class="usrimg" style="background-image: url( <?php echo $chatlist_details['profile_pic']; ?>);"></div>
            <p> <?php echo $chatlist_details['name']; ?></p>
            <?php if ($unread_count > 0): ?>
              <div class="notification"><?= $unread_count; ?></div>
            <?php endif; ?>
          </div>
        <?php endwhile; ?>

        <br>
    </div>
    <div class="all">
    <div class="chat">
        <?php  if($userid==NULL){ ?>
           <div class="nochat">
        <h1>No Chats Selected</h1>
    </div> <?php }else{
            $check_query="SELECT c.chatroom_id FROM chatroom c WHERE c.bakerid = $bakerid AND c.userid = $userid;";
            $check_result=$conn->query($check_query);
            $check=$check_result->fetch_assoc();
            if(!$check){
              $chatroom_id=$check['chatroom_id'];
              $new_chatroom="INSERT INTO chatroom (`userid`,`bakerid`) VALUES ($userid,$bakerid)";
              $chat_exec=$conn->query($new_chatroom);
            }
            $chathead_query="SELECT `name`,`profile_pic`
                            FROM users  
                            WHERE userid = $userid;";
            $chathead_result=$conn->query($chathead_query);
            $chathead_row=$chathead_result->fetch_assoc();
            ?>
        <div>
          <div class="chatHead">
            <img src="<?php echo $chathead_row['profile_pic'] ?>" alt="">
            <p><?php echo $chathead_row['name'];?></p>
            <i>user</i>
          </div>
          <?php 
          
          $fetch_messagesQ="SELECT m.message_text,m.sender_type,m.created_at FROM messages m WHERE m.chatroom_id = (SELECT c.chatroom_id 
                       FROM chatroom c 
                       WHERE c.bakerid = $bakerid AND c.userid = $userid)
                        ORDER BY m.created_at ASC;";
          $fetch_messageR=$conn->query($fetch_messagesQ);
          $markread="UPDATE messages SET is_read=1 WHERE chatroom_id=$chatroom_id AND sender_type='user'";
          $markreadR=$conn->query($markread);

          ?>
          <div class="messageplace">
            
          </div>
          <?php }?>
          <div class="textbox">
            <form method="post" id="sendMess">
              <input type="text" name="sendMessage" placeholder="Message.." class="sendMessage">
              <button type="submit">
                <div class="svg-wrapper-1">
                  <div class="svg-wrapper">
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      viewBox="0 0 24 24"
                      width="24"
                      height="24">
                      <path fill="none" d="M0 0h24v24H0z"></path>
                      <path
                        fill="currentColor"
                        d="M1.946 9.315c-.522-.174-.527-.455.01-.634l19.087-6.362c.529-.176.832.12.684.638l-5.454 19.086c-.15.529-.455.547-.679.045L12 14l6-8-8 6-8.054-2.685z"></path>
                    </svg>
                  </div>
                </div>
                <span>Send</span>
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>
</div>
<?php
  include('../assets/component/Footer.php');
?>
</body>
</html>