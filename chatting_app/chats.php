<?php
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.php");
    exit;
}

require_once "config.php";

$logged_in_username = $_SESSION["username"];

if (isset($_GET['username'])) {
    $contact_username = $_GET['username'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Chat with <?php echo htmlspecialchars($contact_username); ?></title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style type="text/css">
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
        }

        #chat-container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .message {
            margin-bottom: 15px;
            padding: 10px;
            border-radius: 8px;
            background-color: #f0f0f0;
        }

        .message-sender {
            font-weight: bold;
        }

        .message-content {
            margin-left: 10px;
        }

        .message-timestamp {
            font-size: 0.8em;
            color: #666;
            margin-left: 10px;
        }

        #input-container {
            margin-top: 20px;
        }

        #message-input {
            width: calc(100% - 80px);
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            font-size: 16px;
            margin-right: 10px;
        }

        #send-button {
            padding: 10px 20px;
            border-radius: 5px;
            background-color: #007bff;
            color: #fff;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        #send-button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
   
<div id="chat-container">
    <div id="messages-container"></div>
    <div id="input-container" style="display: flex;">
        <input type="text" id="message-input" placeholder="Type your message..." style="flex: 1;">
        <button id="send-button" style="margin-left: 10px;">Send</button>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        let lastUpdateTime = 0;

        function sendMessage() {
            const message = document.getElementById("message-input").value;
            if (message.trim() === "") return;

            const xhr = new XMLHttpRequest();
            xhr.open("POST", "send_message.php", true);
            xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function() {
                if (xhr.readyState == XMLHttpRequest.DONE && xhr.status == 200) {
                    const response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        document.getElementById("message-input").value = "";
                        updateMessages();
                    } else {
                        console.error(response.error);
                    }
                }
            };
            xhr.send("receiver=" + encodeURIComponent('<?php echo $contact_username; ?>') + "&message=" + encodeURIComponent(message));
        }

        function updateMessages() {
            const xhr = new XMLHttpRequest();
            xhr.open("GET", "get_message.php?username=<?php echo $contact_username; ?>&last_update_time=" + lastUpdateTime, true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState == XMLHttpRequest.DONE && xhr.status == 200) {
                    const response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        const messagesContainer = document.getElementById("messages-container");
                        response.messages.forEach(msg => {
                            const div = document.createElement("div");
                            div.innerHTML = msg;
                            messagesContainer.appendChild(div);
                        });
                        if (response.messages.length > 0) {
                            lastUpdateTime = response.last_message_timestamp;
                        }
                    } else {
                        console.error(response.error);
                    }
                }
            };
            xhr.send();
        }

        document.getElementById("send-button").addEventListener("click", function() {
            sendMessage();
        });

        document.getElementById("message-input").addEventListener("keypress", function(event) {
            if (event.key === "Enter") {
                sendMessage();
            }
        });

        setInterval(updateMessages, 2000);
    });
</script>
</body>
</html>
