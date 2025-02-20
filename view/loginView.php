<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <link rel="icon" href="../view/css_js_images/Phone_icon.png">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="../view/css_js_images/style.css"> <!-- linking to style sheet -->
        <script src="../@joint/core/dist/joint.js"></script> <!-- linking to JointJS library -->
        <script src="https://code.jquery.com/jquery-3.7.1.js"></script> <!-- linking to j query -->
        <script type="text/javascript" src="../view/css_js_images/javascript.js"></script> <!-- linking to javascript -->
        <title>Log-in</title>
    </head>

    <body id="loginView">
        <!-- top bar of the screen -->
        <section id='topBar'>
            <h1 id="topBarTitle"> Login </h1>
        </section>   
        <div id="loginContainer">
            <div id="loginSubContainer">

                <div id="loginTitleContainer">
                    <h2 id="loginTitle">Login</h2>
                </div>

                <form id="loginForm" method="post" onsubmit="return logInValidation()" action="../controller/login.php">

                    <div class="loginFlexContainer">
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="logInEmail" required><br>
                    </div>

                    <div class="loginFlexContainer">
                        <label for="password"> Password:</label>
                        <input type="password" id="password" name="logInPassword" required><br>
                    </div>

                    <div id="loginButtonContainer">
                        <button class="loginButton" id="loginFormButton" type="submit" value="Login">Log in</button>
                    </div>
                </form>
            </div>
        </div>
    </body>
</html>