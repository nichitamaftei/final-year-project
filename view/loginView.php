<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="../view/css_js_images/style.css"> <!-- linking to style sheet -->
        <script src="../@joint/core/dist/joint.js"></script> <!-- linking to JointJS library -->
        <script src="https://code.jquery.com/jquery-3.7.1.js"></script> <!-- linking to j query -->
        <script type="text/javascript" src="../view/css_js_images/javascript.js"></script> <!-- linking to javascript -->
        <title>Log-in</title>
    </head>

    <body id="loginView">
        <!-- top bar of the screen -->
        <section id='top-bar'>
            <h1 id="top-bar-title"> Login </h1>
        </section>   
        
        
        <div id="loginDiv">
            <p id="login_title">Log-in: </p>
            <div id="section_login">
                <form method="post" action="../controller/login.php">
                    <label for="username">E-mail:</label>
                    <br>
                    <input type="text" id="username" name="logInEmail">
                    <br>
                    <label for="password">Password:</label>
                    <br>
                    <input type="password" id="password" name="logInPassword">
                    <br>
                    <br>
                    <input type="submit" value="Login">
                    <br>
                    <br> 
                </form>
            </div>
        </div>
    </body>
</html>