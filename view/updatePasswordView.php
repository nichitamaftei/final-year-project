<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="../view/css_js_images/style.css"> <!-- linking to style sheet -->
        <script src="../@joint/core/dist/joint.js"></script> <!-- linking to JointJS library -->
        <script src="https://code.jquery.com/jquery-3.7.1.js"></script> <!-- linking to j query -->
        <script type="text/javascript" src="../view/css_js_images/javascript.js"></script> <!-- linking to javascript -->
        <title>Update Password</title>
    </head>

    <body id="loginView">
        <!-- top bar of the screen -->
        <section id='topBar'>
            <h1 id="topBarTitle"> Update Password</h1>
        </section>   
        <div id="loginContainer">
            <div id="loginSubContainer">

                <div id="loginTitleContainer">
                    <h2 id="loginTitle">Update Password</h2>
                </div>

                <form id="loginForm" onsubmit="return updatePasswordValidation()" method="post" action="../controller/login.php" >

                    <div class="loginFlexContainer">
                        <label for="email">New Password:</label>
                        <input type="password" id="password" name="newPassword" required><br>
                    </div>

                    <div class="loginFlexContainer">
                        <label for="password"> Confirm Password:</label>
                        <input type="password" id="confirmPassword" name="confirmPassword" required><br>
                    </div>

                    <div id="loginButtonContainer">
                        <button class="loginButton" id="loginFormButton" type="submit" value="Login">Update & Log-in</button>
                    </div>
                </form>
            </div>
        </div>
    </body>
</html>