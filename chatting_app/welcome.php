<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Anonymously Chat</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style type="text/css">
        @font-face {
            font-family: headFont;
            src: url(ui/fonts/airstrike.ttf);
        }

        @font-face {
            font-family: mainFont;
            src: url(ui/fonts/Arial.ttf);
        }

        #wrapper {
            max-width: 900px;
            min-height: 500px;
            display: flex;
            margin: auto;
            color: black;
            font-size: 13px;
            font-family: mainFont;
        }

        #left_panel {
            min-height: 500px;
            background-color: lightgray;
            flex: 1;
            text-align: center;
            justify-content: center;
            align-items: center;
        }

        #profile_image {
            width: 150px;
            height: 150px;
            border: solid thin white;
            border-radius: 50%;
            margin: 10px;
        }

        .panel_label {
            width: 100%;
            height: 40px;
            display: block;
            background-color: #ffffff55;
            border-bottom: solid thin white;
            cursor: pointer;
            padding: 5px;
            transition: all 0.5s ease;
        }

        .panel_label:hover {
            background-color: #ffffff;
        }

        .panel_label img {
            float: right;
            width: 30px;
            height: auto;
        }

        #right_panel {
            display: flex;
            flex-direction: column;
            min-height: 500px;
            background-color: whitesmoke;
            flex: 4;
            text-align: center;
        }

        #header {
            background-color: gray;
            height: 70px;
            font-size: 40px;
            text-align: center;
            font-family: headFont;
            display: flex;
            justify-content: center;
            align-items: center;
            color: white;
        }

        #container {
            display: flex;
            position: relative;
            flex: 1;
        }

        #contact_panel {
            background-color: whitesmoke;
            flex: 1;
            min-height: 430px;
        }

        #radio_contacts,
        #radio_signout {
            display: none;
        }

        #radio_contacts:checked ~ #container,
        #radio_signout:checked ~ #container {
            display: none;
        }
    </style>
</head>
<body>
    <div id="wrapper">
        <div id="left_panel">
            <?php
                session_start();
                if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
                    header("location: index.php");
                    exit;
                }
            ?>
            <div id="user_info" style="padding: 10px;">
                <img id="profile_image" src="ui/images/catt.png">
                <br>
                <span id="username"><?php echo htmlspecialchars($_SESSION["username"]); ?></span>
                <br>
                <br>
                <div>
                    <label class="panel_label" id="label_contacts" for="radio_contacts">Contacts <img src="ui/icons/contacts.png"></label>
                    <label class="panel_label" id="label_signout" for="radio_signout">
                    <a href="logout.php" style="text-decoration: none; color: black;">
                    Sign Out <img src="ui/icons/signout.png">
                    </a>
                    </label>
                    <a href="reset-password.php" class="btn btn-warning">Reset Password</a>
                </div>
            </div>
        </div>
        <div id="right_panel">
            <div id="header">Anonymously Chat...</div>
            <div id="container">
                <div id="inner_left_panel"></div>
                <div id="inner_right_panel">
                    <div id="contacts_panel" style="display: none;">
                        <?php include_once("contacts.php"); ?>
                    </div>
                </div>
                <input type="radio" id="radio_contacts" name="main_radio">
                <input type="radio" id="radio_signout" name="main_radio">
            </div>
        </div>
    </div>

    <script>
        document.getElementById("label_contacts").addEventListener("click", function() {
            document.getElementById("radio_contacts").checked = true;
            document.getElementById("header").textContent = "Contacts";
            document.getElementById("contacts_panel").style.display = "block";
        });

        document.getElementById("label_signout").addEventListener("click", function() {
            document.getElementById("radio_signout").checked = true;
            document.getElementById("header").textContent = "Signing Out...";
            window.location.href = 'logout.php'; 
        });
    </script>
</body>
</html>
